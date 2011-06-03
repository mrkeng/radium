<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

// ブートストラップ
require dirname(__DIR__) . '/conf/bootstrap.php';

// 設定
if (!defined('APP_BASE_PATH')) {
	$phpSelf = $_SERVER['PHP_SELF'];
	$pos = strpos($phpSelf, 'app/webroot/index.php');
	if ($pos !== false) {
		$basePath = substr($phpSelf, 0, $pos);
		define('APP_BASE_PATH', substr($phpSelf, 0, $pos));
	} else {
		define('APP_BASE_PATH', '/');
	}
}
if (!defined('DEFAULT_CONTROLLER')) {
	define('DEFAULT_CONTROLLER', 'home');
}

// ディスパッチャーを起動
echo \radium\action\Dispatcher::run(new \radium\net\http\Request());
