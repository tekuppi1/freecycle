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
 * 検索条件を受け取り、結果を JSON 形式で返します。
 * ほんとは検索条件を JSON で受け取りたいんですが HTTP 経由だとなぜかうまくいかないので
 * パラメタを個別に送る形式で実装しています。
 * 送りたいパラメタが増えたら個別対応してください（ダサい）
 */

function echo_posts_data_json(){
	$query = new stdClass(); // create an empty object

	if(isset($_REQUEST["keyword"])){
		$query->s = $_REQUEST["keyword"];
	}

	if(isset($_REQUEST["category"])){
		$query->cat = $_REQUEST["category"];
	}

	$the_query = new WP_Query($query);

	echo json_encode($the_query->posts);
	die;
}

/**
 * サムネイルのURLを返します。
 * サムネイルが存在しない場合、Not Image 画像のURLを返します。
 */

function get_thumbnail_url($post_id){
	$post_thumbnail_id = get_post_thumbnail_id($post_id);
	if(!$post_thumbnail_id){
		return get_stylesheet_directory_uri().'/images/index/NotImage.png';
	}

	$image = wp_get_attachment_image_src($post_thumbnail_id,'medium');
	if(sizeof($image) > 0){
		return $image[0];
	}else{
		return get_stylesheet_directory_uri().'/images/index/NotImage.png';
	}
}
	function echo_thumbnail_url(){
		$post_id = isset($_REQUEST["post_id"])?$_REQUEST["post_id"]:0;
		echo get_thumbnail_url($post_id);
		die;
	}
?>