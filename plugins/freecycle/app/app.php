<?php

/**
 * freecycle/app/app.php
 *
 * Functions called from the smartphone app.
 */
add_action('wp_ajax_get_nonce_from_app', 'get_nonce_from_app');
add_action('wp_ajax_nopriv_get_nonce_from_app', 'get_nonce_from_app');
add_action('wp_ajax_echo_categories_tree_json', 'echo_categories_tree_json');
add_action('wp_ajax_nopriv_echo_categories_tree_json', 'echo_categories_tree_json');
add_action('wp_ajax_echo_main_categories_json', 'echo_main_categories_json');
add_action('wp_ajax_nopriv_echo_main_categories_json', 'echo_main_categories_json');
add_action('wp_ajax_echo_sub_categories_json', 'echo_sub_categories_json');
add_action('wp_ajax_nopriv_echo_sub_categories_json', 'echo_sub_categories_json');
add_action('wp_ajax_echo_posts_data_json', 'echo_posts_data_json');
add_action('wp_ajax_nopriv_echo_posts_data_json', 'echo_posts_data_json');
add_action('wp_ajax_echo_thumbnail_url', 'echo_thumbnail_url');
add_action('wp_ajax_nopriv_echo_thumbnail_url', 'echo_thumbnail_url');
add_action('wp_ajax_echo_tc_custom_properties', 'echo_tc_custom_properties');
add_action('wp_ajax_nopriv_echo_tc_custom_properties', 'echo_tc_custom_properties');
add_action('wp_ajax_echo_posts_data_json_with_image_url_custom_properties', 'echo_posts_data_json_with_image_url_custom_properties');
add_action('wp_ajax_nopriv_echo_posts_data_json_with_image_url_custom_properties', 'echo_posts_data_json_with_image_url_custom_properties');
add_action('wp_ajax_nopriv_get_thumbnail_url', 'get_thumbnail_url');
add_action('wp_ajax_get_thumbnail_url', 'get_thumbnail_url');
add_action('wp_ajax_nopriv_echo_the_category', 'echo_the_category');
add_action('wp_ajax_echo_the_category', 'echo_the_category');

/**
 * 認証用のnonceを取得します。
 */
function get_nonce_from_app(){
	$action = isset($_REQUEST["nonce_action"])?$_REQUEST["nonce_action"]:"";
	echo wp_create_nonce($action);
	die;
}

function get_main_categories(){
	return get_sub_categories(0);
}

function get_sub_categories($parent_id){
	return get_categories(array(
		"parent" => $parent_id,
		"exclude" => 1
	));
}

function echo_categories_tree_json(){
	echo json_encode(get_categories_tree());
	die;
}

function echo_main_categories_json(){
	echo json_encode(get_main_categories());
	die;
}

function echo_sub_categories_json(){
	if(!isset($_REQUEST["parent_id"])){
		echo "{}";
	}else{
		echo json_encode(get_sub_categories($_REQUEST["parent_id"]));
	}
	die;
}


/**
 * 出品情報の検索結果に、サムネイルURLと	カスタムプロパティを全て付与し、
 * JSON 形式にコンバートして返します。
 */

function echo_posts_data_json_with_image_url_custom_properties(){
	$posts = get_posts_data_json($_REQUEST);
	foreach ($posts as $post) {
		$post->image_url = get_thumbnail_url($post->ID)["URL"];
		$post->category = get_the_category($post->ID);
		$custom_properties = get_tc_custom_properties($post->ID);
		foreach ($custom_properties as $key => $value) {
			$post->{$key} = $value;
		}
	}
	echo json_encode($posts);
	die;
}


/**
 * 検索条件を受け取り、結果を JSON 形式で返します。
 * ほんとは検索条件を JSON で受け取りたいんですが HTTP 経由だとなぜかうまくいかないので
 * パラメタを個別に送る形式で実装しています。
 * 送りたいパラメタが増えたら個別対応してください（ダサい）
 */

function get_posts_data_json($args){
	$query = new stdClass(); // create an empty object

	$query->posts_per_page = 100;

	if(isset($args["keyword"])){
		$query->s = $args["keyword"];
	}

	if(isset($args["category"])){
		$query->cat = $args["category"];
	}

	$the_query = new WP_Query($query);
	return $the_query->posts;
}

function echo_posts_data_json(){
	echo json_encode(get_posts_data_json($_REQUEST));
	die;
}

/**
 * サムネイルのURLを返します。
 * サムネイルが存在しない場合、Not Image 画像のURLを返します。
 */

function get_thumbnail_url($post_id){
	$post_thumbnail_id = get_post_thumbnail_id($post_id);
	
	if(!$post_thumbnail_id){
        $post_id_and_thumbnail_url = array(
            "ID"  => $post_id,
            "URL" => get_stylesheet_directory_uri() . '/images/index/NotImage.png'
        );
		return $post_id_and_thumbnail_url;
	}

	$image = wp_get_attachment_image_src($post_thumbnail_id,'medium');
	if(sizeof($image) > 0){
        $post_id_and_thumbnail_url = array(
            "ID"  => $post_id,
            "URL" => $image[0]
        );
		return $post_id_and_thumbnail_url;
	}else{
        $post_id_and_thumbnail_url = array(
            "ID"  => $post_id,
            "URL" => get_stylesheet_directory_uri() . '/images/index/NotImage.png'
        );
		return $post_id_and_thumbnail_url;
	}
}
	function echo_thumbnail_url(){
        $post_id = isset($_REQUEST["post_id"]) ? $_REQUEST["post_id"] : 0;
        echo json_encode(get_thumbnail_url($post_id));
		die;
	}

/**
 * テクスチェンジ独自のカスタムプロパティをまとめて返します。
 */

function get_tc_custom_properties($post_id){
	$rtn = [];
	// アルファベット順にしておくとわかりやすい
	$rtn["asin"] = get_post_meta($post_id, "asin", true);
	$rtn["author"] = get_post_meta($post_id, "author", true);
	$rtn["book_count"] = get_post_meta($post_id, "book_count", true);
	$rtn["course"] = get_post_meta($post_id, "course", true);
	$rtn["department"] = get_post_meta($post_id, "department", true);
	$rtn["ISBN"] = get_post_meta($post_id, "ISBN", true);
	$rtn["item_status"] = get_post_meta($post_id, "item_status", true);
	$rtn["price"] = get_post_meta($post_id, "price", true);
	$rtn["wanted_item_id"]= get_post_meta($post_id, "wanted_item_id", true);

	return $rtn;
}

	function echo_tc_custom_properties(){
		if(isset($_REQUEST["post_id"])){
			echo json_encode(get_tc_custom_properties($_REQUEST["post_id"]));
		}
		die;
	}

/**
 * 投稿IDを元にしてカテゴリ名称を返します。
 * WordPress の標準関数を使っているので echo のメソッドしかありません。
 */

function echo_the_category(){
	$post_id = isset($_REQUEST['post_id'])?$_REQUEST['post_id']:false;
	echo json_encode(get_the_category($post_id));
	die;
}

?>
