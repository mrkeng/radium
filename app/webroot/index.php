<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

// ブートストラップ
require dirname(__DIR__) . '/conf/bootstrap.php';

// ディスパッチャーを起動
echo \radium\action\Dispatcher::run(new \radium\net\http\Request());
