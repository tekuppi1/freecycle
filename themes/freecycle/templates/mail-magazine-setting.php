<?php wp_nonce_field('update-options'); ?>
<table class="form-table">
	<textarea readonly rows="4" cols="50">
<?php $users = get_users(); 
	$is_first = true;
	foreach ($users as $user): 
	$get_mail_magazine = xprofile_get_field_data('メールマガジンを受け取りますか？' ,$user->ID);
	if($get_mail_magazine == "受け取る"){
		if($is_first){
			$is_first = false;
		}else{
			echo ", ";
		}
		echo $user->user_email;
	}
	endforeach; ?>
</textarea>	
</table>