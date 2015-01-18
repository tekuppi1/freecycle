<?php
// TODO: 記事を投稿した時点で、初期テーブルにデータをインサートする

// アクションフック登録
add_action('init', 'custom_init');
add_action('wp_ajax_giveme', 'giveme');
add_action('wp_ajax_cancelGiveme', 'cancelGiveme');
add_action('wp_ajax_confirmGiveme', 'confirmGiveme');
add_action('wp_ajax_exhibiter_evaluation', 'exhibiter_evaluation');
add_action('wp_ajax_bidder_evaluation', 'bidder_evaluation');
add_action('wp_ajax_finish', 'finish');
add_action('wp_ajax_new_entry', 'new_entry');
add_action('wp_ajax_delete_post', 'delete_post');
add_action('wp_ajax_update_comment', 'update_comment');
add_action('wp_ajax_search_wantedbook', 'search_wantedbook');
add_action('wp_ajax_search_wantedlist', 'search_wantedlist');
add_action('wp_ajax_nopriv_search_wantedlist', 'search_wantedlist');
add_action('wp_ajax_add_wanted_item', 'add_wanted_item');
add_action('wp_ajax_del_wanted_item_by_asin', 'del_wanted_item_by_asin');
add_action('wp_ajax_exhibit_to_wanted', 'exhibit_to_wanted');
add_action('wp_ajax_exhibit_from_app', 'exhibit_from_app');
add_action('wp_ajax_nopriv_exhibit_from_app', 'exhibit_from_app');
add_action('wp_ajax_register_app_information', 'register_app_information');
add_action('wp_ajax_cancel_trade_from_exhibitor', 'cancel_trade_from_exhibitor');
add_action('wp_ajax_cancel_trade_from_bidder', 'cancel_trade_from_bidder');
add_action('user_register', 'on_user_added');
remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2);

// ファイル読み込み
require_once('categories/freecycle-categories.php');


//写真を自動で回転して縦にする
function edit_images_before_upload($file)
{
	if($file['type'] == 'image/jpeg')
	{
		$image = wp_get_image_editor($file['file']);
		if(!is_wp_error($image))
		{
			$exif			= exif_read_data($file['file']);
			$orientation	= $exif['Orientation'];
			if(!empty($orientation))
			{
				switch($orientation){
					case 8:	$image->rotate(90);  break;
					case 3: $image->rotate(180); break;
					case 6: $image->rotate(-90); break;
				}
			}
				$image->save($file['file']);
		}
	}
	return $file;
}
add_action('wp_handle_upload','edit_images_before_upload');
	
function redirect_to_home(){
	$redirect_url = get_option('home');
	header("Location: ".$redirect_url);
	exit;
}
add_action('wp_login', 'redirect_to_home');


if(strpos($_SERVER['REQUEST_URI'] ,'archives/category') > 0){
	redirect_to_404();
}
//redirect(/archive/category => 404.php(bp-default直下))
function redirect_to_404(){
	global $wp_query;
	$wp_query -> is_404 = true;
}


function custom_init(){
	add_action('comment_post', 'on_comment_post');	
}

// コメント投稿時にメッセージを飛ばす
function on_comment_post() {
	global $post;
	// 自分自身の記事にコメントした場合はメッセージを飛ばさない
	if(bp_loggedin_user_id() != $post->post_author){
		messages_new_message(array(
		'sender_id' => bp_loggedin_user_id(),
		'recipients' => $post->post_author,
		'subject' => '【自動送信】あなたの商品にコメントがつきました',
		'content' => '以下の商品にコメントが来ています！'
						. '<a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a>'
		));
	}
}

// change timing of sending notification mail
remove_action('messages_message_sent', 'messages_notification_new_message');
add_action('messages_message_after_save', 'messages_notification_new_message', 10);

// 記事検索時の条件追加
function add_costom_join($join){
	global $table_prefix;
	$join .= "LEFT JOIN " . $table_prefix . "fmt_giveme_state "
			. "ON " . $table_prefix . "posts.ID = " . $table_prefix . "fmt_giveme_state.post_id ";
	return $join;
}

function add_custom_where($where){
	global $wpdb;
	global $table_prefix;

	// 検索結果に固定ページを含めない	
	if(is_search()) {
		$where .= "AND post_type = 'post' ";
	}

	// 一覧ページには取引相手確定済の記事を表示しない。
	// 「ください可能」のみの条件で検索された場合も、同様に表示しない。
	if(is_front_page() || (isset($_REQUEST['seachform_itemstatus']) && $_REQUEST['seachform_itemstatus'] == 'givemeable')){
		$where .= "AND (" . $table_prefix . "fmt_giveme_state.confirmed_flg <> 1 "
				. " OR " . $table_prefix . "fmt_giveme_state.confirmed_flg is NULL)";
	}
	return $where;
}

add_filter("posts_join", "add_costom_join");
add_filter("posts_where", "add_custom_where");

/**
 * 記事の状態を調べるユーティリティ関数群
 */


function isEntry($postID){
	global $wpdb;
	global $table_prefix;
	$sql = "SELECT entry_flg FROM " . $table_prefix . "fmt_giveme_state where post_id = %d";
	$state = $wpdb->get_var($wpdb->prepare($sql, $postID));
	return $state == 1;
}

function isGiveme($postID){
	global $wpdb;
	global $table_prefix;
	$sql = "SELECT giveme_flg FROM " . $table_prefix . "fmt_giveme_state where post_id = %d";
	$state = $wpdb->get_var($wpdb->prepare($sql, $postID));
	return $state == 1;
}

function isConfirm($postID){
	global $wpdb;
	global $table_prefix;
	$sql = "SELECT confirmed_flg FROM " . $table_prefix . "fmt_giveme_state where post_id = %d";
	$state = $wpdb->get_var($wpdb->prepare($sql, $postID));
	return $state == 1;
}

function isExhibiterEvaluated($postID){
	global $wpdb;
	global $table_prefix;
	$sql = "SELECT exhibiter_evaluated_flg FROM " . $table_prefix . "fmt_giveme_state where post_id = %d";
	$state = $wpdb->get_var($wpdb->prepare($sql, $postID));
	return $state == 1;
}

function isBidderEvaluated($postID){
	global $wpdb;
	global $table_prefix;
	$sql = "SELECT bidder_evaluated_flg FROM " . $table_prefix . "fmt_giveme_state where post_id = %d";
	$state = $wpdb->get_var($wpdb->prepare($sql, $postID));
	return $state == 1;
}

function isFinish($postID){
	global $wpdb;
	global $table_prefix;
	$sql = "SELECT finished_flg FROM " . $table_prefix . "fmt_giveme_state where post_id = %d";
	$state = $wpdb->get_var($wpdb->prepare($sql, $postID));
	return $state == 1;
}


/**
 * 現在の記事に対して「ください」済か調べる関数。
 * ユーザIDが空の場合、記事に対する「ください」全てを検索します。
 */
function doneGiveme($postID, $userID){
	global $wpdb;
	global $table_prefix;
	$sql = "SELECT count(*) FROM " . $table_prefix . "fmt_user_giveme where post_id = %d";
	if(strlen($userID) > 0){
		$sql .= " and user_id = %d";
	}
	$current_giveme = $wpdb->get_var($wpdb->prepare($sql, $postID, $userID));
	if($current_giveme == 0){
		return false;
	}else{
		return true;
	}
}

/**
 * くださいボタン押下時に呼ばれる関数。
 */
function giveme(){
	global $wpdb;
	global $table_prefix;
	$postID = $_POST['postID'];
	$userID = $_POST['userID'];

	//todoリストに追加
	if(!isGiveme($postID)){
		add_todo_confirm_bidder($postID);
	}

	//ください済み確認
	$current_giveme = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM " . $table_prefix . "fmt_user_giveme where user_id = %d and post_id = %d", $userID, $postID));
	if($current_giveme > 0){
		echo "既にくださいリクエスト済みです。";
		die;
	}
	
	// 記事の状態を「ください」に変更(現在の状態が無い場合はレコードを登録)
	$current_state = $wpdb->get_var($wpdb->prepare("SELECT giveme_flg FROM " . $table_prefix . "fmt_giveme_state where post_id = %d", $postID));
	if(!is_null($current_state)){
		$wpdb->query($wpdb->prepare("
			UPDATE " . $table_prefix . "fmt_giveme_state
			SET update_timestamp = current_timestamp,
			giveme_flg = 1
			WHERE post_id= %d",
			$postID));
	}else{
		$wpdb->query($wpdb->prepare("
			INSERT INTO " . $table_prefix . "fmt_giveme_state
			(update_timestamp, post_id, giveme_flg)
			VALUES (current_timestamp, %d, 1)",
			$postID));
	}
	
	// ログインユーザ→投稿記事に対して「ください」リクエストした記録をつける
	// 既にデータが登録済の場合は何もしません
	if($current_giveme == 0){
		$wpdb->query($wpdb->prepare("
			INSERT INTO " . $table_prefix . "fmt_user_giveme
			(update_timestamp, user_id, post_id)
			VALUES (current_timestamp, %d, %d)",
			$userID, $postID));
	}
	
	// 仮払ポイントを1p増
	add_temp_used_points($userID, 1);
	
	echo "くださいリクエストが送信されました。";

	
	die;
}

/**
 * くださいキャンセル時に呼ばれる関数。
 */
function cancelGiveme(){
	global $wpdb;
	global $table_prefix;
	$postID = $_POST['postID'];
	$userID = $_POST['userID'];

	//ください取消確認
	$current_giveme = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM " . $table_prefix . "fmt_user_giveme where post_id = %d", $postID));
	if($current_giveme == 0){
		echo "既にくださいが取消されています。";
		die;
	}


	// 「ください」リクエストの情報を削除
	$wpdb->query($wpdb->prepare("
		DELETE FROM " . $table_prefix . "fmt_user_giveme
		where user_id = %d
		and post_id = %d",
		$userID, $postID));
	
	// 「ください」件数が0になった場合はgiveme状態をオフにする
	$current_giveme = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM " . $table_prefix . "fmt_user_giveme where post_id = %d", $postID));
	if($current_giveme == 0){
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_giveme_state
		SET update_timestamp = current_timestamp, giveme_flg = 0
		WHERE post_id = %d",
		$postID));
	}
	
	// 仮払ポイントを1p減算
	add_temp_used_points($userID, -1);
	echo "くださいを取消しました。";

	//todoリストstatus="finished"
	if($current_giveme == 0){
		cancel_todo($postID);
	}

	die;
}

/**
 * くださいの取引相手確定時に呼ばれる関数。
 */
function confirmGiveme(){
	global $wpdb;
	global $table_prefix;
	$postID = $_POST['postID'];
	$userID = $_POST['userID'];
	//$exhibiter_userID = $_POST['euserID'];
	$uncheckedUserIDs = explode(",", $_POST['uncheckedUserIDs']);
	$tradeway = $_POST['tradeway'];
	$tradedates = explode(",", $_POST['tradedates']);
	$place = $_POST['place'];
	$message = $_POST['message'];

	// 記事の状態を確定済にする
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_giveme_state
		SET update_timestamp = current_timestamp,
		confirmed_flg = 1
		WHERE post_id = %d",
		$postID));
	
	// ユーザ確定情報の登録
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_user_giveme
		SET update_timestamp = current_timestamp,
		confirmed_flg = 1
		WHERE post_id = %d
		AND user_id = %d",
		$postID, $userID));

	// 取引履歴を登録
	$wpdb->query($wpdb->prepare("
		INSERT INTO " . $table_prefix . "fmt_trade_history
		(update_timestamp, post_id, bidder_id, exhibiter_id)
		VALUES (current_timestamp, %d, %d, %d)",
		$postID, $userID,get_post_author($postID)));

	// 取引相手の仮払ポイントを1p減算
	add_temp_used_points($userID, -1);
	// 取引相手の使用済ポイントをを1p加算
	add_used_points($userID, 1);
	
	
	foreach($uncheckedUserIDs as $uncheckedUserID){
		// 取引相手以外の仮払ポイントを1p減算
		add_temp_used_points($uncheckedUserID, -1);

		//取引相手以外にくださいが承認されなかった旨を通知
		$content_unchecked = '以下の商品に対するくださいは他のユーザーが承認されました' . PHP_EOL . '【商品名】:<a href="' . get_permalink($postID) . '"> '. get_post($postID)->post_title .'</a>';
		messages_new_message(array(
			'sender_id' => bp_loggedin_user_id(),
			'recipients' => $uncheckedUserID,
			'subject' => '【自動送信】くださいリクエストが承認されませんでした',
			'content' => $content_unchecked
			));

	}

	// 取引相手に確定されたことを通知
	$content = 'あなたが以下の商品の取引相手に選ばれました！' . PHP_EOL . ' 【商品名】:<a href="' . get_permalink($postID) . '">' . get_post($postID)->post_title . '</a>' . PHP_EOL;
	if($message){
		$content .= '【メッセージ】:' . $message . PHP_EOL;
	}

	$message_ID = messages_new_message(array(
					'sender_id' => bp_loggedin_user_id(),
					'recipients' => $userID,
					'subject' => '【自動送信】くださいリクエストが承認されました！',
					'content' => $content
					));

	echo "confirm";

	//todoリストの状態をfinishedにする
	$todo_row = get_todo_row(get_post_author($postID), $postID);
	$todoID = $todo_row->todo_id;
	change_todo_status_finished($todoID);

	//todoリストに追加
	add_todo_finish_trade($postID);
	add_todo_dealing($postID, $message_ID);

	die;
}

/**
 * 出品者評価時に呼ばれる関数
 */
function exhibiter_evaluation(){
	global $wpdb;
	global $table_prefix;
	$userID = $_POST['userID'];
	$postID = $_POST['postID'];
	$score  = $_POST['score'];
	$comment  = $_POST['comment'];

	// 記事の状態を出品者評価済に変更
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_giveme_state
		SET update_timestamp = current_timestamp,
		exhibiter_evaluated_flg = 1
		WHERE post_id = %d",
		$postID));
	
	// 取引履歴を更新
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_trade_history
		SET update_timestamp = current_timestamp,
		exhibiter_score = %d,
		exhibiter_comment = %s
		WHERE post_id = %d",
		$score, $comment, $postID));

	//todoリストの状態をfinishedにする
	$todo_row = get_todo_row($userID, $postID);
	$todoID = $todo_row->todo_id;
	change_todo_status_finished($todoID);
	
	die;
}

/**
 * 落札者評価時に呼ばれる関数
 */
function bidder_evaluation(){
	global $wpdb;
	global $table_prefix;
	$userID = $_POST['userID'];
	$postID = $_POST['postID'];
	$score  = $_POST['score'];
	$comment  = $_POST['comment'];

	// 記事の状態を落札者評価済に変更
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_giveme_state
		SET update_timestamp = current_timestamp,
		bidder_evaluated_flg = 1
		WHERE post_id = %d",
		$postID));

	// 取引履歴を更新
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_trade_history
		SET update_timestamp = current_timestamp,
		bidder_score = %d,
		bidder_comment = %s
		WHERE post_id = %d",
		$score, $comment, $postID));

	//todoリストの状態をfinishedにする、評価後
	$todo_row = get_todo_row($userID, $postID);
	$todoID = $todo_row->todo_id;
	change_todo_status_finished($todoID);

	
	die;
}


/**
 * 取引完了時に呼ばれる関数。
 */
function finish(){
	global $wpdb;
	global $table_prefix;
	$postID = $_POST['postID'];
	$userID = $_POST['userID'];
	
	// 記事の状態を取引完了済に変更
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_giveme_state
		SET update_timestamp = current_timestamp,
		finished_flg = 1
		WHERE post_id = %d",
		$postID));

	//todoリストの状態をfinishedにする-exhibiter
	$todo_row = get_todo_row($userID, $postID);
	$todoID = $todo_row->todo_id;
	change_todo_status_finished($todoID);


	//todoリストの状態をfinishedにする-bidder
	$deal = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$table_prefix."fmt_trade_history 
		   	WHERE post_id=%d",
		   	$postID));
	$bidder_userID = $deal->bidder_id;
	$todo_row = get_todo_row($bidder_userID, $postID);
	$todoID = $todo_row->todo_id;
	change_todo_status_finished($todoID);

	//todoリストに追加
	add_todo_evaluate_bidder($userID, $postID);
	add_todo_evaluate_exhibitor($postID);

	

	die;
}

function set_giveme_flg($post_id, $val){
	global $wpdb;
	global $table_prefix;
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_giveme_state
		SET update_timestamp = current_timestamp,
		giveme_flg = %d
		WHERE post_id = %d",
		$val, $post_id));	
}

function set_confirmed_flg($post_id, $user_id, $val){
    global $wpdb;//WordPressでDBを操作するオブジェクト
	global $table_prefix;//テーブルの接頭辞

	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_giveme_state
		SET update_timestamp = current_timestamp,
		confirmed_flg = %d
		WHERE post_id = %d",
		$val, $post_id));//prepared statement

	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_user_giveme
		SET update_timestamp = current_timestamp,
		confirmed_flg = %d
		WHERE post_id = %d
		AND user_id = %d",
		$val, $post_id, $user_id));//prepared statement
}

function delete_trade_history($post_id){
    global $wpdb;//WordPressでDBを操作するオブジェクト
	global $table_prefix;//テーブルの接頭辞

	$wpdb->query($wpdb->prepare("
		DELETE FROM ". $table_prefix . "fmt_trade_history
		WHERE post_id = %d",
		$post_id));
}

function delete_user_giveme($post_id, $user_id=""){
    global $wpdb;//WordPressでDBを操作するオブジェクト
	global $table_prefix;//テーブルの接頭辞

	$sql = "DELETE FROM ". $table_prefix . "fmt_user_giveme WHERE post_id = %d";
	if($user_id){
		$sql .= " AND user_id = %d";
		$wpdb->query($wpdb->prepare($sql, $post_id, $user_id));
	}else{
		$wpdb->query($wpdb->prepare($sql, $post_id));
	}
}

function cancel_trade($post_id){
	$confirmed_user_id = get_bidder_id($post_id);

	//実際のキャンセル処理

	//todoリストstatus="finished"
	cancel_todo($post_id);

	//商品の確定済フラグを下げる
	set_confirmed_flg($post_id, $confirmed_user_id, 0);

	// 取引履歴の削除
	delete_trade_history($post_id);

	// givemeの履歴削除
	delete_user_giveme($post_id);
	set_giveme_flg($post_id, 0);

	// 取引相手の使用済みポイントを1p減算
	add_used_points($confirmed_user_id, -1);
	// 取引相手の獲得ポイントを1p加算
	add_got_points($confirmed_user_id, 1);

}

function cancel_trade_from_exhibitor(){
   $post_id = $_POST['postID'];
   $bidder_id = get_bidder_id($post_id);
   cancel_trade($post_id);
   messages_new_message(array(
		'sender_id' => bp_loggedin_user_id(),
		'recipients' => $bidder_id,
		'subject' => '【自動送信】取引がキャンセルされました',
		'content' => '以下の商品の取引がキャンセルされました。' .
						'<a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a>'
	));

	echo "取引をキャンセルしました。";

	echo "出品者から取引をキャンセルしました。";
	die;
}

function cancel_trade_from_bidder(){
    $post_id = $_POST['postID'];
	cancel_trade($post_id);
	messages_new_message(array(
		'sender_id' => bp_loggedin_user_id(),
		'recipients' => get_post($post_id)->post_author,
		'subject' => '【自動送信】取引がキャンセルされました',
		'content' => '以下の商品の取引がキャンセルされました。' .
						'<a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a>'
	));
	
	echo "落札者から取引をキャンセルしました。";

	//todoリストstatus="finished"
	//cancel_todo($post_id);

	die;
}

function insert_attachment($file_handler,$post_id,$setthumb='false'){  
	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();
	
	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	$attach_id = media_handle_upload($file_handler, $post_id);

	if ($setthumb){
		update_post_meta($post_id,'_thumbnail_id',$attach_id);
	}
	return $attach_id;
}

/**
 * Override original media_sideload_image method
 * to avoid 404 error with filename containing '%'.
 *
 * @param string $file The URL of the image to download
 * @param int $post_id The post ID the media is to be associated with
 * @param string $desc Optional. Description of the image
 * @return string|WP_Error Populated HTML img tag on success
 */
function fcl_media_sideload_image($file, $post_id, $desc = null) {
	if ( ! empty($file) ) {
		// Download file to temp location
		$tmp = download_url( $file );

		// Set variables for storage
		// fix file filename for query strings
		preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
		// Override
		// replace '%' to blank
		$file_array['name'] = validate_filename(basename($matches[0]));
		$file_array['tmp_name'] = $tmp;

		// If error storing temporarily, unlink
		if ( is_wp_error( $tmp ) ) {
			@unlink($file_array['tmp_name']);
			$file_array['tmp_name'] = '';
		}

		// do the validation and storage stuff
		$id = media_handle_sideload( $file_array, $post_id, $desc );
		// If error storing permanently, unlink
		if ( is_wp_error($id) ) {
			@unlink($file_array['tmp_name']);
			return $id;
		}

		$src = wp_get_attachment_url( $id );
	}

	// Finally check to make sure the file has been saved, then return the html
	if ( ! empty($src) ) {
		$alt = isset($desc) ? esc_attr($desc) : '';
		$html = "<img src='$src' alt='$alt' />";
		return $html;
	}
}
/**
 * Use to validate filename.
 * When uploading file, you must use this.
 * Why this was created is that names containing '%' cause 404 error.
 * If there are any additional conditions, fix this function.
 * @param  {String} name of file
 * @return {String} validated name
 */
function validate_filename($filename){
	return str_replace('%', '', $filename);
}

/**
 * 新規出品時に呼ばれる関数
 */
function new_entry(){
	global $bp;
	$exhibitor_id = $_POST['exhibitor_id'];

	$insert_id = exhibit(array(
		'exhibitor_id' => $exhibitor_id,
		'item_name' => $_POST['field_1'],
		'item_description' => $_POST['field_2'],
		'item_category' => isset($_POST['subcategory'])?$_POST['subcategory']:"1",
		'tags' => $_POST['field_4']
	));

	if($insert_id){
		// success
		// add custom field
		add_post_meta($insert_id, "item_status", $_POST["item_status"], true);
		add_post_meta($insert_id, "department", xprofile_get_field_data('学部' ,$exhibitor_id), true);
		add_post_meta($insert_id, "course", xprofile_get_field_data('学科' ,$exhibitor_id), true);

		if($_POST['wanted_item_id']){
			add_post_meta($insert_id, "wanted_item_id", $_POST['wanted_item_id'], true);			
		}

		// image upload
		global $post;
		if($_FILES){
			$files = $_FILES['upload_attachment'];
			// reverse sort
			arsort($files['name'],SORT_NUMERIC);
			arsort($files['type'],SORT_NUMERIC);
			arsort($files['tmp_name'],SORT_NUMERIC);
			arsort($files['error'],SORT_NUMERIC);
			arsort($files['size'],SORT_NUMERIC);

			foreach ($files['name'] as $key => $value){
				if ($files['name'][$key]){
					$file = array(
						'name'     => validate_filename($files['name'][$key]),
						'type'     => $files['type'][$key],
						'tmp_name' => $files['tmp_name'][$key],
						'error'    => $files['error'][$key],
						'size'     => $files['size'][$key]
					);  
					$_FILES = array("upload_attachment" => $file);
					foreach ($_FILES as $file => $array){
						$newupload = insert_attachment($file,$insert_id);
					}
				}
			}
		}
	}else{
	// failure
	}
	echo "";
	die;
}

function exhibit_to_wanted(){
	$exhibitor_id = $_POST['exhibitor_id'];
	$asin = $_POST['asin'];

	$insert_id = exhibit(array(
		'exhibitor_id' => $exhibitor_id,
		'item_name' => $_POST['field_1'],
		'item_description' => $_POST['field_2'],
		'item_category' => $_POST['field_3'],
		'tags' => $_POST['field_4'],
		'image_url' => $_POST['image_url']
	));

	if($insert_id){
		// success
		// add custom field
		add_post_meta($insert_id, "item_status", $_POST["item_status"], true);
		add_post_meta($insert_id, "department", xprofile_get_field_data('学部' ,$exhibitor_id), true);
		add_post_meta($insert_id, "course", xprofile_get_field_data('学科' ,$exhibitor_id), true);
		add_post_meta($insert_id, "wanted_item_id", $_POST['wanted_item_id'], true);
		add_post_meta($insert_id, "asin", $asin, true);
	}

	$recipients = get_others_wanted_list(array(
		'user_id' => $exhibitor_id,
		'asin' => $asin
		));
	foreach ($recipients as $recipient) {
		// 出品があった旨を通知
		messages_new_message(array(
		'sender_id' => $exhibitor_id,
		'recipients' => $recipient->user_id,
		'subject' => 'あなたのほしいものが出品されました！',
		'content' => bp_core_get_userlink($exhibitor_id) . 'さんが、あなたのほしいものを出品しました。くださいしてみましょう！' . PHP_EOL .
						'<a href="' . get_permalink($insert_id) . '">' . get_post($insert_id)->post_title . '</a>'
		));
	}
	echo $insert_id;
	die;
}



/*************************************
 * functions used from smartphone app
 *************************************/


/**
 * exhibition method called from app.
 * 
 */
function exhibit_from_app(){
	// $exhibitor = get_user_by('login', $_POST['exhibitor_id']);
	// if(!$exhibitor || !wp_check_password($_POST['password'], $exhibitor->data->user_pass, $exhibitor->ID)){
	// 	echo "ユーザ名とパスワードの組合せが不正です。";
	// 	die;
	// }

	$current_user_id = get_current_user_id();

	if($current_user_id === 0){
		echo "ログインされていないため出品できません。";
		die;
	}

	$insert_id = exhibit(array(
		'exhibitor_id' => $current_user_id,
		'item_name' => $_POST['item_name'],
		'image_url' => $_POST['image_url'],
		'department' => xprofile_get_field_data('学部', $current_user_id),
		'course' => xprofile_get_field_data('学科', $current_user_id),
	));

	if($insert_id !== 0){
		echo "出品を完了しました！";
	}else{
		echo "出品に失敗しました。しばらくたってから再度試してください。";
	}
	die;
}

/**
 * register or update information used for app.
 * infrormation includes;
 * - device_token
 */
function register_app_information(){
	// add device_token key as a user meta data
	global $current_user;
	if(get_user_meta($current_user->get('ID'), 'device_token')){
		update_user_meta($current_user->get('ID'), 'device_token', $_POST['deviceToken']);
	}else{
		add_user_meta($current_user->get('ID'), 'device_token', $_POST['deviceToken'], true);
	}
	die;
}

add_action('messages_message_sent', 'do_action_after_message_sent', 10, 1);

function do_action_after_message_sent($sent_message){
	$to_tokens = array();
	foreach ($sent_message->recipients as $recipient) {
		$token = get_user_meta($recipient->user_id, 'device_token', true);
		if(strlen($token) > 0){
			$to_tokens[] = $token;
		}
		send_push_notification($token, array(
			'alert'		=> 'メッセージが届きました！',
			'title'		=> $sent_message->subject,
			'vibrate'	=> 'true',
			'sound'		=> 'default',
			'badge'		=> messages_get_unread_count($recipient->user_id) + get_todo_list_count($recipient->user_id)
		));
	}
}

/**
 * send push notification.<br/>
 * Valid parameters should be refered at Titanium documents.
 * 
 * @param {String} recipients comma separated device tokens
 * @param {array} parameters
 * 
 */
function send_push_notification($recipients, $args){
	/*** SETUP ***************************************************/
    $key        = get_option('acs_app_key');
    $username   = get_option('acs_user_name');
    $password   = get_option('acs_password');
    $channel    = "news_alerts";
    $alert    	= isset($args['alert'])?$args['alert']:'';
    $title      = isset($args['title'])?$args['title']:'';
    $tmp_fname  = 'cookie.txt';
    $json = '';

    foreach ($args as $k => $v) {
    	if(strlen($json) > 0) {
    		$json .= ',';
    	}
    	$json = $json .'"' . $k . '":"' . $v . '"';
    }
    $json = '{' . $json . '}';

    /*** PUSH NOTIFICATION ***********************************/
 
    $post_array = array('login' => $username, 'password' => $password);
 
    /*** INIT CURL ******************************************/
    $curlObj    = curl_init();
    curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, false);
    $c_opt      = array(CURLOPT_URL => 'https://api.cloud.appcelerator.com/v1/users/login.json?key='.$key,
                        CURLOPT_COOKIEJAR => $tmp_fname, 
                        CURLOPT_COOKIEFILE => $tmp_fname, 
                        CURLOPT_RETURNTRANSFER => true, 
                        CURLOPT_POST => 1,
                        CURLOPT_POSTFIELDS  =>  "login=".$username."&password=".$password,
                        CURLOPT_FOLLOWLOCATION  =>  1,
                        CURLOPT_TIMEOUT => 60);
 
    /*** LOGIN **********************************************/
    curl_setopt_array($curlObj, $c_opt); 
    $session = curl_exec($curlObj);

    /*** SEND PUSH ******************************************/
    $c_opt[CURLOPT_URL]         = "https://api.cloud.appcelerator.com/v1/push_notification/notify_tokens.json?key=".$key; 
    $c_opt[CURLOPT_POSTFIELDS]  = "channel=".$channel."&to_tokens=".$recipients."&payload=".urlencode($json); 
 
    curl_setopt_array($curlObj, $c_opt); 
    $session = curl_exec($curlObj);
    /*** THE END ********************************************/
    curl_close($curlObj);
}


/**
 * ログインユーザの未読メッセージ件数および未完了next action件数の合計を通知します。メッセージは通知されません。
 * メッセージを既読にしたときや削除したときなど、メッセージを通知する必要のないときに呼び出してください。
 * Push notification of the count of unread messages and unfinished actions
 * of the login user. This sends only the count number, does not include any messages.
 * This should be called when a message is read or deleted.
 */
function push_updated_count(){
	global $user_ID;
	$token = get_user_meta($user_ID, 'device_token', true);
	if($token){
		send_push_notification($token, array(
			'badge'		=> messages_get_unread_count($user_ID) + get_todo_list_count($user_ID)
		));
	}
}
add_action('messages_delete_thread', 'push_updated_count'); 
add_action('messages_action_conversation', 'push_updated_count');
add_action('wp_login', 'push_updated_count');

function delete_post(){
	wp_delete_post($_POST['postID']);
	// minus point on delete post
	add_got_points($_POST['userID'], -1 * get_option('exhibition-point'));
	die;
}

function update_comment(){
	$comment_ID = $_POST['comment_ID'];
	$comment_content = $_POST['comment_content'];
	wp_update_comment(array(
			'comment_ID' => $comment_ID,
			'comment_content' => $comment_content
		));
	die;
}

function search_wantedbook(){
	$xml = get_search_result_from_amazon(array('keyword' => $_POST['keyword']));
	$items = $xml->Items->Item;
	$return = '';
	foreach ($items as $item) {
		$return .= create_item_detail($item);
	}
	echo $return;
	die;
}

function search_wantedlist(){
	$keyword = isset($_POST['keyword'])?$_POST['keyword']:"";
	$page = isset($_POST['page'])?$_POST['page']:0;

	$items = get_others_wanted_list(array(
		'keyword' => $keyword,
		'page' => $page,
		'count' => true));
	$next_page = $page + 1;
	$previous_page = $page - 1;
	$return = '';
	foreach ($items as $item) {
		$return .= create_wanted_item_detail($item);
	}
	if(get_others_wanted_list(array(
		'keyword' => $keyword,
		'page' => $next_page,
		))){
		$return .= '<div class="alignleft"><a href="#" onClick="onClickSearchWantedList(' . $next_page .')">次の10件</a></div>';
	}
	if($previous_page >= 0){
		$return .= '<div class="alignright"><a href="#" onClick="onClickSearchWantedList(' . $previous_page .')">前の10件</a></div>';
	}
	echo $return;
	die;
}


//ホームにほしいものリストを表示
function home_wantedlist(){
	$lists = get_others_wanted_list(array(
		'page'       => $_POST['page'],
		'count'      => true));
	$return = '';
	$i = 0;
	foreach($lists as $list){
		if( $i >= 9 ){
			break;
		}
		$return .= create_wanted_item_detail_home($list);
		$i += 1; 
	}
	echo $return;
	die;
}
add_action('wp_ajax_nopriv_home_wantedlist','home_wantedlist');
add_action('wp_ajax_home_wantedlist','home_wantedlist');

//ほしいものリストレイアウト
function create_wanted_item_detail_home($list){
	$return = '';
	$return .= '<div class="wantedlist_detail" id="'. $list->wanted_item_id .'">';
	//画像
	$return .= '<a href="' . home_url() .'/wanted-list?item_name='. urlencode($list->item_name) .'"><img class="wantedlist_image" src="' .$list->image_url .'">';
	return $return;
}


function add_wanted_item(){
	global $wpdb;
	global $table_prefix;
	global $user_ID;
	$wpdb->query($wpdb->prepare("
		INSERT INTO " . $table_prefix . "fmt_wanted_list
		(update_timestamp, user_id, item_name, ASIN, image_url) 
		VALUES (current_timestamp, %d, %s, %s, %s)",
		$user_ID, $_POST['item_name'], $_POST['asin'], $_POST['image_url']));
	die;
}

function del_wanted_item_by_asin(){
	global $wpdb;
	global $table_prefix;
	global $user_ID;
	$wpdb->query($wpdb->prepare("
		DELETE FROM " . $table_prefix . "fmt_wanted_list 
		WHERE user_id = %d
		AND ASIN = %s",
		$user_ID, $_POST['asin']));
	die;
}

/**********************************************
 * Using Amazon API
 **********************************************
 */

function get_search_result_from_amazon($array){
	$accesss_key_id = get_option('amazon_accesss_key_id'); 
	$secret_access_key = get_option('amazon_secret_access_key');
	$associate_tag = get_option('amazon_associate_tag');

	$baseurl = 'http://ecs.amazonaws.jp/onca/xml';
	$params = array();
	$params['Service'] = 'AWSECommerceService';
	$params['AWSAccessKeyId'] = $accesss_key_id;
	$params['AssociateTag'] = $associate_tag;
	$params['Version'] = '2009-03-31';
	$params['Operation'] = 'ItemSearch';
	$params['ResponseGroup'] = 'Images,ItemAttributes';
	$params['SearchIndex'] = 'Books';
	if($array['keyword']){
		$params['Keywords'] = $array['keyword'];
	}
	$params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');

	ksort($params);

	$canonical_string = '';
	foreach ($params as $k => $v ) {
		$canonical_string .= '&' . urlencode_rfc2986($k) . '=' . urlencode_rfc2986($v);
	}

	$canonical_string = substr($canonical_string, 1);
	$parsed_url = parse_url($baseurl);
	$string_to_sign = "GET\n{$parsed_url['host']}\n{$parsed_url['path']}\n{$canonical_string}";
	$signature = base64_encode(hash_hmac('sha256', $string_to_sign, $secret_access_key, true));

	$url = $baseurl . '?' . $canonical_string . '&Signature=' . urlencode_rfc2986($signature);
	$xml = @simplexml_load_file($url);
	return $xml;
}

function urlencode_rfc2986($str){
	return str_replace('%7E', '~', rawurlencode($str));
}

function create_item_detail($item){
	$return = '';
	if($item->MediumImage->URL){
		$return .= '<div class="item_detail" id="' . $item->ASIN . '" style="height:' . $item->MediumImage->Height .'px;margin:15px 5px 15px 5px;">';
		$return .= '<img src="' . $item->MediumImage->URL . '" style="float:left;">';
	}else{
		$return .= '<div class="item_detail" id="' . $item->ASIN . '" style="height:160px;margin:15px 5px 15px 5px;">';
		$return .= '<img src="' . get_stylesheet_directory_uri() . '/images/noimg.jpg" style="float:left;">';
	}
	$return .= '<div id="name_'. $item->ASIN .'">' . $item->ItemAttributes->Title . '</div>';
	$return .= '<ul>';
	if($item->ItemAttributes->Author){
		$return .= '<li name="author">著者:' . $item->ItemAttributes->Author . '</li>';
	}
	if($item->ItemAttributes->Publisher){
		$return .= '<li>出版社:' . $item->ItemAttributes->Publisher . '</li>';
	}
	if($item->ItemAttributes->ReleaseDate){
		$return .= '<li>発行日:' . $item->ItemAttributes->ReleaseDate . '</li>';
	}
	if($item->ItemAttributes->ListPrice->FormattedPrice){
		$return .= '<li>価格:' . $item->ItemAttributes->ListPrice->FormattedPrice . '</li>';
	}
	$return .= '</ul>';
	if(get_wanted_item_by_asin($item->ASIN)){
		$return .= '<input type="button" class="button_del_wanted" id="button_'. $item->ASIN .'" value="追加済" asin="' . $item->ASIN .'">';
	}else{
		$return .= '<input type="button" class="button_add_wanted" id="button_'. $item->ASIN .'" value="追加" asin="' . $item->ASIN .'">';
	}
	$return .= '</div>';
	return $return;
}

/**********************************************
 * 取引状態確認用関数群
 **********************************************
 */

/**
 * 記事IDから取引相手のユーザIDを取得する関数
 */
function get_bidder_id($post_id){
	global $wpdb;
	global $table_prefix;
	$confirmed_user_id = $wpdb->get_var($wpdb->prepare("
		SELECT user_id
		FROM " . $table_prefix . "fmt_user_giveme
		WHERE post_id = %d
		AND confirmed_flg = 1",
		$post_id));
	return $confirmed_user_id;
}

/**********************************************
 * 評価関連関数群
 **********************************************
 */

/**
 * 出品者、落札者全体の平均評価点数を取得します。
 */
function get_average_score($user_id){
	if(get_count_evaluation($user_id) == 0){
		return "0.00";
	}
	return (get_average_exhibiter_score($user_id) * get_count_exhibiter_evaluation($user_id)
				+ get_average_bidder_score($user_id) * get_count_bidder_evaluation($user_id))
				/ (get_count_exhibiter_evaluation($user_id) + get_count_bidder_evaluation($user_id));
}

/**
 * 出品者としての平均の評価点数を取得します。
 * スコアが0のレコードは評価未実施のため対象外です。
 */

function get_average_exhibiter_score($user_id){
	global $wpdb;
	global $table_prefix;
	$average_score = $wpdb->get_var($wpdb->prepare("
		SELECT TRUNCATE(AVG(exhibiter_score),2)
		FROM " . $table_prefix . "fmt_trade_history
		WHERE " . $table_prefix . "fmt_trade_history.exhibiter_id = %d
		and exhibiter_score <> 0
		GROUP BY exhibiter_id",
		$user_id));
	if(is_null($average_score)){
		$average_score = 0;
	}
	return $average_score;
}


/**
 * 落札者としての平均の評価点数を取得します。
 * スコアが0のレコードは評価未実施のため対象外です。
 */

function get_average_bidder_score($user_id){
	global $wpdb;
	global $table_prefix;
	$average_score = $wpdb->get_var($wpdb->prepare("
		SELECT TRUNCATE(AVG(bidder_score),2)
		FROM " . $table_prefix . "fmt_trade_history
		WHERE " . $table_prefix . "fmt_trade_history.bidder_id = %d
		AND bidder_score <> 0
		GROUP BY bidder_id",
		$user_id));
	if(is_null($average_score)){
		$average_score = 0;
	}
	return $average_score;
}

/**
 * 出品者、落札者全体の評価件数を取得します。
 */
function get_count_evaluation($user_id){
	return get_count_exhibiter_evaluation($user_id) + get_count_bidder_evaluation($user_id);
}


/**
 * 出品者としての評価件数を取得します。
 * スコアが0のレコードは評価未実施のため対象外です。
 */
function get_count_exhibiter_evaluation($user_id){
	global $wpdb;
	global $table_prefix;
	$count = $wpdb->get_var($wpdb->prepare("
		SELECT count(*)
		FROM " . $table_prefix . "fmt_trade_history
		WHERE " . $table_prefix . "fmt_trade_history.exhibiter_id = %d
		and exhibiter_score <> 0
		GROUP BY exhibiter_id",
		$user_id));
	if(is_null($count)){
		$count = 0;
	}
	return $count;
}

/**
 * 落札者としての評価件数を取得します。
 * スコアが0のレコードは評価未実施のため対象外です。
 */
function get_count_bidder_evaluation($user_id){
	global $wpdb;
	global $table_prefix;
	$count = $wpdb->get_var($wpdb->prepare("
		SELECT count(*)
		FROM " . $table_prefix . "fmt_trade_history
		WHERE " . $table_prefix . "fmt_trade_history.bidder_id = %d
		and bidder_score <> 0
		GROUP BY bidder_id",
		$user_id));
	if(is_null($count)){
		$count = 0;
	}
	return $count;
}

/**********************************************
 * ポイント関連関数群
 **********************************************
 */

/**
 * ユーザが使用可能なポイントを取得する関数
 */
function get_usable_point($user_id){
	global $wpdb;
	global $table_prefix;
	$usable_point = $wpdb->get_var($wpdb->prepare("
		SELECT got_points - temp_used_points - used_points
		FROM " . $table_prefix . "fmt_points
		WHERE user_id = %d",
		$user_id));
	return $usable_point;
}

/**
 * 仮払状態のポイントを取得する関数
 */
function get_temp_used_point($user_id){
	global $wpdb;
	global $table_prefix;
	$temp_used_points = $wpdb->get_var($wpdb->prepare("
		SELECT temp_used_points
		FROM " . $table_prefix . "fmt_points
		WHERE user_id = %d",
		$user_id));
	return $temp_used_points;
}


/**
 * 獲得ポイントを加算する関数。
 */
function add_got_points($user_id, $point){
	global $wpdb;
	global $table_prefix;
	// 引数の数だけ獲得ポイントを加算
	// 引数がマイナスの場合減算されます
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_points
		SET update_timestamp = current_timestamp,
		got_points = got_points + %d
		WHERE user_id = %d",
		$point, $user_id));
}

/**
 * 仮払ポイントを加算する関数。
 */
function add_temp_used_points($user_id, $point){
	global $wpdb;
	global $table_prefix;
	// 引数の数だけ仮払ポイントを加算
	// 引数がマイナスの場合減算されます
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_points
		SET update_timestamp = current_timestamp,
		temp_used_points = temp_used_points + %d
		WHERE user_id = %d",
		$point, $user_id));
}

/**
 * 使用済ポイントを加算する関数。
 */
function add_used_points($user_id, $point){
	global $wpdb;
	global $table_prefix;
	// 引数の数だけ使用済ポイントを加算
	// 引数がマイナスの場合減算されます
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_points
		SET update_timestamp = current_timestamp,
		used_points = used_points + %d
		WHERE user_id = %d",
		$point, $user_id));
}

/**
 * 新規ユーザ追加時に呼ばれる関数。
 * 初期付与ポイントはgot_pointsの値で設定してください。
 */
function on_user_added($user_id){
	global $wpdb;
	global $table_prefix;
	// ユーザ追加時にポイントを付与
	$wpdb->query($wpdb->prepare("
		INSERT INTO " . $table_prefix . "fmt_points
		(update_timestamp, user_id, got_points, temp_used_points, used_points)
		VALUES (current_timestamp, %d, %d, 0, 0)",
		$user_id, get_option('register-point')));
	// メッセージを送付
	if(get_option('newuser_message_sender') != -1){
		messages_new_message(array(
			'sender_id' => get_option('newuser_message_sender'),
			'recipients' => $user_id,
			'subject' => '登録ありがとうございます！',
			'content' => get_option('newuser_message_content')
			));
	}
	//メールマガジンを受け取る処理もし入れなかったらデフォルトでオンにする
	$get_mail_magazine = xprofile_get_field_data('メールマガジンを受け取りますか？' ,$user_id);
	if($get_mail_magazine == ""){
		xprofile_set_field_data( 'メールマガジンを受け取りますか？',$user_id, '受け取る');
	}
}

/**********************************************
 * ユーティリティ関数群
 **********************************************
 */

/**
 * 記事IDから投稿者IDを取得する関数。
 */
function get_post_author($post_id){
	global $wpdb;
	global $table_prefix;
	$author_id = $wpdb->get_var($wpdb->prepare("
		SELECT post_author
		FROM " . $table_prefix . "posts
		WHERE ID = %d",
		$post_id));
	return $author_id;
}

/**
 * 商品状態を表示用文字列に変換する関数。
 */
function get_display_item_status($item_status){
	$display_map = array(
			"verygood"  => "良",
			"good"		=> "可",
			"bad"		=> "悪",
		);
	return $display_map[$item_status];
}

/**
 * 取引方法を表示用文字列に変換する関数。
 */
function get_display_tradeway($tradeway){
	$display_map = array(
			"handtohand" => "直接手渡し",
			"delivery"  => "配送"
		);
	return $display_map[$tradeway];
}


/**********************************************
 * ユーザページカスタマイズ用関数群
 **********************************************
 */

function fmt_messages_screen_conversation() {

	// Bail if not viewing a single message
	if ( !bp_is_messages_component() || !bp_is_current_action( 'view' ) )
		return false;

	$thread_id = (int) bp_action_variable( 0 );

	if ( empty( $thread_id ) || !messages_is_valid_thread( $thread_id ) || ( !messages_check_thread_access( $thread_id ) && !bp_current_user_can( 'bp_moderate' ) ) )
		bp_core_redirect( trailingslashit( bp_displayed_user_domain() . bp_get_messages_slug() ) );

	// Load up BuddyPress one time
	$bp = buddypress();

	// Decrease the unread count in the nav before it's rendered
	/*** custom begin ***/
	if(bp_get_total_unread_messages_count() > 0){
		$bp->bp_nav[$bp->messages->slug]['name'] = sprintf( __( 'Messages <span>%s</span>', 'buddypress' ), bp_get_total_unread_messages_count() );
	}else{
		$bp->bp_nav[$bp->messages->slug]['name'] = sprintf( __( 'Messages', 'buddypress' ));		
	}
	/*** custom end ***/
	do_action( 'messages_screen_conversation' );

	bp_core_load_template( apply_filters( 'messages_template_view_message', 'members/single/home' ) );
}

remove_action( 'bp_screens', 'messages_screen_conversation' );
add_action( 'bp_screens', 'fmt_messages_screen_conversation' );

/**
 * マイページのメニューを編集
 */
function my_setup_nav() {
	global $bp;
	global $user_ID;

	// メッセージ「作成」メニューを削除
	if(!current_user_can('administrator')){
		bp_core_remove_subnav_item(BP_MESSAGES_SLUG, 'compose');
	}

	// override buddypress default mypage menu
	$messages_name;
	if(bp_get_total_unread_messages_count() > 0){
		$messages_name = sprintf( __( 'Messages <span>%s</span>', 'buddypress' ), bp_get_total_unread_messages_count() );
	}else{
		$messages_name = sprintf( __( 'Messages', 'buddypress' ));
	}
	bp_core_new_nav_item(array(
		'name'                    => $messages_name,
		'slug'                    => BP_MESSAGES_SLUG,
		'position'                => 50,
		'show_for_displayed_user' => false,
		'screen_function'         => 'messages_screen_inbox',
		'default_subnav_slug'     => 'inbox',
		'item_css_id'			  => $bp->messages->id
	));

	// ログインユーザのプロフィールにのみ表示させるメニュー。
	if($user_ID == bp_displayed_user_id()){
		$entry_list_name;
		if(has_todo_in_entry_list()){
			$entry_list_name = '出品一覧<span>！</span>';
		}else{
			$entry_list_name = '出品一覧';
		}
		bp_core_new_nav_item( array( 
			'name' => $entry_list_name, 
			'slug' => 'entry_list', 
			'position' => 55,
			'screen_function' => 'entry_list_notinprogress_link',
			'show_for_displayed_user' => true,
			'item_css_id' => 'entry-list'
			) );
		bp_core_new_subnav_item( array( 
			'name' => __( 'ください待ち', 'buddypress' ), 
			'slug' => 'notinprogress', 
			'parent_url' => trailingslashit($bp->displayed_user->domain . 'entry_list'),
			'parent_slug' => 'entry_list',
			'position' => 56,
			'screen_function' => 'entry_list_notinprogress_link',
			'item_css_id' => 'notinprogress'
		) );
		bp_core_new_subnav_item( array( 
			'name' => __( '取引相手確定待ち', 'buddypress' ), 
			'slug' => 'toconfirm', 
			'parent_url' => trailingslashit($bp->displayed_user->domain . 'entry_list'),
			'parent_slug' => 'entry_list',
			'position' => 57,
			'screen_function' => 'entry_list_toconfirm_link',
			'item_css_id' => 'toconfirm'
		) );
		bp_core_new_subnav_item( array( 
			'name' => __( '取引中', 'buddypress' ), 
			'slug' => 'inprogress', 
			'parent_url' => trailingslashit($bp->displayed_user->domain . 'entry_list'),
			'parent_slug' => 'entry_list',
			'position' => 58,
			'screen_function' => 'entry_list_inprogress_link',
			'item_css_id' => 'inprogress'
		) );
		bp_core_new_subnav_item( array( 
			'name' => __( '取引完了', 'buddypress' ), 
			'slug' => 'finished', 
			'parent_url' => trailingslashit($bp->displayed_user->domain . 'entry_list'),
			'parent_slug' => 'entry_list',
			'position' => 59,
			'screen_function' => 'entry_list_finished_link',
			'item_css_id' => 'finished'
		) );

		bp_core_new_nav_item( array( 
			'name' => __( '新規出品', 'buddypress' ), 
			'slug' => 'new_entry', 
			'position' => 65,
			'screen_function' => 'new_entry_link',
			'show_for_displayed_user' => true,
			'item_css_id' => 'new-entry'
			) );

		bp_core_new_subnav_item( array( 
			'name' => __( '通常出品', 'buddypress' ), 
			'slug' => 'normal', 
			'parent_url' => trailingslashit($bp->displayed_user->domain . 'new_entry'),
			'parent_slug' => 'new_entry',
			'position' => 66,
			'screen_function' => 'new_entry_link',
			'item_css_id' => 'new-entry'
		) );
	
		bp_core_new_subnav_item( array( 
			'name' => __( 'ほしいものリストへ出品', 'buddypress' ), 
			'slug' => 'to_wanted_list', 
			'parent_url' => trailingslashit($bp->displayed_user->domain . 'new_entry'),
			'parent_slug' => 'new_entry',
			'position' => 67,
			'screen_function' => 'to_wanted_list_link',
			'item_css_id' => 'new-entry'
		) );

		$giveme_name;
		if(get_count_giveme_from_others() > 0){
			$giveme_name = 'ください<span>！</span>';
		}else{
			$giveme_name = 'ください';
		}

		bp_core_new_nav_item( array( 
			'name' => $giveme_name, 
			'slug' => 'giveme', 
			'position' => 75,
			'screen_function' => 'your_giveme_link',
			'show_for_displayed_user' => true,
			'default_subnav_slug' => 'your-giveme',
			'item_css_id' => 'giveme-from-others'
			) );

		bp_core_new_subnav_item( array( 
			'name' => __( 'あなたのください', 'buddypress' ), 
			'slug' => 'your-giveme', 
			'parent_url' => trailingslashit($bp->displayed_user->domain . 'giveme'),
			'parent_slug' => 'giveme',
			'position' => 85,
			'screen_function' => 'your_giveme_link',
			'item_css_id' => 'your-giveme'
			) );

		$giveme_from_others_name;
		if(get_count_giveme_from_others() > 0){
			$giveme_from_others_name = sprintf('あなたの出品へのください<span>%s</span>', get_count_giveme_from_others());
		}else{
			$giveme_from_others_name = 'あなたの出品へのください';
		}
		bp_core_new_subnav_item( array( 
			'name' => $giveme_from_others_name,
			'slug' => 'giveme-from-others', 
			'parent_url' => trailingslashit($bp->displayed_user->domain . 'giveme'),
			'parent_slug' => 'giveme',
			'position' => 95,
			'screen_function' => 'giveme_from_others_link',
			'item_css_id' => 'giveme-from-others'
			) );

		bp_core_new_nav_item( array( 
			'name' => __( 'ほしいものリスト', 'buddypress' ),  
			'slug' => 'wanted-list', 
			'position' => 105,
			'screen_function' => 'new_wanted_list_link',
			'show_for_displayed_user' => true,
			'default_subnav_slug' => 'wanted-list',
			'item_css_id' => 'wanted-list'
			) );

		bp_core_new_subnav_item( array( 
			'name' => __( '新規登録', 'buddypress' ), 
			'slug' => 'new-wanted-list', 
			'parent_url' => trailingslashit($bp->displayed_user->domain . 'wanted-list'),
			'parent_slug' => 'wanted-list',
			'position' => 106,
			'screen_function' => 'new_wanted_list_link',
			'item_css_id' => 'wanted-list'
			) );

		bp_core_new_subnav_item( array( 
			'name' => __( '一覧', 'buddypress' ), 
			'slug' => 'show-wanted-list', 
			'parent_url' => trailingslashit($bp->displayed_user->domain . 'wanted-list'),
			'parent_slug' => 'wanted-list',
			'position' => 107,
			'screen_function' => 'show_wanted_list_link',
			'item_css_id' => 'wanted-list'
			) );

		if(bp_get_total_unread_messages_count() > 0){
		$messages_name = sprintf( __( 'Messages <span>%s</span>', 'buddypress' ), bp_get_total_unread_messages_count() );
	}else{
		$messages_name = sprintf( __( 'Messages', 'buddypress' ));
	}

		$todo_list_name;
		$todo_list_count = get_todo_list_count($user_ID);
		$todo_list_style_id;
		if($todo_list_count){
			$todo_list_name = sprintf( __( 'next action <span>%d</span>', 'buddypress'), $todo_list_count);
			$todo_list_style_id = "exist_todo";
		}else{
			$todo_list_name = sprintf( __( 'next action', 'buddypress' ));
			$todo_list_style_id = "none_todo";
		}
		bp_core_new_nav_item( array( 
			'name' => $todo_list_name,  
			'slug' => 'todo-list', 
			'position' => 115,
			'screen_function' => 'todo_list_link',
			'show_for_displayed_user' => true,
			'default_subnav_slug' => 'unfinished-todo-list',
			'item_css_id' => $todo_list_style_id
			) );

		bp_core_new_subnav_item( array( 
			'name' => __( '未完了', 'buddypress' ), 
			'slug' => 'unfinished-todo-list', 
			'parent_url' => trailingslashit($bp->displayed_user->domain . 'todo-list'),
			'parent_slug' => 'todo-list',
			'position' => 116,
			'screen_function' => 'unfinished_todo_list_link',
			'item_css_id' => 'todo-list'
			) );

	}
}
 
add_action( 'bp_setup_nav', 'my_setup_nav', 1000 );


/**
	todoリストの表示関数一式
*/
function todo_list_link(){
	add_action( 'bp_template_title', 'todo_list_title' );
	add_action( 'bp_template_content', 'todo_list_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function todo_list_title(){
	echo "next action";
}

function todo_list_content(){
	include_once get_stylesheet_directory().DIRECTORY_SEPARATOR."members/single/your-todo-list.php";
}




/**********************************************
 * 「出品一覧」表示時に使う関数一式
 **********************************************
 */
function entry_list_title() {
	echo '出品一覧';
}


function entry_list_link(){
	add_action( 'bp_template_title', 'entry_list_title' );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}


function entry_list_notinprogress_title() {
	echo 'ください待ちの出品一覧';
}

function entry_list_notinprogress_content() {
	include_once get_stylesheet_directory().DIRECTORY_SEPARATOR."members/single/entry-list-notinprogress.php";
}

function entry_list_notinprogress_link(){
	add_action( 'bp_template_title', 'entry_list_notinprogress_title' );
	add_action( 'bp_template_content', 'entry_list_notinprogress_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function entry_list_toconfirm_title() {
	echo '取引相手確定待ちの出品一覧';
}

function entry_list_toconfirm_content() {
	include_once get_stylesheet_directory().DIRECTORY_SEPARATOR."members/single/entry-list-toconfirm.php";
}

function entry_list_toconfirm_link(){
	add_action( 'bp_template_title', 'entry_list_toconfirm_title' );
	add_action( 'bp_template_content', 'entry_list_toconfirm_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function entry_list_inprogress_title() {
	echo '取引中の出品一覧';
}

function entry_list_inprogress_content() {
	include_once get_stylesheet_directory().DIRECTORY_SEPARATOR."members/single/entry-list-inprogress.php";
}

function entry_list_inprogress_link(){
	add_action( 'bp_template_title', 'entry_list_inprogress_title' );
	add_action( 'bp_template_content', 'entry_list_inprogress_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function entry_list_finished_title() {
	echo '取引完了した出品一覧';
}

function entry_list_finished_content() {
	include_once get_stylesheet_directory().DIRECTORY_SEPARATOR."members/single/entry-list-finished.php";
}

function entry_list_finished_link(){
	add_action( 'bp_template_title', 'entry_list_finished_title' );
	add_action( 'bp_template_content', 'entry_list_finished_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function has_todo_in_entry_list(){
	global $user_ID;
	$entry_list = get_posts(array('author' => $user_ID, 'showposts' => -1));
	foreach($entry_list as $entry){
		if(isFinish($entry->ID)){
			if(isBidderEvaluated($entry->ID)){
				continue;
			}else{
				return true;
			}
		}elseif(isConfirm($entry->ID)){
			return true;
		}elseif(isGiveme($entry->ID)){
			return true;
		}else{
			continue;
		}
	}
	return false;
}

/**********************************************
 * 「新規出品」関連関数一式
 **********************************************
 */

/**
 * common function to exhibit.
 * valid parameters:
 * - exhibitor_id -> user id of author (required)
 * - item_name -> name of item (required) 
 * - item_description
 * - item_category
 * - item_status
 * - tags -> item tags, sprit by space
 * - department
 * - course
 * - image_url -> url of image on the internet. Not attached files.
 * @param array parameters
 * @return int insert_id
 */
function exhibit(array $args){
	$insert_id;
	$post = array(
	'comment_status' => 'open', // open comment
	'ping_status' => 'closed', // pinback, trackback off
	'post_date' => date('Y-m-d H:i:s'), 
	'post_date_gmt' => date('Y-m-d H:i:s'),
	'post_status' => 'publish', // public open
	'post_type' => 'post' // entry type name
	);

	if($args['exhibitor_id'] !== null){
		$post['post_author'] = $args['exhibitor_id'];
	}

	if($args['item_name'] !== null){
		$post['post_title'] = strip_tags($args['item_name']);
	}

	if($args['item_description'] !== null){
		$post['post_content'] = htmlentities($args['item_description'], ENT_QUOTES, 'UTF-8');
	}

	if($args['item_category'] !== null){
		$post['post_category'] = array($args['item_category']);
	}

	if($args['tags'] !== null){
		$post['tags_input'] = str_replace(array(" ", "　"), array("," ,",") , $args['tags']);
	}

	$insert_id = wp_insert_post($post);

	if($insert_id === 0){
		return $insert_id;
	}

	if($args['department'] !== null){
		add_post_meta($insert_id, "department", $args['department'], true);
	}

	if($args['course'] !== null){
		add_post_meta($insert_id, "course", $args['course'], true);
	}

	if($args['item_status'] !== null){
		add_post_meta($insert_id, "item_status", $args['item_status'], true);
	}

	if($args['asin'] !== null){
		add_post_meta($insert_id, "asin", $args['asin'], true);
	}

	if($args['image_url'] !== null){
		// attach_idはinsert_id+1になる。
		// fcl_media_sideload_image は attach_idを返さないのでこれ以上の実装方法が見つからない。汗
		fcl_media_sideload_image($args['image_url'] ,$insert_id);
		update_post_meta($insert_id,'_thumbnail_id',$insert_id + 1);
	}

	// add point on exhibition
	add_got_points($args['exhibitor_id'], get_option('exhibition-point'));

	return $insert_id;
}

function get_new_entry_url() {
	return bp_loggedin_user_domain() . "new_entry/";
}

function new_entry_title() {
	echo '新規出品';
}

function new_entry_content() {
	include_once get_stylesheet_directory().DIRECTORY_SEPARATOR."members/single/new-entry.php";
}

function new_entry_link(){
	add_action( 'bp_template_title', 'new_entry_title' );
	add_action( 'bp_template_content', 'new_entry_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function to_wanted_list_title() {
	echo 'ほしいものリストへ出品';
}

function to_wanted_list_content() {
	include_once get_stylesheet_directory().DIRECTORY_SEPARATOR."members/single/to-wanted-list.php";
}

function to_wanted_list_link(){
	add_action( 'bp_template_title', 'to_wanted_list_title' );
	add_action( 'bp_template_content', 'to_wanted_list_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function create_wanted_item_detail($item){
	global $user_ID;
	$NODATA = 'データがありません';

	$return = '';
	$return .= '<div class="item_detail" id="' . $item->wanted_item_id . '" style="height:auto;margin:15px 5px;padding:0px 0px 0px 0px">';
	$return .= '<img src="' . $item->image_url . '" style="float:left; width:113px; height:160px; overflow:hidden">';

	$return .= '<div id="title_'. $item->wanted_item_id .'"><strong>' . $item->item_name . '</strong></div>';
	$return .= '<div>ほしがっている人:<a href="' . home_url() . '/members/' . get_user_by('id', $item->user_id)->user_nicename .'">'. get_user_by('id', $item->user_id)->display_name . '</a>さん';
	if($item->count > 1){
		$return .= ' ほか' . $item->count . '人';
	}
	$return .= '</div>';
	if(is_user_logged_in()){
		$return .='
		<label for="item_status">状態:</label>
		<select name="item_status">
		<option value="verygood">'. get_display_item_status("verygood") .'</option>
		<option value="good">' . get_display_item_status("good") .'</option>
		<option value="bad">' . get_display_item_status("bad") .'</option>
		</select>
		</br>
		';
		$post_id = get_post_id_to_wanted($user_ID, $item->wanted_item_id);
		if($post_id){
			$return .= '<input type="button" class="button_del_exhibition_to_wanted" id="button_'. $item->wanted_item_id .'" value="出品取消" wanted_item_id="' . $item->wanted_item_id .'" post_id="' . $post_id .'", asin="' . $item->ASIN . '">';
		}else{
			$return .= '<input type="button" class="button_exhibit_to_wanted" id="button_'. $item->wanted_item_id .'" value="出品" wanted_item_id="' . $item->wanted_item_id .'", asin="' . $item->ASIN . '">';
		}
	}
	$return .= '</div>';
	$return .= '<hr>';
	return $return;
}

function get_post_id_to_wanted($user_id, $wanted_item_id){
	global $wpdb;
	global $table_prefix;
	$sql = "SELECT post_id FROM " . $wpdb->posts . ", " . $wpdb->postmeta
			 . " WHERE " . $wpdb->posts . ".ID = " . $wpdb->postmeta . ".post_id "
			 . " AND " . $wpdb->postmeta . ".meta_key = 'wanted_item_id'"
			 . " AND " . $wpdb->postmeta . ".meta_value = %s"
			 . " AND " . $wpdb->posts . ".post_author = %d"
			 . " AND " . $wpdb->posts . ".post_status = 'publish'";
	$post_id = $wpdb->get_var($wpdb->prepare($sql, $wanted_item_id, $user_id));
	return $post_id;
}

/**********************************************
 * 「くださいリクエスト」一覧表示時に使う関数一式
 **********************************************
 */
function get_giveme_from_others_url() {
	return bp_loggedin_user_domain() . "giveme/giveme-from-others/";
}

function giveme_from_others_title() {
	echo 'くださいリクエスト一覧';
}


/**
 * 「くださいリクエスト」が来ている記事の一覧を表示します。
 *  - 記事タイトル
 *  - 写真
 *  - くださいしているユーザ
 *  - ユーザ選択用ラジオボタン
 *  - 取引相手確定ボタン
 */
function giveme_from_others_content() {
	include_once get_stylesheet_directory().DIRECTORY_SEPARATOR."members/single/giveme-from-others-list.php";
}

function giveme_from_others_link () {
	add_action( 'bp_template_title', 'giveme_from_others_title' );
	add_action( 'bp_template_content', 'giveme_from_others_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

/**
 * くださいリクエストが来ている記事の一覧を取得します。
 */
function get_giveme_from_others_list(){
	global $wpdb;
	global $table_prefix;
	global $user_ID;
	$givemes = $wpdb->get_results($wpdb->prepare("
		SELECT " . $table_prefix . "fmt_user_giveme.post_id, display_name, user_nicename, " . $table_prefix . "fmt_user_giveme.user_id
		FROM " . $table_prefix . "fmt_user_giveme, " . $table_prefix . "posts, " . $wpdb->users . ", " . $table_prefix . "fmt_giveme_state
		WHERE " . $table_prefix . "fmt_user_giveme.post_id = " . $table_prefix . "posts.ID
		AND " . $table_prefix . "fmt_user_giveme.post_id = " . $table_prefix . "fmt_giveme_state.post_id
		AND " . $table_prefix . "fmt_user_giveme.user_id = " . $wpdb->users .".ID
		AND " . $table_prefix . "fmt_giveme_state.confirmed_flg = 0
		AND " . $table_prefix . "posts.post_author = %d
		ORDER BY post_id",
		$user_ID));
		
	return $givemes;
}

function get_count_giveme_from_others(){
	global $wpdb;
	global $table_prefix;
	global $user_ID;
	$count = $wpdb->get_var($wpdb->prepare("
		SELECT count(DISTINCT(" . $table_prefix . "posts.ID))  
		FROM " . $table_prefix . "fmt_user_giveme, " . $table_prefix . "posts, " . $table_prefix . "fmt_giveme_state
		WHERE " . $table_prefix . "fmt_user_giveme.post_id = " . $table_prefix . "posts.ID
		AND " . $table_prefix . "fmt_user_giveme.post_id = " . $table_prefix . "fmt_giveme_state.post_id
		AND " . $table_prefix . "fmt_giveme_state.confirmed_flg = 0
		AND " . $table_prefix . "posts.post_author = %d",
		$user_ID));
		
	return $count;
}


/**********************************************
 * あなたの「ください」一覧表示時に使う関数一式
 **********************************************
 */
function your_giveme_title() {
	echo '「ください」済の記事一覧';
}


/**
 * 「ください」している記事の一覧を表示します。
 */
function your_giveme_content() {
	include_once get_stylesheet_directory().DIRECTORY_SEPARATOR."members/single/your-giveme-list.php";
}

function your_giveme_link(){
	add_action( 'bp_template_title', 'your_giveme_title' );
	add_action( 'bp_template_content', 'your_giveme_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

/**
 * 自分が「ください」している記事の一覧を取得します。
 */
function get_your_giveme_list(){
	global $wpdb;
	global $table_prefix;
	global $user_ID;
	$givemes = $wpdb->get_results($wpdb->prepare("
		SELECT " . $table_prefix . "fmt_user_giveme.post_id
		FROM " . $table_prefix . "fmt_user_giveme, " . $table_prefix . "fmt_giveme_state
		WHERE " . $table_prefix . "fmt_user_giveme.post_id = " . $table_prefix . "fmt_giveme_state.post_id
		AND " . $table_prefix . "fmt_user_giveme.user_id = %d
		AND " . $table_prefix . "fmt_giveme_state.confirmed_flg = 0
		ORDER BY post_id",
		$user_ID));
		
	return $givemes;
}

/**********************************************
 * 「ほしいものリスト」表示時に使う関数一式
 **********************************************
 */
function get_wanted_list_url() {
	return bp_loggedin_user_domain() . "wanted-list/";
}

function new_wanted_list_title() {
	echo 'ほしいものを登録';
}

function new_wanted_list_content() {
	include_once get_stylesheet_directory().DIRECTORY_SEPARATOR."members/single/new-wanted-list.php";
}

function new_wanted_list_link(){
	add_action( 'bp_template_title', 'new_wanted_list_title' );
	add_action( 'bp_template_content', 'new_wanted_list_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function show_wanted_list_title() {
	echo 'ほしいものリスト';
}

function show_wanted_list_content() {
	include_once get_stylesheet_directory().DIRECTORY_SEPARATOR."members/single/show-wanted-list.php";
}

function show_wanted_list_link(){
	add_action( 'bp_template_title', 'show_wanted_list_title' );
	add_action( 'bp_template_content', 'show_wanted_list_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function test_get_wanted_list($user_id){
	echo $user_id;
}

function get_wanted_list($user_id){
	global $wpdb;
	global $table_prefix;
	$wanted_list;
	$sql = "SELECT wanted_item_id, user_id, item_name, ASIN, image_url
			FROM ". $table_prefix . "fmt_wanted_list";
	if($user_id){
		$sql .= " WHERE user_id = %d ORDER BY wanted_item_id desc";
		$wanted_list = $wpdb->get_results($wpdb->prepare($sql, $user_id));
	}else{
		$sql .= " ORDER BY wanted_item_id desc";
		$wanted_list = $wpdb->get_results($sql);	
	}
	return $wanted_list;
}

function get_others_wanted_list($args=''){
	global $wpdb;
	global $bp;
	global $table_prefix;

	$wanted_list;
	$sql = "SELECT wanted_item_id, " . $table_prefix . "fmt_wanted_list.user_id, item_name, ASIN, image_url, value as department";
	if(isset($args['count']) && $args['count'] == true){
		$sql .= ", count(*) as count";
	}
	$sql .= " FROM ". $table_prefix . "fmt_wanted_list";
	$sql .= " LEFT JOIN " . $bp->profile->table_name_data;
	$sql .= " ON " . $table_prefix . "fmt_wanted_list.user_id = " . $bp->profile->table_name_data . ".user_id";
	$sql .= " AND " . $bp->profile->table_name_data . ".field_id = " . xprofile_get_field_id_from_name('学部');
	if(isset($args['user_id']) && $args['user_id'] < 0){
		$args['user_id'] = 0;
	}
	if(!isset($args['user_id'])){
		$args['user_id'] = 0;
	}
	$sql .= " WHERE " . $table_prefix . "fmt_wanted_list.user_id <> %d";
	$sql .= " AND item_name LIKE '%s'";
	if(isset($args['wanted_item_id'])){
		$sql .= " AND wanted_item_id = %d";
	}else{
		$args['wanted_item_id'] = 0;
		$sql .= " AND wanted_item_id <> %d";		
	}
	if(isset($args['asin'])){
		$sql .= " AND ASIN = %s";
	}else{
		$args['asin'] = 'DUMMY'; // to get all results
		$sql .= " AND ASIN <> %s";		
	}
	if(isset($args['department'])){
		$sql .= " AND value = %s";
	}else{
		$args['department'] = 'DUMMY'; // to get all results
		$sql .= " AND ifnull(value,0) <> %s";
	}
	if(isset($args['count']) && $args['count'] == true){
		$sql .=	" GROUP BY ASIN";
	}
	$sql .=	" ORDER BY wanted_item_id desc";
	if($args['page'] >= 0){
		$sql .=	" LIMIT %d, 10";
	}else{
		$args['page'] = 0;
		$sql .=	" LIMIT %d, 100000";		
	}
	if(!isset($args['keyword'])){
		$args['keyword'] = "";
	}

	$wanted_list = $wpdb->get_results($wpdb->prepare($sql, $args['user_id'], '%' . $args['keyword'] . '%', $args['wanted_item_id'], $args['asin'],  $args['department'], ($args['page'])*10));
	return $wanted_list;
}

function get_wanted_item_by_asin($asin){
	global $wpdb;
	global $table_prefix;
	global $user_ID;
	$wanted_item_id = $wpdb->query($wpdb->prepare("
		SELECT wanted_item_id FROM " . $table_prefix . "fmt_wanted_list
		WHERE user_id = %d
		AND ASIN = %s",
		$user_ID, $asin));
	return $wanted_item_id;
}

// 学部の一覧が標準関数で取れないので無理やり作りました。
// テスト環境と本番環境でIDが同じなので動きますが、
// プロフィール欄の項目を追加するとおかしくなる可能性があります。
// やり方に気づいていないだけかもしれないので、標準関数での実装を探してほしい……
function get_department_options(){
	global $wpdb, $bp;
	$group_id = 1;
	$parent_id = 2;
	$html = "<option value=''>すべて</option>";
	$sql = "SELECT name FROM ". $bp->profile->table_name_fields . " WHERE group_id = " . $group_id . " AND parent_id = " . $parent_id . " ORDER BY id;";
	$departments = $wpdb->get_results($sql);
	foreach($departments as $department){
		$html .= "<option value='" . $department->name ."'>" . $department->name . "</option>";
	}
	return $html;
}




// デバッグログ吐き出しメソッド
function debug_log($str){
	$fp = fopen('debug.txt', 'a');
	fwrite($fp, $str);
	fwrite($fp, PHP_EOL);
	fclose($fp);
}

// コメント欄をカスタマイズ
function my_comment_field_init($defaults){
	// タグに関する注意書きを非表示
	$defaults["comment_notes_after"] = "";
	return $defaults;
}
add_filter("comment_form_defaults", "my_comment_field_init");

function html_to_text($comment_content) {
  if ( get_comment_type() == 'comment' ) {
    $comment_content = htmlspecialchars($comment_content, ENT_QUOTES);
  }
  return $comment_content;
}
add_filter('comment_text', 'html_to_text', 9);

// アカウント作成画面のカスタマイズ
function my_signup_validate(){
	global $bp;
	if(!$_POST["agreewithpolicy"]){
		$bp->signup->errors["agreewithpolicy"] = __( '規約に同意してください。', 'buddypress' );
		add_action("agreewithpolicy_action", "show_error_on_agreewithpolicy");
	}
}
function show_error_on_agreewithpolicy(){
	echo "<div class='error'>規約に同意してください。</div>";
}

add_action("bp_signup_validate", "my_signup_validate");

// 管理者以外の場合ツールバーを非表示
function my_function_admin_bar($content){
	return (current_user_can("administrator"))?$content:false;
}

add_filter('show_admin_bar', 'my_function_admin_bar');

// 管理者以外の場合ダッシュボードにログインさせない
add_action('admin_init', 'disable_admin_pages' );
function disable_admin_pages() {
	if(!current_user_can('administrator')
		&& strpos($_SERVER['REQUEST_URI'] ,'admin-ajax') === false){
		$redirect_url = get_option('home');
		header("Location: ".$redirect_url);
		exit;
	}
}

// short code
function social_login_button(){
	if(function_exists('gianism_login')){
    	gianism_login();
	}
}

function show_wanted_list(){
	include_once get_stylesheet_directory().DIRECTORY_SEPARATOR."wanted-list.php";
}

add_shortcode('home_url','home_url');
add_shortcode('social_login_button','social_login_button');
add_shortcode('show_wanted_list', 'show_wanted_list');

function endsWith($haystack, $needle){
    $length = (strlen($haystack) - strlen($needle));
    // 文字列長が足りていない場合はFALSEを返します。
    if($length < 0) return FALSE;
    return strpos($haystack, $needle, $length) !== FALSE;
}

/**
 * override default function
 * header image is always get_header_image()
 */
function bp_dtheme_header_style() {
	$header_image = get_header_image();
?>
	<style type="text/css">
		<?php if ( !empty( $header_image ) ) : ?>
			#header { background-image: url(<?php echo $header_image ?>); }
		<?php endif; ?>

		<?php if ( 'blank' == get_header_textcolor() ) { ?>
		#header h1, #header #desc { display: none; }
		<?php } else { ?>
		#header h1 a, #desc { color:#<?php header_textcolor(); ?>; }
		<?php } ?>
	</style>
<?php
}

/**
 * override default function
 * enable to edit comment on single page
 */
function bp_dtheme_blog_comments( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	global $user_ID;

	if ( 'pingback' == $comment->comment_type )
		return false;

	if ( 1 == $depth )
		$avatar_size = 50;
	else
		$avatar_size = 25;
	?>

	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<div class="comment-avatar-box">
			<div class="avb">
				<a href="<?php echo get_comment_author_url(); ?>" rel="nofollow">
					<?php if ( $comment->user_id ) : ?>
						<?php echo bp_core_fetch_avatar( array( 'item_id' => $comment->user_id, 'width' => $avatar_size, 'height' => $avatar_size, 'email' => $comment->comment_author_email ) ); ?>
					<?php else : ?>
						<?php echo get_avatar( $comment, $avatar_size ); ?>
					<?php endif; ?>
				</a>
			</div>
		</div>

		<div class="comment-content">
			<div class="comment-meta">
				<p>
					<?php
						/* translators: 1: comment author url, 2: comment author name, 3: comment permalink, 4: comment date/timestamp*/
						printf( __( '<a href="%1$s" rel="nofollow">%2$s</a> said on <a href="%3$s"><span class="time-since">%4$s</span></a>', 'buddypress' ), get_comment_author_url(), get_comment_author(), get_comment_link(), get_comment_date() );
					?>
				</p>
			</div>

			<div class="comment-entry">
				<?php if ( $comment->comment_approved == '0' ) : ?>
				 	<em class="moderate"><?php _e( 'Your comment is awaiting moderation.', 'buddypress' ); ?></em>
				<?php endif; ?>

				<?php comment_text(); ?>
			</div>

			<div class="comment-options">
					<?php if ( comments_open() ) : ?>
						<?php comment_reply_link( array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ); ?>
					<?php endif; ?>

					<?php if ( $user_ID == $comment->user_id ) : ?>
						<?php //printf( '<a class="button comment-edit-link bp-secondary-action" href="%1$s" title="%2$s">%3$s</a> ', get_update_comment_link( $comment->comment_ID ), esc_attr__( 'Edit comment', 'buddypress' ), __( 'Edit', 'buddypress' ) ); ?>
						<?php printf( '<a class="button comment-edit-link bp-secondary-action edit-button" onClick="%1$s" title="%2$s">%3$s</a> ', 'onClickEditCommentButton('. $comment->comment_ID .')', esc_attr__( 'Edit comment', 'buddypress' ), __( 'Edit', 'buddypress' ) ); ?>
					<?php endif; ?>

			</div>

		</div>

<?php
}

function render(){
	require_once get_stylesheet_directory().DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'setting.php';
}

function add_custom_menu() {
    add_options_page('テクスチェンジ', 'テクスチェンジ', 'read', 'texchange', 'render');
}
add_action( 'admin_menu', 'add_custom_menu' );


/**
	todoリスト系関数
*/

/**
 * wp_todoテーブルに情報を加える関数
 * todo追加時にアプリにpush通知を送信する
 * statusはデフォルトでunfinishedに設定されている
 * @param {int} $user_ID
 * @param {int} $item_ID 商品のＩＤ
 * @param {string} $message TODOリストに表示するメッセージ（リンク先込）
 * @return {int} 追加したtodo_id
 */
function add_todo($user_ID, $item_ID, $message){
	global $table_prefix;
	global $wpdb;

	$wpdb->query($wpdb->prepare(
		"INSERT INTO " .$table_prefix. "todo
			(user_id, item_id, message, created, modified)
			VALUES
			( %d, %d, %s, current_timestamp, current_timestamp)",$user_ID ,$item_ID ,$message));

	$token = get_user_meta($user_ID, 'device_token', true);
	if($token){
		send_push_notification($token, array(
			'alert'		=> 'NextActionが追加されました！',
			'vibrate'	=> 'true',
			'sound'		=> 'default',
			'badge'		=> messages_get_unread_count($user_ID) + get_todo_list_count($user_ID)
		));
	}
	return $wpdb->insert_id;//確認！！TODO
}

/**
 * 取引キャンセル時に呼ばれる関数
 * @param $item_ID 取引商品ＩＤ
 * @return 空 
 */
function cancel_todo($item_ID){

	$exhibitor_ID = get_post($item_ID)->post_author;
	$bidder_ID = get_bidder_id($item_ID);

	//出品者側
	$exhibitor_todo_ID = get_todo_row($exhibitor_ID, $item_ID)->todo_id;
	change_todo_status_finished($exhibitor_todo_ID);
	change_todo_modified($exhibitor_todo_ID);

	//落札者側
	$bidder_todo_ID = get_todo_row($bidder_ID, $item_ID)->todo_id;
	if($bidder_todo_ID){
		change_todo_status_finished($bidder_todo_ID);
		change_todo_modified($bidder_todo_ID);

	}else{
		return;
	}

}


/**
 * todoテーブルの特定のユーザーＩＤと商品ＩＤをもち、未完了の行を返す関数
 * @param {int} $user_ID
 * @param {int} $item_ID 商品のＩＤ
 * @return {Object} 特定のユーザーＩＤと商品ＩＤをもち、未完了の行
 */
function get_todo_row($user_ID, $item_ID){
	global $table_prefix;
	global $wpdb;

	return $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$table_prefix."todo 
		   WHERE user_id=%d AND item_id=%d AND status='unfinished'",
		   $user_ID ,$item_ID));

}

/**
 * 特定のtodo_idを持つ行のstatusを変更する
 * @param {int} $todo_ID todo_id
 * @param {string} $status 変更したいstatus("finished"もしくは"unfinished")
 */
function change_todo_status($todo_ID, $status){
	global $table_prefix;
	global $wpdb;

	if($status == "finished" || $status == "unfinished"){

		$wpdb->query($wpdb->prepare(
			"UPDATE ".$table_prefix."todo SET status=%s where todo_id= %d ",$status,$todo_ID));

		change_todo_modified($todo_ID);
	}else{
		return ;
	}

}

/**
 * 特定のtodo_idを持つ行のstatusをfinishedに変更する
 * todoが完了状態になった時のためのファサード関数
 * @param {int} $todo_ID todo_id
 */
function change_todo_status_finished($todo_ID){
	change_todo_status($todo_ID, 'finished');

	// send push notification
	$user_ID = get_user_id_by_todo_id($todo_ID);
	$token = get_user_meta($user_ID, 'device_token', true);
	if($token){
		send_push_notification($token, array(
			'badge'		=> messages_get_unread_count($user_ID) + get_todo_list_count($user_ID)
		));
	}
}

/**
 * 情報が更新された際に更新時間（=modified)を現在時刻に変更する関数
 * @param {int} $todo_ID todo_id
 */
function change_todo_modified($todo_ID){
	global $table_prefix;
	global $wpdb;

	$wpdb->query($wpdb->prepare(
		"UPDATE ".$table_prefix."todo SET modified = current_timestamp WHERE todo_id=%d",$todo_ID));
}


/**
 * 取引相手を確定するTODOを追加する関数※出品者のTODO
 * @param {int} $item_ID くださいされた商品ＩＤ
 */
function add_todo_confirm_bidder($item_ID){
	
	$user_ID = get_post($item_ID)->post_author;

	add_todo($user_ID, $item_ID, '<a href = "' . home_url() . '/archives/' . $item_ID . '">
		あなたの商品に「ください」がされました。取引相手を確定させてください</a>');
}

/**
 * 取引を完了させるTODOを追加する関数※出品者のTODO
 * @param {int} $user_ID 出品者ＩＤ
 * @param {int} $item_ID 取引商品ＩＤ
 */
function add_todo_finish_trade($item_ID){
	$user_ID = get_post($item_ID)->post_author;

	add_todo($user_ID, $item_ID, '<a href = "'. home_url() . '/archives/' . $item_ID .'">商品受け渡し後、取引完了ボタンを押してください</a>');

}

/** 
 * 取引詳細をメッセージでやり取りするTODOを追加する関数※落札者のTODO
 * @param {int} $item_ID 取引商品ＩＤ
 * @param {int} $thread_ID 取引相手確定メッセージのid
 */
function add_todo_dealing($item_ID, $thread_ID){
	global $table_prefix;
	global $wpdb;

	$user_ID = get_bidder_id($item_ID);
	$user = get_user_by('id', $user_ID);
	$user_login_name = $user->user_login;

	add_todo($user_ID, $item_ID, 
		'<a href = "'. home_url() . '/members/' . $user_login_name .'/messages/view/' .$thread_ID. '" onClick="todo_dealing('.$user_ID.','.$item_ID.')">
			くださいリクエストが承認されました。承認メッセージに返信してください</a>');
}

/**
 * 落札者を評価するTODOを追加する関数※出品者のTODO
 * @param {int} $user_ID 出品者ＩＤ
 * @param {int} $item_ID 取引商品ＩＤ
 */
function add_todo_evaluate_bidder($user_ID, $item_ID){

	add_todo($user_ID, $item_ID, '<a href = "'. home_url() . '/archives/' . $item_ID .'">落札者を評価してください</a>');
}

/**
 * 出品者を評価するTODOを追加する関数※落札者のTODO
 * @param {int} $item_ID 取引商品ＩＤ
 */
function add_todo_evaluate_exhibitor($item_ID){
	
	$user_ID = get_bidder_id($item_ID);

	add_todo($user_ID, $item_ID, '<a href = "'. home_url() . '/archives/' . $item_ID .'">出品者を評価してください</a>');
}

/**
 * POSTされた、ユーザーIDと商品ＩＤをもつTODOを消す関数
 */
function todo_dealing_finished(){
	$user_ID = $_POST[userID];
	$item_ID = $_POST[itemID];
	
	$todo_ID = get_todo_row($user_ID, $item_ID)->todo_id;
	change_todo_status_finished($todo_ID);

}
add_action('wp_ajax_todo_dealing', 'todo_dealing_finished');

/**
 * ユーザーのtodoをすべて取り出す関数
 * @param {int} $user_ID 取り出すユーザーID
 * @param {int} $status todoの状態("finished"もしくは"unfinished")
 */
function get_todo_list($user_ID,$status){
	global $wpdb;
	global $table_prefix;
	$todo_list = $wpdb->get_results($wpdb->prepare(
					"SELECT * FROM ".$table_prefix."todo WHERE user_id =%d AND status =%s",$user_ID ,$status));
	
	return $todo_list;
}

/**
 * 取引相手のIDを返す関数
 * @param {int} $item_ID 取引商品ＩＤ
 * @param {int} $user_ID 自分のID
 */
function deal_user($item_ID, $userID){

	$exhibitor_ID = get_post($item_ID)->post_author;
	$bidder_ID = get_bidder_id($item_ID);

	if($userID == $exhibitor_ID){
		$deal_user_id = $bidder_ID;
	}else if($userID == $bidder_ID){
		$deal_user_id = $exhibitor_ID;
	}

	return $deal_user_id;
}

/**
 * TODOの数を返す関数
 * @param {int} $user_ID ユーザーＩＤ
 * @return {int} TODOリストの数
 */
function get_todo_list_count($user_ID){
	$todo_list = get_todo_list($user_ID, "unfinished");
	return count($todo_list);
}

/**
 * TODOのIDからユーザIDを検索して返します
 * @param {int} $todo_ID todoリストID
 * @return {int} $user_ID ユーザID 
 */
function get_user_id_by_todo_id($todo_ID){
	global $wpdb;
	global $table_prefix;
	$sql = "SELECT user_id FROM " . $table_prefix . "todo where todo_id = %d";
	$user_ID = $wpdb->get_var($wpdb->prepare($sql, $todo_ID));
	return $user_ID;	
}

?>
