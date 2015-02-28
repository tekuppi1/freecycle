    <?php
    		wp_nonce_field('update-options');
    		$url = get_site_url();
    		$process = isset($_REQUEST["process"])?$_REQUEST["process"]:"";

    		if($process == "delete"){
    			$map_id = isset($_REQUEST["map_id"])?$_REQUEST["map_id"]:"";
    			delete_trade_map($map_id);
    		}
    ?>
    <h2>大学一覧
    <a href=
    <?php echo "{$url}/wp-admin/options-general.php?page=texchange&view=map-setting-uni-update&process=add" ?>
    class="add-new-h2">新規追加</a></h2>
    <table class="form-table" border="1">
		<tr valign="top">
		<th>大学名</th>
		<th>表示順</th>
		<th></th>
		<th></th>		
		</tr>
	<?php
	$indexes = get_trade_map_indexes();
	foreach ($indexes as $index) {
	echo <<<ROWS
	<tr valign="top">
	<td><a href="{$url}/wp-admin/options-general.php?page=texchange&view=map-setting-uni-update&map_id=$index->map_id&process=update">$index->name</a></td>
	<td>$index->display_order</td>
	<td><a href="{$url}/wp-admin/options-general.php?page=texchange&view=map-setting-places&parent_id=$index->map_id">取引場所一覧</a></td>
	<td><a href="{$url}/wp-admin/options-general.php?page=texchange&view=map-setting&map_id=$index->map_id&process=delete">削除</a></td>	
	</tr>
ROWS;
	}
	?>
    </table>