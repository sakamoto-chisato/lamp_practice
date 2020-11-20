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

// 公開商品のみ取得
$items = get_open_items($db);
// 商品一覧ページの表示
include_once VIEW_PATH . 'index_view.php';