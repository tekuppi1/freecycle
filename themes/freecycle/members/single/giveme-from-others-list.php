<?php
function echo_map_section($last_post_id){
	$map_indexes = get_trade_map_indexes();

echo <<<MAP_SECTION
	<span class="label">取引場所:</span>
	<select name="map_search"  id="map_search_$last_post_id">
		<option value="">取引場所を選択</option>
MAP_SECTION;
	foreach ($map_indexes as $index) {
		echo "<option value=''>$index->name</option>";
		$children = get_child_trade_map($index->map_id);
		foreach ($children as $child) {
			echo "<option value='$child->latitude,$child->longitude'>&nbsp;&nbsp;$child->name</option>";

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
		<div class="radio">
		<span class="label">取引相手:</span>
				<?php
				$last_post_id = $giveme->post_id;
			} ?>
					<label><input type="radio" name="sendto_user_<?php echo $giveme->post_id ?>" value="<?php echo $giveme->user_id ?>" id="post<?php echo $giveme->post_id; ?>_user<?php echo $giveme->user_id ?>"/><a href="<?php echo home_url() . "/members/" . $giveme->user_nicename ?>" id="<?php echo $giveme->user_id ?>_<?php echo $giveme->post_id; ?>">&nbsp;<?php echo $giveme->display_name; ?></a></label>
		<?php
		}
		?>
		<?php if($last_post_id != ""){ ?>
	</div> <!-- 取引相手 -->
	<?php
	echo_map_section($last_post_id);
	echo_message_section($last_post_id);
	?>			
		<hr>
		<?php } ?>
</div>