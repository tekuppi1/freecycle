<?php

/**
 * BuddyPress Member Settings
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

get_header('buddypress');
wp_register_script(
	'detailSettingsScript',
	get_stylesheet_directory_uri() . '/members/single/js/detail-settings.js'
);
wp_enqueue_script('detailSettingsScript');

// register detail settings data
if(isset($_POST['trade_location'])){
	update_user_meta(get_current_user_id(), 'default_trade_location', $_POST['trade_location']);
	do_action( 'template_notices' );
}

?>
<div id="item-body" role="main">
	<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/detail'; ?>" method="post" class="standard-form" id="settings-form">

		<label for="trade_location_university">標準取引場所</label>
		<select id="trade_location_university">
			<option value="">-- 大学を選択 --</option>
		<?php
			global $user_ID;
			$mylocation = get_user_meta($user_ID, "default_trade_location", true);
			$myuniversity = get_parent_trade_map($mylocation);

			$universities = get_trade_map_indexes();
			foreach ($universities as $university) {
				$selected = "";
				if($myuniversity->map_id == $university->map_id){
					$selected = "selected";
				}
				echo "<option value='$university->map_id' $selected>$university->name</option>";
			}
		?>
		</select>
		<select name="trade_location">
			<option value="">-- 取引場所を選択 --</option>
		<?php
			if($myuniversity){
				$locations = get_child_trade_map($myuniversity->map_id);
				foreach ($locations as $location) {
					$selected = "";
					if($mylocation == $location->map_id){
						$selected = "selected";
					}
					echo "<option value='$location->map_id' $selected>$location->name</option>";
				}
			}
		?>
		</select>
		<p>よく使う取引場所を設定しておくと便利です。</p>
		<div class="submit">
			<input type="submit" name="submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?>" id="submit" class="auto" />
		</div>

	</form>

	<?php do_action( 'bp_after_member_body' ); ?>

</div><!-- #item-body -->

<?php do_action( 'bp_after_member_settings_template' ); ?>