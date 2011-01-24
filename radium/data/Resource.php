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
	 * @param array $data マスターデータベース設定
	 * @param array $data スレイブデータベース設定（オプション） 
	 */
	public static function add()
	{
		$num = func_num_args();
		if ($num < 2) {
			throw new ErrorException(StringUtil::getLocalizedString('Invalid Arguments'));
		}
		$args = func_get_args();
		$name = array_shift($args);
		
		static::$resources[$name] = $args;
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
	
	public $data;
	
	/**
	 * コンストラクタ
	 * @param array $data データベース設定
	 */
	public function __construct(array $data)
	{
		$data[0] += array(
			'adapter' => 'radium.data.adapter.MongoDB'
		);
		
		$this->data = $data;
	}
}
