<?php
// 設定ファイルの読み込み
require_once '../conf/const.php';
// 汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
// ユーザ関数ファイルの読み込み
require_once MODEL_PATH . 'user.php';
// 商品関数ファイルの読み込み
require_once MODEL_PATH . 'item.php';

// session 開始
session_start();
// ログイン済みでなければログインページへリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}
// DBに接続
$db = get_db_connect();
// DBからユーザ情報配列を取得
$user = get_login_user($db);

// 管理者ユーザでなければログインページへリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

// check CSRF token
$token = get_session('csrf_token');
if (is_valid_csrf_token($token) === false) {
  echo "不正なリクエストです";
  exit;
}

// POSTされた商品IDと変更在庫数を取得
$item_id = get_post('item_id');
$stock = get_post('stock');

// 在庫数の変更
if(update_item_stock($db, $item_id, $stock)){
  set_message('在庫数を変更しました。');
} else {
  set_error('在庫数の変更に失敗しました。');
}

// 管理ページへリダイレクト
redirect_to(ADMIN_URL);