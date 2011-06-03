<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

use \radium\net\http\Router;


// ルーティング

Router::connect('/', array('controller' => 'home', 'action' => 'index'));

Router::connect('/s/{:args}', array('controller' => 'hello_world', 'action' => 'show'));
Router::connect('/hello', array('controller' => 'hello_world', 'action' => 'hello'));
//Router::connect('/data/{:Model:column}', array('controller' => 'data', 'action' => 'show'));
