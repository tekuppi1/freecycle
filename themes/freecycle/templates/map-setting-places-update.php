    <?php wp_nonce_field('update-options'); ?>
    <?php
        $map_id = isset($_REQUEST["map_id"])?$_REQUEST["map_id"]:null;
        $parent_id = isset($_REQUEST["parent_id"])?$_REQUEST["parent_id"]:"";
        $process = isset($_REQUEST["process"])?$_REQUEST["process"]:"";
        $name = "";
        $display_order = "";
        $latitude = 0;
        $longitude = 0;
        $default_flg = 0;
        $default_checked = "";
        $map;
        $url = get_site_url();

        if(!empty($map_id)){
            $map = get_trade_map($map_id);
            $name = $map->name;
            $latitude = $map->latitude;
            $longitude = $map->longitude;
            $display_order = $map->display_order;
            $default_flg = $map->default_flg;
        }else{
            $display_order = get_max_display_order($parent_id) + 1;
        }

        // process map settings
        if(isset($_POST['map_action'])){
            $name = isset($_POST['place-name'])?$_POST['place-name']:"";
            $parent_id = isset($_POST['parent_id'])?$_POST['parent_id']:"";
            $latitude = isset($_POST['latitude'])?$_POST['latitude']:"";
            $longitude = isset($_POST['longitude'])?$_POST['longitude']:"";
            $display_order = isset($_POST['display_order'])?$_POST['display_order']:"";
            $default_flg = isset($_POST['default_flg'])?1:0;
            switch ($_POST['map_action']) {
                case 'add':
                    $map_id = add_trade_map($name, $parent_id, $latitude, $longitude, $display_order, $default_flg);
                    $process = "update";
                    break;
                case 'update':
                    $map_id = $_POST['map_id'];
                    update_trade_map($map_id, $name, $parent_id, $latitude, $longitude, $display_order, $default_flg);
                    break;
                default:
                    break;
            }
        }

        if($default_flg == 1){
            $default_checked = "checked";
        }

    ?>
    <h2>登録場所</h2>
    <table class="form-table">
    	<tbody>
        <tr valign="top">
        <th scope="row">大学名</th>
        <td>
        <select name="parent_id">
        <?php
        $indexes = get_trade_map_indexes();
        foreach ($indexes as $index) {
            $selected = "";
            if($parent_id === $index->map_id){
                $selected = "selected";
            }
            echo <<<OPTIONS
            <option value="$index->map_id" $selected>$index->name</option>
OPTIONS;
        }
        ?>
        </select>
        </td>
        </tr>
		<tr valign="top">
    	<th scope="row">名称</th>
		<td><input type="text" name="place-name" value="<?php echo $name ?>"></td>
		</tr>
        <tr valign="top">
        <th scope="row">緯度</th>
        <td><input type="number" min="-90.00000000000000" max="90.00000000000000" step="0.00000000000001" name="latitude" value="<?php echo $latitude ?>"></td>
        </tr>
        <tr valign="top">
        <th scope="row">経度</th>
        <td><input type="number" min="-180.00000000000000" max="180.00000000000000" step="0.00000000000001" name="longitude" value="<?php echo $longitude ?>"></td>
        </tr>
		<tr valign="top">
    	<th>表示順</th>
		<td>
			<input type="number" min='1' max='30' name="display_order" value="<?php echo $display_order ?>" />
		</td>
		</tr>
        <tr valign="top">
        <th>デフォルト</th>
        <td>
            <input type="checkbox" name="default_flg" value="1" <?php echo $default_checked ?>/>
        </td>
        </tr>
		</tbody>	
    </table>
    <input type="hidden" name="map_id" value="<?php echo $map_id?>" />
    <input type="hidden" name="map_action" value="<?php echo $process?>" />
    <p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />&nbsp;
    <input type="button" class="button" value="取引場所一覧に戻る" onclick="location.href='<?php echo "{$url}/wp-admin/options-general.php?page=texchange&view=map-setting-places&parent_id=$parent_id" ?>'"/>
	</p>