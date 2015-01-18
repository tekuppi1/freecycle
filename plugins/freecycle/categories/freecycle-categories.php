<?php

/**
 * return custom category information as a JSON string
 */
function get_freecycle_category_JSON($args){
	$categories = get_categories($args);
	return json_encode($categories);
}

?>