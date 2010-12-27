<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

$uri = $this->request->uri;
$lang = \radium\storage\Session::read('lang');
ll('');
list($htmlLang, $country) = explode('_', DEFAULT_LANG);

?><!DOCTYPE html>
<html>
<head>
<?php echo $this->html->charset();?>

<title><?php echo $this->title(); ?></title>
<?php echo $this->html->script(array('libs/jquery-1.4.4.min')); ?>

<!--[if IE]>
<?php echo $this->html->script(array('libs/html5')); ?>

<![endif]-->
<!--[if IE 6]>
<?php echo $this->html->script(array('libs/DD_belatedPNG_0.0.8a-min', 'ie6')); ?>

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
	<p class="lang-selector"><?php echo ll('Languages'); ?>: 
		<?php if ($lang == 'en'): ?>English<?php else: ?><?php echo $this->html->link('English', $uri . '?lang=en'); ?><?php endif; ?>,
		<?php if ($lang == 'ja'): ?>日本語 (Japanese)<?php else: ?><?php echo $this->html->link('日本語 (Japanese)', $uri . '?lang=ja'); ?><?php endif; ?>,
		<?php if (!$lang): ?><?php echo ll('Default Language of Web Browser'); ?><?php else: ?><?php echo $this->html->link(ll('Default Language of Web Browser'), $uri . '?lang=default'); ?><?php endif; ?></p>
	<p id="copyright">&copy; 2010 <a href="http://www.playwell.co.jp/">Playwell Inc.</a></p>
</footer>
</body>
</html>