<?php
/**
 * @package WordPress
 * @subpackage HTML5_Boilerplate
 */
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
  <meta charset="utf-8">

  <!-- www.phpied.com/conditional-comments-block-downloads/ -->
  <!--[if IE]><![endif]-->

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <!-- Does not currently validate. Known issue with the Boilerplate. -->

  <title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!--  Mobile Viewport Fix
        j.mp/mobileviewport & davidbcalhoun.com/2010/viewport-metatag
  device-width : Occupy full width of the screen in its current orientation
  initial-scale = 1.0 retains dimensions instead of zooming out if page height > device height
  maximum-scale = 1.0 retains dimensions instead of zooming in if page width < device width
  -->
  <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">

  <!-- Place favicon.ico and apple-touch-icon.png in the root of your domain and delete these references -->
  <link rel="shortcut icon" href="/favicon.ico">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">

 <?php wp_enqueue_script('jquery'); ?>

  <!-- CSS : implied media="all" -->
  <?php versioned_stylesheet($GLOBALS["TEMPLATE_RELATIVE_URL"]."html5-boilerplate/css/style.css") ?>
  
   <!-- CSS : responsive stylesheet -->
  <?php versioned_stylesheet($GLOBALS["TEMPLATE_RELATIVE_URL"]."html5-boilerplate/css/responsive.css") ?> 
  
  <!-- CSS : template stylesheet -->
  <?php versioned_stylesheet($GLOBALS["TEMPLATE_RELATIVE_URL"]."style.css") ?>

  <!-- For the less-enabled mobile browsers like Opera Mini -->
  <?php versioned_stylesheet($GLOBALS["TEMPLATE_RELATIVE_URL"]."html5-boilerplate/css/handheld.css", 'media="handheld"') ?>

  <!-- All JavaScript at the bottom, except for Modernizr which enables HTML5 elements & feature detects -->
  <?php versioned_javascript($GLOBALS["TEMPLATE_RELATIVE_URL"]."html5-boilerplate/js/modernizr-1.5.min.js") ?>
  
  <link rel="stylesheet" type="text/css" media="all" href="http://www.bytewire.co.uk/wp-content/themes/bytewirev4/assets/css/poweredby.css">

  <!-- Wordpress Head Items -->
  <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

  <?php wp_head(); ?>

<script language="javascript" type="text/javascript" src="<?php bloginfo( 'stylesheet_directory' ); ?>/js/jquery.juice.slideshow.js"></script>
<script language="javascript" type="text/javascript" src="<?php bloginfo( 'stylesheet_directory' ); ?>/js/futube.js"></script>

<script type="text/javascript">
jQuery(function(){jQuery('ul.sites li a').mouseover(function(){jQuery('a',jQuery(this).parent().siblings()).stop(true).animate({width:45});jQuery(this).animate({width:115});}); jQuery( 'ul.social li a' ).mouseover(function() {jQuery( 'a', jQuery( this ).parent().siblings() ).stop( true ).animate( { width: 26 } );jQuery( this ).animate( { width: 85 } );}).mouseout(function() {jQuery( this ).animate( { width: 26 } );});});
</script>   

</head>

<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->

<!--[if lt IE 7 ]> <body <?php body_class('ie6'); ?>> <![endif]-->
<!--[if IE 7 ]>    <body <?php body_class('ie7'); ?>> <![endif]-->
<!--[if IE 8 ]>    <body <?php body_class('ie8'); ?>> <![endif]-->
<!--[if IE 9 ]>    <body <?php body_class('ie9'); ?>> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <body <?php body_class('ie6'); ?>> <!--<![endif]-->

  <div id="wrapper">
    <header id="header" class="<?php if(is_page_template('tpl-homepage.php')): 
    		echo 'header'; 
    		else: echo 'header-alt'; 
    		endif; ?>" role="banner">
    	<div class="<?php if(is_page_template('tpl-homepage.php')): 
    		echo 'header_bg'; 
    		else: echo 'header_bg_reg_page'; 
    		endif; ?>">
    		<div class="container rel">
    			<div id="col-1" class="column five">
    				<a href="<?php bloginfo('url'); ?>" class="logo"></a>
    			</div>
    			<div id="col-2" class="column seven">
		    		<nav id="primary-nav">
		    			<?php wp_nav_menu( array(
								'menu' => 'Primary', 
								'container' => '',
								'menu_class' => 'primary_nav', 
								'before' => '',
								'after'=>'')
							); ?>
		    		</nav>
	    		</div>	
    		</div>
    	</div> 	
    </header>
	
	<section id="content" class="container">
	