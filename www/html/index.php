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

// constから引数に渡してあげる
$per_page_items = PER_PAGE_ITEMS;

// ページ数関連の取得
$page = get_item_page();
$total_page = get_item_total_page($db, $per_page_items);

// 公開商品のみかつページごとの商品情報取得
$items = get_page_items($db, $page, $per_page_items);

// get CSRF token
$token = get_csrf_token();

// 商品一覧ページの表示
include_once VIEW_PATH . 'index_view.php';