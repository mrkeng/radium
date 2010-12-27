<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\data\adapter;

use \Mongo;
use \radium\core\Object;
use \radium\data\Resource;

/**
 * MongoDB のアダプタ
 */
class MongoDB extends Object
{
	/**
	 * 条件式の変換
	 * @param array $conditions
	 */
	private static function _conditions(array $conditions = array())
	{
		$operators = array(
			'<' => '$lt',
			'>' => '$gt',
			'<=' =>  '$lte',
			'>=' => '$gte',
			'!=' => '$ne',
			'<>' => '$ne'
		);
		
		$results = array();
		foreach ($conditions as $key => $value) {
			if (is_array($value)) {
				$values = $value;
				$value = array();
				foreach ($values as $k => $v) {
					$value[isset($operators[$k]) ? $operators[$k] : $k] = $v;
				}
			}
			$results[$key] = $value;
		}
		return $results;
	}
	
	protected $model;
	protected $modelClass;
	protected $collection;
	
	/**
	 * コンストラクタ
	 */
	public function __construct($model, $resource)
	{
		parent::__construct();
		
		$host = $resource['host'];
		$databaseName = $resource['database'];
		
		$mongo = new Mongo('mongodb://' . $host, array('persist' => ''));
		$database = $mongo->selectDB($databaseName);
		
		$this->model = $model;
		$this->modelClass= get_class($model);
		
		$this->collection = $database->selectCollection($model->className());
	}
	
	/**
	 * 検索
	 * @param array $options 条件
	 * @param bool $raw PHP の配列を返却する場合は true にする。デフォルトは false。
	 * @return array 
	 */
	public function find(array $options = array(), $raw = false)
	{
		$collection = $this->collection;
		$cursor = $collection->find(static::_conditions($options['conditions']));
		
		if (isset($options['offset'])) $cursor->skip($options['offset']);
		if (isset($options['limit'])) $cursor->limit($options['limit']);
		if (isset($options['order'])) $cursor->sort($options['order']);
		
		$modelClass = $this->modelClass;
		
		$list = array();
		while ($data = $cursor->getNext()) {
			if ($raw) {
				$list[] = $data;
				continue;
			}
			
			$model = new $modelClass();
			
			$keys = array_keys($data);
			foreach ($keys as $key) {
				$model->$key = $data[$key];
			}
			$list[] = $model;
		}
		
		return $list;
	}
	
	/**
	 * カウント
	 * @param array $conditions 条件
	 * @return int 
	 */
	public function count(array $conditions = array())
	{
		$collection = $this->collection;
		$cursor = $collection->find(static::_conditions($conditions));
		return $cursor->count();
	}
	
	/**
	 * 保存
	 * @return bool 成功=true, 失敗=false
	 */
	public function save()
	{
		$model = $this->model;
		$collection = $this->collection;
		
		$data = $model->data();
		
		// 更新
		if (isset($data['_id']) && $data['_id'])
		{
			return $collection->save($data);
		}
		
		// 新規作成
		else
		{
			return $collection->insert($data);
		}
	}
	
	/**
	 * 削除
	 */
	public function delete()
	{
		$model = $this->model;
		$data = $model->data();
		if (isset($data['_id']))
		{
			$collection = $this->collection;
			return $collection->remove(array('_id' => $data['_id']), array("justOne" => true));
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * 
	 */
	public function deleteAll()
	{
		$collection = $this->collection;
		return $collection->remove(array());
	}
}
