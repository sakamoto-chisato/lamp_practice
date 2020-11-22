<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';

session_start();

if(is_logined() === true){
  redirect_to(HOME_URL);
}

// get CSRF token
$token = get_csrf_token();

// ユーザ新規登録ページの表示
include_once VIEW_PATH . 'signup_view.php';



