<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\utils;

/**
 * エラー処理のユーティリティクラス
 */
final class StringUtil
{
	/**
	 * 文字列をエスケープする
	 * @param string $string
	 */
	public static function escape($string)
	{
		return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
	}
	
	/**
	 * キャメルケース化
	 * @param string $str
	 */
	final public static function camelcase($str)
	{
		$result = '';
		$words = explode('_', $str);
		foreach ($words as $word)
		{
			if (strlen($word) > 1)
			{
				$result .= strtoupper($word{0}) . substr($word, 1);
			}
			else
			{
				$result .= strtoupper($word);
			}
		}
		return $result;
	}
	
	/**
	 * アンキャメルケース化
	 * @param string $str
	 */
	final public static function uncamelcase($str)
	{
		$result = preg_replace('/([A-Z]+)([A-Z])([^A-Z])|([A-Z]+)([^A-Z]+)|([^A-Z])([A-Z])/','$1$6_$2$3$4$5$7', $str);
		$result = preg_replace('/([^_A-Z])([A-Z])/','$1_$2', $result);
		$result = strtolower($result);
		
		if (!preg_match('/^[A-Z][A-Z]/', $str) && !preg_match('/^[a-z0-9]/', $str))
			$result = substr($result, 1);
		
		return $result;
	}
	
	/**
	 * ローカライズ
	 * @param string $key キー名
	 * @param array $params 置換文字のリスト
	 * @return ローカライズされた文字列
	 */
	final public static function getLocalizedString($key, array $params = array(), $lang = null)
	{
		if (!isset($GLOBALS['radium_locale'])) {
			
			if (!defined('DEFAULT_LANG')) {
				$headers = getallheaders();
				if (isset($headers['Accept-Language'])) {
					$lang = $headers['Accept-Language'];
					$lang = preg_replace('/-([a-z]+)$/e', '"_" . strtoupper("\\1")', $lang);
					define('DEFAULT_LANG', $lang);
				}
			}
			
			$radium_locale = array();
			
			$i18nDir = RADIUM_APP_PATH . '/i18n';
			if (is_dir($i18nDir)) {
				if ($dh = opendir($i18nDir)) {
					while (($file = readdir($dh)) !== false) {
						if (preg_match('/\\.php$/', $file)) {
							include($i18nDir . '/' . $file);
						}
					}
					closedir($dh);
				}
			}
			
			$GLOBALS['radium_locale'] = $radium_locale;
		}
		
		if (is_null($lang)) $lang = DEFAULT_LANG;
		$result = $key;
		
		$radium_locale = $GLOBALS['radium_locale'];
		if (isset($radium_locale[$lang][$key])) {
			$result = $radium_locale[$lang][$key];
		}
		else if (isset($radium_locale['en_US'][$key])) {
			$result = $radium_locale['en_US'][$key];
		}
		
		if (is_array($params)) {
			$n = count($params);
			for ($i = 0; $i < $n; $i++) {
				$result = str_replace('{' . ($i + 1) . '}', $params[$i], $result);
			}
		}
		
		return $result;
	}
}
