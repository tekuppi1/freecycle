<!-- <form action="" method="post" enctype="multipart/form-data" id="newentry"> -->
<form action="new_entry" method="post" enctype="multipart/form-data" id="newentry" name="newentry">

<!-- item name -->
<label for="field_1">商品名(必須)</label></br>
<input type="text" name="field_1" id="field_1" value=""></br>

<!-- item description -->
<label for="field_2">商品説明(必須)</label></br>
<textarea rows="5" cols="40" name="field_2" id="field_2"></textarea></br>

<!-- category -->
<label for="main_category">カテゴリ</label></br>
<select name="main_category" onChange="onChangeMainCategory(0)">
<option value="">-- 大学 --</option>
<?php $main_category = output_main_category(0); ?>
</select>
<select name="subcategory">
<option value="">-- 学部 --</option>
<?php output_sub_category($main_category,0); ?>
</select>
</br>
<!-- status -->
<label for="item_status">状態</label></br>
<select name="item_status">
<option value="verygood"><?php echo get_display_item_status("verygood"); ?></option>
<option value="good"><?php echo get_display_item_status("good"); ?></option>
<option value="bad"><?php echo get_display_item_status("bad"); ?></option>
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
<input type="hidden" name="exhibitor_id" value="<?php global $bp; echo $bp->loggedin_user->id ?>">
<br/>
<input type="button" value="出品" onClick="callOnNewEntry()" >
<br/>
</form>
<br/>
<hr> <!-- 仕切り線 -->