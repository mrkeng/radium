<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\net\http;

use \Exception;
use \radium\core\ClassLoader;
use \radium\core\Object;
use \radium\errors\NotFoundError;
use \radium\utils\StringUtil;

/**
 * ルーティング情報を管理するクラス
 */
final class Router extends Object
{
	private static $_routes = array();
	
	public static function connect($url, array $data = array())
	{
		static::$_routes[$url] = $data;
	}
	
	public static function get($url, array $args = array())
	{
		$routes = static::$_routes;
		
		$url = urldecode($url);
		
		if (isset($routes[$url])) {
			$route = $routes[$url];
			$route['args'] = $args;
			return $route;
		}
		
		foreach ($routes as $key => $route) {
			
			$checkKey = $key;
			$checkDataData = array();
			$checkDataRegexp = array();
			$checkDataArg = array();
			
			// {:Model:Column}
			preg_match_all('/\{:([a-zA-Z0-9]+):([_a-zA-Z0-9]+)\}/', $key, $matches);
			if ($matches && count($matches) > 0 && count($matches[0]) > 0) {
				$n = count($matches[0]);
				for ($i = 0; $i < $n; $i++) {
					$param = $matches[0][$i];
					$checkKey = str_replace($param, '______data_____', $checkKey);
					
					$model = $matches[1][$i];
					$column = $matches[2][$i];
					
					$modelClass = 'app\\models\\' . StringUtil::camelcase($model);
					
					$result = ClassLoader::load($modelClass, false);
					
					// モデルクラスが見つかった
					if ($result === true && class_exists($modelClass)) {
						$checkDataData[] = array('data', $modelClass, $column);
					} else {
						throw new NotFoundError(StringUtil::getLocalizedString('Model Class "{1}" is not found. ({2} at {2})', array($modelClass, __FILE__, __LINE__)), ACTION_NOT_FOUND);
					}
				}
			}
			
			// {:args}
			$checkKey = preg_replace_callback('/\{:args?\}/',
				function($matches) use (&$checkDataArg) {
					$checkDataArg[] = array('arg');
					return '______arg_____';
				}, $checkKey);
				
			// {:regexp}
			$regexpList = array();
			$checkKey = preg_replace_callback('/\{\:\/((?:[-+*.?,|\/a-zA-Z0-9]|\\\\|\\{|\\}|\\(|\\)|\\[|\\])*)\/\}/',
				function($matches) use (&$regexpList, &$checkDataRegexp) {
					$regexp = $matches[1];
					$regexp = str_replace('\\(', '_____regexp_escape_____', $regexp);
					$regexp = str_replace('(', '(?:', $regexp);
					$regexp = str_replace('_____regexp_escape_____', '\\(', $regexp);
					$regexpList[] = $regexp;
					$checkDataRegexp[] = array('regexp');
					return '______regexp_____';
				},
				$checkKey);
			
			// checkData を並び替えながら準備
			$checkData = array();
			preg_match_all('/______(data|arg|regexp)_____/', $checkKey, $matches);
			if ($matches && count($matches[0]) > 0) {
				for ($i = 0; $i < count($matches[0]); $i++) {
					switch ($matches[1][$i]) {
						case 'data': $checkData[] = array_shift($checkDataData); break;
						case 'arg': $checkData[] = array_shift($checkDataArg); break;
						case 'regexp': $checkData[] = array_shift($checkDataRegexp); break;
					}
				}
			}
			
			$checkKey = preg_quote($checkKey);
			$checkKey = str_replace('/', '\\/', $checkKey);
			
			// {:Model:Column} を戻す
			$checkKey = str_replace('______data_____', '([^\\/]+)', $checkKey);
			
			// {:args} を戻す
			$checkKey = str_replace('______arg_____', '([^\\/]+)', $checkKey);
			
			// {:regexp} を戻す
			$checkKey = preg_replace_callback('/______regexp_____/',
			function($matches) use (&$regexpList) {
				return '(' . array_shift($regexpList) . ')';
			},
			$checkKey);
			
			$matches = null;
			if (count($checkData) > 0) {
				//echo $checkKey . "\n";
				//print_r($checkData);
				try {
					preg_match('/^' . $checkKey . '$/', $url, $matches);
				} catch (Exception $e) {
				}
			}
			
			if ($matches && count($matches) > 0) {
				$resultArgs = array();
				array_shift($matches);
				$n = count($matches);
				for ($i = 0; $i < $n; $i++) {
					$type = $checkData[$i][0];
					$value = $matches[$i];
					
					if ($type == 'data') {
						$modelClass = $checkData[$i][1];
						$column = $checkData[$i][2];
						
						$result = null;
						if ((!$result || count($result) == 0) && preg_match('/^\\d+(\\.\\d+)?$/', $value)) {
							$result = $modelClass::all(array('conditions' => array($column => floatval($value))));
						}
						if (!$result || count($result) == 0) {
							$result = $modelClass::all(array('conditions' => array($column => $value)));
						}
						if ((!$result || count($result) == 0) && preg_match('/^(true|false)$/', $value)) {
							$result = $modelClass::all(array('conditions' => array($column => ($value == 'true'))));
						}
						
						//header('Content-Type: text/plain;charset=UTF-8');print_r(array('conditions' => array($column => $value) + $extra));exit;
						
						if ($result && count($result) > 0) {
							$resultArgs[] = $result;
						} else {
							break;
						}
					} else if ($type == 'arg') {
						$resultArgs[] = $value;
					} else if ($type == 'regexp') {
						$resultArgs[] = $value;
					}
				}
				
				if (count($resultArgs) == count($matches)) {
					$route['args'] = $resultArgs;
					return $route;
				}
			}
		}
		
		// 
		$controller = null;
		$arg = array_shift($args);
		if (preg_match('/^[a-zA-Z][_a-zA-Z0-9]*$/', $arg)) {
			$controller = $arg;
			
			// アクションのチェック
			if (count($args) > 0) {
				$arg = array_shift($args);
				if (preg_match('/^[a-zA-Z][_a-zA-Z0-9]*$/', $arg)
					&& !in_array($arg, explode(' ', 'render redirect content json'))) {
					$action = $arg;
				} else {
					$action = 'index';
					array_unshift($args, $arg);
				}
			} else {
				$action = 'index';
				array_unshift($args, $arg);
			}
		} else {
			array_unshift($args, $arg);
		}
		
		// コントローラがあったら、、
		if ($controller && $action) {
			$route = array(
				'controller' => $controller,
				'action' => $action,
				'args' => $args
			);
			return $route;
		}
		
		return false;
	}
}
