    <?php wp_nonce_field('update-options'); ?>
    <h2>プッシュ通知設定</h2>
    <h3>テスト環境</h3>
    <table class="form-table">
		<tr valign="top">
		<th scope="row">ACS APP Key</th>
		<td>
		<input type="text" name="acs_app_key" value="<?php echo get_option('acs_app_key'); ?>"> 
		</td>
		</tr>
		<tr valign="top">
		<th scope="row">ユーザ名</th>
		<td>
		<input type="text" name="acs_user_name" value="<?php echo get_option('acs_user_name'); ?>"> 
		</td>
		</tr>
		<tr valign="top">
		<th scope="row">パスワード</th>
		<td>
		<input type="text" name="acs_password" value="<?php echo get_option('acs_password'); ?>"> 
		</td>
		</tr>
    </table>
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options"
    	value="acs_app_key, acs_user_name, acs_password" />
    <p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>