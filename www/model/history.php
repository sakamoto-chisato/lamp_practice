<?php
// 汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
// DB関数ファイルの読み込み
require_once MODEL_PATH . 'db.php';

/**
 * 購入履歴情報を全て取得
 * @param mixed $db
 * @return array|bool
 */
function get_all_history_items($db) {
  $sql = "
    SELECT 
      h.purchased_id, 
      h.purchased_datetime, 
      SUM(d.purchased_price * d.purchased_amount) AS total
    FROM purchased_history AS h
    INNER JOIN purchased_history_detail AS d 
    ON h.purchased_id = d.purchased_id
    GROUP BY purchased_id 
    ORDER BY h.purchased_datetime DESC
  ";
  return fetch_all_query($db, $sql);
}

/**
 * 該当ユーザの購入履歴情報を取得
 * @param mixed $db
 * @param int $user_id
 * @param int $id 
 * @return array|bool
 */
function get_history_items($db, $user_id) {
  $sql = "
    SELECT 
      h.purchased_id, 
      h.purchased_datetime, 
      SUM(d.purchased_price * d.purchased_amount) AS total
    FROM purchased_history AS h
    INNER JOIN purchased_history_detail AS d
    ON h.purchased_id = d.purchased_id
    WHERE h.user_id = ?
    GROUP BY purchased_id 
    ORDER BY h.purchased_datetime DESC
  ";
  return fetch_all_query($db, $sql, [$user_id]);
}

/**
 * 該当注文番号の購入明細情報を取得
 * @param mixed $db
 * @param int $purchased_id 注文番号
 * @return array|bool
 */
function get_history_details($db, $purchased_id) {
  $sql = "
    SELECT
      h.purchased_id,
      h.user_id,
      h.purchased_datetime, 
      d.purchased_name,
      d.purchased_price,
      d.purchased_amount,
      d.purchased_price * d.purchased_amount AS subtotal
    FROM purchased_history_detail AS d
    INNER JOIN purchased_history AS h
    ON d.purchased_id = h.purchased_id
    WHERE h.purchased_id = ?
  ";
  return fetch_all_query($db, $sql, [$purchased_id]);
}

/**
 * 合計金額の計算
 * @param array $values
 * @return int $total
 */
function purchased_total($values) {
  foreach ($values as $value) {
    $total += $value['subtotal'];
  }
  return $total;
}
