<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2011, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\core;

use \ErrorException;
use \radium\utils\StringUtil;

/**
 * すべてのクラスの規定クラス
 */
class Object
{
	/**
	 * コンストラクタ
	 */
	public function __construct()
	{
	}
	
	/**
	 * プロテクテッドなメソッドを呼ぶためのプロキシ機能
	 * @param string $method メソッド名
	 * @param array $args 引数
	 * @throws Exception メソッドが呼べなかった場合にスローされる
	 */
	final public function invokeMethod($method, array $args = array())
	{
		if (is_callable(array($this, $method))) {
			call_user_func_array(array($this, $method), $args);
			return;
		}
		
		throw new ErrorException(StringUtil::getLocalizedString('Method "{1}" cannot be called.', array($method)), INVALID_METHOD);
	}
	
	/**
	 * 処理の停止
	 */
	final protected function _stop()
	{
		exit();
	}
}
