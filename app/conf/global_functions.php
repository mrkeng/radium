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
 * セッション
 */
/*
ini_set('session.save_path', '/var/lib/php/session');
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
	global $sess_save_path;
	$session = \app\models\Session::findOne(array('id' => $id));
	if ($session) {
		return $session->data;
	}
	return '';
}
function sess_write($id, $data)
{
	$session = \app\models\Session::findOne(array('id' => $id));
	if ($session) {
		if (!$data) {
			return $session->delete();
		}
	} else {
		if (!$data) return true;
		$session = \app\models\Session::create(array(
			'id' => $id,
		));
	}
	$session->data = $data;
	$session->timestamp = time();
	return $session->save();
}
function sess_destroy($id)
{
	return \app\models\Session::deleteAll(array('id' => $id));
}

function sess_gc($maxlifetime)
{
	//return true;
	return \app\models\Session::deleteAll(array('timestamp' => array('<' => time() - $maxlifetime)));
}
session_set_save_handler("sess_open", "sess_close", "sess_read", "sess_write", "sess_destroy", "sess_gc");
*/
