<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\template\helper;

use \radium\template\Helper;

/**
 * HTML ヘルパークラス
 */
class Html extends Helper
{
	/**
	 * 
	 * @param string $encoding
	 */
	public function charset($encoding = null)
	{
		return '<meta charset="UTF-8" />';
	}
		
	public function link($label, $url, array $options = array())
	{
		$defaults = array('escape' => true);
		$options += $defaults;
		
		$tag = '<a href="{:url}" {:options}>{:title}</a>';
		
		if (isset($options['type']) && $options['type'] == 'icon')
		{
			if (is_null($url)) $url = 'favicon.ico';
			$options['type'] = 'image/x-icon';
			$tag = '<link href="{:url}" title="{:title}" rel="icon" {:options}/>' . "\n";
			$tag .= '<link href="{:url}" title="{:title}" rel="shortcut icon" {:options}/>';
		}
		
		$replace = array(
			'url' => $this->path($url),
			'options' => $options,
			'title' => $this->escape($label, $options)
		);
		
		return $this->_replace($tag, $replace, 'type');
	}
	
	public function style($path, array $options = array())
	{
		if (is_array($path))
		{
			$results = array();
			foreach ($path as $p)
			{
				$results[] = call_user_func_array(array($this, __FUNCTION__), array($p, $options));
			}
			return implode("\n", $results);
		}
		
		$tag = '<link rel="{:type}" type="text/css" href="{:path}" {:options} />';
		
		$dirName = defined('CSS_DIR') ? CSS_DIR : 'css';
		
		$replace = array(
			'path' => $this->path($path, APP_BASE_PATH . $dirName, '.css'),
			'options' => $options,
			'type' => 'stylesheet'
		);
		
		return $this->_replace($tag, $replace);
	}
	
	public function script($path, array $options = array())
	{
		if (is_array($path))
		{
			$results = array();
			foreach ($path as $p)
			{
				$results[] = call_user_func_array(array($this, __FUNCTION__), array($p, $options));
			}
			return implode("\n", $results);
		}
				
		$tag = '<script type="text/javascript" src="{:path}" {:options}></script>';
		
		$dirName = defined('JS_DIR') ? JS_DIR : 'js';
		
		$replace = array(
			'path' => $this->path($path, APP_BASE_PATH . $dirName, '.js'),
			'options' => $options
		);
		
		return $this->_replace($tag, $replace);
	}
		
	public function image($url, array $options = array())
	{
		$defaults = array('escape' => true, 'alt' => '');
		$options += $defaults;
		
		$tag = '<img src="{:url}" {:options} />';
		
		$replace = array(
			'url' => $this->path($url),
			'options' => $options
		);
		
		return $this->_replace($tag, $replace);
	}
}
