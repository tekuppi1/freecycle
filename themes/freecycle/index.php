<?php get_header(); ?>

<!--
	style sheet : style/index.css
-->


<!--広告（写真は600x400固定で）-->
<div id="huruhon">
	<img src=" <?php echo get_stylesheet_directory_uri() ?>/images/index/huru.png" alt="古本市の宣伝">
</div>
<!--広告-->


<!--div id="head">Textbook for FREE</div-->

<div id="index_title">ワード検索</div>
<!--サーチフォーム検索-->
<div id="index_searchform"><?php get_search_form(); ?></div>
<div style="clear:both"></div>
<!--サーチフォーム検索-->

<div id="index_title">カテゴリ検索</div>
<!--カテゴリ検索-->
<div id="searchbox">
	<div class="category">
		<img src="<?php echo get_stylesheet_directory_uri() ?>/images/index/a.png">
		<p class="main">法学部</p>
	</div>
	<!--div class="clear"></div-->

	<div class="category">
		<img src="<?php echo get_stylesheet_directory_uri() ?>/images/index/b.png">
		<p class="main">経済学部</p>
	</div>
	<div class="clear"></div>

	<div class="category">
		<img src="<?php echo get_stylesheet_directory_uri() ?>/images/index/c.png">
		<p class="main">外国語学部</p>
		<p class="sub">英米学科</p>
		<p class="sub">スペイン・ラテンアメリカ学科</p>
		<p class="sub">フランス学科</p>
		<p class="sub">ドイツ学科</p>
		<p class="sub">アジア学科</p>
	</div>
	<div class="clear"></div>
</div>
<!--カテゴリ検索-->



<!--ログアウト処理-->
<div class="index_profile">
	<?php bp_loggedin_user_avatar( 'type=thumb&width=30&height=30' ); ?>
	<span class="user-nicename"><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></span>
	<a class="button logout" href="<?php echo wp_logout_url( wp_guess_url() ); ?>"><?php _e( 'Log Out', 'buddypress' ); ?></a>
</div>
<!--ログアウト処理-->

<?php get_footer(); ?>
