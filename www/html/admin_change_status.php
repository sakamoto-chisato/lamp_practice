<?php
// 設定ファイルの読み込み
require_once '../conf/const.php';
// 汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
// ユーザに関する関数ファイルの読み込み
require_once MODEL_PATH . 'user.php';
// 商品に関する関数ファイルの読み込み
require_once MODEL_PATH . 'item.php';

// session開始 
session_start();
// ログイン済みでなければログインページにリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}
// DBに接続
$db = get_db_connect();
// DBからユーザ情報配列を取得
$user = get_login_user($db);
// 管理者ユーザでない場合にはログインページへリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}
// POSTされた商品IDとステータスを取得
$item_id = get_post('item_id');
$changes_to = get_post('changes_to');

// 公開か非公開かで処理を分ける
if($changes_to === 'open'){
  update_item_status($db, $item_id, ITEM_STATUS_OPEN);
  set_message('ステータスを変更しました。');
}else if($changes_to === 'close'){
  update_item_status($db, $item_id, ITEM_STATUS_CLOSE);
  set_message('ステータスを変更しました。');
}else {
  set_error('不正なリクエストです。');
}

// 管理ページへリダイレクト
redirect_to(ADMIN_URL);