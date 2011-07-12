<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2011, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\managers;

use \radium\core\ClassLoader;
use \radium\utils\StringUtil;

/**
 * 例外を管理するマネージャクラス
 */
final class ExceptionManager
{
	private static $controller;
	private static $action;
	
	public static function init()
	{
		//set_error_handler('\\radium\\managers\\ExceptionManager::catchError');
		set_exception_handler('\\radium\\managers\\ExceptionManager::catchException');
	}
	
	/**
	 *
	 */
	public static function catchError($errno, $errstr, $errfile, $errline)
	{
		throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	
	/**
	 * 
	 * @param Exception $exception
	 */
	public static function catchException($exception)
	{
		$code = $exception->getCode();
		
		$errorController = 'app\\controllers\\ErrorController';
		$result = ClassLoader::load($errorController, false);
		
		if ($result && class_exists($errorController)) {
			$controllerObj = new $errorController(new \radium\net\http\Request());
			$action = 'index';
			
			$controllerObj->_render['template'] = $action;
			$controllerObj->invokeMethod('_init');
			$data = call_user_func_array(array($controllerObj, $action), array($exception));
			
			$contentType = 'text/html';
			$output = '';
			if (is_string($data)) {
				$output = $data;
				$contentType = 'text/plain';
			} else {
				$controllerObj->invokeMethod('_finalize', $data ? array($data) : array());
				$output = $controllerObj->renderedContent();
				$contentType = $controllerObj->view->contentType();
			}
			
			header('Content-Type: ' . $contentType .  '; charset=UTF-8');
			echo $output;
			exit;
		}
		
		$backtrace = $exception->getTrace();
		$line = array_shift($backtrace);
		while ($line && !isset($line['file'])) {
			$line = array_shift($backtrace);
		}
		
		header('Content-Type: text/plain; charset=UTF-8');
		echo StringUtil::getLocalizedString('Exception was thrown. ({1}): {2} at {3} line {4} ({5} line {6})', array($code, $exception->getMessage(), $line['file'], $line['line'], $exception->getFile(), $exception->getLine())) . "\n";
		exit;
	}
}

ExceptionManager::init();
