<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\controllers;

class HelloWorldController extends AbstractBaseController
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
	 * 文字列を表示
	 * @param string $str 文字列
	 */
	public function eecho($str) {
		return $str;
	}
	
	/**
	 * 文字列を表示
	 */
	public function hello()
	{
		return 'world';
	}
	
	/**
	 * json
	 */
	public function show_json($arg = 'world')
	{
		$json = array(
			'hello' => $arg
		);
		
		$this->json($json);
	}
	
	/**
	 * リクエストヘッダを表示
	 */
	public function headers()
	{
		$this->print_r($this->request->headers);
	}
}
