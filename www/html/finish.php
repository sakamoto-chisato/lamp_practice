<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

// 特定ユーザのカート情報を取得
$carts = get_user_carts($db, $user['user_id']);
// 購入処理
if(purchase_carts($db, $carts, $user['user_id']) === false){
  set_error('商品が購入できませんでした。');
  // 購入できなかった場合にはカートページにリダイレクト
  redirect_to(CART_URL);
} 
// 合計金額を取得
$total_price = sum_carts($carts);
// 購入後ページの表示
include_once '../view/finish_view.php';