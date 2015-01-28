<?php
add_action('wp_ajax_get_child_trade_maps', 'get_child_trade_maps_ajax');
add_action('wp_ajax_get_trade_map', 'get_trade_map_ajax');

/**
 * 指定されたIDを持つ取引先地図を返します。
 * Returns trade map which has the given id.
 * @return {Object}
 */
function get_trade_map($map_id){
	global $table_prefix;
	global $wpdb;

	return $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$table_prefix."fmt_trade_maps WHERE map_id = %d", $map_id));
}

/**
 * 指定された名前と一致する取引先地図を返します。
 * Returns trade map which has the given name.
 * @return {Object}
 */
function get_trade_map_by_name($name){
	global $table_prefix;
	global $wpdb;

	return $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$table_prefix."fmt_trade_maps WHERE name = %s", $name));
}

/**
 * 指定されたIDを持つ取引先地図を返します。
 * ajaxで呼び出されるメソッドです。直接コールしないでください。
 * Returns trade maps which have the given id.
 * This method is called through ajax. Do not call directly.
 * @return {String}
 */
function get_trade_map_ajax(){
	if(empty($_POST["map_id"])){
		echo "";
	}
	echo json_encode(get_trade_map($_POST["map_id"]));
	die;
}

/**
 * 取引先地図の見出し一覧を返します。
 * Returns indexes of trade maps.
 * @return {Object}
 */
function get_trade_map_indexes(){
	global $table_prefix;
	global $wpdb;

	return $wpdb->get_results("SELECT * FROM ".$table_prefix."fmt_trade_maps WHERE parent_id = 0 order by display_order");
}

/**
 * 指定された親を持つ取引先地図の一覧を返します。
 * Returns trade maps which have the given parent.
 * @return {Array(Object)}
 */
function get_child_trade_map($parent_id){
	global $table_prefix;
	global $wpdb;

	return $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$table_prefix."fmt_trade_maps WHERE parent_id = %d order by display_order", $parent_id));
}

/**
 * 指定された地図の親を返します。
 * Returns the parent trade map of the given child.
 * @return {Array(Object)}
 */
function get_parent_trade_map($map_id){
	global $table_prefix;
	global $wpdb;

	return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_prefix}fmt_trade_maps
			WHERE map_id = (select parent_id from {$table_prefix}fmt_trade_maps where map_id = %d)", $map_id));
}

/**
 * 指定された親を持つ取引先地図の一覧をJSON形式で返します。
 * Returns trade maps which have the given parent in json format.
 * @return {String}
 */
function get_child_trade_map_json($parent_id){
	return json_encode(get_child_trade_map($parent_id));
}

/**
 * 指定された親を持つ取引先地図の一覧をJSON形式で返します。
 * ajaxから呼び出されるメソッドです。直接コールしないでください。	
 * Returns trade maps which have the given parent in json format.
 * This method is called through ajax. Do not call directly.
 * @return {String}
 */
function get_child_trade_maps_ajax(){
	$map_id = isset($_POST['map_id'])?$_POST['map_id']:"";
	echo get_child_trade_map_json($map_id);
	die;
}

/**
 * デフォルトに指定されている取引場所を返します。
 * Returns the default trade location. 
 * @return {Object} $child デフォルト取引場所
 */
function get_default_map(){
	global $table_prefix;
	global $wpdb;

	return $wpdb->get_row("SELECT * FROM ".$table_prefix."fmt_trade_maps WHERE default_flg = 1 ORDER BY map_id");
}

/**
 * 新規取引場所を追加します。
 * Add a new trade location.
 * @param {string} name 取引場所名
 * @param {int} parent_id 親ID
 * @param {double} latitude 緯度
 * @param {double} longitude 経度
 * @param {int} display_order 表示順
 * @param {int} default_flg デフォルト表示フラグ
 */
function add_trade_map($name, $parent_id, $latitude, $longitude, $display_order, $default_flg){
	global $table_prefix;
	global $wpdb;

	if(empty($name) || empty($display_order)) return;
	$wpdb->insert("{$table_prefix}fmt_trade_maps", 
					array("name"=>$name, "parent_id"=>$parent_id, 
						"latitude"=>$latitude, "longitude"=>$longitude,
						"display_order"=>$display_order,
						"default_flg"=>$default_flg,
						"update_timestamp"=>current_time("mysql")));
	return $wpdb->insert_id;
}

/**
 * 取引場所の親を追加します。一般に大学の名前を登録します。
 * Add a new parent of trade location. Generally, this is a university name.
 * @param {string} name 場所名(大学名)
 * @param {int} display_order 表示順
 */
function add_trade_map_parent($name, $display_order){
	global $table_prefix;
	global $wpdb;

	if(empty($name) || empty($display_order)) return;
	$wpdb->insert("{$table_prefix}fmt_trade_maps", 
					array("name"=>$name, "display_order"=>$display_order, "update_timestamp"=>current_time("mysql")),
					array("%s", "%d", "%s"));
	return $wpdb->insert_id;
}

/**
 * 取引場所情報を更新します。
 * Update the trade location.
 * @param {int} map_id 地図ID
 * @param {string} name 取引場所名
 * @param {int} parent_id 親ID
 * @param {double} latitude 緯度
 * @param {double} longitude 経度
 * @param {int} display_order 表示順
 * @param {int} default_flg デフォルト表示フラグ
 */
function update_trade_map($map_id, $name, $parent_id, $latitude, $longitude, $display_order, $default_flg){
	global $table_prefix;	
	global $wpdb;

	$wpdb->update("{$table_prefix}fmt_trade_maps",
					array("name"=>$name, "parent_id"=>$parent_id, 
						"latitude"=>$latitude, "longitude"=>$longitude,
						"display_order"=>$display_order,
						"default_flg" => $default_flg,
						"update_timestamp"=>current_time("mysql")),
					array("map_id" => $map_id),
					array("%s", "%d", "%f", "%f", "%d", "%s", "%d"),
					array("%d"));
}

/**
 * 取引場所情報を削除します。
 * Delete the trade location.
 * @param {int} map_id 地図ID
 */
function delete_trade_map($map_id){
	global $table_prefix;	
	global $wpdb;

	// delete children
	$children = get_child_trade_map($map_id);
	if(isset($children)){
		foreach ($children as $child) {
			delete_trade_map($child->map_id);
		}
	}

	$wpdb->delete("{$table_prefix}fmt_trade_maps",
					array("map_id"=>$map_id),
					array("%d"));	
}

/**
 *
 */
function get_max_display_order($parent_id=0){
	global $table_prefix;
	global $wpdb;

	return $wpdb->get_var($wpdb->prepare("SELECT max(display_order) FROM ".$table_prefix."fmt_trade_maps WHERE parent_id = %d", $parent_id));
}

function get_max_display_order_of_map_index(){
	global $table_prefix;
	global $wpdb;

	return $wpdb->get_var("SELECT max(display_order) FROM ".$table_prefix."fmt_trade_maps WHERE parent_id = 0");
}

/**
 * 取引場所
 */
function echo_map_select_options($name, $id){
	$map_indexes = get_trade_map_indexes();
echo <<<MAP_SECTION
	<select name="$name" id="$id">
		<option value="">取引場所を選択</option>
MAP_SECTION;
	foreach ($map_indexes as $index) {
		echo "<option value=''>$index->name</option>";
		$children = get_child_trade_map($index->map_id);
		foreach ($children as $child) {
			echo "<option value='$child->latitude,$child->longitude'>&nbsp;&nbsp;$child->name</option>";

		}
	}
echo "</select>";
}
?>