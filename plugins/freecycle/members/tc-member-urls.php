<?php
/**
 * マイページに関するURLを出力します。
 *
 */

function get_entry_list_url(){
	return bp_loggedin_user_domain()."entry_list";
}
	function echo_entry_list_url(){
		echo get_entry_list_url();
	}

?>