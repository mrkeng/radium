<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\action;

use \radium\action\Dispatcher;
use \radium\core\Object;
use \radium\net\http\Request;
use \radium\template\View;
use \radium\utils\StringUtil;

/**
 * コントローラのベースクラス
 */
class Controller extends Object
{
	public $view;
	public $request;
	public $dispatcher;
	public $type = 'html';
	public $layout = 'default';
	public $template = '';
	public $_controller;
	public $_render;
	protected $_renderedContent = '';
	protected $_params;
	
	/**
	 * コンストラクタ
	 * @param \radium\net\http\Request $request
	 */
	public function __construct(Request $request, Dispatcher $dispatcher = null)
	{
		$this->request = $request;
		$this->dispatcher = $dispatcher;
		
		$class = get_class($this);
		$class = substr($class, strrpos($class, '\\') + 1);
		$class = substr($class, 0, strrpos($class, 'Controller'));
		
		$this->_controller = StringUtil::uncamelcase($class);
				
		$this->_render = array();
		$this->_params = array();
		
		$headers = $request->headers;
		if (isset($headers['X-PJAX'])) {
			$this->layout = null;
		}
	}
	
	/**
	 * アクションの前に呼ばれる初期化処理
	 */
	protected function _init()
	{
	}
	
	/**
	 * テンプレートに送るデータをセット
	 */
	protected function param($name, $value = null, $force = false)
	{
		if ($value !== null || $force) {
			$this->_params[$name] = $value;
		}
		
		return isset($this->_params[$name]) ? $this->_params[$name] : null;
	}
	protected function params($name, $value = null, $force = false)
	{
		return $this->param($name, $value, $force);
	}
	
	/**
	 * JSON 出力
	 * @param array $json
	 */
	public function json(array $json)
	{
		$this->render(array('json' => $json));
	}
	
	/**
	 * print_r 出力
	 * @param array $obj
	 */
	public function print_r($obj)
	{
		header('Content-Type: text/plain');
		print_r($obj);
		exit;
	}
	
	/**
	 * レンダリング
	 * @param array $options
	 */
	public function render(array $options = array())
	{
		if ($this->_renderedContent) return;
		
		$default = array(
			'controller' => $this->_controller,
			'data' => array()
		);
		
		$additionals = array();
		if ($this->type) {
			$additionals['type'] = $this->type;
		}
		if ($this->layout) {
			$additionals['layout'] = $this->layout;
		}
		if ($this->template) {
			$additionals['template'] = $this->template;
		}
		$options += $additionals;
		
		$options += $this->_render;
		$options += $default;
		$options['data'] += $this->_params;
		
		// レンダリング処理
		$view = new View($this, $options);
		$this->_renderedContent = $view->render();
		
		$this->view = $view;
	}
	
	/**
	 * リダイレクト
	 * @param string $url
	 */
	public function redirect($url)
	{
		$to = $url;
		if (!preg_match('/^(https?|mailto):\\/\\//', $url)) {
			$url = preg_replace('/^\\//', '', $url);
			$to = $this->request->baseURI . $url;
		}
		
		header('Location: ' . $to);
		
		$this->_stop();
	}
	
	/**
	 * 他のコントローラのアクションを呼ぶ
	 */
	public function call($controller, $action, array $args = array())
	{
		return $this->dispatcher->dispatch($controller, $action, $args);
	}
	
	/**
	 * レンダリング済みコンテンツを取得
	 */
	public function renderedContent()
	{
		return $this->_renderedContent;
	}
	
	/**
	 * ファイナライズ
	 */
	protected function _finalize(array $data = array())
	{
		$this->render(array(
			'data' => $data
		));
	}
}