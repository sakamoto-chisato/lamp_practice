<?php 
// 汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
// DB関数ファイルの読み込み
require_once MODEL_PATH . 'db.php';

/**
 * ユーザのカートに入っている全ての商品情報取得
 * @param mixed $db DBハンドル
 * @param int $user_id ユーザID
 * @return array 結果取得
 */
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
  return fetch_all_query($db, $sql, array($user_id));
}

/**
 * ユーザのカートに入っている特定の商品情報取得
 * @param mixed $db DBハンドル
 * @param int $user_id ユーザID
 * @param int $item_id 商品ID
 * @return array 結果取得
 */
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";

  return fetch_query($db, $sql, array($user_id, $item_id));

}

/**
 * カートへ追加ボタン押下時の処理
 * @param mixed $db DBハンドル
 * @param int $user_id ユーザID
 * @param $item_id 商品ID
 * @return bool DB登録、更新後の結果
 */
function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  // 情報が取得できなかったら新規登録する
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  // 存在していた場合には購入数を1追加する
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

/**
 * DBへのカート情報新規追加
 * @param mixed $db DBハンドル
 * @param int $user_id ユーザID
 * @param int $item_id 商品ID
 * @param int $amount 購入数
 * @return bool SQL実行結果
 */
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?, ?, ?)
  ";

  return execute_query($db, $sql, array($item_id, $user_id, $amount));
}

/**
 * カートにすでに存在する商品の購入数を変更する
 * @param mixed $db DBハンドル
 * @param int $cart_id 該当カートID
 * @param int $amount 購入数
 * @return bool SQL実行結果
 */
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  return execute_query($db, $sql, array($amount, $cart_id));
}

/**
 * DBからカートに入っている、該当商品の削除
 * @param mixed $db DBハンドル
 * @param int $cart_id 該当カートID
 * @return bool SQL実行結果
 */
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  return execute_query($db, $sql, array($cart_id));
}

/**
 * 購入できるか確認→在庫数変更→カート情報消去
 * @param mixed $db DBハンドル
 * @param array $carts カートの登録情報
 * @return bool 実行結果
 */
function purchase_carts($db, $carts){
  // 購入できるかチェック
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  // トランザクション開始
  $db -> beginTransaction();
  try {
    // 購入履歴に追加
    write_history($db, $carts[0]['user_id']);
    $last_insert_id = $db -> lastInsertId();

    // 配列から1商品ずつ取り出す
    foreach($carts as $cart){
      // 在庫数変更
      update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      );

      // 購入明細に追加
      write_history_detail(
        $db,
        $last_insert_id,
        $cart['name'],
        $cart['price'],
        $cart['amount']
      );
    }
    // 特定ユーザのカート情報の削除
    delete_user_carts($db, $carts[0]['user_id']);
    return $db -> commit();

  } catch (Exception $e) {
    $db -> rollBack();
    return false;
  }
}

/**
 * 特定ユーザのカート情報を消去
 * @param mixed $db DBハンドル
 * @param int $user_id ユーザID
 * @return bool 実行結果
 */
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";

  execute_query($db, $sql, array($user_id));
}

/**
 * 購入合計金額の計算
 * @param array $carts カートの商品情報
 * @return int $total_price 合計金額
 */
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

/**
 * 購入できるかチェック
 * @param array $carts 商品情報
 * @return bool チェック結果
 */
function validate_cart_purchase($carts){
  // 登録された情報が存在するか確認
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  // 1商品ずつ取り出して
  foreach($carts as $cart){
    // 公開かどうかを確認
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    // 在庫数が足りるかを確認
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  // エラーが一つでも存在するならfalseを返す
  if(has_error() === true){
    return false;
  }
  return true;
}

/**
 * 購入履歴情報をDB登録
 * @param mixed $db DBハンドル
 * @param int $user_id ユーザID
 * @return bool 
 */
function write_history($db, $user_id) {
  $sql = "
    INSERT INTO
      purchased_history(
        user_id
      )
      VALUES(?)
  ";
  return execute_query($db, $sql, [$user_id]);

}

/**
 * 購入明細情報をDB登録
 * @param mixed $db
 * @param int $last_insert_id
 * @param str $item_name
 * @param int $item_price
 * @param int $amount
 * @return bool 
 */
function write_history_detail($db, $last_insert_id, $item_name, $item_price, $amount) {
  $sql = "
    INSERT INTO
      purchased_history_detail(
        purchased_id,
        purchased_name,
        purchased_price,
        purchased_amount
      )
    VALUES(?, ?, ?, ?)
  ";
   return execute_query($db, $sql, [$last_insert_id, $item_name, $item_price, $amount]);
} 