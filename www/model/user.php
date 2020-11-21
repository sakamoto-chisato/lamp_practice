<?php
// 汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
// DB関数ファイルの読み込み
require_once MODEL_PATH . 'db.php';

/**
 * 
 */
function get_user($db, $user_id){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      user_id = ?
    LIMIT 1
  ";

  return fetch_query($db, $sql, array($user_id));
}

/**
 * 名前から一致するユーザの情報取得
 * @param mixed $db
 * @param str $name
 * @return array 取得結果
 */
function get_user_by_name($db, $name){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = ?
    LIMIT 1
  ";

  return fetch_query($db, $sql, array($name));
}

/**
 * ユーザ情報との照合
 * @param mixed $db
 * @param str $password
 * @return bool 照合結果
 */
function login_as($db, $name, $password){
  $user = get_user_by_name($db, $name);
  if($user === false || $user['password'] !== $password){
    return false;
  }
  set_session('user_id', $user['user_id']);
  return $user;
}

/**
 * ログイン済みユーザの情報取得
 * @param mixed $db
 * @return array|bool 結果取得かfalseか
 */
function get_login_user($db){
  // loginユーザのsession id 取得
  $login_user_id = get_session('user_id');
  // ユーザ情報取得かfalseか
  return get_user($db, $login_user_id);
}

/**
 * ユーザの入力確認＆新規登録
 * @param mixed $db
 * @param str $name
 * @param str $password
 * @param str $password_confirmation
 * @return bool
 */
function regist_user($db, $name, $password, $password_confirmation) {
  if( is_valid_user($name, $password, $password_confirmation) === false){
    return false;
  }
  
  return insert_user($db, $name, $password);
}

/**
 * 管理者かどうかのチェック
 * @param array ユーザ情報
 * @return bool 管理者true|false
 */
function is_admin($user){
  return $user['type'] === USER_TYPE_ADMIN;
}

/**
 * ユーザ、パスワードの入力チェック
 * @param str $name
 * @param str $password
 * @param str $password_confirmation 確認用パスワード
 * @return bool
 */
function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるため一旦代入。
  $is_valid_user_name = is_valid_user_name($name);
  $is_valid_password = is_valid_password($password, $password_confirmation);
  return $is_valid_user_name && $is_valid_password ;
}

/**
 * ユーザの入力チェック
 * @param str $name
 * @return bool
 */
function is_valid_user_name($name) {
  $is_valid = true;
  // 文字数チェック
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  // 英数字かチェック
  if(is_alphanumeric($name) === false){
    set_error('ユーザー名は半角英数字で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

/**
 * パスワードの入力チェック
 * @param str $password
 * @param str $password_confirmation
 * @return bool 
 */
function is_valid_password($password, $password_confirmation){
  $is_valid = true;
  // 文字数チェック
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  // 英数字かチェック
  if(is_alphanumeric($password) === false){
    set_error('パスワードは半角英数字で入力してください。');
    $is_valid = false;
  }
  // 確認用と一致するか確認
  if($password !== $password_confirmation){
    set_error('パスワードがパスワード(確認用)と一致しません。');
    $is_valid = false;
  }
  return $is_valid;
}

/**
 * ユーザの新規追加
 * @param mixed $db
 * @param str $name
 * @param str $password
 * @return bool
 */
function insert_user($db, $name, $password){
  $sql = "
    INSERT INTO
      users(name, password)
    VALUES (?, ?);
  ";

  return execute_query($db, $sql, array($name, $password));
}

