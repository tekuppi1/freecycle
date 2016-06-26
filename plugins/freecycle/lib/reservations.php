<?php
/**
 * DESCRIPTION := サイトの予約系を扱う関数を含んでいる
 * (i.e 予約状況を表示させる関数)
 */

// ログインしているユーザーの ID から、予約情報、つまり、本の名前とかを
// 引っぱってくる関数
function get_reservation_info() {
	// ワードプレスの組みこみ関数。wp_users, wp_usermeta テーブルあたりから
	// 情報を持ってきてるっぽいよ。
	$current_user = wp_get_current_user();

	global $wpdb;
	$query_item_id = "SELECT item_id FROM wp_fmt_reserve" .
									 "WHERE wp_fmt_reserve.user_id = " . $current_user->ID;

	// (サブクエリ) 現在のユーザーの ID に一致する item_id をとってきて、
	// item_id に一致する本の名前をとってくる。
	$query_item_name = "SELECT post_title FROM wp_posts" .	// FIXME := ここのテーブル名は変えた方がいいかもね。分かりにくいから。
										 "WHERE wp_posts.ID = " . "(" . $query_item_id . ")";
	$book_titles = $wpdb->get_results($query_item_name);

	return $book_titles;
}
?>
