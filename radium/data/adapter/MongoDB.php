<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2011, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\data\adapter;

use \ErrorException;
use \Exception;
use \InvalidArgumentException;
use \Mongo;
use \MongoConnnectionException;
use \MongoCursorException;
use \radium\core\Object;
use \radium\data\Resource;
use \radium\errors\DatabaseError;

/**
 * MongoDB のアダプタ
 */
class MongoDB extends Object
{
	private static $collections = array();
	public static $count = 0;
	
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
	protected $collectionName;
	protected $resource;
	
	/**
	 * コンストラクタ
	 */
	public function __construct($model, $resource)
	{
		parent::__construct();
		
		$this->model = $model;
		$this->collectionName = $model->className();
		$this->resource = $resource;
	}
	
	/**
	 * コレクションを取得
	 */
	public function getCollection($master = false)
	{
		static::$count++;
		if ($master) {
			$resource = $this->resource[0];
		} else {
			if (count($this->resource) == 1) {
				$resource = $this->resource[0];
			} else {
				$resource = $this->resource[rand(1, count($this->resource) - 1)];
			}
		}
		
		$host = $resource['host'];
		$databaseName = $resource['database'];
		
		$key = implode('_', array($host, $databaseName, $this->collectionName));
		if (isset(static::$collections[$key])) return static::$collections[$key];
		
		//echo ' ' . $key . '<br /> ';
		
		$collection = null;
		try {
			
			$mongo = new Mongo('mongodb://' . $host, array("persist" => ""));
			//$mongo = new Mongo('mongodb://' . $host);
			$database = $mongo->selectDB($databaseName);
			$collection = $database->selectCollection($this->collectionName);
			
			static::$collections[$key] = $collection;
			
		} catch (MongoConnnectionException $e) {
			throw new DatabaseError($e->getMessage());
		} catch (InvalidArgumentException $e) {
			throw new DatabaseError($e->getMessage());
		} catch (ErrorException $e) {
			throw new DatabaseError($e->getMessage());
		} catch (Exception $e) {
			throw new DatabaseError($e->getMessage());
		}
		
		return $collection;
	}
	
	/**
	 * 検索
	 * @param array $options 条件
	 * @param bool $raw PHP の配列を返却する場合は true にする。デフォルトは false。
	 * @return array 
	 */
	public function find(array $options = array(), $raw = false)
	{
		$collection = $this->getCollection();
		$cursor = $collection->find(static::_conditions($options['conditions']));
		
		if (isset($options['order'])) $cursor->sort($options['order']);
		if (isset($options['offset'])) $cursor->skip($options['offset']);
		if (isset($options['limit'])) $cursor->limit($options['limit']);
		
		$modelClass = get_class($this->model);
		
		$list = array();
		while ($data = $cursor->getNext()) {
			if ($raw) {
				$list[] = $data;
				continue;
			}
			
			$model = new $modelClass();
			
			foreach ($data as $key => $value) {
				$model->$key = $value;
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
		$collection = $this->getCollection();
		$cursor = $collection->find(static::_conditions($conditions));
		return $cursor->count();
	}
	
	/**
	 * 更新
	 * @param array $conditions 条件
	 * @param array $values 更新後の値
	 */
	public function update(array $conditions, array $values = array(), array $options = array())
	{
		$collection = $this->getCollection(true);
		
		$options += array(
			'upsert' => false,
			'multiple' => true,
		);
		
		try {
			return $collection->update(static::_conditions($conditions), $values, array('safe' => true) + $options);
		} catch (MongoCursorException $e) {
			return false;
		}
	}
	
	/**
	 * 保存
	 * @return bool 成功=true, 失敗=false
	 */
	public function save()
	{
		$collection = $this->getCollection(true);
		
		$model = $this->model;
		$data = $model->data();
		
		// 更新
		if (isset($data['_id']) && $data['_id']) {
			return $collection->save($data);

		// 新規作成
		} else {
			return $collection->insert($data);
		}
	}
	
	/**
	 * 1データ削除
	 */
	public function delete()
	{
		$model = $this->model;
		$data = $model->data();
		if (isset($data['_id'])) {
			$collection = $this->getCollection(true);
			try {
				return $collection->remove(array('_id' => $data['_id']), array("justOne" => true, 'safe' => true));
				return $result['n'];
			} catch (MongoCursorException $e) {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * 削除
	 * @param array $conditions 条件
	 */
	public function deleteAll(array $conditions)
	{
		$collection = $this->getCollection(true);
		
		try {
			$result =  $collection->remove(static::_conditions($conditions), array('safe' => true));
			return $result['n'];
		} catch (MongoCursorException $e) {
			return false;
		}
	}
}
