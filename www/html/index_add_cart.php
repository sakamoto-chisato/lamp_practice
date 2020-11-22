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

// カートに追加する商品IDの取得
$item_id = get_post('item_id');
// カートに商品を追加
if(add_cart($db,$user['user_id'], $item_id)){
  set_message('カートに商品を追加しました。');
} else {
  set_error('カートの更新に失敗しました。');
}
// 商品一覧ページへリダイレクト
redirect_to(HOME_URL);