<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\errors;

use \ErrorException;

/**
 * データが見つからないエラー
 */
final class NotFoundError extends ErrorException
{
	public function __construct($message = null, $code = 404)
	{
		parent::__construct($message, $code);
	}
}
