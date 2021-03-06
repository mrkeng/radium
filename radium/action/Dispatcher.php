<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\action;

use \radium\core\ClassLoader;
use \radium\core\Object;
use \radium\errors\NotFoundError;
use \radium\net\http\Request;
use \radium\net\http\Router;
use \radium\utils\StringUtil;

/**
 * ディスパッチャー
 * URL とコントローラ、アクションのマッピングを行い、コントローラを動作させる
 */
final class Dispatcher extends Object
{
	/**
	 * ディスパッチャーを起動します
	 * @param \radium\action\Request $request 
	 */
	public static function run(Request $request)
	{
		$dispatcher = new Dispatcher($request);
		list($controllerObj, $data) = $dispatcher->dispatch();
		
		$contentType = '';
		$output = '';
		if (is_string($data) || is_numeric($data) || is_bool($data)) {
			$output = $data;
			$contentType = 'text/plain';
		} else {
			$controllerObj->invokeMethod('_finalize', $data ? array($data) : array());
			$output = $controllerObj->renderedContent();
			$contentType = $controllerObj->view->contentType();
		}
		
		header('Content-Type: ' . $contentType .  '; charset=UTF-8');
		echo $output;
	}
	
	//--------------------------------------
	// 
	//--------------------------------------
	private $request;
	private $lastControllerObj;
	
	/**
	 * コンストラクタ
	 * @param \radium\action\Request $request
	 */
	public function __construct(Request $request)
	{
		parent::__construct();
		
		$uri = $request->uri;
		
		$plugin = '';
		$controller = 'home';
		$action = 'index';
		
		StringUtil::getLocalizedString('');
		
		// URI を分割します
		$args = strlen(substr($uri, 1)) > 0 ? explode('/', substr($uri, 1)) : array();
		foreach ($args as &$arg) $arg = urldecode($arg);
		
		// ルーティング
		$route = Router::get($uri, $args);
		
		if ($route === false) {
			throw new NotFoundError(StringUtil::getLocalizedString('Not Found.'));
		}
		
		$controller = $route['controller'];
		$action = $route['action'];
		$args = $route['args'];
		
		$request->params['path'] = $uri;
		$request->params['controller'] = $controller;
		$request->params['action'] = $action;
		$request->params['args'] = $args;
		
		$this->request = $request;
	}
	
	/**
	 *
	 */
	public function dispatch($controller = null, $action = null, $args = null)
	{
		$containsController = $controller == null;
		
		$request = $this->request;
		if (is_null($controller)) {
			$controller = $request->params['controller'];
			$action = $request->params['action'];
			$args = $request->params['args'];
		} elseif (is_null($action)) {
			$action = $request->params['action'];
			$args = $request->params['args'];
		} elseif (is_null($args)) {
			$args = $request->params['args'];
		}
		
		// コントローラクラス
		$controllerClass = '\\app\\controllers\\' . StringUtil::camelcase($controller) . 'Controller';
		
		$result = ClassLoader::load($controllerClass, false);
		
		// コントローラクラスが見つからない！
		if ($result === false || !class_exists($controllerClass)) {
			throw new NotFoundError(StringUtil::getLocalizedString('Controller "{1}" is not found.', array($controllerClass)), CONTROLLER_NOT_FOUND);
		}
		
		// コントローラの処理
		$controllerObj = new $controllerClass($request, $this);
		
		$callAction = $action;
		
		// アクションがない場合は _global をコール
		if (!is_callable(array($controllerObj, $action)) && is_callable(array($controllerObj, '_global'))) {
			$callAction = '_global';
		} elseif (!is_callable(array($controllerObj, $action))) {
			throw new NotFoundError(StringUtil::getLocalizedString('Action "{1}" is not found. (Controller: {2})', array($action, $controllerClass)), ACTION_NOT_FOUND);
		}
		
		$controllerObj->template = $action;
		$controllerObj->invokeMethod('_init');
		$data = @call_user_func_array(array($controllerObj, $callAction), $args);
		
		if ($containsController) return array($controllerObj, $data);
		
		return $data;
	}
}
