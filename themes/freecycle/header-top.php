<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<?php if ( current_theme_supports( 'bp-default-responsive' ) ) : ?><meta name="viewport" content="width=device-width, initial-scale=1.0" /><?php endif; ?>
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<link href="<?php echo home_url(); ?>/wp-content/themes/freecycle/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen"><?php bp_head(); ?>
		<link href="<?php echo home_url(); ?>/wp-content/themes/freecycle/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
		<!-- jQuery library -->
		<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
		<!-- bxSlider Javascript file -->
		<script src="wp-content/themes/freecycle/js/jquery.bxslider.min.js"></script>
		<!-- bxSlider CSS file -->
		<link href="wp-content/themes/freecycle/style/jquery.bxslider.css" rel="stylesheet" />
		<?php include_once "js/freecycleJS.php" ?>
		<style type="text/css">
		.bx-custom-pager{bottom: -50px !important;}
		.bx-custom-pager .bx-pager-item{width: 33%}
		.bx-pager-item .active img{opacity: 0.1}
		</style>
		<?php bp_head(); ?>
		<?php wp_head(); ?>
		<?php include_once "wp-content/themes/freecycle/js/freecycleJS.php" ?>
		<?php
		wp_register_script(
			'flexSliderScript',
			get_stylesheet_directory_uri() . '/js/jquery.flexslider-min.js'
		);
		wp_register_script(
			'tooltipsterScript',
			get_stylesheet_directory_uri() . '/js/tooltipster-master/js/jquery.tooltipster.min.js'
		);
		wp_register_style(
			'flexSliderStyle',
			get_stylesheet_directory_uri() . '/style/flexslider.css'
		);
		wp_register_style(
			'tooltipsterStyle',
			get_stylesheet_directory_uri() . '/js/tooltipster-master/css/tooltipster.css'
		);
		wp_register_style(
			'tooltipsterStyle-noir',
			get_stylesheet_directory_uri() . '/js/tooltipster-master/css/themes/tooltipster-noir.css'
		);	
		wp_enqueue_script('flexSliderScript');
		wp_enqueue_style('flexSliderStyle');
		wp_enqueue_script('tooltipsterScript');
		wp_enqueue_style('tooltipsterStyle');
		?>
	</head>

	<body <?php body_class(); ?> id="top_page">
		<?php do_action( 'bp_before_header' ); ?>

		<div id="header_menu_top">
			<a id="logo_top" href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/texchange_header_b_logosize_2.png" alt="ヘッダー"></a>
		</div><!-- header -->

