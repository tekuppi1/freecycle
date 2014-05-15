

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<?php if ( current_theme_supports( 'bp-default-responsive' ) ) : ?><meta name="viewport" content="width=device-width, initial-scale=1.0" /><?php endif; ?>
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php bp_head(); ?>
		<?php wp_head(); ?>
		<?php include_once "js/freecycleJS.php" ?>
		<?php
		wp_register_script(
			'flexSliderScript',
			get_stylesheet_directory_uri() . '/js/jquery.flexslider-min.js'
		);
		wp_register_style(
			'flexSliderStyle',
			get_stylesheet_directory_uri() . '/style/flexslider.css'
		);

		wp_enqueue_script('flexSliderScript');
		wp_enqueue_style('flexSliderStyle');
		?>
	<script>
	jQuery(document).ready(function(){
		jQuery("#header_copy").flexslider({
			animation: "fade",
			controlNav: false,
			directionNav: false,
			prevText: "",
			nextText: "",
			auto: true,
			intval: 5,
		});
	});
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
				<?php } ?>
			</div>
		</header><!-- header -->
		<div class="grobal_nav_div_sp">
				<nav>
					<ul class="navi" >
					　		<?php if(is_user_logged_in()){ ?>
					　		<li class="grobal_nav_blue_navi"><a href="<?php echo bp_loggedin_user_domain(); ?>" >マイページ</a></li>
					　		<li class="grobal_nav_important_navi" ><a href="<?php echo bp_loggedin_user_domain(); ?>new_entry/normal/" >新規出品</a></li>
					　		<?php }else{ ?>
					　		<li class="grobal_nav"><a href="http://texchg.com/how-to-use">How to use</a></li>
					　		<li class="grobal_nav"><a href="http://texchg.com/review">利用者の声</a></li>
							<?php } ?>
					　		<li class="grobal_nav_important_navi"><a href="<?php echo home_url(); ?>/wanted-list" >ほしいものリスト</a></li>
							<!-- <li class="grobal_nav"><a href="http://texchg.com/manage" >運営メンバー紹介</a></li> -->
					　		<li class="grobal_nav_blue_navi"><a href="<?php echo home_url() . "/faq"; ?>" >FAQ</a></li>			
					</ul>
				</nav>
		</div>

		
<div class="header_img_navi">

				<div class="header_img_navi_contents">
					<?php if(is_user_logged_in()) { 
								 if(messages_get_unread_count() > 0){ 
					?>
							<a class="unread_alert" href="<?php echo bp_loggedin_user_domain() . "messages"; ?>">未読メッセージが<?php echo messages_get_unread_count();?>件あります！</a>
					<?php 		} 
						} ?> 
				</div>
				
				<?php if(!is_user_logged_in()){ ?>
				<div id="header_copy">
					<ul class="slides">
						<li>
							<img src="<?php echo get_stylesheet_directory_uri() ?>/images/slide_4.jpg" alt="slide_4" width="640px" >
						</li>
						<li>
							<img src="<?php echo get_stylesheet_directory_uri() ?>/images/slide_1.jpg" alt="slide_1" width="640px" >
						</li>
						<li>
							<img src="<?php echo get_stylesheet_directory_uri() ?>/images/slide_2.jpg" alt="slide_2" width="640px">
						</li>
						<li>
							<img src="<?php echo get_stylesheet_directory_uri() ?>/images/slide_3.jpg" alt="slide_3" width="640px">
						</li>
					</ul>
				</div>
				<div id="entry_login_form_pc">
					<a href="<?php echo home_url(); ?>/register" class="entry_buttons" id="entry_form">新規登録</a>
					<a href="<?php echo home_url(); ?>/login" class="entry_buttons" id="login_form" >ログイン</a>
					<?php social_login_button(); ?>
					<a href="<?php echo home_url() . "/about"; ?>"  class="entry_buttons" id="detail_texchange">→てくすちぇんじってどんなサービス？</a>
				</div>
				<?php } ?>
				
				<div class="grobal_nav_div">
					<ul class="navi" >
					　		<?php if(is_user_logged_in()){ ?>
					　		<li class="grobal_nav blue_navi"><a href="<?php echo bp_loggedin_user_domain(); ?>" >マイページ</a></li>
					　		<li class="grobal_nav important_navi" ><a href="<?php echo bp_loggedin_user_domain(); ?>new_entry/normal/" >新規出品</a></li>
					　		<?php }else{ ?>
					　		<li class="grobal_nav blue_navi"><a href="http://texchg.com/how-to-use">How to use</a></li>
					　		<li class="grobal_nav blue_navi"><a href="http://texchg.com/review">利用者の声</a></li>
							<?php } ?>
					　		<li class="grobal_nav important_navi" ><a href="<?php echo home_url(); ?>/wanted-list" >ほしいものリスト</a></li>
					　		<li class="grobal_nav blue_navi"><a href="http://texchg.com/manage" >運営メンバー紹介</a></li>
					　		<li class="grobal_nav blue_navi"><a href="<?php echo home_url() . "/faq"; ?>" >FAQ</a></li>
					</ul>
				</div><!-- .grobal_nav_img -->
</div><!-- .header_img_navi -->

<?php if(!is_user_logged_in()){ ?>
<div id="entry_login_form">
	<a href="<?php echo home_url(); ?>/register" class="entry_buttons" id="entry_form">新規登録</a>
	<a href="<?php echo home_url(); ?>/login" class="entry_buttons" id="login_form" >ログイン</a>
	<?php social_login_button(); ?>
	<a href="<?php echo home_url() . "/about"; ?>"  class="entry_buttons" id="detail_texchange">→てくすちぇんじってどんなサービス？</a>
</div>
<?php } ?>

	

<div class="header_form">
	<!-- <?php if(is_user_logged_in()){ ?>
     <div class="header_entry_button_div">
     <a href="<?php echo bp_loggedin_user_domain(); ?>new_entry/normal/"  id="header_entry_button">いますぐ出品</a>
     </div>
<?php } ?> -->

		<?php if(bp_is_front_page() || is_archive() || is_search() || is_single()){ ?>
	<div id="search-23" class="widget widget_search"><!-- 検索バー -->
				<form role="search" method="get" id="searchform_main" action="<?php echo home_url(); ?>">
						<div id="searchform_text"> 
					  			<input type="text" id="searchtext" name="s" id="s" value="<?php echo $_GET['s'];?>"/>
					  	</div>
							<div id="searchform_pulldown">
								<select name="seachform_itemstatus">
									<option value="all">すべて</option>
									<option value="givemeable">ください可能</option>
								</select>
							</div>
							<div id="searchform_submit">
								<input type="submit" id="searchsubmit" value="検索" />
							</div> 
						</div> 
				</form>
	</div><!-- 検索バー -->
</div><!-- header_form -->
		<!--<hr class="line-search"> -->
		<?php } ?>
		<div id="container">
