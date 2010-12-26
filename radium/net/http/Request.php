<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\net\http;

use \ErrorException;
use \radium\core\Object;
use \radium\utils\StringUtil;

/**
 * HTTP の入力を管理するクラス
 */
final class Request extends Object
{
	private $_config;
	
	public $params;
	
	/**
	 * コンストラクタ
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_config = array();
		
		// uri
		$uri = $_SERVER['REQUEST_URI'];
		
		$basePath = defined('APP_BASE_PATH') ? APP_BASE_PATH : '/';
		if (strpos($uri, $basePath) == '0')
		{
			$uri = substr($uri, strlen($basePath));
		}
		
		$argPos = strpos($uri, '?');
		if ($argPos > 0) $uri = substr($uri, 0, $argPos);
		
		$uri = rtrim($uri, '/');
		if (substr($uri, 0, 1) != '/') $uri = '/' . $uri;
		
		$this->_config['uri'] = $uri;
		
		// data and query
		$this->_config['data'] = $this->_config['query'] = array();
		$this->_config['data'] += $_POST;
		$this->_config['query'] += $_GET;
	}
	
	/**
	 * 
	 * @param string $name
	 * @throws \Exception
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'uri':
				return $this->_config['uri'];
			case 'data':
				return $this->_config['data'];
			case 'query':
				return $this->_config['query'];
		}
		
		throw new ErrorException(StringUtil::getLocalizedString('{1} is an illegal property.', array($name)), INVALID_PROPERTY);
	}
}