<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';

session_start();

if(is_logined() === true){
  redirect_to(HOME_URL);
}

// check CSRF token
$token = get_session('csrf_token');
if (is_valid_csrf_token($token) === false) {
  echo "不正なリクエストです";
  exit;
}

// ユーザ名、パスワード、確認用パスワードの取得
$name = get_post('name');
$password = get_post('password');
$password_confirmation = get_post('password_confirmation');

$db = get_db_connect();

try{
  // 新規ユーザのDB登録
  $result = regist_user($db, $name, $password, $password_confirmation);
  if( $result=== false){
    set_error('ユーザー登録に失敗しました。');
    // 登録できない場合新規ユーザ登録ページへリダイレクト
    redirect_to(SIGNUP_URL);
  }
}catch(PDOException $e){
  set_error('ユーザー登録に失敗しました。');
  redirect_to(SIGNUP_URL);
}

set_message('ユーザー登録が完了しました。');
// ユーザ情報の照合
login_as($db, $name, $password);
// 商品一覧ページへリダイレクト
redirect_to(HOME_URL);