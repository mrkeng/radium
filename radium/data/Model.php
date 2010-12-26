<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\data;

use \Mongo;
use \radium\core\ArrayAccessable;
use \radium\data\Resource;

/**
 * モデルのベースクラス
 */
class Model extends ArrayAccessable
{
	private static $collection;
	private static function _instance()
	{
		$class = get_called_class();
		return new $class();
	}
	
	/**
	 * 作成
	 * @param $data
	 */
	public static function create(array $data = array())
	{
		$class = get_called_class();
		$instance = new $class();
		
		foreach ($data as $key => $value)
		{
			$instance->_data[$key] = $value;
		}
		
		return $instance;
	}
	
	/**
	 * 検索
	 * @param string $type 'all' OR 'first'
	 * @param array $options 条件
	 * @param bool $raw PHP の配列を返却する場合は true にする。デフォルトは false。
	 * @return array
	 */
	public static function find($type, array $options = array(), $raw = false)
	{
		$defaults = array('conditions' => array());
		$options += $defaults;
		
		switch ($type)
		{
			case 'first':
				return static::findOne($options['conditions'], $raw);
			case 'all':
			default:
				return static::findAll($options, $raw);
		}
	}
	
	/**
	 * すべてのデータを取得します
	 * @param array $options 条件
	 * @param bool $raw PHP の配列を返却する場合は true にする。デフォルトは false。
	 * @return array
	 */
	public static function all(array $options = array(), $raw = false)
	{
		return static::findAll($options, $raw);
	}
	
	/**
	 * 検索
	 * @param array $options 条件
	 * @param bool $raw PHP の配列を返却する場合は true にする。デフォルトは false。
	 * @return array
	 */
	public static function findAll(array $options = array(), $raw = false)
	{
		$defaults = array(
			'conditions' => array()
		);
		$options += $defaults;
		
		$instance = static::_instance();
		
		$adapter = $instance->_adapter;
		return $adapter->find($options, $raw);
	}
	
	/**
	 * 検索してひとつだけ取得
	 * @param array $conditions 条件
	 * @param bool $raw PHP の配列を返却する場合は true にする。デフォルトは false。
	 * @return array
	 */
	public static function findOne(array $conditions = array(), $raw = false)
	{
		$options = array(
			'limit' => 1,
			'conditions' => $conditions
		);
		$list = static::findAll($options, $raw);
		return is_array($list) && count($list) > 0 ? $list[0] : null;
	}
	
	/**
	 * 指定した条件にマッチする件数を取得
	 * @param array $conditions 条件
	 * @return int
	 */
	public static function count(array $conditions = array())
	{
		$instance = static::_instance();
		
		$adapter = $instance->_adapter;
		return $adapter->count($conditions);
	}
	
	/**
	 *
	 */
	public static function deleteAll()
	{
		$instance = static::_instance();
		$adapter = $instance->_adapter;
		return $adapter->deleteAll();
	}
	
	protected $_class;
	protected $_resource = 'default';
	protected $_adapter;
	
	/**
	 * コンストラクタ
	 */
	public function __construct()
	{
		parent::__construct();
		
		if (is_null($this->_class))
		{
			$class = get_class($this);
			$this->_class = strtolower(substr($class, strrpos($class, '\\') + 1));
		}
		
		// adapter
		$resource = Resource::get($this->_resource);
		
		$adapter = $resource['adapter'];
		$adapter = str_replace('.', '\\', $adapter);
		
		$this->_adapter = new $adapter($this, $resource);
	}
	
	/**
	 *
	 */
	public function className()
	{
		return $this->_class;
	}
	
	/**
	 * 保存
	 * @return bool 成功=true, 失敗=false
	 */
	public function save(array $data = array())
	{
		$this->_data = $data + $this->_data;
		
		$adapter = $this->_adapter;
		return $adapter->save();
	}
	
	/**
	 * 削除
	 */
	public function delete()
	{
		$adapter = $this->_adapter;
		return $adapter->delete();
	}
}
