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

			<div id="site-generator" role="contentinfo">
				<?php do_action( 'bp_dtheme_credits' ); ?>
				<a href="<?php echo home_url(); ?>/contact">お問い合わせ</a>・
				<a href="<?php echo home_url(); ?>/request">不具合報告・改善要望</a>・
				<a href="<?php echo home_url(); ?>/guideline">ガイドライン</a><br>
				<a href="<?php echo home_url(); ?>/disclaimer">免責事項</a>・
				<a href="<?php echo home_url(); ?>/privacy-policy">個人情報保護方針</a>
				<br><br>
				<p>
				<?php printf( __( 'Copyright  <a href="%1$s">てくすちぇんじ</a>.', 'buddypress' ),  'http://texchg.com' ); ?>
				</p>
			</div>

			<?php do_action( 'bp_footer' ); ?>

		</div><!-- #footer -->

		<?php do_action( 'bp_after_footer' ); ?>

		<?php wp_footer(); ?>
	</body>

</html>