<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2011, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

error_reporting(E_ALL | E_STRICT);
//error_reporting(0);


/**
 * サービス定義
 */


/**
 * デバッグ
 */
define('DEBUG', 1);


/**
 * ローカライズ設定
 */
mb_language('Japanese');
setlocale(LC_ALL, 'ja_JP.UTF-8');
mb_internal_encoding('UTF-8');
mb_regex_encoding("UTF-8");
if (function_exists('date_default_timezone_set')) date_default_timezone_set('Asia/Tokyo');


// 定数をセット
define('RADIUM_PATH', dirname(dirname(__DIR__)));
define('RADIUM_APP_PATH', RADIUM_PATH . '/app');
define('RADIUM_LIBRARY_PATH', RADIUM_PATH . '/radium');

define('NOT_FOUND', 404);
define('CLASSFILE_NOT_FOUND', 1);
define('CLASS_NOT_FOUND', 2);
define('CONTROLLER_NOT_FOUND', 3);
define('ACTION_NOT_FOUND', 4);
define('TEMPLATE_NOT_FOUND', 5);
define('CONNECT_DATABASE_ERROR', 6);
define('INVALID_METHOD', 7);
define('INVALID_PROPERTY', 8);


// コアライブラリを読み込み
require __DIR__ . '/libraries.php';

// データベース設定を読み込み
require __DIR__ . '/database.php';

// ルーティング設定を読み込み
require __DIR__ . '/routes.php';

// グローバル関数を読み込み
require __DIR__ . '/functions.php';
