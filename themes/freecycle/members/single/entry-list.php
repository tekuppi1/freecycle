	<div id="entrylist-labels" class="entrylist-labels">
		<span id="posttitle-label" class="posttitle-label">商品名</span>
		<span id="poststatus-label" class="poststatus-label">状態</span>
		<span id="posttodo-label" class="posttodo-label">やること</span>
	</div>
<?php
	global $user_ID;
	$entry_list = get_entry_list($user_ID);
	foreach($entry_list as $entry){
?>
	<div id="post_<?php echo $entry->post_id; ?>" class="entry">
		<span class="posttitle"><a href="<?php echo get_permalink($entry->post_id) ?>"><?php echo get_post($entry->post_id)->post_title; ?></a></span>
		<span class="poststatus">
			<?php
				if(isFinish($entry->post_id)){
					if(isBidderEvaluated($entry->post_id)){
					echo "取引完了";
					}else{
					echo "落札者未評価";
					}
				}elseif(isConfirm($entry->post_id)){
					echo "取引未完了";
				}elseif(isGiveme($entry->post_id)){
					echo "落札者未確定";
				}else{
					echo "ください待ち";
				}
			?>
		</span>
		<span class="posttodo"><a href="<?php echo get_permalink($entry->post_id) ?>">
			<?php
				if(isFinish($entry->post_id)){
					if(isBidderEvaluated($entry->post_id)){
					echo "";
					}else{
					echo "落札者を評価してください。";
					}
				}elseif(isConfirm($entry->post_id)){
					echo "商品を受渡し、取引を確定してください。";
				}elseif(isGiveme($entry->post_id)){
					echo "落札者を確定してください。";
				}else{
					echo "";
				}
			?>
		</a></span>
	</div>
<?php
	}
?>