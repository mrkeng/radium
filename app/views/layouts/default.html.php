<?php
/**
 * radium: the most RAD php framework
 *
 * @copyright Copyright 2010, Playwell Inc.
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */
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
	<p id="copyright">&copy; 2010 Playwell Inc.</p>
</footer>
</body>
</html>