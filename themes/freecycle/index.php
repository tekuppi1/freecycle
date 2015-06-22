<?php
	if(!is_user_logged_in()){
		header('Location:' . home_url() . '/top-page.php');
	}
?>

<?php get_header(); ?>
	<div class="BlackBoard" style="position: relative;">
		<div class="box1">
			<span class="yello">○○さん(✩5)</span>へ
			<span class="pink">「本のタイトル」</span><br>
			<span class="">
				あああああああああああああああああああああああああああああああああああああああああああああああああ</span><br>
			<span class="yello">○○さん(✩5)</span>より
		</div>
		<div class="box2"><span class="white">使ってくれて<br>ありがとうだっぴー</span></div>
		<?php
			echo('<img alt="" src="'.get_stylesheet_directory_uri().'/images/blackboard1.bmp" width="100%"/>');
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

	<div id="top_image_box">
	<div class="phone_sub_title">こんな本が出品されてます！</div>
	<div id="top_image"></div>
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
				pause: 6500,
				speed: 700,
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
