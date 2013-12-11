<?php
function insert_attachment($file_handler,$post_id,$setthumb='false'){  
	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();
	
	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	$attach_id = media_handle_upload($file_handler, $post_id);

	if ($setthumb){
		update_post_meta($post_id,'_thumbnail_id',$attach_id);
	}
	return $attach_id;
}

$post = array(  
  'comment_status' => 'open', // open comment
  'ping_status' => 'closed', // pinback, trackback off
  'post_author' => $bp->loggedin_user->id, // login user ID
  'post_category' => array($_POST['field_3']),
  'post_content' => htmlentities($_POST['field_2'], ENT_QUOTES, 'UTF-8'), // item desctiption
  'post_date' => date('Y-m-d H:i:s'), 
  'post_date_gmt' => date('Y-m-d H:i:s'),
  'post_status' => 'publish', // public open
  'post_title' => strip_tags($_POST['field_1']), // title
  'post_type' => 'post', // entry type name
  'tags_input' => $_POST['field_4']

);  

$insert_id = wp_insert_post($post);

if($insert_id){
	// success
	// image upload
	global $post;
	if($_FILES){
		$files = $_FILES['upload_attachment'];
		// reverse sort
		arsort($files['name'],SORT_NUMERIC);
		arsort($files['type'],SORT_NUMERIC);
		arsort($files['tmp_name'],SORT_NUMERIC);
		arsort($files['error'],SORT_NUMERIC);
		arsort($files['size'],SORT_NUMERIC);

		foreach ($files['name'] as $key => $value){
			if ($files['name'][$key]){
				$file = array(
					'name'     => $files['name'][$key],
					'type'     => $files['type'][$key],
					'tmp_name' => $files['tmp_name'][$key],
					'error'    => $files['error'][$key],
					'size'     => $files['size'][$key]
				);  
				$_FILES = array("upload_attachment" => $file);

				foreach ($_FILES as $file => $array){
					$newupload = insert_attachment($file,$insert_id);
				}
			}
		}
	}
}else{
	// failure
}
?>

<form action="" method="post" enctype="multipart/form-data" id="newentry">
<!-- item name -->
<label for="field_1">商品名(必須)</label></br>
<input type="text" name="field_1" id="field_1" value=""></br>
<!-- item description -->
<label for="field_2">商品説明(必須)</label></br>
<textarea rows="5" cols="40" name="field_2" id="field_2"></textarea></br>
<!-- category -->
<label for="field_3">カテゴリ</label></br>
<?php
	wp_dropdown_categories(array(
		'orderby'		=> 'ID',
		'hide_empty'	=> 0,
		'exclude'		=> '1',
		'name'			=> 'field_3'
	));
?>
</br>
<!-- tags -->
<label for="field_4">タグ(コンマ区切り)</label></br>
<input type="text" name="field_4" id="field_4" value="" size="30"></br>
<!-- picture -->
<label for="field_5">写真(最大3枚)</label></br>
<input type="file" class="multi" name="upload_attachment[]" maxlength="3" accept="jpeg|jpg|png"></br>
<input type="button" value="出品" onClick="callOnNewEntry()">
<p>注意:出品後の記事の編集はできません。内容を十分確認して下さい。</p>
</form>
