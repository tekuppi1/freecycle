</div> <!-- #container -->

<?php do_action( 'bp_after_container' ); ?>
<?php do_action( 'bp_before_footer'   ); ?>

<div id="footer">
<!-- <div id="pagetop_link"><a href="#logo">Go to pagetop</a></div> -->
<?php if ( is_active_sidebar( 'first-footer-widget-area' ) || is_active_sidebar( 'second-footer-widget-area' ) || is_active_sidebar( 'third-footer-widget-area' ) || is_active_sidebar( 'fourth-footer-widget-area' ) ) : ?>
<div id="footer-widgets">
<?php get_sidebar( 'footer' ); ?>
</div>
<?php endif; ?>

	
<?php do_action( 'bp_dtheme_credits' ); ?>
	
<!--フッター------------------->
<div id="footer_menu" role="contentinfo">
	<div id="footer_left">
		<div>テクスチェンジについて</div>
		<ul>
			<li>利用規約</li>
			<li><a href="<?php echo home_url(); ?>/contact">お問い合わせ</a></li>
			<li><a href="<?php echo home_url(); ?>/request">不具合報告・改善要望</a></li>
			<li><a href="<?php echo home_url(); ?>/guideline">ガイドライン</a></li>
			<li><a href="<?php echo home_url(); ?>/disclaimer">免責事項</a></li>
			<li><a href="<?php echo home_url(); ?>/privacy-policy">個人情報保護方針</a></li>
			<li>運営団体</li>
		</ul>
		<hr>
	</div>
	
	<div id="footer_bottom">
		<div id="footer_text">
			<div id="text_right"><div id="footer_logo_icon" alt="フッター"></div></div>
			<div id="text_left"><div id="copyright">© 2014-2015 <a href="<?php echo home_url(); ?>">Texchange</a></div></div>
		</div>
	</div>
</div>
<!--フッター------------------->

<?php do_action( 'bp_footer' ); ?>
</div><!-- #footer -->
<?php do_action( 'bp_after_footer' ); ?>
<?php wp_footer(); ?>
</body>

</html>