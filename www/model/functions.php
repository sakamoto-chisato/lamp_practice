<?php
/**
 * 変数の内容出力
 * @param mixed $var 変数
 * @return null
 */
function dd($var){
  var_dump($var);
  // 関数の終了？
  exit();
}

/**
 * ページ遷移
 * @param str $url 遷移先URL
 * @return null
 */
function redirect_to($url){
  header('Location: ' . $url);
  exit;
}

/**
 * GETされたデータの存在確認＆取得
 * @param mixed $name dataのname
 * @return str|null $_GET[$name]が存在すればその値を返す
 */
function get_get($name){
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}

/**
 * POSTされたデータの存在確認＆取得
 * @param str $name dataのname
 * @return mixed|null $_POST[$name]が存在すればその値を返す 
 */
function get_post($name){
  if(isset($_POST[$name]) === true){
    return $_POST[$name];
  };
  return '';
}

/**
 * アップしたファイルの存在確認＆名前取得
 * @param str $name type=fileのname
 * @return array $_FILES[$name]が存在すれば配列を返す 
 */
function get_file($name){
  if(isset($_FILES[$name]) === true){
    return $_FILES[$name];
  };
  return array();
}

/**
 * session情報の存在確認＆取得
 * @param str $name session情報のname
 * @return mixed|null $_SESSION[$name]が存在すればその値を返す
 */
function get_session($name){
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  return '';
}

/**
 * session情報の設定
 * @param str $name ユーザ名？
 * @param mixed $value 値？
 * @return null 
 */
function set_session($name, $value){
  $_SESSION[$name] = $value;
}

/**
 * エラーメッセージをsession変数に設定する
 * @param str $error エラーメッセージ
 * @return null
 */
function set_error($error){
  $_SESSION['__errors'][] = $error;
}

/**
 * エラー情報を取得し、表示させたあとは空にする
 * @param null
 * @return array() errorメッセージ
 */
function get_errors(){
  $errors = get_session('__errors');
  if($errors === ''){
    return array();
  }
  // エラー情報を空にする
  set_session('__errors',  array());
  return $errors;
}

/**
 * session変数にエラーメッセージが存在するかを確認
 * @param null
 * @return bool
 */
function has_error(){
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

/**
 * messageをsession情報に設定する
 * 関数内で完結させる→表示したあとは消している様子
 * @param str $message メッセージ
 * @return null
 */
function set_message($message){
  $_SESSION['__messages'][] = $message;
}

/**
 * messageを取得して空に戻す→表示したあと複数回表示しないように
 * @param null
 * @return array メッセージ
 */
function get_messages(){
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}

/**
 * ログイン済みかどうか確認
 * @param null
 * @return bool session情報が空かどうかでtrue,falseを返す
 */
function is_logined(){
  return get_session('user_id') !== '';
}

/**
 * アップした画像のチェック＆名前取得
 * @param mixed $file 
 * @return str 新しいファイル名
 */
function get_upload_filename($file){
  // ファイルが存在するか＆拡張子が正しいかを確認
  if(is_valid_upload_image($file) === false){
    // 満たさない場合には空で返す
    return '';
  }
  // 拡張子情報を取得
  $mimetype = exif_imagetype($file['tmp_name']);
  // 拡張子をリストから取ってくる
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  // 新しいファイル名を返す
  return get_random_string() . '.' . $ext;
}

/**
 * ランダムに文字列を20文字取得する
 * @param int $length 文字列の長さ
 * @return str 文字列20字
 */
function get_random_string($length = 20){
  // 13文字の文字列をハッシュ化し、16進数を36進数に変換し、1文字目から20文字を取得した
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

/**
 * fileを指定パスに移動させる
 * @param mixed $image　ファイル情報
 * @param str $filename 新しいファイル名
 * @return bool
 */
function save_image($image, $filename){
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}

/**
 * ファイルの削除
 * @param str $filename ファイル名
 * @return bool
 */
function delete_image($filename){
  // ファイルが存在した場合削除する
  if(file_exists(IMAGE_DIR . $filename) === true){
    unlink(IMAGE_DIR . $filename);
    return true;
  }
  return false;
}

/**
 * 文字数の確認
 * @param str $string 対象文字列
 * @param int $minimum_length 最小文字数
 * @param int $maximum_length 最大文字数→PHP_INT_MAXが何なのかわからない 
 * @return bool
 */
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  $length = mb_strlen($string);
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

/**
 * 入力チェック（英数字）
 * @param str $string 文字列
 * @return bool is_valid_formatの結果
 */
function is_alphanumeric($string){
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

/**
 * 入力チェック（数字）
 * @param int $string 数字の文字列
 * @return bool is_valid_formatの結果
 */
function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

/**
 * 正規表現チェック
 * @param str $string 文字列
 * @param str $format 正規表現
 * @return bool
 */
function is_valid_format($string, $format){
  return preg_match($format, $string) === 1;
}

/**
 * ファイルの存在や拡張子を確認する
 * @param mixed $image
 * @return bool
 */
function is_valid_upload_image($image){
  // ファイルが存在するか確認
  if(is_uploaded_file($image['tmp_name']) === false){
    // 存在しない場合エラーを設定,falseを返す
    set_error('ファイル形式が不正です。');
    return false;
  }
  // 拡張子を調べる
  $mimetype = exif_imagetype($image['tmp_name']);
  // 拡張子がリストに存在するか
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    // session情報にエラーを設定する
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    // エラーのあとfalseを返す
    return false;
  }
  // 問題ない場合trueを返す
  return true;
}

/**
 * HTMLエスケープ関数
 * @param str $str 文字
 * @return str エスケープした文字列
 */
function h($str) {
  return htmlspecialchars($str , ENT_QUOTES, 'UTF-8');
}