<?php
// 汎用関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
// DB関数ファイル読み込み
require_once MODEL_PATH . 'db.php';

// DB利用
/**
 * id指定で商品id,name,stock,price,image,statusを取得
 * @param mixed $db db hundle
 * @param it $item_id 商品ID
 * @return array 検索結果
 */
function get_item($db, $item_id){
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = ?
  ";

  return fetch_query($db, $sql, array($item_id));
}

/**
 * 商品一覧の情報を取得
 * @param mixed $db db hundle
 * @param bool $is_open 全取得(false)か、公開のみ(true)か
 * @return array 取得結果
 */
function get_items($db, $is_open = false){
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  if($is_open === true){
    $sql .= '
      WHERE status = 1
    ';
  }

  return fetch_all_query($db, $sql);
}

/**
 * 商品情報すべて取得、ステータス関係なし
 * @param mixed $db DB hundle
 * @return array 取得結果
 */
function get_all_items($db){
  return get_items($db);
}

/**
 * 公開の商品のみの情報取得
 * @param mixed $db DB hundle
 * @param int $page
 * @return array 取得結果
 */
// get_page_itemsの関数のみで良いと考えコメントアウト
//function get_open_items($db, $page=1){
//  return get_page_items($db, true, $page);
//}

/**
 * 入力情報チェック＆商品登録
 * @param mixed $db DBハンドル
 * @param str $name 商品名
 * @param int $price 値段
 * @param int $stock 在庫数
 * @param str $status open or close
 * @param mixed $image
 * @return bool
 */
function regist_item($db, $name, $price, $stock, $status, $image){
  $filename = get_upload_filename($image);
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    return false;
  }
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}

/**
 * 商品一覧の情報を取得
 * @param mixed $db db hundle
 * @param int $page 取得ページ数
 * @param int $per_page_items １ページあたりの商品取得数
 * @return array 取得結果
 */
function get_page_items($db, $page=1, $per_page_items){
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE status = 1
    LIMIT ?,?
  ';

  return fetch_all_query($db, $sql, [($page-1)*$per_page_items, $per_page_items]);
}

/**
 * 総ページ数の取得
 * @param mixed $db
 * @return array|bool
 */
function get_item_total_page($db, $per_page_items) {
  $sql = "SELECT count(*)/? AS total FROM items WHERE status=1";
  $data = fetch_query($db, $sql, [$per_page_items]);
  return ceil($data['total']);
}

/**
 * 現在のページ数の取得
 * @return int
 */
function get_item_page() {
  if (get_get('page') === "") {
    return 1;
  } else {
    return get_get('page');
  }
}

/**
 * DBへの商品登録と画像保存
 * @param mixed $db
 * @param str $name 商品名
 * @param int $price 値段
 * @param int $stock 在庫数
 * @param str $status open or close
 * @param mixed $image
 * @param str $filename ファイル名
 * @return bool
 */
function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  $db->beginTransaction();
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
    && save_image($image, $filename)){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
  
}

/**
 * DBへの商品登録
 * @param mixed $db DBハンドル
 * @param str $name 商品名
 * @param int $price 金額
 * @param int $stock 在庫数
 * @param str $filename ファイル名
 * @param str $status 公開(open) or 非公開(close)
 * @return bool execute_queryの結果
 */
function insert_item($db, $name, $price, $stock, $filename, $status){
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES(?, ?, ?, ?, ?);
  ";

  return execute_query($db, $sql, array($name, $price, $stock, $filename, $status_value));
}

/**
 * 商品ステータスの変更
 * @param mixed $db DBハンドル
 * @param int $item_id 商品ID
 * @param int $status 1 or 0
 * @return bool
 */
function update_item_status($db, $item_id, $status){
  $sql = "
    UPDATE
      items
    SET
      status = ?
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  return execute_query($db, $sql, array($status, $item_id));
}

/**
 * 商品の在庫数変更
 * @param mixed $db DBハンドル
 * @param int $item_id 商品ID
 * @param int $stock 在庫数
 * @return bool 
 */
function update_item_stock($db, $item_id, $stock){
  $sql = "
    UPDATE
      items
    SET
      stock = ?
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  return execute_query($db, $sql, array($stock, $item_id));
}

/**
 * 商品情報、画像の削除
 * @param mixed $db DBハンドル
 * @param int $item_id 商品ID
 * @return bool
 */
function destroy_item($db, $item_id){
  // 商品情報を取得
  $item = get_item($db, $item_id);
  // 取得できない場合はfalseを返す
  if($item === false){
    return false;
  }
  $db->beginTransaction();
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
}

/**
 * DBから商品情報削除
 * @param mixed $db DBハンドル
 * @param int $item_id 商品ID
 * @return bool
 */
function delete_item($db, $item_id){
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  return execute_query($db, $sql, array($item_id));
}


// 非DB
/**
 * ステータスの確認
 * @param array $item 商品情報
 * @return bool
 */
function is_open($item){
  return $item['status'] === 1;
}

/**
 * 入力チェック＆値代入
 * @param str $name 商品名
 * @param int $price 値段
 * @param int $stock 在庫数
 * @param str $filename ファイル名
 * @param str $status open or close
 * @return mixed 各値が代入された変数を返す
 */
function validate_item($name, $price, $stock, $filename, $status){
  $is_valid_item_name = is_valid_item_name($name);
  $is_valid_item_price = is_valid_item_price($price);
  $is_valid_item_stock = is_valid_item_stock($stock);
  $is_valid_item_filename = is_valid_item_filename($filename);
  $is_valid_item_status = is_valid_item_status($status);

  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

/**
 * 名前の入力チェック
 * @param str $name 商品名
 * @param bool
 */
function is_valid_item_name($name){
  $is_valid = true;
  // 文字数の条件に満たない場合にはエラーメッセージを設定する
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}

/**
 * 値段の入力チェック
 * @param int $price 値段
 * @return bool
 */
function is_valid_item_price($price){
  $is_valid = true;
  // 条件を満たさない場合にエラーメッセージを設定する
  if(is_positive_integer($price) === false){
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

/**
 * 在庫数の入力チェック
 * @param int $stock
 * @return bool
 */
function is_valid_item_stock($stock){
  $is_valid = true;
  // 条件を満たさない場合にはエラーメッセージを設定する
  if(is_positive_integer($stock) === false){
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

/**
 * ファイル名の取得確認
 * @param str $filename
 * @return bool
 */
function is_valid_item_filename($filename){
  $is_valid = true;
  // ファイル名が空の場合はfalseを返す
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}

/**
 * ステータスの確認
 * @param str $status open or close
 * @return bool
 */
function is_valid_item_status($status){
  $is_valid = true;
  // statusが存在しない場合にfalseを返す
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}