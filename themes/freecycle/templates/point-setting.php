    <?php wp_nonce_field('update-options'); ?>
    <table class="form-table">
		<tr valign="top">
		<th scope="row">新規登録時</th>
		<td>
		<input type="number" min='1' max='30' name="register-point" value="<?php echo get_option('register-point'); ?>" />p
		</td>
		</tr>
		<tr valign="top">
		<th scope="row">出品時</th>
		<td>
		<input type="number" min='1' max='30' name="exhibition-point" value="<?php echo get_option('exhibition-point'); ?>" />p
		</td>
		</tr>
    </table>
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options"
    	value="register-point, exhibition-point" />
    <p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>