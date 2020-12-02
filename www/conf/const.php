<?php
// 関数ファイルのパス
define('MODEL_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../model/');
// ビューファイルのパス
define('VIEW_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../view/');

// 画像ファイルのパス
define('IMAGE_PATH', '/assets/images/');
// CSSファイルのパス
define('STYLESHEET_PATH', '/assets/css/');
// ??
define('IMAGE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/assets/images/' );

// DB情報
define('DB_HOST', 'mysql');
define('DB_NAME', 'sample');
define('DB_USER', 'testuser');
define('DB_PASS', 'password');
define('DB_CHARSET', 'utf8');

// 遷移先の指定
define('SIGNUP_URL', '/signup.php');
define('LOGIN_URL', '/login.php');
define('LOGOUT_URL', '/logout.php');
define('HOME_URL', '/index.php');
define('CART_URL', '/cart.php');
define('FINISH_URL', '/finish.php');
define('ADMIN_URL', '/admin.php');
define('HISTORY_URL', '/history.php');
define('HISTORY_DETAIL_URL', '/history_detail.php');

// 文字列チェック
// 英数字のチェック
define('REGEXP_ALPHANUMERIC', '/\A[0-9a-zA-Z]+\z/');
// 数字のチェック 1~ or 0
define('REGEXP_POSITIVE_INTEGER', '/\A([1-9][0-9]*|0)\z/');

// ユーザ名の入力文字数制限
define('USER_NAME_LENGTH_MIN', 6);
define('USER_NAME_LENGTH_MAX', 100);
// ユーザパスワードの入力文字数制限
define('USER_PASSWORD_LENGTH_MIN', 6);
define('USER_PASSWORD_LENGTH_MAX', 100);
// 管理者権限の有無
define('USER_TYPE_ADMIN', 1);
define('USER_TYPE_NORMAL', 2);
// 商品名の文字数
define('ITEM_NAME_LENGTH_MIN', 1);
define('ITEM_NAME_LENGTH_MAX', 100);
// 商品ステータス
define('ITEM_STATUS_OPEN', 1);
define('ITEM_STATUS_CLOSE', 0);
// 商品ステータスの定義
define('PERMITTED_ITEM_STATUSES', array(
  'open' => 1,
  'close' => 0,
));
// 画像ファイルの拡張子確認
define('PERMITTED_IMAGE_TYPES', array(
  IMAGETYPE_JPEG => 'jpg',
  IMAGETYPE_PNG => 'png',
));

// １ページあたりの取得件数
define('PER_PAGE_ITEMS', 8);