<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

$this->title('Show');

?>
<section>
	<h2><?php echo $this->title(); ?></h2>
	<article><?php echo $this->html->escape($str); ?></article>
</section>
