<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\template\helper;

use \radium\template\Helper;

/**
 * フォームのヘルパークラス
 */
class Form extends Helper
{
	/**
	 * 
	 * @param mixed $binding
	 * @param array $options
	 */
	public function create($binding = null, array $options = array())
	{
		$tag = '<form action="{:action}" {:options}>';
		
		$options += array(
			'action' => null,
			'method' => 'post'
		);
		
		$action = $options['action'];
		unset($options['action']);
		
		$replace = array(
			'action' => $this->escape($this->path($action)),
			'options' => $options
		);
		
		return $this->_replace($tag, $replace);
	}
	
	/**
	 * 
	 */
	public function end()
	{
		return '</form>';
	}
	
	/**
	 * 
	 * @param string $name
	 * @param array $options
	 */
	public function text($name, array $options = array())
	{
		$tag = '<input name="{:name}" {:options} />';
		
		$options += array('type' => 'text', 'value' => '');
		
		$replace = array(
			'name' => $name,
			'options' => $options
		);
		
		return $this->_replace($tag, $replace);
	}
	
	/**
	 * 
	 * @param string $name
	 * @param array $options
	 */
	public function password($name, array $options = array())
	{
		$options += array('type' => 'password');
		return $this->text($name, $options);
	}
	
	/**
	 * 
	 * @param string $name
	 * @param array $options
	 */
	public function textarea($name, array $options = array())
	{
		$tag = '<textarea name="{:name}" {:options}>{:value}</textarea>';
		
		$value = isset($options['value']) ? $options['value'] : '';
		unset($options['value']);
		
		$replace = array(
			'name' => $name,
			'options' => $options,
			'value' => $this->escape($value),
		);
		
		return $this->_replace($tag, $replace);
	}
}