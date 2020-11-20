<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';

// session開始
session_start();
// session情報の破棄
$_SESSION = array();
// sessionのcookie情報を配列で取得
$params = session_get_cookie_params();
// cookieを削除する
setcookie(session_name(), '', time() - 42000,
  $params["path"], 
  $params["domain"],
  $params["secure"], 
  $params["httponly"]
);
// SESSIONデータの破棄
session_destroy();
// ログインページへリダイレクト
redirect_to(LOGIN_URL);

