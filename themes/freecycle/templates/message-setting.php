    <?php wp_nonce_field('update-options'); ?>
    <table class="form-table">
    	<h3>新規登録時メッセージ</h3>
		<tr valign="top">
		<th scope="row">送信元ユーザ</th>
		<td>
		<select name="newuser_message_sender">
		<option value='-1'>※送信を停止</option>
<?php
		$admins = get_users(array('role' => 'Administrator'));
		foreach ($admins as $admin) {
			echo "<option value='". $admin->ID . "'";
			if(get_option('newuser_message_sender') == $admin->ID){
				echo " selected";
			} 
			echo ">" . $admin->last_name . $admin->first_name . "</option>";
		}
?>
		</select>
		</td>
		</tr>			
		<th scope="row">本文</th>
		<td>
		<textarea name="newuser_message_content"
			style="width:700px; height:150px;">
<?php echo get_option('newuser_message_content'); ?>
		</textarea>
		</td>
		</tr>
    </table>
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options"
    	value="newuser_message_sender, newuser_message_content"
    <p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>