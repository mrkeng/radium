<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

use \radium\data\Resource;

// データベース
Resource::add('default', array(
		'host' => 'localhost',
		'database' => 'radium'
	));

Resource::add('default', array(
		'host' => 'localhost',
		'database' => 'sessions'
	));


/**
 * php-activerecord
 */
//require RADIUM_APP_PATH . '/libraries/php-activerecord/ActiveRecord.php';
//ActiveRecord\Config::initialize(function($cfg)
//{
//	$cfg->set_model_directory(RADIUM_APP_PATH . '/models');
//	$cfg->set_connections(array(
//		'production' => 'mysql://root@localhost/radium'
//	));
//});
