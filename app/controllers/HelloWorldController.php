<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2011, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\controllers;

class HelloWorldController extends \radium\action\Controller
{
	/**
	 * 文字列を表示
	 * @param string $str 文字列
	 */
	public function index()
	{
	}
	
	/**
	 * 文字列を表示
	 * @param string $str 文字列
	 */
	public function show($str = null)
	{
		return compact('str');
	}
	
	/**
	 * [JSON-API]アンケート用マスタデータをロード
	 */
	public function hello()
	{
		return 'world';
	}
	
	/**
	 * json
	 */
	public function show_json()
	{
		$json = array(
			'hello' => 'world'
		);
		
		$this->render(array('json' => $json));
	}
}
