<!-- <form action="" method="post" enctype="multipart/form-data" id="newentry"> -->
<form action="new_entry" method="post" enctype="multipart/form-data" id="newentry">

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
<br/>
<!-- status -->
<label for="item_status">状態</label></br>
<select name="item_status">
<option value="excellent" selected><?php echo get_display_item_status("excellent"); ?></option>
<option value="verygood"><?php echo get_display_item_status("verygood"); ?></option>
<option value="good"><?php echo get_display_item_status("good"); ?></option>
<option value="bad"><?php echo get_display_item_status("bad"); ?></option>
<option value="verybad"><?php echo get_display_item_status("verybad"); ?></option>
<option value="poor"><?php echo get_display_item_status("poor"); ?></option>
</select>
</br>
<!-- tags -->
<label for="field_4">タグ(スペース区切り)</label></br>
<input type="text" name="field_4" id="field_4" value="" size="30"></br>
<!-- picture -->
<label for="field_5">写真(最大3枚)</label></br>
<input type="file" class="multi" name="upload_attachment[]" ></br>
<input type="file" class="multi" name="upload_attachment[]" ></br>
<input type="file" class="multi" name="upload_attachment[]" ></br>

<input type="button" value="出品" onClick="callOnNewEntry()">
<p>注意:出品後の記事の編集はできません。内容を十分確認して下さい。</p>
</form>
<hr> <!-- 仕切り線 -->