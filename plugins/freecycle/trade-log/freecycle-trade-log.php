<?php
// 取引状態を表す定数群
define('FC_TRADE_FINISHED', 'finished'); // 取引完了
define('FC_TRADE_BIDDER_EVALUATED', 'bidder_evaluated'); // 取引相手評価済
define('FC_TRADE_EXHIBITOR_EVALUATED', 'exhibitor_evaluated'); // 出品者評価済
define('FC_TRADE_ITEM_PASSED', 'item_passed'); // 商品受け渡し済
define('FC_TRADE_CONFIRMED', 'confirmed'); // 取引相手確定済
define('FC_TRADE_GIVEMEED', 'givemed'); // ください済
define('FC_TRADE_ENTRIED', 'entried'); // 出品済
define('FC_TRADE_UNKNOWN', 'unknown'); // 不明

/**
 * 取引状態を定数値で返します。
 * @param {int} $id 商品ID(postID)
 * @return {string} 取引状態の定数値を返します。詳細は上の定数群を参照してください。
 */
function get_trade_status($id){
	if(isFinish($id) && isBidderEvaluated($id) && isExhibiterEvaluated($id)){
		return FC_TRADE_FINISHED;
	}else if(isFinish($id) && isBidderEvaluated($id) && !isExhibiterEvaluated($id)){
		return FC_TRADE_BIDDER_EVALUATED;
	}else if(isFinish($id) && !isBidderEvaluated($id) && isExhibiterEvaluated($id)){
		return FC_TRADE_EXHIBITOR_EVALUATED;
	}else if(isFinish($id) && !isBidderEvaluated($id) && !isExhibiterEvaluated($id)){
		return FC_TRADE_ITEM_PASSED;
	}else if(!isFinish($id) && isConfirm($id)){
		return FC_TRADE_CONFIRMED;
	}else if(!isConfirm($id) && isGiveme($id)){
		return FC_TRADE_GIVEMEED;
	}else if(!isGiveme($id) && isEntry($id)){
		return FC_TRADE_ENTRIED;
	}else{
		return FC_TRADE_UNKNOWN;
	}
}

/**
 * くださいの履歴を返します。
 * @param {array} $options 検索条件
 *	count: trueなら検索結果の件数を返します
 *	period_from: 日付範囲検索の開始日付
 * 	period_to: 日付範囲検索の終了日付
 * @return {int/array} $result 検索結果
 *	$options['count'] = true のとき int
 *	それ以外のとき array[fmt_user_givemeオブジェクト]
 */
function get_giveme_log($options){
	global $wpdb, $table_prefix;
	$sql;
	$result;
	$where = [];
	$bind_values = [];

	if(isset($options['count']) && $options['count']){
		$sql = "SELECT count(*) FROM {$table_prefix}fmt_user_giveme ";
	}else{
		$sql = "SELECT * FROM {$table_prefix}fmt_user_giveme ";
	}

	// 日付範囲検索条件構築部分
	if(isset($options['period_from'])){
		array_push($where, "insert_timestamp >= %s");
		array_push($bind_values, $options['period_from']);
	}

	if(isset($options['period_to'])){
		array_push($where, "insert_timestamp <= %s");
		array_push($bind_values, $options['period_to']);
	}	

	if(count($where) > 0){
		$sql .= 'WHERE ' . implode($where, ' and ');
	}

	if(isset($options['count']) && $options['count']){
		$result = $wpdb->get_var($wpdb->prepare($sql, $bind_values));
	}else{
		$result = $wpdb->get_results($wpdb->prepare($sql, $bind_values));
	}

	return $result;
}

/**
 * 取引の履歴を返します。
 * 
 * @param {array} $options 検索条件
 * @return {int/array} $result 検索結果
 *	$options['count'] = true のとき int
 *	それ以外のとき array[fmt_user_givemeオブジェクト]
 */
function get_trade_log($options){
	global $wpdb, $table_prefix;
	$sql;
	$result;
	$where = [];
	$bind_values = [];

	if(isset($options['count']) && $options['count']){
		$sql = "SELECT count(*) FROM {$table_prefix}fmt_giveme_state ";
	}else{
		$sql = "SELECT * FROM {$table_prefix}fmt_giveme_state ";
	}

	// 固定検索条件
	array_push($where, "giveme_flg = 1 and entry_flg = 1");

	// 状態検索条件構築部分
	if(isset($options['state'])){
		switch ($options['state']) {
			case 'finished':
				array_push($where, "finished_flg = 1 and bidder_evaluated_flg = 1
					and exhibiter_evaluated_flg = 1 and confirmed_flg = 1");
				break;
			case 'bidder_evaluated':
				array_push($where, "finished_flg = 1 and bidder_evaluated_flg = 1
					and exhibiter_evaluated_flg = 0 and confirmed_flg = 1");
				break;
			case 'exhibitor_evaluated':
				array_push($where, "finished_flg = 1 and bidder_evaluated_flg = 0
					and exhibiter_evaluated_flg = 1 and confirmed_flg = 1");
				break;
			case 'item_passed':
				array_push($where, "finished_flg = 1 and bidder_evaluated_flg = 0
					and exhibiter_evaluated_flg = 0 and confirmed_flg = 1");
				break;
			case 'confirmed':
				array_push($where, "finished_flg = 0 and bidder_evaluated_flg = 0
					and exhibiter_evaluated_flg = 0 and confirmed_flg = 1");
				break;
			case 'giveme':
				array_push($where, "finished_flg = 0 and bidder_evaluated_flg = 0
					and exhibiter_evaluated_flg = 0 and confirmed_flg = 0");
				break;
			default:
				break;
		}
	}	

	// 日付範囲検索条件構築部分
	if(isset($options['period_from'])){
		array_push($where, "update_timestamp >= %s");
		array_push($bind_values, $options['period_from']);
	}

	if(isset($options['period_to'])){
		array_push($where, "update_timestamp <= %s");
		array_push($bind_values, $options['period_to']);
	}	

	if(count($where) > 0){
		$sql .= 'WHERE ' . implode($where, ' and ');
	}

	// 並べ替え
	if(isset($options['order'])){
		$order = ' ORDER BY';
		foreach ($options['order'] as $k => $v){
			$order .= " $k $v,";
		}
		$sql .= rtrim($order, ',');
		//return $sql;
	}	

	// 検索件数上限
	if(isset($options['limit'])){
		$sql .= ' LIMIT %d';
		array_push($bind_values, $options['limit']);
	}

	if(isset($options['count']) && $options['count']){
		$result = $wpdb->get_var($wpdb->prepare($sql, $bind_values));
	}else{
		$result = $wpdb->get_results($wpdb->prepare($sql, $bind_values));
	}

	return $result;
}