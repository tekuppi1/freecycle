<?php 
function head_load(){
?>

	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<?php if ( current_theme_supports( 'bp-default-responsive' ) ) : ?><meta name="viewport" content="width=device-width, initial-scale=1.0" /><?php endif; ?>
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php bp_head(); ?>
		<?php wp_head(); ?>
		<?php include_once "js/freecycleJS.php"; ?>
		<?php
		$user_ID = get_current_user_id();
		// if facebook dialog has not be shown before, show it
		if(is_user_connected_with('facebook', $user_ID) && !get_user_meta($user_ID, 'is_fb_share_popup_displayed')){
			// change status
			update_user_meta($user_ID, 'is_fb_share_popup_displayed', 1);
			include_once "js/fcFbDialog.js.php";
		}
		// if twitter dialog has not be shown before, show it
		if(is_user_connected_with('twitter', $user_ID) && !get_user_meta($user_ID, 'is_twitter_popup_displayed')){
			// change status
			update_user_meta($user_ID, 'is_twitter_popup_displayed', 1);
			include_once "js/fcTwitterDialog.js.php";
		}
		// if first login
		if(!get_user_meta($user_ID, "is_first_login_page_displayed")){
			add_todo_first_new_entry($user_ID);
			add_todo_first_giveme($user_ID);
			if(!xprofile_get_field_data('大学名', $user_ID) || !xprofile_get_field_data('学部', $user_ID)){
				add_todo_first_category($user_ID);
			}
			update_user_meta($user_ID, "is_first_login_page_displayed", 1);
		}
		?>
		<?php
		wp_register_script(
			'tooltipsterScript',
			get_stylesheet_directory_uri() . '/js/tooltipster-master/js/jquery.tooltipster.min.js'
		);
		wp_register_style(
			'tooltipsterStyle',
			get_stylesheet_directory_uri() . '/js/tooltipster-master/css/tooltipster.css'
		);
		wp_register_style(
			'tooltipsterStyle-noir',
			get_stylesheet_directory_uri() . '/js/tooltipster-master/css/themes/tooltipster-noir.css'
		);
		wp_register_script(
			'sweetalertScript',
			get_stylesheet_directory_uri() . '/lib/sweetalert-master/lib/sweet-alert.min.js'
		);
		wp_register_style(
			'sweetalertStyle',
			get_stylesheet_directory_uri() . '/lib/sweetalert-master/lib/sweet-alert.css'
		);
		wp_enqueue_script('tooltipsterScript');
		wp_enqueue_style('tooltipsterStyle');
		wp_enqueue_script('sweetalertScript');
		wp_enqueue_style('sweetalertStyle');
		?>
	<script>
	jQuery(document).ready(function(){
		jQuery('.tooltip').tooltipster({theme: "tooltipster-noir"});
	});
	// Google Analytics tracking code
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	ga('create', 'UA-58394320-1', 'auto');
	ga('send', 'pageview');
	
	
	</script>
	</head>

<?php 
}
?>
