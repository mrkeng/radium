<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
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
	private $queryString;
	
	public $params;
	
	/**
	 * コンストラクタ
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_config = array(
			'queryString' => '',
		);
		
		if (strpos($_SERVER['REQUEST_URI'], '?') > 0) {
			$this->_config['queryString'] = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?'));
		}
		
		// uri
		$uri = $_SERVER['REQUEST_URI'];
		if (strpos($uri, '?') !== false) {
			$uri = substr($uri, 0, strpos($uri, '?'));
		}
		
		// base
		$base = str_replace('\\', '/', dirname($_SERVER['PHP_SELF']));
		$base = rtrim(str_replace(array("/app/webroot", '/webroot'), '', $base), '/');
		$idx = strlen($base);
		if ($base) {
			$idx += strpos($uri, $base);
		}
		$appBase = substr($uri, 0, $idx);
		$uri = substr($uri, $idx);
		
		if (!defined('RADIUM_APP_BASE_URI')) define('RADIUM_APP_BASE_URI', $appBase . '/');
		
		$argPos = strpos($uri, '?');
		if ($argPos > 0) $uri = substr($uri, 0, $argPos);
		
		$uri = rtrim($uri, '/');
		if (substr($uri, 0, 1) != '/') $uri = '/' . $uri;
		
		$this->_config['uri'] = $uri;
		
		$domain = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['SERVER_NAME'];
		if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80) {
			$domain .= ':' . $_SERVER['SERVER_PORT'];
		}
		
		// data and query
		$this->_config['domain'] = $domain;
		$this->_config['appBaseURI'] = RADIUM_APP_BASE_URI;
		$this->_config['base'] = $base;
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
		switch ($name) {
			case 'baseURI':
				return $this->_config['domain'] . $this->_config['appBaseURI'];
			case 'uri':
				return $this->_config['uri'];
			case 'data':
				return $this->_config['data'];
			case 'query':
				return $this->_config['query'];
			case 'queryString':
				return $this->_config['queryString'];
		}
		
		throw new ErrorException(StringUtil::getLocalizedString('{1} is an illegal property.', array($name)), INVALID_PROPERTY);
	}
}