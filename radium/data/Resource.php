<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\data;

use \ErrorException;
use \radium\core\ArrayAccessable;
use \radium\utils\StringUtil;

/**
 * データベースリソースクラス
 */
class Resource extends ArrayAccessable
{
	private static $resources = array();
	
	/**
	 * データベースリソースを追加
	 * @param string $name データベースリソース名
	 * @param array $data データベース設定
	 */
	public static function add($name, array $data)
	{
		static::$resources[$name] = $data;
	}
	
	/**
	 * データベースリソースを取得
	 * @param string $name データベースリソース名
	 * @return array データベース設定
	 * @throws Exception
	 */
	public static function get($name)
	{
		if (isset(static::$resources[$name])) {
			return new Resource(static::$resources[$name]);
		}
		
		throw new ErrorException(StringUtil::getLocalizedString('Database resource is not found'), CONNECT_DATABASE_ERROR);
	}
	
	/**
	 * コンストラクタ
	 * @param array $data データベース設定
	 */
	public function __construct(array $data = array())
	{
		$data += $defaults = array(
			'adapter' => 'radium.data.adapter.MongoDB'
		);
		$this->_data = $data;
	}
}
