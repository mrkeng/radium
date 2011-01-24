<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\controllers;

use \radium\errors\NotFoundError;
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
		
		return compact('exception');
	}
}
