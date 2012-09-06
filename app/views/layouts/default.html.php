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

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" 
	xmlns:og="http://ogp.me/ns#" 
	xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<?php echo $this->html->charset();?>

<title><?php echo $this->title(); ?></title>
<?php echo $this->ogp(); ?>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
<!--[if IE]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<?php echo $this->html->style(array('styles')); ?>

<?php echo $this->styles(); ?>

<?php echo $this->scripts(); ?>

<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>

</head>
<body>
<header>
	<h1><?php echo $this->title(); ?></h1>
</header>

<div id="content-wrapper">
	<div id="content">
<?php echo $this->content(); ?>

	</div>
</div>

<footer>
	<p class="lang-selector">
		<?php if ($lang == 'ja'): ?>日本語<?php else: ?><?php echo $this->html->link('日本語', $uri . '?lang=ja'); ?><?php endif; ?> | 
		<?php if ($lang == 'en'): ?>English<?php else: ?><?php echo $this->html->link('English', $uri . '?lang=en'); ?><?php endif; ?>
	<p id="copyright">&copy; 2012 <a href="http://www.playwell.co.jp/">Playwell Inc.</a></p>
</footer>
</body>
</html>