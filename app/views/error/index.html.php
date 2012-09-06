<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

use \radium\utils\StringUtil;

$this->title(StringUtil::getLocalizedString('Error'));

?>
<section>
	<p><?php ee($errorMessage); ?></p>
</section>
