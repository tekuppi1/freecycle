<?php
wp_register_script(
	'freecycleScript',
	get_stylesheet_directory_uri() . '/js/freecycle.js',
	false,
	'20131028'
);

wp_enqueue_script('freecycleScript');
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
					　		<li class="grobal_nav"><a href="<?php echo home_url(); ?>" >ホーム</a></li>
					　		<?php if(is_user_logged_in()){ ?>
					　		<li class="grobal_nav"><a href="<?php echo bp_loggedin_user_domain(); ?>" >マイページ</a></li>
					　		<?php } ?>
					　		<li class="grobal_nav"><a href="<?php echo home_url() . "/about"; ?>" >TexChangeとは</a></li>
					　		<li class="grobal_nav"><a href="http://texchg.com/guide" >使い方ガイド</a></li>
					　		<li class="grobal_nav"><a href="http://texchg.com/manage" >運営メンバー紹介</a></li>
					　		<li class="grobal_nav"><a href="http://texchg.com/review" >利用者の声</a></li>
					　		<li class="grobal_nav"><a href="<?php echo home_url() . "/faq"; ?>" >FAQ</a></li>			
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
					<img src="<?php echo get_stylesheet_directory_uri() ?>/images/texchange_header_20140122.png" alt="てくすちぇんじとは？" width="700px" height="200px">
				</div>
				<?php } ?>
				
				<div class="grobal_nav_div">
					<ul class="navi" >
					　		<li class="grobal_nav"><a href="<?php echo home_url(); ?>" >ホーム</a></li>
					　		<?php if(is_user_logged_in()){ ?>
					　		<li class="grobal_nav"><a href="<?php echo bp_loggedin_user_domain(); ?>" >マイページ</a></li>
					　		<?php } ?>
					　		<li class="grobal_nav"><a href="<?php echo home_url() . "/about"; ?>" >TexChangeとは</a></li>
					　		<li class="grobal_nav"><a href="http://texchg.com/guide" >使い方ガイド</a></li>
					　		<li class="grobal_nav"><a href="http://texchg.com/review" >利用者の声</a></li>
					　		<li class="grobal_nav"><a href="http://texchg.com/manage" >運営メンバー紹介</a></li>
					　		<li class="grobal_nav"><a href="<?php echo home_url() . "/faq"; ?>" >FAQ</a></li>
					</ul>
				</div><!-- .grobal_nav_img -->
</div><!-- .header_img_navi -->

					

<div class="header_form_">
		<?php if(bp_is_front_page()){ ?>
	<div id="search-23" class="widget widget_search"><!-- 検索バー -->
				<form role="search" method="get" id="searchform_main" action="<?php echo home_url(); ?>">
					<!-- <label>検索：</label> -->
						<div id="searchform_text">
					  			<input type="text" id="searchtext" value="" name="s" id="s" />
					  	</div>
						<div id="searchform_text_pulldown">
							<div id="searchform_pulldown">
								<select name="categories_seachform">
									<option value="#">すべて</option>
									<option value="#">ください可能</option>
								</select>
							</div>
							<div id="searchform_submit">
								<input type="submit" id="searchsubmit" value="Search" />
							</div> 
						</div> 
				</form>	
	</div><!-- 検索バー -->
	
	<div id="categories-header" class="widget widget_categories">
			<h3 class="categories_h3">カテゴリー検索はこちら</h3>
			<select name='cat' id='cat' class='postform' >
				<option value='-1'>カテゴリーを選択</option>
				<option class="level-0" value="24">実用書&nbsp;&nbsp;(49)</option>
				<option class="level-0" value="23">小説&nbsp;&nbsp;(30)</option>
				<option class="level-0" value="21">就活&nbsp;&nbsp;(13)</option>
				<option class="level-0" value="25">教科書・参考書&nbsp;&nbsp;(119)</option>
				<option class="level-1" value="26">&nbsp;&nbsp;&nbsp;全学教養&nbsp;&nbsp;(5)</option>
				<option class="level-1" value="18">&nbsp;&nbsp;&nbsp;工学部&nbsp;&nbsp;(10)</option>
				<option class="level-1" value="15">&nbsp;&nbsp;&nbsp;情報文化学部（社会システム情報）&nbsp;&nbsp;(1)</option>
				<option class="level-1" value="13">&nbsp;&nbsp;&nbsp;教育学部&nbsp;&nbsp;(1)</option>
				<option class="level-1" value="14">&nbsp;&nbsp;&nbsp;文学部&nbsp;&nbsp;(3)</option>
				<option class="level-1" value="28">&nbsp;&nbsp;&nbsp;法学部&nbsp;&nbsp;(41)</option>
				<option class="level-1" value="12">&nbsp;&nbsp;&nbsp;経済学部&nbsp;&nbsp;(42)</option>
				<option class="level-1" value="27">&nbsp;&nbsp;&nbsp;語学&nbsp;&nbsp;(12)</option>
				<option class="level-0" value="22">漫画&nbsp;&nbsp;(9)</option>
			</select>

<script type='text/javascript'>
/* <![CDATA[ */
	var dropdown = document.getElementById("cat");
	function onCatChange() {
		if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
			location.href = "http://texchg.com/?cat="+dropdown.options[dropdown.selectedIndex].value;
		}
	}
	dropdown.onchange = onCatChange;
/* ]]> */
</script>

	</div><!-- categories-header -->
</div>	
		<!--<hr class="line-search"> -->
		<?php } ?>
		<div id="container">
