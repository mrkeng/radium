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

class ErrorController extends \radium\action\Controller
{
	public function index($exception)
	{
		// データベースダウン
		if (strpos($exception->getMessage(), 'mongodb') !== false) {
			return ll('Sorry, Database is down...');
		}
		
		$title = ll('Error');
		$errorMessage = ll('Sorry, Internal Server Error.');
		
		if ($exception instanceof NotFoundError) {
			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not Found");
			
			$title = ll('Not Found.');
			$errorMessage = ll('Not Found.');
		}
		
		if ($exception instanceof UserError) {
			$errorMessage = $exception->getMessage();
		} else if (defined('DEBUG') && DEBUG) {
			$errorMessage .= ' (' . $exception->getMessage() . ')';
		}
		
		return compact('exception', 'title', 'errorMessage');
	}
}
