<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2011, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

use \radium\data\Resource;

// データベース
Resource::add('default', array(
		'host' => 'localhost',
		'database' => 'radium'
	));

//Resource::add('default', array(
//		'host' => 'localhost',
//		'database' => 'radium',
//		'adapter' => 'radium.data.adapter.MySQL'
//	));
