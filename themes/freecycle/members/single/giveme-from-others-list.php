<?php
function echo_map_section($last_post_id){
	global $user_ID;
	$map_indexes = get_trade_map_indexes();
	$mylocation = get_user_meta($user_ID, "default_trade_location", true);
echo <<<MAP_SECTION
	<span class="label">取引場所:</span>
MAP_SECTION;
	if(!$mylocation){
		echo "<p>標準取引場所を設定すると、取引場所を選択する手間が省けます。<a href='".
			bp_displayed_user_domain() . bp_get_settings_slug() . "/detail'>こちらから設定してください。</a></p>";
		$mylocation = get_default_map()->map_id;
	};
echo <<<MAP_SECTION
	<select name="map_search" id="map_search_$last_post_id">
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
						<span class="label">メッセージ : </span>例 今週は水曜から金曜の午後に名古屋大学にいるので、大学周辺でよければその時間に渡せます！ご都合いかがですか？</p>
						<textarea id="message_$last_post_id" name="message_$last_post_id" class="trade_message" rows=3 cols=30></textarea></br>
						<input type="button" value="確定" onClick="callOnConfirmGiveme($last_post_id)">
					</div><!-- #post_(id) -->
				</div>
MESSAGE_SECTION;
} ?>
<div id="giveme-from-others" class="giveme-from-others standard-form">
	<?php if(get_count_giveme_from_others() > 0 ){ ?>
	以下の商品にくださいリクエストが来ています。取引相手、取引場所を選んで確定させてください。
	<?php }else{ ?>
	くださいリクエストがきている商品はありません。
	<?php }?>
	<?php
		$givemes = get_giveme_from_others_list();
		$last_post_id = "";
		foreach($givemes as $giveme){
			$post = get_post($giveme->post_id);
			if($last_post_id != $giveme->post_id){
				if($last_post_id != ""){
	?>

	<?php
	echo "</select>";
	echo '<input type = "button" value = "プロフィール確認"  onclick ="   linkToOthersprofile('.$last_post_id.');" >';
	echo_map_section($last_post_id);
	echo_message_section($last_post_id);
	?>
				<hr>
				<?php
				}
				?>
				<div class="posts-row">
					<div id="post_<?php echo $giveme->post_id; ?>">
					<div id="post-<?php echo $post->ID; ?>" <?php post_class('post'); ?> class="entry-on-index">
						<div class="post-content">
							<div class="entry">
							<a href="<?php echo get_permalink($post->ID); ?>" class="post-img-contents"><?php echo get_the_post_thumbnail($post->ID, array(150, 150)) ?></a>
							<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
							<span class="index-item-title"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></span>
							</div>
						</div><!-- post-content -->
					</div><!-- post名 -->
					<div id="post-dummy" <?php post_class('post'); ?> class="entry-on-index">
						<div class="post-content">
							<div class="entry">
							</div>
						</div><!-- post-content -->
					</div><!-- post名 -->
		<span class="label">取引相手:</span>
				<?php
				$last_post_id = $giveme->post_id;
				?>
		<select id='postID_<?php echo $last_post_id;?>'>
		<?php
				echo '<option label="-- 選択 --" value="" >';
			}
				echo '<option name="sendto_user_' . $giveme->post_id . '" value="' . $giveme->user_id . '" data-nicename = "'.$giveme->user_nicename.'">' . $giveme->display_name . '</option>';
		}
		?>


		<?php if($last_post_id != ""){ ?>
	</div> <!-- 取引相手 -->
	<?php
	echo "</select>";
	echo '<input type = "button" value = "プロフィール確認"  onclick ="   linkToOthersprofile('.$last_post_id.');" >';
	echo_map_section($last_post_id);
	echo_message_section($last_post_id);
	?>
		<hr>
		<?php } ?>
</div>
