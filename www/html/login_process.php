<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';

session_start();

if(is_logined() === true){
  redirect_to(HOME_URL);
}
// POSTされたユーザ名とパスワードの取得
$name = get_post('name');
$password = get_post('password');

$db = get_db_connect();

// ログインユーザとDBの照合
$user = login_as($db, $name, $password);
if( $user === false){
  set_error('ログインに失敗しました。');
  // 照合できなかった場合ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

set_message('ログインしました。');
// 管理者ユーザの場合は管理ページへリダイレクト
if ($user['type'] === USER_TYPE_ADMIN){
  redirect_to(ADMIN_URL);
}
// 一般ユーザの場合は商品一覧ページへリダイレクト
redirect_to(HOME_URL);