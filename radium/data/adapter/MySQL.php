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
use \PDO;
use \PDOException;
use \radium\core\Object;
use \radium\data\Resource;
use \radium\errors\DatabaseError;

/**
 * MySQL のアダプタ
 */
class MySQL extends Object
{
	private static $dbh = array();
	public static $count = 0;
	
	/**
	 * 条件式の変換
	 * @param array $conditions
	 */
	private static function _conditions(array $conditions = array())
	{
		/*
		'conditions' => array(
			'accountId' => $this->id,
			'deleted' => array('!=' => true),
			'or' => array(
				array('name' => 'hoge'),
				array('name' => array('!=' => 'fuga'))
			)
		),
		*/
		
		$placeholders = array();
		$values = array();
		foreach ($conditions as $key => $value) {
			if (is_array($value)) {
				$values = $value;
				$value = array();
				foreach ($values as $k => $v) {
					if ($k == 'or') {
						$orList = array();
						foreach ($v as $vv) {
							foreach ($vv as $k2 => $v2) {
								// TODO
							}
						}
						//$placeholders[] = implode(' OR ', $orList);
					} else {
						$placeholders[] = $key . ' ' . $k . ' :' . $key;
						$values[] = $v;
					}
				}
			} else {
				$placeholders[] = $key . ' = :' . $key;
				$values[] = $value;
			}
		}
		
		if (count($placeholders) == 0) return null;
		return array(
			'placeholders' => implode(' AND ', $placeholders),
			'values' => $values
		);
	}
	
	protected $model;
	protected $tableName;
	protected $resource;
	
	/**
	 * コンストラクタ
	 */
	public function __construct($model, $resource)
	{
		parent::__construct();
		
		$this->model = $model;
		$this->tableName = $model->className();
		$this->resource = $resource;
	}
	
	/**
	 * データベースハンドラを取得
	 */
	public function getDatabaseHandler($master = false)
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
		$user = isset($resource['user']) ? $resource['user'] : null;
		$password = isset($resource['password']) ? $resource['password'] : null;
		
		$dsn = 'mysql:dbname=' . $databaseName . ';host=' . $host;
		
		if (isset(static::$dbh[$dsn])) return static::$dbh[$dsn];
		
		//echo ' ' . $key . '<br /> ';
		
		$dbh = null;
		try {
			$dbh = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			static::$dbh[$dsn] = $dbh;
			
		} catch (PDOException $e) {
			throw new DatabaseError($e->getMessage());
		} catch (Exception $e) {
			throw new DatabaseError($e->getMessage());
		}
		
		return $dbh;
	}
	
	/**
	 * 検索
	 * @param array $options 条件
	 * @param bool $raw PHP の配列を返却する場合は true にする。デフォルトは false。
	 * @return array 
	 */
	public function find(array $options = array(), $raw = false)
	{
		$dbh = $this->getDatabaseHandler();
		
		$sql = 'SELECT * FROM ' . $this->tableName;
		
		$conditions = static::_conditions($options['conditions']);
		if ($conditions) {
			$sql .= ' WHERE ' . $conditions['placeholders'];
		}
		
		if (isset($options['order'])) {
			$order = $options['order'];
			$orders = array();
			foreach ($order as $key => $value) {
				$orders[] = $key . ' ' . ($value == -1 ? 'DESC' : 'ASC');
			}
			$sql .= ' ORDER BY ' . implode(', ', $orders);
		}
		if (isset($options['limit'])) {
			$sql .= ' LIMIT ' . intval($options['limit']);
		}
		if (isset($options['offset'])) {
			$sql .= ' OFFSET ' . intval($options['offset']);
		}
		
		$sth = $dbh->prepare($sql);
		$sth->execute($conditions ? $conditions['values'] : null);
		
		$modelClass = get_class($this->model);
		
		$list = array();
		if ($raw) {
			while ($obj = $sth->fetch(PDO::FETCH_ASSOC)) {
				$list[] = $obj;
			}
		} else {
			while ($data = $sth->fetchObject($modelClass)) {
				$list[] = $obj;
			}
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
		$dbh = $this->getDatabaseHandler();
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
		$dbh = $this->getDatabaseHandler(true);
		
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
		$dbh = $this->getDatabaseHandler(true);
		
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
			$dbh = $this->getDatabaseHandler(true);
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
		$dbh = $this->getDatabaseHandler(true);
		
		try {
			$result =  $collection->remove(static::_conditions($conditions), array('safe' => true));
			return $result['n'];
		} catch (MongoCursorException $e) {
			return false;
		}
	}
}
