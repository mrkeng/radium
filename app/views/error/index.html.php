<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2011, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

use \radium\utils\StringUtil;

$this->title(StringUtil::getLocalizedString('Error'));

?>
<section>
	<h2><?php echo $this->title(); ?></h2>
	<p><?php echo $exception->getMessage(); ?></p>
</section>
