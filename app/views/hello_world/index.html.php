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
	<li class="active"><?php ee(ll('Hello World!')); ?></li>
</ul>
<section>
	<h2><?php ee(ll('Hello World!')); ?></h2>
	
	<?php for ($i = 0; $i < 100; $i++): ?>
	<p><?php ee(ll('Hello World!')); ?></p>
	<?php endfor; ?>
</section>
