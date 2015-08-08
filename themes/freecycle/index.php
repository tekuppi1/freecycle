<?php
	if(!is_user_logged_in()){
		header('Location:' . home_url() . '/top-page.php');
	}
?>

<?php get_header(); ?>
<?php
	$xml_setting = get_stylesheet_directory_uri()."/xml/setting.xml";
	$xmlData_setting = simplexml_load_file($xml_setting);
	$setting = $xmlData_setting->obj->item[0];
	if(($setting->event)=="true"){
		$xml_massage = get_stylesheet_directory_uri()."/xml/event.xml";
		$texbanTitle = $setting->event_title;
	}else{
		$xml_massage = get_stylesheet_directory_uri()."/xml/message.xml";
		$texbanTitle = $setting->message_title;
	}
	$xml_texp = get_stylesheet_directory_uri()."/xml/texp.xml";
	$xmlData_massage = simplexml_load_file($xml_massage);//xmlを読み込む
	$xmlData_texp = simplexml_load_file($xml_texp);
?>
	<div class="BlackBoard" style="position: relative;">
		<div class="title"><?php echo($texbanTitle); ?></div>
		<div class="box1">
			<?php 
				$num = mt_rand(0,$xmlData_massage->obj->item->count()-1);
				$node = $xmlData_massage->obj->item[$num];
				echo("<span class=\"yellow\">".$node->from."さん");
				if(($node->fromrank)!="null")echo("(✩".$node->fromrank.")");
				echo("</span>から　");
				echo("<span class=\"yellow\">".$node->to."さん");
				if(($node->torank)!="null")echo("(✩".$node->torank.")");
				echo("</span>へ<br>");
				if(($node->title)!="null")echo("<span class=\"pink\">「".$node->title."」</span><br>");
				if(($node->text)!="null")echo("<span class=\"comment\">".$node->text."</span><br>");
			?>
		</div>
		<?php $num = mt_rand(0,$xmlData_texp->obj->item->count()-1);
			if(($setting->event)=="true")
				echo("<div class=\"box2\"><a href=\"$setting->event_url\" class='event_button'>$setting->texp</a></div>");
			else
				echo("<div class=\"box2\"><span class=\"white\">".$xmlData_texp->obj->item[$num]."</span></div>");

			if(($setting->event)=="true")
				echo('<img alt="" src="'.get_stylesheet_directory_uri().'/images/texban_none.png" width="100%"/>');
			else
				echo('<img alt="" src="'.get_stylesheet_directory_uri().'/images/texban.png" width="100%"/>');
		?>
	</div>

	<br>

	<div id="app_logo">
		<div>スマホアプリはこちらから<br>
			<a href="https://itunes.apple.com/jp/app/tekusuchenji/id913755762?mt=8&uo=4"
				 target="itunes_store"
				 style="display:inline-block;overflow:hidden;background:url(https://linkmaker.itunes.apple.com/htmlResources/assets/ja_jp//images/web/linkmaker/badge_appstore-lrg.png)
				 no-repeat;width:135px;height:45px;
				 @media only screen{background-image:url(https://linkmaker.itunes.apple.com/htmlResources/assets/ja_jp//images/web/linkmaker/badge_appstore-lrg.svg);}"
			></a>
			<a href="https://play.google.com/store/apps/details?id=com.texchg">
				<img 
					alt="Android app on Google Play"
					src="https://developer.android.com/images/brand/ja_app_rgb_wo_45.png" 
				/>
			</a>
		</div>
	</div>

	<div id="icon">
		<?php
			/******/
			global $user_ID;
			echo('<a href="'.bp_loggedin_user_domain().'">' );
			if(get_todo_list_count($user_ID) > 0)
				echo('<img alt="" src="'.get_stylesheet_directory_uri().'/images/mypage_.png" width="100%"/>');
			else
				echo('<img src="'.get_stylesheet_directory_uri().'/images/mypage.png" width="100%"/>');
			echo('</a>');
			/******/
			echo('<a href="'.bp_loggedin_user_domain().'new_entry/normal/">' );
			echo('<img src="'.get_stylesheet_directory_uri().'/images/icon1.png" width="50%"/></a>');
			/******/
			echo('<a href="'.bp_loggedin_user_domain().'messages/">' );
			if(messages_get_unread_count() > 0)
				echo('<img alt="" src="'.get_stylesheet_directory_uri().'/images/icon2_.png" width="50%"/>');
			else
				echo('<img alt="" src="'.get_stylesheet_directory_uri().'/images/icon2.png" width="50%"/>');
			echo('</a>');
			/******/
			echo('<a href='.home_url().'/howtouse><img alt="" src="'.get_stylesheet_directory_uri().'/images/icon3.png" width="50%"/></a>');
			/******/
			echo('<a href='.home_url().'/search-page><img alt="" src="'.get_stylesheet_directory_uri().'/images/icon4.png" width="50%"/></a>');
		?>
	</div><!-- #icon -->

	<hr class="hr-posts-row">
	<div id="top_image_box" class="image_box" style="background-image: url(<?php echo(get_stylesheet_directory_uri().'/images/booklack.bmp'); ?>); ">
	<div class="phone_sub_title">こんな本が出品されてます！</div>
	<div id="top_image"></div>
	</div>
	
	<div class="index_profile">
		<?php bp_loggedin_user_avatar( 'type=thumb&width=30&height=30' ); ?>
		<span class="user-nicename"><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></span>
			<a class="button logout" href="<?php echo wp_logout_url( wp_guess_url() ); ?>"><?php _e( 'Log Out', 'buddypress' ); ?></a>
	</div>
	

<!-- SlideImageScript -->
		<!-- jQuery library -->
		<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="wp-content/themes/freecycle/js/jquery.bxslider.min.js"></script>
		<link href="wp-content/themes/freecycle/style/jquery.bxslider.css" rel="stylesheet" />
		<script src="wp-content/themes/freecycle/js/owl-carousel/owl.carousel.js"></script>
		<link rel="stylesheet" href="wp-content/themes/freecycle/js/owl-carousel/owl.carousel.css">
		<link rel="stylesheet" href="wp-content/themes/freecycle/js/owl-carousel/owl.theme.css">
	<script>
		jQuery(function() {
			$('#top_slide').bxSlider({
				auto:true,
				pause: 6000,
				speed: 1000,
				captions: true,
				infiniteLoop: false,
				hideControlOnEnd: true,
				autoDelay: 3000,
				controls: true
			});
			displayImages();
		});
	</script>
<!-- SlideImageScript -->
<?php get_footer(); ?>
