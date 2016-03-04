<?php

function echo_map_section($last_post_id){
	global $user_ID;
	$map_indexes = get_trade_map_indexes();
	$mylocation = get_user_meta($user_ID, "default_trade_location", true);
echo <<<MAP_SECTION
	<span class="labels">2.取引場所を選ぶ</span></br>
MAP_SECTION;
	if(!$mylocation){
		echo "<p>標準取引場所を設定すると、取引場所を選択する手間が省けます。<a href='".
			bp_displayed_user_domain() . bp_get_settings_slug() . "/detail'>こちらから設定してください。</a></p>";
		$default_location = get_default_map();
		if($default_location){
			// if the system default map is set
			$mylocation = $default_location->map_id;
		}else{
			// if the system default map is not set
			echo "<p><b>Notice:</b> デフォルトの取引場所が設定されていないため地図が表示できません。</p>";
			return;
		}
	};
echo <<<MAP_SECTION
	<select name="map_search" id="map_search_$last_post_id" disabled>
		<option value="">-- 選択 --</option>
MAP_SECTION;
	foreach ($map_indexes as $index) {
		$children = get_child_trade_map($index->map_id);
		foreach ($children as $child) {
			$selected = "";
			if($child->map_id == $mylocation){
				$selected = "selected";
			}
			echo "<option value='$child->map_id' $selected>{$index->name}&nbsp-&nbsp$child->name</option>";

		}
	}
echo <<<MAP_SECTION
	</select>
	<div id="map_canvas_$last_post_id" name="map-canvas">
	</div>
MAP_SECTION;
}
function echo_message_section($last_post_id){
	$uri = get_stylesheet_directory_uri();
	echo <<<MESSAGE_SECTION
	</p>
						<span class="labels">3.メッセージを書く</span></br>例 今週は水曜から金曜の午後に名古屋大学にいるので、大学周辺でよければその時間に渡せます！ご都合いかがですか？</p>
						<textarea id="message_$last_post_id" name="message_$last_post_id" class="trade_message" rows=3 cols=30 disabled></textarea></br>
						<input type="button" class="decision" value="取引内容を確定する" onClick="callOnConfirmGiveme($last_post_id)" disabled>
					</div><!-- #post_(id) -->
				</div>
MESSAGE_SECTION;
} 

echo '現在、ユーザ間での取引は停止されています';

?>

