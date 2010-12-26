<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\controllers;

use \Exception;
use \radium\utils\StringUtil;

class ErrorController extends AbstractController
{
	public function index(Exception $exception)
	{
		// データベースダウン
		if (strpos($exception->getMessage(), 'mongodb') !== false)
		{
			return StringUtil::getLocalizedString('Sorry, Database is down...');
		}
		
		return compact('exception');
	}
}
