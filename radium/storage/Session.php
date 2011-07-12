<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2011, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\storage;

use \radium\core\Object;

/**
 * セッション情報を管理するクラス
 */
final class Session extends Object
{
	private static $_isStarted;
	
	private static $options;
	
	/**
	 * 
	 * @param array $options
	 */
	public static function config(array $options = array())
	{
		if (!static::$options) {
			$defaults = array(
				'session.name' => basename(RADIUM_APP_PATH),
				'session.cookie_lifetime' => 1209600, // 2w
				'session.gc_maxlifetime' => 1209600,
			);
			
			$options += $defaults;
			static::$options = $options;
		} else {
			static::$options = $options + static::$options;
		}
		return static::$options;
	}
	
	/**
	 * セッションの初期化処理
	 */
	public static function init($force = false)
	{
		if (!$force && static::$_isStarted) return;
		if (!session_id()) {
			$options = static::config();
			foreach ($options as $key => $value) {
				if (strpos($key, 'session.') == 0) {
					ini_set($key, $value);
				}
			}
			session_start();
		}
		
		static::$_isStarted = true;
	}
	
	/**
	 * セッションのデータを読み込む
	 * @param $name キー
	 */
	public static function read($name)
	{
		static::init();
		if (isset($_SESSION[$name])) return $_SESSION[$name];
		return null;
	}
	
	/**
	 * セッションにデータを書き込む
	 * @param string $name キー
	 * @param object $value 値
	 */
	public static function write($name, $value)
	{
		static::init();
		$_SESSION[$name] = $value;
	}
	
	/**
	 * セッションに保存されたデータを削除する
	 * @param string $name キー
	 */
	public static function delete($name)
	{
		static::init();
		unset($_SESSION[$name]);
	}
}