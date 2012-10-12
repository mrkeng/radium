<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\core;

use \ErrorException;
use \radium\utils\StringUtil;

/**
 * クラスの自動ロード機能を提供するクラス
 */
final class ClassLoader extends Object
{
	/**
	 * 初期化処理
	 */
	public static function init()
	{
		set_include_path(get_include_path() . ':' . RADIUM_APP_PATH . DIRECTORY_SEPARATOR . 'libraries');
		spl_autoload_register('\radium\core\ClassLoader::load');
	}
	
	/**
	 * クラスファイルの自動ローディング
	 * @param string $className クラス名
	 */
	public static function load($className, $fireExceptionIfNeeded = true)
	{
		//if (class_exists($className)) return;
		
		$classFile = static::classFile($className);
		
		$prefixes = array(
			RADIUM_PATH,
			RADIUM_APP_PATH . DIRECTORY_SEPARATOR . 'libraries'
		);
		$prefixes = array_merge($prefixes, explode(':', get_include_path()));
		
		foreach ($prefixes as $prefix) {
			$path = $prefix . DIRECTORY_SEPARATOR . $classFile;
			if (file_exists($path) && include_once($path)) {
				return true;
			}
		}
		
		if ($fireExceptionIfNeeded) {
			throw new ErrorException(StringUtil::getLocalizedString('Class file "{1}" is not found.', array($classFile)), CLASSFILE_NOT_FOUND);
		}
		
		return false;
	}
	
	/**
	 * クラスファイル名を取得
	 * @param string $className クラス名
	 */
	private static function classFile($className)
	{
		$className = ltrim($className, '\\');
		$fileName  = '';
		$namespace = '';
		if ($lastNsPos = strripos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		
	    return $fileName;
	}
}

ClassLoader::init();
