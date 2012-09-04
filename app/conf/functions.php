<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2011, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

use \radium\utils\StringUtil;

/**
 * ローカライズ
 * @param string $key キー名
 * @param array $params 置換文字のリスト
 * @return ローカライズされた文字列
 */
function ll($key, array $params = array(), $lang = null)
{
	return StringUtil::getLocalizedString($key, $params, $lang);
}


/**
 * 文字列をエスケープして出力
 * @param string $str 文字列
 * @param bool true の場合は出力しないで return する
 */
function ee($str, $return = false) {
	$str = StringUtil::escape($str);
	if ($return) return $str;
	echo $str;
}


/**
 * セッション
 */
/*
function sess_open($save_path, $session_name)
{
	return true;
}
function sess_close()
{
	return true;
}
function sess_read($id)
{
	try {
		$session = Session::findOne(array('id' => $id)); // mongodb
		//$session = Session::find($id); // php-activerecord
	} catch (Exception $exception) {
	}
	if ($session) {
		return $session->data;
	}
	return '';
}
function sess_write($id, $data)
{
	$session = null;
	try {
		$session = Session::findOne(array('id' => $id)); // mongodb
		//$session = Session::find($id); // php-activerecord
	} catch (Exception $exception) {
	}
	if ($session) {
		if (!$data) {
			return $session->delete();
		}
	} else {
		if (!$data) return true;
		$session = Session::create(array(
			'id' => $id,
		));
	}
	$session->data = $data;
	$session->timestamp = time();
	return $session->save();
}
function sess_destroy($id)
{
	try {
		$session = Session::findOne(array('id' => $id)); // mongodb
		//$session = Session::find($id); // php-activerecord
	} catch (Exception $exception) {
	}
	if ($session) $session->delete();
	return true;
}

function sess_gc($maxlifetime)
{
	// mongodb
	return \app\models\Session::deleteAll(array('timestamp' => array('<' => time() - $maxlifetime)));
	
	// php-activerecord
	//Session::table()->delete_all(array(
	//	'conditions' => array(
	//		'timestamp < ?', (time() - $maxlifetime),
	//	)
	//));
	//return true;
}
ini_set('session.save_path', '/var/lib/php/session');
session_set_save_handler("sess_open", "sess_close", "sess_read", "sess_write", "sess_destroy", "sess_gc");
*/
