<?php
/**
 * radium: the most RAD PHP Framework
 *
 * @copyright Copyright 2012, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

use \radium\storage\Session;

$uri = $this->request->uri;
list($lang, $country) = explode('_', Session::read('lang'));

$title = $this->title();
if (!$title) {
	$title = $this->title('radium PHP Framework');
}


?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" 
	xmlns:og="http://ogp.me/ns#" 
	xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<?php echo $this->html->charset();?>

<title><?php echo $this->title(); ?></title>
<?php echo $this->ogp(); ?>

<link rel="stylesheet" href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.1.0/css/bootstrap-combined.min.css" />
<script type="text/javascript" src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.1.0/js/bootstrap.min.js"></script>
<!--[if IE]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<?php echo $this->html->style(array('styles.css')); ?>

<?php echo $this->styles(); ?>

<?php echo $this->html->script(array('common.js')); ?>
<?php echo $this->scripts(); ?>

<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>

</head>
<body>
<header class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<?php echo $this->html->link($this->title(), '', array('class' => 'brand')); ?>
		</div>
	</div>
</header>

<div id="content-wrapper">
	<div id="content" class="container">
<?php echo $this->content(); ?>

	</div>
</div>

<footer class="navbar navbar-fixed-bottom">
	<div class="navbar-inner">
		<div class="container">
			<p><span class="copyright">&copy; 2012 <a href="http://www.playwell.co.jp/">Playwell Inc.</a></span>
				<span class="lang-selector">
					<?php if ($lang == 'ja'): ?>日本語<?php else: ?><?php echo $this->html->link('日本語', $uri . '?lang=ja'); ?><?php endif; ?> | 
					<?php if ($lang == 'en'): ?>English<?php else: ?><?php echo $this->html->link('English', $uri . '?lang=en'); ?><?php endif; ?>
				</span>
			</p>
		</div>
	</div>
</footer>
</body>
</html>