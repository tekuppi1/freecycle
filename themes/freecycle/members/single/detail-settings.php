<?php

/**
 * BuddyPress Member Settings
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

get_header( 'buddypress' ); ?>
<div id="item-body" role="main">
	<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/detail'; ?>" method="post" class="standard-form" id="settings-form">

		<label for="trade-location">取引場所</label>
		<?php
			echo_map_select_options();
		?>
		<p>よく使う取引場所を設定しておくと便利です。</p>
		<div class="submit">
			<input type="submit" name="submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?>" id="submit" class="auto" />
		</div>

	</form>

	<?php do_action( 'bp_after_member_body' ); ?>

</div><!-- #item-body -->

<?php do_action( 'bp_after_member_settings_template' ); ?>