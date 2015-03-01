<?php
add_action('wp_ajax_get_categories', 'get_freecycle_category_JSON_ajax');

/**
 * Return custom category information as a JSON string.
 * See the WordPress official reffrence about arguments.
 * @param {array}
 * @return {string} json string
 */
function get_freecycle_category_JSON($args=array()){
	$categories = get_categories($args);
	return json_encode($categories);
}

/**
 * an interface to get categories from ajax.
 * TODO: modify to take arguments as a json string
 */
function get_freecycle_category_JSON_ajax(){
	$parent = isset($_POST["parent"])?$_POST["parent"]:0;
	echo get_freecycle_category_JSON(array(
		"parent" => $parent,
		"hide_empty" => 0,
		"exclude" => 1
	));
	die;
}
?>