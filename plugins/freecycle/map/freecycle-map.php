<?php
/**
 * 指定されたIDを持つ取引先地図のを返します。
 * Returns trade maps which have the given id.
 * @return {Object}
 */
function get_trade_map($map_id){
	global $table_prefix;
	global $wpdb;

	return $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$table_prefix."fmt_trade_maps WHERE map_id = %d", $map_id));
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

function add_trade_map($name, $parent_id, $latitude, $longitude, $display_order){
	global $table_prefix;
	global $wpdb;

	if(empty($name) || empty($display_order)) return;
	$wpdb->insert("{$table_prefix}fmt_trade_maps", 
					array("name"=>$name, "parent_id"=>$parent_id, 
						"latitude"=>$latitude, "longitude"=>$longitude,
						"display_order"=>$display_order,
						"update_timestamp"=>current_time("mysql")));
	return $wpdb->insert_id;
}

function add_trade_map_index($name, $display_order){
	global $table_prefix;
	global $wpdb;

	if(empty($name) || empty($display_order)) return;
	$wpdb->insert("{$table_prefix}fmt_trade_maps", 
					array("name"=>$name, "display_order"=>$display_order, "update_timestamp"=>current_time("mysql")),
					array("%s", "%d", "%s"));
	return $wpdb->insert_id;
}

function update_trade_map($map_id, $name, $parent_id, $latitude, $longitude, $display_order){
	global $table_prefix;	
	global $wpdb;

	$wpdb->update("{$table_prefix}fmt_trade_maps",
					array("name"=>$name, "parent_id"=>$parent_id, 
						"latitude"=>$latitude, "longitude"=>$longitude,
						"display_order"=>$display_order,
						"update_timestamp"=>current_time("mysql")),
					array("map_id" => $map_id),
					array("%s", "%d", "%f", "%f", "%d", "%s"),
					array("%d"));
}

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

?>