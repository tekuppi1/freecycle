<?php
/**
 * DESCRIPTION := サイトの予約系を扱う関数を含んでいる
 * (i.e 予約状況を表示させる関数)
 */

// ログインしているユーザーの ID から、
// 予約情報 (本の名前、本の画像、古本市の日付、必要ポイント数)
// を引っぱってくる
function get_reservation_info_by_current_user_id() {
	// ワードプレスの組みこみ関数。wp_users, wp_usermeta テーブルあたりから
	// 情報を持ってきてるっぽいよ。
	$current_user = wp_get_current_user();

	global $wpdb;
	$query_whole_info_in_reserve = "SELECT * FROM wp_fmt_reserve " .
																 "WHERE wp_fmt_reserve.user_id = " . $current_user->ID;
	$whole_info_in_reserve = $wpdb->get_results($query_whole_info_in_reserve);

	return $whole_info_in_reserve;
}

// 古本市id から古本市の開始日時、終了日時、開催場所を取ってくる
function get_bookfair_info_by_bookfair_id($bookfair_id) {
	global $wpdb;
	global $table_prefix;
 	$sql = "SELECT starting_time, ending_time, date, venue, classroom " .
				 "FROM " . $table_prefix . "fmt_book_fair ";
 	$bookfair_info = $wpdb->get_results($wpdb->prepare(
 		$sql .
		"WHERE " . $table_prefix . "fmt_book_fair.bookfair_id = %d",
 		$bookfair_id 
 	));
	
	return $bookfair_info;
}

function provide_formatted_books_info() {
	$whole_info_in_reserve = get_reservation_info_by_current_user_id(); // wp_fmt_reserveテーブルの、ログインID のデータ全て
	$books_info = array();                                              // 1つの要素は、 1つの本に対応してるよ
	
	foreach ($whole_info_in_reserve as $single_book_info) {
		$post_id    = $single_book_info->item_id;
		$book_title = get_the_title($post_id);

		// $bookfair_info を展開しときましょう。説明変数にもなるし
		$bookfair_id   = $single_book_info->bookfair_id;
		$bookfair_info = get_bookfair_info_by_bookfair_id($bookfair_id);
		$starting_time = $bookfair_info[0];
		$ending_time   = $bookfair_info[1];
		$date          = $bookfair_info[2];
		$venue         = $bookfair_info[3];
		$classroom     = $bookfair_info[4];
		
		$clear_single_book_info = array($book_title, $date, $starting_time, $ending_time, $venue, $classroom);
		array_push($books_info, $clear_single_book_info);
		
		/* 
			 サムネイルを取ってこれるはず。。。確認はしてません。ほんと分かりません。
			 $args = array(
			 'post_type'   => 'attachment',
			 'post_parent' => $post_id,
			 );
			 $attachment = array_reverse(get_posts($args));
			 $thumbnail_url[$key] = wp_get_attachment_image_url($attachment->ID, 'thumbnail');
		 */
	}
	return $books_info;
}

?>
