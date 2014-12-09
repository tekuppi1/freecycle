<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<?php if ( current_theme_supports( 'bp-default-responsive' ) ) : ?><meta name="viewport" content="width=device-width, initial-scale=1.0" /><?php endif; ?>
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<link href="<?php echo home_url(); ?>wp-content/themes/freecycle/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">		<?php bp_head(); ?>
		<?php wp_head(); ?>
		<?php include_once "js/freecycleJS.php" ?>
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

		<div id="header_top">
			<div id="header_menu_top">
				<a id="logo_top" href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/texchange_header_b_logosize_2.png" alt="ヘッダー"></a>
								
				<a href="javascript:onClickMenuIcon();"  id="menu_icon_sp" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"><img  id="menu_icon" src="<?php echo get_stylesheet_directory_uri() ?>/images/menu_icon.png" alt="ヘッダー" width="50px" height="50px"></a>
				<a href="<?php echo home_url(); ?>/login"  id="user_icon_sp" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"><img  id="user_icon" src="<?php echo get_stylesheet_directory_uri() ?>/images/user_icon.png" alt="ヘッダー"></a>
			</div>
		</div><!-- header -->