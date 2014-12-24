<?php 
	require_once($_SERVER['DOCUMENT_ROOT'].'/wp/wp-load.php');
  	require_once($_SERVER['DOCUMENT_ROOT'].'/wp/wp-content/plugins/freecycle/functions.php');
?>
<?php get_header("top"); ?>

<div id="top_content">
	<div id="top_background">
		<div id="top_black_background">
			<div id="top_block">
				<h1>いらなくなった”本”を誰かの”ありがとう”へ</h1>
				<div id="top_block_message" class="col-md-6" >	
					<p>大学生のための書籍無料交換サービス</br>
					登録30秒！　出品10秒！</br>
					いらない本、譲ってください</br>
					欲しい本、探してみてください</p>
				</div>
				<div id="top_block_login" class="col-md-6">
					<a href="<?php echo home_url(); ?>/register" class="entry_buttons" id="entry_form">新規登録</a>
					<a href="<?php echo home_url(); ?>/login" class="entry_buttons" id="login_form" >ログイン</a>
					<?php social_login_button(); ?>
				</div>
			</div>
		</div>
	</div>
	<div id="top_slide_title">
	TexChangeの利用イメージ
	</div>
	<div id="top_slide">
		<div><img src="images/top_slider1.jpg" title="①さてどうしようか？"/></div>
		<div><img src="images/top_slider2.jpg" title="②身近で欲しい人いないかな？"/></div>
		<div><img src="images/top_slider3.jpg" title="③出品している人いないかな？"/></div>
		<div><img src="images/top_slider4.jpg" title="④テクスチェンジ見てよかった！"/></div>
		<div><img src="images/top_slider5.jpg" title="⑤とっても、幸せな気分♫"/></div>
	</div>
</div>
<script>
	$(window).load(topSlide());
</script>

<?php get_footer("top"); ?>
