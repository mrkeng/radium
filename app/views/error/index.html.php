<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

?>
<ul class="breadcrumb">
	<li><?php echo $this->html->link(ll('Home'), ''); ?> <span class="divider">/</span></li>
	<li class="active"><?php ee(ll('Error')); ?></li>
</ul>
<section>
	<h2><?php ee($title); ?></h2>
	<p><?php ee($errorMessage); ?></p>
</section>
