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
add_action('user_register', 'on_user_added');

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
	// 一覧ページには取引完了済みの記事を表示しない。
	if(is_front_page()){
		$where .= "AND (" . $table_prefix . "fmt_giveme_state.finished_flg <> 1 "
				. " OR " . $table_prefix . "fmt_giveme_state.finished_flg is NULL)";
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
			SET giveme_flg = 1
			WHERE post_id= %d",
			$postID));
	}else{
		$wpdb->query($wpdb->prepare("
			INSERT INTO " . $table_prefix . "fmt_giveme_state
			(post_id, giveme_flg)
			VALUES (%d, 1)",
			$postID));
	}
	
	// ログインユーザ→投稿記事に対して「ください」リクエストした記録をつける
	// 既にデータが登録済の場合は何もしません
	$current_giveme = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM " . $table_prefix . "fmt_user_giveme where user_id = %d and post_id = %d", $userID, $postID));
	if($current_giveme == 0){
		$wpdb->query($wpdb->prepare("
			INSERT INTO " . $table_prefix . "fmt_user_giveme
			(user_id, post_id)
			VALUES (%d, %d)",
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
		SET giveme_flg = 0
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
	$tradedate = $_POST['tradedate'];
	$tradetime = $_POST['tradetime'];
	$place = $_POST['place'];
	$message = $_POST['message'];

	// 記事の状態を確定済にする
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_giveme_state
		SET confirmed_flg = 1
		WHERE post_id = %d",
		$postID));
	
	// ユーザ確定情報の登録
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_user_giveme
		SET confirmed_flg = 1
		WHERE post_id = %d
		AND user_id = %d",
		$postID, $userID));

	// 取引履歴を登録
	$wpdb->query($wpdb->prepare("
		INSERT INTO " . $table_prefix . "fmt_trade_history
		(post_id, bidder_id, exhibiter_id)
		VALUES (%d, %d, %d)",
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
	// TODO sender_id は システム管理者のIDにしておくべきか。
	$content = 'あなたが以下の商品の取引相手に選ばれました！' . PHP_EOL . ' 【商品名】<a href="' . get_permalink($postID) . '">' . get_post($postID)->post_title . '</a>' . PHP_EOL;
	$content .= '【受渡方法】:' . get_display_tradeway($tradeway) . PHP_EOL;
	if($tradeway == "handtohand"){
		$content .= '以下の受渡希望条件を確認してください。問題なければ「OKです」と返信してください！' . PHP_EOL;
		$content .= 'もし不都合があれば、代わりの日時、場所を記入して返信してください。'. PHP_EOL;
		$content .= '【受渡希望日時】:' . $tradedate . ' ' . $tradetime . PHP_EOL;
		$content .= '【受渡希望場所】:' . $place . PHP_EOL;
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
		SET exhibiter_evaluated_flg = 1
		WHERE post_id = %d",
		$postID));
	
	// 取引履歴を更新
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_trade_history
		SET exhibiter_score = %d,
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
		SET bidder_evaluated_flg = 1
		WHERE post_id = %d",
		$postID));

	// 取引履歴を更新
	$wpdb->query($wpdb->prepare("
		UPDATE " . $table_prefix . "fmt_trade_history
		SET bidder_score = %d,
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
		SET finished_flg = 1
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

function delete_post(){
	wp_delete_post($_POST['postID']);
	die;
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
		SET got_points = got_points + %d
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
		SET temp_used_points = temp_used_points + %d
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
		SET used_points = temp_used_points + %d
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
		(user_id, got_points, temp_used_points, used_points)
		VALUES (%d, 3, 0, 0)",
		$user_id));
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
			"excellent" => "新品、未使用",
			"verygood"  => "未使用に近い",
			"good"		=> "目立った傷や汚れなし",
			"bad"		=> "やや傷や汚れあり",
			"verybad"	=> "傷や汚れあり",
			"poor"		=> "全体的に状態が悪い"
		);
	return $display_map[$item_status];
}

/**
 * 取引方法を表示用文字列に変換する関数。
 */
function get_display_tradeway($tradeway){
	$display_map = array(
			"handtohand" => "直接手渡し",
			"delivary"  => "配送"
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
	bp_core_remove_subnav_item(BP_MESSAGES_SLUG, 'compose');

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
			'position' => 65,
			'screen_function' => 'entry_list_link',
			'show_for_displayed_user' => true,
			'item_css_id' => 'entry-list'
			) );
	

		bp_core_new_nav_item( array( 
			'name' => __( '新規出品', 'buddypress' ), 
			'slug' => 'new_entry', 
			'position' => 65,
			'screen_function' => 'new_entry_link',
			'show_for_displayed_user' => true,
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

function has_todo_in_entry_list(){
	global $user_ID;
	$entry_list = get_posts(array('author' => $user_ID));
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
					<label for="tradeway_<?php echo $last_post_id; ?>">取引方法:</label>
					<select id="tradeway_<?php echo $last_post_id; ?>" name="tradeway_<?php echo $last_post_id; ?>">
						<option value="handtohand">直接手渡し</option>
						<!-- <option value="delivary">配送</option> -->
					</select></br>
					受渡希望日時:</br>
					<select id="year_<?php echo $last_post_id; ?>" name="year_<?php echo $last_post_id; ?>">
						<option value="<?php echo date('Y',strtotime('now'))?>"><?php echo date('Y',strtotime('now'))?></option>
						<option value="<?php echo date('Y',strtotime('+1 year'))?>"><?php echo date('Y',strtotime('+1 year'))?></option>
					</select>年
					<select id="month_<?php echo $last_post_id; ?>" name="month_<?php echo $last_post_id; ?>">
						<?php for ($i=1; $i<13; $i++) { 
							echo '<option value="' . $i . '">' . $i . '</option>';
						}?>
					</select>月
					<select id="date_<?php echo $last_post_id; ?>" name="date_<?php echo $last_post_id; ?>">
						<?php for ($i=1; $i<32; $i++) { 
							echo '<option value="' . $i . '">' . $i . '</option>';
						}?>
					</select>日
					<select id="tradetime_<?php echo $last_post_id; ?>" name="tradetime_<?php echo $last_post_id; ?>">
						<?php for ($i=10; $i<21; $i++) { 
							echo '<option value="' . $i . ':00">' . $i . ':00</option>';
							echo '<option value="' . $i . ':30">' . $i . ':30</option>';
						}?>
					</select>頃</br>
					受渡希望場所:</br>
					<input type="text" id="place_<?php echo $last_post_id; ?>" name="place_<?php echo $last_post_id; ?>" size=30 maxlength=30></br>
					※原則、大学構内の場所を指定してください</br>
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
				<p><input type="radio" name="sendto_user_<?php echo $giveme->post_id ?>" value="<?php echo $giveme->user_id ?>" id="post<?php echo $giveme->post_id; ?>_user<?php echo $giveme->user_id ?>"/><label for="<?php echo $giveme->display_name; ?>"><a href="<?php echo home_url() . "/members/" . $giveme->user_nicename ?>"><?php echo $giveme->display_name; ?></a></label>
			<?php
			}
			?>
			<?php if($last_post_id != ""){ ?>
			</p>
			<label for="tradeway_<?php echo $last_post_id; ?>">取引方法:</label>
			<select id="tradeway_<?php echo $last_post_id; ?>" name="tradeway_<?php echo $last_post_id; ?>">
				<option value="handtohand">直接手渡し</option>
				<!-- <option value="delivary">配送</option> -->
			</select></br>
			受渡希望日時:</br>
			<select id="year_<?php echo $last_post_id; ?>" name="year_<?php echo $last_post_id; ?>">
				<option value="<?php echo date('Y',strtotime('now'))?>"><?php echo date('Y',strtotime('now'))?></option>
				<option value="<?php echo date('Y',strtotime('+1 year'))?>"><?php echo date('Y',strtotime('+1 year'))?></option>
			</select>年
			<select id="month_<?php echo $last_post_id; ?>" name="month_<?php echo $last_post_id; ?>">
				<?php for ($i=1; $i<13; $i++) { 
					echo '<option value="' . $i . '">' . $i . '</option>';
				}?>
			</select>月
			<select id="date_<?php echo $last_post_id; ?>" name="date_<?php echo $last_post_id; ?>">
				<?php for ($i=1; $i<32; $i++) { 
					echo '<option value="' . $i . '">' . $i . '</option>';
				}?>
			</select>日
			<select id="tradetime_<?php echo $last_post_id; ?>" name="tradetime_<?php echo $last_post_id; ?>">
				<?php for ($i=10; $i<21; $i++) { 
					echo '<option value="' . $i . ':00">' . $i . ':00</option>';
					echo '<option value="' . $i . ':30">' . $i . ':30</option>';
				}?>
			</select>頃</br>
			受渡希望場所:</br>
			<input type="text" id="place_<?php echo $last_post_id; ?>" name="place_<?php echo $last_post_id; ?>" size=30 maxlength=30></br>
			※原則、大学構内の場所を指定してください</br>
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
add_action( 'admin_init', 'disable_admin_pages' );
function disable_admin_pages() {
	if(!current_user_can('administrator')){
		$redirect_url = get_option('home');
		header("Location: ".$redirect_url);
	}
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
?>