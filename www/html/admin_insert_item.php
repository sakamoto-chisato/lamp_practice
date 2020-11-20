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

// 新規追加する商品名、価格、ステータス、在庫数を取得
$name = get_post('name');
$price = get_post('price');
$status = get_post('status');
$stock = get_post('stock');
// ファイル情報を取得
$image = get_file('image');

// 商品のDB登録
if(regist_item($db, $name, $price, $stock, $status, $image)){
  set_message('商品を登録しました。');
}else {
  set_error('商品の登録に失敗しました。');
}

// 管理ページへリダイレクト
redirect_to(ADMIN_URL);