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
				ああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ</span><br>
			<span class="yello">○○さん(✩5)</span>より
		</div>
		<div class="box2"><span class="white">使ってくれて<br>ありがとうだっぴー</span></div>
		<?php
			echo('<img alt="" src="'.get_stylesheet_directory_uri().'/images/blackboard.bmp" width="100%"/>');
		?>
	</div>

	<br>
	<hr class="hr-posts-row">

	<div id="top_image_box">
	<div class="phone_sub_title">こんな本が出品されてます！</div>
	<div id="top_image"></div>
	</div>

	<div id="app_logo">
		<div class="textwidget">スマホアプリはこちらから<br>
			<a href="https://itunes.apple.com/jp/app/tekusuchenji/id913755762?mt=8&uo=4"
				 target="itunes_store"
				 style="display:inline-block;overflow:hidden;background:url(https://linkmaker.itunes.apple.com/htmlResources/assets/ja_jp//images/web/linkmaker/badge_appstore-lrg.png)
				 no-repeat;width:135px;height:40px;
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

	<hr class="hr-posts-row">
	<div id="icon">
		<div class="phone_sub_title">何をしますか？</div>
		<?php
			echo('<img alt="" src="'.get_stylesheet_directory_uri().'/images/mypage.png" width="100%"/>');
			echo('<img alt="" src="'.get_stylesheet_directory_uri().'/images/icon1.png" width="49%"/>');
			echo('<img alt="" src="'.get_stylesheet_directory_uri().'/images/icon2.png" width="49%"/>');
			echo('<img alt="" src="'.get_stylesheet_directory_uri().'/images/icon3.png" width="49%"/>');
			echo('<img alt="" src="'.get_stylesheet_directory_uri().'/images/icon4.png" width="49%"/>');
		?>
	</div><!-- #icon -->

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
