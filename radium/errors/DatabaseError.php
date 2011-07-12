<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2011, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace radium\errors;

use \ErrorException;

/**
 * データベースのエラー
 */
final class DatabaseError extends ErrorException
{
	public function __construct($message = null, $code = 0)
	{
		parent::__construct($message, $code);
	}
}
