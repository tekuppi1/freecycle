<?php 
	require_once($_SERVER['DOCUMENT_ROOT'].'/wp/wp-load.php');
  require_once($_SERVER['DOCUMENT_ROOT'].'/wp/wp-content/plugins/freecycle/functions.php');
?>
<?php get_header("top"); ?>

<div id="top_content">
	<div id="top_background">
		<div id="top_black_background">
			<div id="top_block">
				<h1>使わなくなった”本”を誰かの”ありがとう”に</h1>
				<div id="top_block_message" class="col-md-6" >	
					<p>無料教科書交換</br>大学生が運営する、大学生のためのWebサービス</p>
				</div>
				<div id="top_block_login" class="col-md-6">
					<a href="<?php echo home_url(); ?>/register" class="entry_buttons" id="entry_form">新規登録</a>
					<a href="<?php echo home_url(); ?>/login" class="entry_buttons" id="login_form" >ログイン</a>
					<?php social_login_button(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
	
<?php get_footer("top"); ?>