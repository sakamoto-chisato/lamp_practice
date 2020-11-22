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

// check CSRF token
$token = get_session('csrf_token');
if (is_valid_csrf_token($token) === false) {
  echo "不正なリクエストです";
  exit;
}

$db = get_db_connect();
$user = get_login_user($db);

// 削除するカートIDを取得する
$cart_id = get_post('cart_id');
// カートから削除
if(delete_cart($db, $cart_id)){
  set_message('カートを削除しました。');
} else {
  set_error('カートの削除に失敗しました。');
}
// カートページへリダイレクト
redirect_to(CART_URL);