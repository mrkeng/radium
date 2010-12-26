<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

$this->title('エラー');

?>
<section>
	<h2>エラーが発生しました</h2>
	<p><?php echo $exception->getMessage(); ?></p>
</section>
