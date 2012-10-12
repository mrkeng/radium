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
	<li class="active">Routes Test 1</li>
</ul>
<section>
	<h2><?php ee(ll('Show')); ?></h2>
	<article><?php echo $this->html->escape($str); ?></article>
</section>
