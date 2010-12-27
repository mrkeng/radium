<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

use \radium\utils\StringUtil;

/**
 * ローカライズ
 * @param string $key キー名
 * @param array $params 置換文字のリスト
 * @return ローカライズされた文字列
 */
function ll($key, array $params = array(), $lang = null)
{
	return StringUtil::getLocalizedString($key, $params, $lang);
}
