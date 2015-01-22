    <?php wp_nonce_field('update-options'); ?>
    <?php
    	$parent_id = $_REQUEST["parent_id"];
    	$map_id = isset($_REQUEST["map_id"])?$_REQUEST["map_id"]:"";
    	$parent = get_trade_map($parent_id);
    	$url = get_site_url();
    	$process = isset($_REQUEST["process"])?$_REQUEST["process"]:"";

    	if($process == "delete"){
    		delete_trade_map($map_id);
    	}

    	$children = get_child_trade_map($parent_id);
    ?>
    <h2>取引場所一覧<a href="
	<?php echo "{$url}/wp-admin/options-general.php?page=texchange&view=map-setting-places-update&parent_id=$parent_id&process=add" ?>
    " class="add-new-h2">新規追加</a></h2>
    <h3><?php echo $parent->name ?></h3>
    <table class="form-table" border="1">
		<tr valign="top">
		<th scope="row">名称</th>
		<th scope="column">緯度</th>
		<th scope="column">経度</th>
		<th scope="column">表示順</th>
		<td></td>
		<td></td>
		</tr>
	<?php
	foreach ($children as $child) {
	echo <<<ROWS
	<tr valign="top">
	<td>$child->name</td>
	<td>$child->latitude</td>
	<td>$child->longitude</td>
	<td>$child->display_order</td>
	<td><a href="{$url}/wp-admin/options-general.php?page=texchange&view=map-setting-places-update&map_id=$child->map_id&parent_id=$parent_id&process=update">詳細編集</a></td>
	<td><a href="{$url}/wp-admin/options-general.php?page=texchange&view=map-setting-places&parent_id=$parent_id&map_id=$child->map_id&process=delete">削除</a></td>
	</tr>
ROWS;
	}
	?>
    </table>
    <p class="submit">
    <input type="button" class="button" value="大学一覧に戻る" onclick="location.href='<?php echo "{$url}/wp-admin/options-general.php?page=texchange&view=map-setting" ?>'"/>
	</p>