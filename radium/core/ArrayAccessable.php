<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\core;

use \ArrayAccess;
use \radium\core\Object;
use \radium\data\Resource;

/**
 * 配列ライクなアクセスをするためのベースクラス
 */
class ArrayAccessable extends Object implements ArrayAccess
{
	protected $_data = array();
	
	/**
	 * コンストラクタ
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 生データを取得
	 * @return mixed 生データ
	 */
	public function data()
	{
		return $this->_data;
	}
	
	/**
	 * 配列に変換
	 * @return mixed 配列
	 */
	public function toArray()
	{
		return $this->_data;
	}
	
	/**
	 * セッター
	 * @param string $name プロパティ名
	 * @param mixed $value 値
	 */
	public function __set($name, $value)
	{
		$this->_data[$name] = $value;
	}
	
	/**
	 * ゲッター
	 * @param string $name プロパティ名
	 * @return mixed 値
	 */
	public function __get($name)
	{
		return isset($this->_data[$name]) ? $this->_data[$name] : null;
	}
	
	/**
	 * Checks whether or not an offset exists.
	 *
	 * @param string $offset An offset to check for.
	 * @return boolean `true` if offset exists, `false` otherwise.
	 */
	public function offsetExists($offset) {
		return isset($this->_data[$offset]);
	}

	/**
	 * Returns the value at specified offset.
	 *
	 * @param string $offset The offset to retrieve.
	 * @return mixed Value at offset.
	 */
	public function offsetGet($offset) {
		return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
	}

	/**
	 * Assigns a value to the specified offset.
	 *
	 * @param string $offset The offset to assign the value to.
	 * @param mixed $value The value to set.
	 * @return mixed The value which was set.
	 */
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			return $this->_data[] = $value;
		}
		return $this->_data[$offset] = $value;
	}

	/**
	 * Unsets an offset.
	 *
	 * @param string $offset The offset to unset.
	 * @return void
	 */
	public function offsetUnset($offset) {
		unset($this->_data[$offset]);
	}
}