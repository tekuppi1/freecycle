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
add_action('wp_ajax_add_wanted_item', 'add_wanted_item');
add_action('wp_ajax_del_wanted_item_by_asin', 'del_wanted_item_by_asin');
add_action('wp_ajax_exhibit_to_wanted', 'exhibit_to_wanted');
add_action('user_register', 'on_user_added');
remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2);

function redirect_to_home(){
	$redirect_url = get_option('home');
	header("Location: ".$redirect_url);
	exit;
}
add_action('wp_login', 'redirect_to_home');


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
	if(is_front_page() || $_REQUEST['seachform_itemstatus'] == 'givemeable'){
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
	$current_giveme = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM " . $table_prefix . "fmt_user_giveme where user_id = %d and post_id = %d", $userID, $postID));
	if($current_giveme == 0){
		$wpdb->query($wpdb->prepare("
			INSERT INTO " . $table_prefix . "fmt_user_giveme
			(update_timestamp, user_id, post_id)
			VALUES (current_timestamp, %d, %d)",
			$userID, $postID));
	}
	
	// 仮払ポイントを1p増
	add_temp_used_points($userID, 1);
	
	// 「ください」リクエストが来たことを記事投稿者に通知する
	messages_new_message(array(
	'sender_id' => $userID,
	'recipients' => get_post_author($postID),
	'subject' => '【自動送信】あなたの商品に「ください」がされました',
	'content' => bp_core_get_userlink($userID) . 'さんが以下の商品に「ください」をしています！<br>' .
					'<a href="' . get_permalink($postID) . '">' . get_post($postID)->post_title . '</a>'
	));
	
	echo $current_giveme;
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
	echo "cancel";
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
	
	// 取引相手以外の仮払ポイントを1p減算
	foreach($uncheckedUserIDs as $uncheckedUserID){
		add_temp_used_points($uncheckedUserID, -1);
	}

	// 取引相手に確定されたことを通知
	$content = 'あなたが以下の商品の取引相手に選ばれました！' . PHP_EOL . ' 【商品名】:<a href="' . get_permalink($postID) . '">' . get_post($postID)->post_title . '</a>' . PHP_EOL;
	$content .= '【受渡方法】:' . get_display_tradeway($tradeway) . PHP_EOL;
	$content .= PHP_EOL;
	if($tradeway == "handtohand"){
		$content .= '以下の受渡希望条件を確認してください。問題なければ「OKです」と返信してください！' . PHP_EOL;
		$content .= 'もし不都合があれば、代わりの日時、場所を記入して返信してください。'. PHP_EOL;
		$content .= '【受渡希望日時】:' . PHP_EOL;
		for($i=1; $i<count($tradedates); $i++){
			$content .= ' 第' . $i .'希望:' . $tradedates[$i] . PHP_EOL;
		}
		$content .= '【受渡希望場所】:' . $place . PHP_EOL;
	}elseif($tradeway == "delivery"){
		$content .= '配送による受渡に同意する場合、受取先の住所と名前を記入して返信してください！' . PHP_EOL;
		$content .= '同意しない場合、直接手渡しの希望日時、場所を記入して返信してください。	' . PHP_EOL;
	}
	if($message){
		$content .= '【メッセージ】:' . $message . PHP_EOL;
	}

	messages_new_message(array(
		'sender_id' => bp_loggedin_user_id(),
		'recipients' => $userID,
		'subject' => '【自動送信】くださいリクエストが承認されました！',
		'content' => $content
	));

	echo "confirm";
	die;
}

/**
 * 出品者評価時に呼ばれる関数
 */
function exhibiter_evaluation(){
	global $wpdb;
	global $table_prefix;
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
	
	die;
}

/**
 * 落札者評価時に呼ばれる関数
 */
function bidder_evaluation(){
	global $wpdb;
	global $table_prefix;
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
		
	// 獲得ポイントを1p加算
	add_got_points($userID, 1);
	
	// 取引完了メールを送信
	messages_new_message(array(
		'sender_id' => $userID,
		'recipients' => get_confirmed_user_id($postID),
		'subject' => '【自動送信】出品者の評価をしてください！',
		'content' => '出品者が取引を完了状態にしました。以下のリンクから出品者の評価を実施してください。' .
						'<a href="' . get_permalink($postID) . '">' . get_permalink($postID) . '</a>'
	));

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
 * 新規出品時に呼ばれる関数
 */
function new_entry(){
	global $bp;

	$post = array(  
	'comment_status' => 'open', // open comment
	'ping_status' => 'closed', // pinback, trackback off
	'post_author' => $bp->loggedin_user->id, // login user ID
	'post_category' => array($_POST['field_3']),
	'post_content' => htmlentities($_POST['field_2'], ENT_QUOTES, 'UTF-8'), // item desctiption
	'post_date' => date('Y-m-d H:i:s'), 
	'post_date_gmt' => date('Y-m-d H:i:s'),
	'post_status' => 'publish', // public open
	'post_title' => strip_tags($_POST['field_1']), // title
	'post_type' => 'post', // entry type name
	'tags_input' => str_replace(array(" ", "　"), array("," ,",") , $_POST['field_4']) // スペース(半角および全角)をカンマに置換
	);  

	$insert_id = wp_insert_post($post);

	if($insert_id){
		// success
		// add custom field
		add_post_meta($insert_id, "item_status", $_POST["item_status"], true);
		add_post_meta($insert_id, "department", xprofile_get_field_data('学部' ,$bp->loggedin_user->id), true);
		add_post_meta($insert_id, "course", xprofile_get_field_data('学科' ,$bp->loggedin_user->id), true);
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
						'name'     => $files['name'][$key],
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
	global $bp;
	global $user_ID;

	$post = array(  
	'comment_status' => 'open', // open comment
	'ping_status' => 'closed', // pinback, trackback off
	'post_author' => $bp->loggedin_user->id, // login user ID
	'post_category' => array($_POST['field_3']),
	'post_content' => htmlentities($_POST['field_2'], ENT_QUOTES, 'UTF-8'), // item desctiption
	'post_date' => date('Y-m-d H:i:s'), 
	'post_date_gmt' => date('Y-m-d H:i:s'),
	'post_status' => 'publish', // public open
	'post_title' => strip_tags($_POST['field_1']), // title
	'post_type' => 'post', // entry type name
	'tags_input' => str_replace(array(" ", "　"), array("," ,",") , $_POST['field_4']) // スペース(半角および全角)をカンマに置換
	);  

	$insert_id = wp_insert_post($post);

	if($insert_id){
		// success
		// add custom field
		add_post_meta($insert_id, "item_status", $_POST["item_status"], true);
		add_post_meta($insert_id, "department", xprofile_get_field_data('学部' ,$bp->loggedin_user->id), true);
		add_post_meta($insert_id, "course", xprofile_get_field_data('学科' ,$bp->loggedin_user->id), true);
		add_post_meta($insert_id, "wanted_item_id", $_POST['wanted_item_id'], true);

		$image_url = $_POST['image_url'];
		media_sideload_image($image_url ,$insert_id);
		// attach_idはinsert_id+1になる。
		// media_sideload_image は attach_idを返さないのでこれ以上の実装方法が見つからない。汗
		update_post_meta($insert_id,'_thumbnail_id',$insert_id + 1);
	}

	// 出品があった旨を通知
	messages_new_message(array(
	'sender_id' => $user_ID,
	'recipients' => get_others_wanted_list($user_ID, '', $_POST['wanted_item_id'])[0]->user_id,
	'subject' => 'あなたのほしいものが出品されました！',
	'content' => bp_core_get_userlink($user_ID) . 'さんが、あなたのほしいものを出品しました。くださいしてみましょう！' . PHP_EOL .
					'<a href="' . get_permalink($insert_id) . '">' . get_post($insert_id)->post_title . '</a>'
	));

	echo $insert_id;
	die;
}

function delete_post(){
	wp_delete_post($_POST['postID']);
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
	$items = get_others_wanted_list($_POST['user_id'], $_POST['keyword']);
	$return = '';
	foreach ($items as $item) {
		$return .= create_wanted_item_detail($item);
	}
	echo $return;
	die;
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
	// TODO Register in Database
	$accesss_key_id = 'AKIAJRLJRJDZGH57XPBA';
	$secret_access_key = '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';
	$associate_tag = '7072-3416-5582';

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
function get_confirmed_user_id($post_id){
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
			$giveme_from_others_name = sprintf('みんなからのください<span>%s</span>', get_count_giveme_from_others());
		}else{
			$giveme_from_others_name = 'みんなからのください';
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

	}
}
 
add_action( 'bp_setup_nav', 'my_setup_nav', 1000 );

/**********************************************
 * 「出品一覧」表示時に使う関数一式
 **********************************************
 */
function entry_list_title() {
	echo '出品一覧';
}

function entry_list_content() {
	include_once "members/single/entry-list.php";
}

function entry_list_link(){
	add_action( 'bp_template_title', 'entry_list_title' );
	add_action( 'bp_template_content', 'entry_list_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}


function entry_list_notinprogress_title() {
	echo 'ください待ちの出品一覧';
}

function entry_list_notinprogress_content() {
	include_once "members/single/entry-list-notinprogress.php";
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
	include_once "members/single/entry-list-toconfirm.php";
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
	include_once "members/single/entry-list-inprogress.php";
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
	include_once "members/single/entry-list-finished.php";
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
 * 「新規出品」表示時に使う関数一式
 **********************************************
 */
function get_new_entry_url() {
	return bp_loggedin_user_domain() . "new_entry/";
}

function new_entry_title() {
	echo '新規出品';
}

function new_entry_content() {
	include_once "members/single/new-entry.php";
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
	include_once "members/single/to-wanted-list.php";
}

function to_wanted_list_link(){
	add_action( 'bp_template_title', 'to_wanted_list_title' );
	add_action( 'bp_template_content', 'to_wanted_list_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function create_wanted_item_detail($item){
	global $user_ID;

	$return = '';
	$return .= '<div class="item_detail" id="' . $item->wanted_item_id . '" style="height:160px;margin:15px 5px 15px 5px;">';
	$return .= '<img src="' . $item->image_url . '" style="float:left;">';

	$return .= '<div id="title_'. $item->wanted_item_id .'"><strong>' . $item->item_name . '</strong></div>';
	$return .= '<div>ほしがっている人:' . get_user_by('id', $item->user_id)->display_name . '</div>';
	if($item->ASIN){
		$return .= '<ul>';
		$i = get_search_result_from_amazon(array('keyword'=>$item->ASIN))->Items->Item;
		if($i->ItemAttributes->Author){
			$return .= '<li name="author">著者:' . $i->ItemAttributes->Author . '</li>';
		}
		if($i->ItemAttributes->Publisher){
			$return .= '<li>出版社:' . $i->ItemAttributes->Publisher . '</li>';
		}
		if($i->ItemAttributes->ReleaseDate){
			$return .= '<li>発行日:' . $i->ItemAttributes->ReleaseDate . '</li>';
		}
		if($i->ItemAttributes->ListPrice->FormattedPrice){
			$return .= '<li>価格:' . $i->ItemAttributes->ListPrice->FormattedPrice . '</li>';
		}
		$return .= '</ul>';
	}
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
		$return .= '<input type="button" class="button_del_exhibition_to_wanted" id="button_'. $item->wanted_item_id .'" value="出品済" wanted_item_id="' . $item->wanted_item_id .'" post_id="' . $post_id .'">';
	}else{
		$return .= '<input type="button" class="button_exhibit_to_wanted" id="button_'. $item->wanted_item_id .'" value="出品" wanted_item_id="' . $item->wanted_item_id .'">';
	}
	$return .= '</div>';
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
	?>
	<div id="giveme-from-others" class="giveme-from-others">
		<?php if(get_count_giveme_from_others() > 0 ){ ?>
		以下の商品にくださいリクエストが来ています。取引相手、取引方法を選んで確定させてください。
		<?php }else{ ?>
		くださいリクエストがきている商品はありません。
		<?php }?>
		<?php 
			$givemes = get_giveme_from_others_list();
			$last_post_id = "";
			foreach($givemes as $giveme){
				if($last_post_id != $giveme->post_id){
					if($last_post_id != ""){
		?>
					</p>
					<label for="tradeway_<?php echo $last_post_id; ?>" >取引方法:</label>
					<select id="tradeway_<?php echo $last_post_id; ?>" name="tradeway_<?php echo $last_post_id; ?>" onChange="onChangeTradeWay(<?php echo $last_post_id; ?>)">
						<option value="handtohand">直接手渡し</option>
						<option value="delivery">配送</option>
					</select></br>
					<div id="handtohand-option_<?php echo $last_post_id; ?>">
					受渡希望日時:</br>
					<?php for ($k=1; $k < 4; $k++) { ?>
						第<?php echo $k?>希望<?php echo $k==1?"(必須)":""; ?></br>
						<select id="month_<?php echo $last_post_id; ?>_<?php echo $k; ?>" name="month_<?php echo $last_post_id; ?>_<?php echo $k; ?>">
							<?php echo $k==1?"":"<option value=''>--</option>" ?>
							<?php for ($i=1; $i<13; $i++) { 
								echo '<option value="' . $i . '">' . $i . '</option>';
							}?>
						</select>月
						<select id="date_<?php echo $last_post_id; ?>_<?php echo $k; ?>" name="date_<?php echo $last_post_id; ?>_<?php echo $k; ?>">
							<?php echo $k==1?"":"<option value=''>--</option>" ?>
							<?php for ($i=1; $i<32; $i++) { 
								echo '<option value="' . $i . '">' . $i . '</option>';
							}?>
						</select>日
						<select id="tradetime_<?php echo $last_post_id; ?>_<?php echo $k; ?>" name="tradetime_<?php echo $last_post_id; ?>_<?php echo $k; ?>">
								<?php echo $k==1?"":"<option value=''>--</option>" ?>
								<option value="AM">AM</option>;
								<option value="PM">PM</option>';
						</select></br>
					<?php } ?>
						受渡希望場所(必須):</br>
						<input type="text" id="place_<?php echo $last_post_id; ?>" name="place_<?php echo $last_post_id; ?>" size=30 maxlength=30></br>
						※原則、大学構内の場所を指定してください</br>
					</div>
					<label for="message_<?php echo $last_post_id; ?>">メッセージ:</label></br>
					<textarea id="message_<?php echo $last_post_id; ?>" name="message_<?php echo $last_post_id; ?>" rows=3 cols=30></textarea></br>
					<input type="button" value="確定" onClick="callOnConfirmGiveme(<?php echo $last_post_id; ?>)">
					</div><!-- #post_(id) -->
					<hr>
					<?php
					}
					?>
					<div id="post_<?php echo $giveme->post_id; ?>">
					<div class="posttitle"><?php echo get_post($giveme->post_id)->post_title; ?></div>
					<?php
					$last_post_id = $giveme->post_id;
				} ?>
				<p><input type="radio" name="sendto_user_<?php echo $giveme->post_id ?>" value="<?php echo $giveme->user_id ?>" id="post<?php echo $giveme->post_id; ?>_user<?php echo $giveme->user_id ?>"/><label for="<?php echo $giveme->display_name; ?>"><a href="<?php echo home_url() . "/members/" . $giveme->user_nicename ?>" id="<?php echo $giveme->user_id ?>_<?php echo $giveme->post_id; ?>"><?php echo $giveme->display_name; ?></a></label>
			<?php
			}
			?>
			<?php if($last_post_id != ""){ ?>
			</p>
			<label for="tradeway_<?php echo $last_post_id; ?>">取引方法:</label>
			<select id="tradeway_<?php echo $last_post_id; ?>" name="tradeway_<?php echo $last_post_id; ?>" onChange="onChangeTradeWay(<?php echo $last_post_id; ?>)">
				<option value="handtohand">直接手渡し</option>
				<option value="delivery">配送</option>
			</select></br>
			<div id="handtohand-option_<?php echo $last_post_id; ?>">
			受渡希望日時:</br>
				<?php for ($k=1; $k < 4; $k++) { ?>
					第<?php echo $k?>希望<?php echo $k==1?"(必須)":""; ?></br>
					<select id="month_<?php echo $last_post_id; ?>_<?php echo $k; ?>" name="month_<?php echo $last_post_id; ?>_<?php echo $k; ?>">
						<?php echo $k==1?"":"<option value=''>--</option>" ?>
						<?php for ($i=1; $i<13; $i++) { 
							echo '<option value="' . $i . '">' . $i . '</option>';
						}?>
					</select>月
					<select id="date_<?php echo $last_post_id; ?>_<?php echo $k; ?>" name="date_<?php echo $last_post_id; ?>_<?php echo $k; ?>">
						<?php echo $k==1?"":"<option value=''>--</option>" ?>
						<?php for ($i=1; $i<32; $i++) { 
							echo '<option value="' . $i . '">' . $i . '</option>';
						}?>
					</select>日
					<select id="tradetime_<?php echo $last_post_id; ?>_<?php echo $k; ?>" name="tradetime_<?php echo $last_post_id; ?>_<?php echo $k; ?>">
							<?php echo $k==1?"":"<option value=''>--</option>" ?>
							<option value="AM">AM</option>;
							<option value="PM">PM</option>';
					</select></br>
				<?php } ?>
			受渡希望場所(必須):</br>
			<input type="text" id="place_<?php echo $last_post_id; ?>" name="place_<?php echo $last_post_id; ?>" size=30 maxlength=30></br>
			※原則、大学構内の場所を指定してください</br>
			</div>
			<label for="message_<?php echo $last_post_id; ?>">メッセージ:</label></br>
			<textarea id="message_<?php echo $last_post_id; ?>" name="message_<?php echo $last_post_id; ?>" rows=3 cols=30></textarea></br>
			<input type="button" value="確定" onClick="callOnConfirmGiveme(<?php echo $last_post_id; ?>);">
			</div>
			<hr>
			<?php } ?>
	</div>
	<?php
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
	?>
	<div id="your-giveme" class="your-giveme">
		あなたは以下の商品に「ください」中です。
		<?php 
			$givemes = get_your_giveme_list();
			foreach($givemes as $giveme){
		?>
			<h2 class="posttitle"><a href="<?php echo get_permalink($giveme->post_id) ?>">・<?php echo get_post($giveme->post_id)->post_title; ?></a></h2>
		<?php
			}
		?>
	</div>
	<?php
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
	include_once "members/single/new-wanted-list.php";
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
	include_once "members/single/show-wanted-list.php";
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

function get_others_wanted_list($user_id, $keyword, $wanted_item_id=0){
	global $wpdb;
	global $table_prefix;
	$wanted_list;
	$sql = "SELECT wanted_item_id, user_id, item_name, ASIN, image_url
			FROM ". $table_prefix . "fmt_wanted_list";
	$sql .= " WHERE user_id <> %d";
	$sql .= " AND item_name LIKE '%s'";
	if($wanted_item_id){
		$sql .= " AND wanted_item_id = %d";
	}else{
		$sql .= " AND wanted_item_id <> %d";		
	}
	$sql .=	" ORDER BY wanted_item_id desc";
	$wanted_list = $wpdb->get_results($wpdb->prepare($sql, $user_id, '%' . $keyword . '%', $wanted_item_id));
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

add_shortcode('home_url','home_url');
add_shortcode('social_login_button','social_login_button');


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
?>