<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();

$user = get_login_user($db);

if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

// check CSRF token
$token = get_session('csrf_token');
if (is_valid_csrf_token($token) === false) {
  echo "不正なリクエストです";
  exit;
}

// 消去する商品IDを取得
$item_id = get_post('item_id');

// 商品の削除
if(destroy_item($db, $item_id) === true){
  set_message('商品を削除しました。');
} else {
  set_error('商品削除に失敗しました。');
}


// 管理ページへリダイレクト
redirect_to(ADMIN_URL);