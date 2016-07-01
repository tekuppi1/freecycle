<?php
/**
 * DESCRIPTION := サイトの予約系を扱う関数を含んでいる
 * (i.e 予約状況を表示させる関数)
 */

// ログインしているユーザーの ID から、
// 予約情報 (本の名前、本の画像、古本市の日付、必要ポイント数)
// を引っぱってくる関数
function get_reservation_info_by_current_user_id() {
	// ワードプレスの組みこみ関数。wp_users, wp_usermeta テーブルあたりから
	// 情報を持ってきてるっぽいよ。
	$current_user = wp_get_current_user();

	global $wpdb;
	$query_whole_info = "SELECT * FROM wp_fmt_reserve" .
											"WHERE wp_fmt_reserve.user_id = " . $current_user->ID;
	$whole_info = $wpdb->get_results($query_whole_info);

	return $whole_info;
}

?>
