<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\controllers;

use \radium\errors\NotFoundError;
use \radium\errors\UserError;
use \radium\utils\StringUtil;

class ErrorController extends \radium\action\Controller
{
	public function index($exception)
	{
		// データベースダウン
		if (strpos($exception->getMessage(), 'mongodb') !== false) {
			return StringUtil::getLocalizedString('Sorry, Database is down...');
		}
		
		if ($exception instanceof NotFoundError) {
			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not Found");
		}
		
		$errorMessage = StringUtil::getLocalizedString('Sorry, Internal Server Error.');
		
		if ($exception instanceof UserError) {
			$errorMessage = $exception->getMessage();
		} else if (defined('DEBUG') && DEBUG) {
			$errorMessage .= ' (' . $exception->getMessage() . ')';
		}
		
		return compact('exception', 'errorMessage');
	}
}
