<?php
global $current_user;
get_currentuserinfo();

// DELETE TEMPORARILY
// because of unable to login from smartphone apps
if(!is_user_logged_in() || $current_user->user_level != ADMIN_LEVEL){
	header('Location:' . home_url() . '/renewal.php');
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

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

	<body <?php body_class(); ?> id="bp-default">
		<?php do_action( 'bp_before_header' ); ?>

		<header>
			<div id="header_menu">
				<a id="logo" href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/texchange_header_b_logosize_2.png" alt="ヘッダー" width="100px" height="50px"></a>

				<a href="javascript:onClickMenuIcon();"  id="menu_icon_sp" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"><img  id="menu_icon" src="<?php echo get_stylesheet_directory_uri() ?>/images/menu_icon.png" alt="ヘッダー" width="50px" height="50px"></a>
				<?php if(!is_user_logged_in()){ ?>
				<a href="<?php echo home_url(); ?>/login"  id="user_icon_sp" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"><img  id="user_icon" src="<?php echo get_stylesheet_directory_uri() ?>/images/user_icon.png" alt="ヘッダー" width="50px" height="50px"></a>
				<?php }else{ ?>
				<a href="<?php echo home_url(); ?>" id="home_icon_sp"><img id="home_icon" src="<?php echo get_stylesheet_directory_uri() ?>/images/home_icon.png" alt="ホーム" width="100%" height="100%"></a>
				<?php } ?>
			</div>
		</header><!-- header -->

		<div class="grobal_nav_div_sp">
				<nav>
					<ul class="navi" >
					　		<?php if(is_user_logged_in()){ ?>
					　		<li class="grobal_nav_important_navi"><a href="<?php echo bp_loggedin_user_domain(); ?>" >マイページ</a></li>
					　		<!--li class="grobal_nav" ><a href="<?php echo bp_loggedin_user_domain(); ?>new_entry/normal/" >新規出品</a></li-->
					　		<li class="grobal_nav"><a href="<?php echo bp_loggedin_user_domain(); ?>messages" >メッセージ</a></li>
					　		<?php }else{ ?>
					　		<li class="grobal_nav"><a href="http://texchg.com/how-to-use">How to use</a></li>
					　		<li class="grobal_nav"><a href="http://texchg.com/review">利用者の声</a></li>
							<?php } ?>
							<li class="grobal_nav" ><a href="<?php echo home_url(); ?>/all-items/1" >商品一覧</a></li>
							<li class="grobal_nav" ><a href="<?php echo home_url(); ?>/search-page" >検索</a></li>
					　		<li class="grobal_nav"><a href="<?php echo home_url(); ?>/howtouse">ヘルプ</a></li>
					</ul>
				</nav>
		</div>


<div class="header_img_navi">
	<?php
   get_search_form();
?>

				<div class="header_img_navi_contents">
					<?php if(get_option('use-topics') === 'on' && is_home()){ ?>
						<b><p><a class="unread_alert" href="<?php echo get_option('topics-link') ?>"><?php echo get_option('topics-text') ?></a></p></b>
					<?php } ?>
					<?php if(is_user_logged_in()) {
								 if(messages_get_unread_count() > 0){
					?>
							<a class="unread_alert" href="<?php echo bp_loggedin_user_domain() . "messages"; ?>">未読メッセージが<?php echo messages_get_unread_count();?>件あります！</a></br>
					<?php 		}
								global $user_ID;
								if(get_todo_list_count($user_ID)){
					?>
							<a id="nextaction_alert" class="unread_alert" href="<?php echo bp_loggedin_user_domain(); ?>">next actionが<?php echo get_todo_list_count($user_ID);?>件あります！</a>
					<?php
								}
						} ?>

				</div>

				<?php if(!is_user_logged_in()){ ?>
				<div id="header_copy">
					<img src="<?php echo get_stylesheet_directory_uri() ?>/images/slide_1.jpg" width="640px">
				</div>
				<div id="entry_login_form_pc">
					<a href="<?php echo home_url(); ?>/register#signup_form" class="entry_buttons" id="entry_form">新規登録</a>
					<a href="<?php echo home_url(); ?>/login#blog-page" class="entry_buttons" id="login_form" >ログイン</a>
					<?php social_login_button(); ?>
					<a href="<?php echo home_url() . "/about"; ?>"  class="entry_buttons" id="detail_texchange">→てくすちぇんじってどんなサービス？</a>
				</div>
				<?php } ?>

				<div class="grobal_nav_div">
					<ul class="navi" >
					　		<?php if(is_user_logged_in()){ ?>
					　		<li class="grobal_nav important_navi"><a href="<?php echo bp_loggedin_user_domain(); ?>"
							<?php
								global $user_ID;
								$todo_list_count = get_todo_list_count($user_ID);
								if($todo_list_count){
								echo 'id="header_todo_exist"';
								}
							?>>マイページ<?php
							if($todo_list_count){
									echo "<span>$todo_list_count</span>";
							}
							?>
							</a>
							</li>
					　		<!--li class="grobal_nav blue_navi" ><a href="<?php echo bp_loggedin_user_domain(); ?>new_entry/normal/" >新規出品</a></li-->
						 	<li class="grobal_nav blue_navi"><a href="<?php echo bp_loggedin_user_domain(); ?>messages">メッセージ</a></li>
					　		<?php }else{ ?>
					　		<li class="grobal_nav blue_navi"><a href="http://texchg.com/how-to-use">How to use</a></li>
					　		<li class="grobal_nav blue_navi"><a href="http://texchg.com/review">利用者の声</a></li>
							<?php } ?>
					　		<li class="grobal_nav blue_navi" ><a href="<?php echo home_url(); ?>/all-items/1" >商品一覧</a></li>
					　		<li class="grobal_nav blue_navi" ><a href="<?php echo home_url(); ?>/search-page" >検索</a></li>
					　		<li class="grobal_nav blue_navi"><a href="<?php echo home_url(); ?>/howtouse">ヘルプ</a></li>
					</ul>
				</div><!-- .grobal_nav_img -->
</div><!-- .header_img_navi -->

<div class="header_form">
		<?php if(is_archive() || is_search() || is_single()){ ?>
		<?php } ?>
		<div id="container">
