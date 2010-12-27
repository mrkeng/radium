<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\net\http;

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
		$arg = array_shift($args);
		
		$controller = null;
		if (preg_match('/^[a-zA-Z]/', $arg)) {
			// プラグインをチェック
			if (self::plugin_exists($arg)) {
				$plugin = $arg;
				$arg = count($args) > 0 ? array_shift($args) : '';
			}
			
			// コントローラのチェック
			if (preg_match('/^[a-zA-Z]/', $arg)) {
				$controller = $arg;
				
				// コントローラクラス
				$controllerClass = 'app\\controllers\\' . StringUtil::camelcase($controller) . 'Controller';
				
				$result = ClassLoader::load($controllerClass, false);
				
				// コントローラクラスが見つかった
				if ($result === true && class_exists($controllerClass)) {
					// アクションのチェック
					if (count($args) > 0) {
						$arg = array_shift($args);
						if (preg_match('/^[a-zA-Z]/', $arg)
							&& !in_array($arg, explode(' ', 'render redirect content json'))) {
							$action = $arg;
						} else {
							$action = 'index';
							array_unshift($args, $arg);
						}
					} else {
						$action = 'index';
						array_unshift($args, $arg);
						
						$route = array(
							'controller' => $controller,
							'action' => $action,
							'args' => $args
						);
						return $route;
					}
				} else {
					array_unshift($args, $controller);
					$controller = null;
				}
			} else {
				array_unshift($args, $arg);
			}
		} else if ($arg != '') {
			array_unshift($args, $arg);
		}
		
		$routes = static::$_routes;
		
		if (isset($routes[$url])) {
			$route = $routes[$url];
			$route['args'] = $args;
			return $route;
		}
		
		foreach ($routes as $key => $route) {
			// {:Model:column}
			preg_match_all('/\\{:([a-zA-Z0-9]+):([a-zA-Z0-9]+)\\}/', $key, $matches);
			if ($matches && count($matches) > 0 && count($matches[0]) > 0) {
				$checkKey = $key;
				$checkData = array();
				$n = count($matches[0]);
				for ($i = 0; $i < $n; $i++) {
					$param = $matches[0][$i];
					$checkKey = str_replace($param, '______param_____', $checkKey);
					
					$model = $matches[1][$i];
					$column = $matches[2][$i];
					
					$modelClass = 'app\\models\\' . StringUtil::camelcase($model);
					
					$result = ClassLoader::load($modelClass, false);
					
					// モデルクラスが見つかった
					if ($result === true && class_exists($modelClass)) {
						$checkData[] = array($modelClass, $column);
					} else {
						throw new NotFoundError(StringUtil::getLocalizedString('Model Class "{1}" is not found. ({2} at {2})', array($modelClass, __FILE__, __LINE__)), ACTION_NOT_FOUND);
					}
				}
				
				$checkKey = preg_quote($checkKey);
				$checkKey = str_replace('/', '\\/', $checkKey);
				$checkKey = str_replace('______param_____', '([-_+a-zA-Z0-9]+)', $checkKey);
				
				if (preg_match('/^' . $checkKey . '$/', $url, $matches)) {
					$data = array();
					$exists = false;
					array_shift($matches);
					$n = count($matches);
					for ($i = 0; $i < $n; $i++) {
						$modelClass = $checkData[$i][0];
						$column = $checkData[$i][1];
						
						$value = $matches[$i];
						
						$extra = array();
						if (count($data) > 0) {
							$target = count($data) > 0 ? $data[count($data) - 1] : null;
							if (!$target || count($target) == 0) {
								$data[] = null;
								continue;
							}
							
							$target = $target[0];
							
							$model = basename(str_replace('\\', '/', get_class($target)));
							$model = StringUtil::uncamelcase($model);
							
							$relationKey = defined('RADIUM_RELATION_KEY') ? RADIUM_RELATION_KEY : '_id';
							$targetValue = $target->$relationKey;
							$extra[$model . '_id'] = $targetValue;
						}
						
						$result = $modelClass::all(array('conditions' => array($column => $value) + $extra));
						if ((!$result || count($result) == 0) && preg_match('/^\\d+(\\.\\d+)?$/', $value)) {
							$result = $modelClass::all(array('conditions' => array($column => floatval($value)) + $extra));
						}
						if ((!$result || count($result) == 0) && preg_match('/^(true|false)$/', $value)) {
							$result = $modelClass::all(array('conditions' => array($column => ($value == 'true')) + $extra));
						}
						
						$data[] = $result;
						if ($result && count($result) > 0) {
							$exists = true;
						}
					}
					if ($exists) {
						$route['args'] = $data;
						return $route;
					}
				}
			}
			
			// {:args}
			if (strpos($key, '{:args}') !== false) {
				$key = str_replace('{:args}', '______args_____', $key);
				$key = preg_quote($key);
				$key = str_replace('/', '\\/', $key);
				$key = str_replace('______args_____', '([-_+a-zA-Z0-9]+)', $key);
				
				if (preg_match('/^' . $key . '$/', $url, $matches)) {
					array_shift($matches);
					
					$route['args'] = $matches;
					return $route;
				}
			}
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
		
		$route = array();
		$route['controller'] = array_shift($args);
		$route['action'] = array_shift($args);
		$route['args'] = $args;
		
		return $route;
	}
	
	/**
	 * プラグインの存在確認
	 * @param string $plugin プラグイン名
	 */
	private static function plugin_exists($plugin)
	{
		$class = StringUtil::camelcase($plugin);
		$pluginFile = RADIUM_PLUGIN_PATH . '/' . $class . '.php';
		
		if (file_exists($pluginFile)) {
			include $pluginFile;
			return true;
		}
		
		return false;
	}
}
