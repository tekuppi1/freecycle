    <?php wp_nonce_field('update-options'); ?>
    <?php
        $map_id = isset($_REQUEST["map_id"])?$_REQUEST["map_id"]:null;
        $process = isset($_REQUEST["process"])?$_REQUEST["process"]:"";
        $name = "";
        $display_order = "";
        $map;
        $url = get_site_url();

        if(isset($map_id)){
            $map = get_trade_map($map_id);
            $name = $map->name;
            $display_order = $map->display_order;
        }else{
            $display_order = get_max_display_order() + 1;
        }

    // process map settings
    if(isset($_POST['map_action'])){
        $name = isset($_POST['uni-name'])?$_POST['uni-name']:"";
        $display_order = $_POST['display_order'];
        switch ($_POST['map_action']) {
            case 'add':
                $map_id = add_trade_map_parent($name, $display_order);
                $display_order = get_max_display_order();
                $process = "update";
                break;
            case 'update':
                $map_id = $_POST['map_id'];
                update_trade_map($map_id, $name, 0, 0, 0, $display_order);
                break;
            default:
                break;
        }
    }

    ?>
    <h2>大学</h2>
    <table class="form-table">
    	<tbody>
		<tr valign="top">
    	<th scope="row">大学名</th>
		<td><input type="text" name="uni-name" value="<?php echo $name ?>"></td>
		</tr>
		<tr valign="top">
    	<th>表示順</th>
		<td>
			<input type="number" min='1' max='30' name="display_order" value="<?php echo $display_order ?>" />
		</td>
		</tr>
		</tbody>	
    </table>
    <input type="hidden" name="map_id" value="<?php echo $map_id?>" />
    <input type="hidden" name="map_action" value="<?php echo $process?>" />
    <p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />&nbsp;
    <input type="button" class="button" value="大学一覧に戻る" onclick="location.href='<?php echo "{$url}/wp-admin/options-general.php?page=texchange&view=map-setting" ?>'"/>
	</p>