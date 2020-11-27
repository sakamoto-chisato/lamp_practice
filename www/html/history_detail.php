<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'history.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();

$user = get_login_user($db);

$purchased_id = get_get('purchased_id');
$history_details = get_history_details($db, $purchased_id);
$total = purchased_total($history_details);
if (is_admin($user) === false && $history_details[0]['user_id'] !== $user['user_id']) {
    set_error('指定した注文番号の購入明細は閲覧できません');
    redirect_to(HISTORY_URL);
}

include_once '../view/history_detail_view.php';
