<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

$this->title(ll('Hello World!'));

?>
<p><?php echo $this->html->link('< ' . ll('Back'), ''); ?></p>
<section>
	<h2><?php echo $this->title(); ?></h2>
</section>
