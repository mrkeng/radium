<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\template;

use \radium\core\Object;
use \radium\net\http\Request;
use \radium\utils\StringUtil;

/**
 * ヘルパーのベースクラス
 */
class Helper extends Object
{
	protected $controller;
	
	/**
	 * コンストラクタ
	 * @param \radium\action\Controller $controller
	 */
	public function __construct()
	{
	}
	
	/**
	 * 文字列の HTML エスケープ
	 * @param $value エスケープする文字列 OR 配列
	 * @param array $options
	 */
	public function escape($value, array $options = array())
	{
		$defaults = array('escape' => true);
		$options += $defaults;
		
		if ($options['escape'] === false) return $value;
		
		if (is_array($value)) {
			return array_map(array($this, __FUNCTION__), $value);
		}
		return StringUtil::escape($value);
	}
	
	/**
	 * パスを変換
	 * @param string $path
	 * @param string $prefix
	 * @param string $suffix
	 */
	public function path($path, $prefix = null, $suffix = null)
	{
		if (is_null($path) || preg_match('/^\\?/', $path)) {
			if (is_null($path)) $path = '';
			$controller = $this->controller;
			$request = new Request();
			return RADIUM_APP_BASE_URI . substr($request->uri, 1) . $path;
		} elseif (preg_match('/^\#/', $path)) {
			return $path;
		} elseif (!preg_match('/^https?:\\/\\//', $path)) {
			
			if (is_null($prefix)) {
				if (substr($path, 0, 1) == '/') $path = substr($path, 1);
				$path = RADIUM_APP_BASE_URI . $path;
			} elseif (preg_match('/^\\//', $path)) {
				$path = $path;
			} else {
				$path = $prefix . '/' . $path;
			}
		}
		
		$filePath = $path;
		if (strpos($filePath, '?') !== false) {
			$filePath = substr($filePath, 0, strpos($filePath, '?'));
		}
		
		if (!is_null($suffix) && strlen($filePath) != (strlen($suffix) + strrpos($filePath, $suffix))) $path .= $suffix;
		
		return $path;
	}
	
	/**
	 * 
	 * @param $string
	 * @param $replace
	 */
	protected function _replace($string, $replace, $excludeAttr = '')
	{
		$excludeAttr .= ' escape';
		$excludes = explode(' ', $excludeAttr);
		
		foreach ($replace as $key => $value) {
			if (is_array($value)) {
				$list = array();
				foreach ($value as $k => $v) {
					if (in_array($k, $excludes)) continue;
					$list[] = $k . '="' . $this->escape($v) . '"';
				}
				$value = implode(' ', $list);
			}
			$string = str_replace('{:' . $key . '}', $value, $string);
		}
		return $string;
	}
}
