<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2011, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\template;

use \radium\action\Controller;
use \radium\core\Object;
use \radium\errors\NotFoundError;
use \radium\template\helper\Html;
use \radium\template\helper\Form;
use \radium\utils\StringUtil;

/**
 * ビュークラス
 */
class View extends Object
{
	private $controller;
	private $options;
	private $request;
	private $layoutFile;
	private $templateFile;
	private $data;
	private $params;
	private $content;
	private $contentType;
	
	private $html;
	private $form;
	
	private $_title;
	private $_ogp;
	private $_styles;
	private $_scripts;
	
	public static $mimeTypes = array(
		'html' => 'text/html',
		'json' => 'application/json',
		'xml' => 'application/xml'
	);
	
	/**
	 * コンストラクタ
	 * @param \radium\action\Controller $controller コントローラ
	 * @param array $options レンダリング情報
	 */
	public function __construct(Controller $controller, array $options)
	{
		$type = $options['type'];
		$layout = $options['layout'];
		$template = $options['template'];
		
		if (isset($options['json'])) {
			$options['type'] = 'json';
			$type = 'json';
		}
		
		// layout
		$layoutFile = $layout . '.' . $type . '.php';
		
		// template
		$templateFile = $controller->_controller . '/'
			. $template . '.' . $type . '.php';
		
		$this->layoutFile = RADIUM_APP_PATH . '/views/layouts/' . $layoutFile;
		$this->templateFile = RADIUM_APP_PATH . '/views/' . $templateFile;
		
		// ヘルパー
		$this->html = new Html();
		$this->form = new Form();
		
		$this->controller = $controller;
		$this->request = $controller->request;
		$this->options = $options;
		$this->data = $options['data'];
		
		$this->params = array();
	}
	
	/**
	 * レンダリングを行う
	 * @return string レンダリング結果
	 */
	public function render()
	{
		if (isset($this->options['json'])) {
			return json_encode($this->options['json']);
		}
		if ($this->options['type'] == 'json') {
			return json_encode($this->data);
		}
		
		$this->content = $this->_render($this->templateFile);
		return $this->_render($this->layoutFile);
	}
	
	/**
	 * コンテントタイプ
	 */
	public function contentType()
	{
		if ($this->contentType) {
			return $this->contentType;
		} elseif (isset(static::$mimeTypes[$this->options['type']])) {
			return static::$mimeTypes[$this->options['type']];
		}
		return 'text/plain';
	}
	
	/**
	 * テンプレートの処理を行う
	 * @param string $template テンプレートファイルのパス
	 * @return string レンダリング結果
	 */
	private function _render($template)
	{
		// テンプレートが見つからない！
		if (!file_exists($template)) {
			throw new NotFoundError(StringUtil::getLocalizedString('Template "{1}" is not found.', array($template)), TEMPLATE_NOT_FOUND);
		}
		
		$html = $this->html;
		$form = $this->form;
		
		extract($this->data, EXTR_OVERWRITE);
		ob_start();
		try {
			require $template;
		} catch (Exception $e) {
			ob_clean();
			throw $e;
		}
		
		return ob_get_clean();
	}
	
	/**
	 * リクエストを取得
	 * @return \radium\net\http\Request
	 */
	private function request()
	{
		return $this->request;
	}
	
	/**
	 * コンテンツのレンダリングデータ
	 */
	private function content()
	{
		return $this->content;
	}
	
	/**
	 * テンプレートのパラメータを設定する
	 * @param string $name パラメータ名
	 * @param $value 値
	 * @return 値
	 */
	private function params($name, $value = null)
	{
		if ($value !== null) {
			$this->params[$name] = $value;
		}
		
		return isset($this->params[$name]) ? $this->params[$name] : null;
	}
	
	/**
	 * ページのタイトルを設定、取得する
	 * @param string $title ページタイトル
	 * @return string ページタイトル
	 */
	private function title($title = null)
	{
		if (!is_null($title)) {
			$this->_title = $title;
		}
		return $this->_title;
	}
	
	/**
	 * Open Graph Protocol
	 * @param array OGP データ
	 */
	private function ogp(array $ogp = null)
	{
		if (!is_null($ogp)) {
			$this->_ogp = $ogp;
		}
		$result = '';
		if (is_array($this->_ogp)) {
			foreach ($this->_ogp as $key => $value) {
				if (is_null($value) || $value == '') continue;
				$result .= '<meta property="og:'
					. $this->html->escape($key)
					. '" content="'
					. $this->html->escape($value) . '" />' . "\n";
			}
		}
		return $result;
	}
	
	/**
	 * スタイルシートのタグを設定、取得する
	 * @param string $style スタイルシートのタグ
	 * @return string スタイルシートのタグ
	 */
	private function styles($style = null)
	{
		if (!is_array($this->_styles)) {
			$this->_styles = array();
		}
		if (is_array($style)) {
			foreach ($style as $s) $this->styles($s);
		}
		if (is_string($style)) {
			$this->_styles[] = $style;
		}
		return implode("\n", $this->_styles);
	}
	
	/**
	 * スクリプトタグを設定、取得する
	 * @param string $script スクリプトのタグ
	 * @return string スクリプトのタグ
	 */
	private function scripts($script = null)
	{
		if (!is_array($this->_scripts)) {
			$this->_scripts = array();
		}
		if (is_array($script)) {
			foreach ($script as $s) $this->scripts($s);
		}
		if (is_string($script)) {
			$this->_scripts[] = $script;
		}
		return implode("\n", $this->_scripts);
	}
}