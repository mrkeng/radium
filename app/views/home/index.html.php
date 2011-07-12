<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2011, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

$this->title('radium PHP Framework');

?>
<section>
	<h2><?php echo ll('Welcome to radium PHP Framework!'); ?></h2>
	<article>
		<ul>
			<li><?php echo $this->html->link(ll('Hello World'), 'hello_world'); ?></li>
			<li><?php echo $this->html->link('Routes Test 1', 'hello'); ?></li>
			<li><?php echo $this->html->link('Routes Test 2', 's/barfoo'); ?></li>
			<li><?php echo $this->html->link('JSON Render Test', 'hello_world/show_json'); ?></li>
		</ul>
	</article>
</section>
