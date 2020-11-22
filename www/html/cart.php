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
// 合計金額の取得
$total_price = sum_carts($carts);

// get CSRF token 
$token = get_csrf_token();

// カートページを表示
include_once VIEW_PATH . 'cart_view.php';