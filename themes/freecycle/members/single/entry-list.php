<?php
	if(has_todo_in_entry_list()){
?>
	<div class="alert-todo-text">
		出品した商品に「やること」が残っています！
	</div>
<?php } ?>
	<div id="entrylist-labels" class="entrylist-labels">
		<span id="posttitle-label" class="posttitle-label">商品名</span>
		<span id="poststatus-label" class="poststatus-label">状態</span>
		<span id="posttodo-label" class="posttodo-label">やること</span>
	</div>
<?php
	global $user_ID;
	$entry_list = get_posts(array('author' => $user_ID));
	foreach($entry_list as $entry){
?>
	<div id="post_<?php echo $entry->ID; ?>" class="entry">
		<span class="posttitle"><a href="<?php echo get_permalink($entry->ID) ?>"><?php echo get_post($entry->ID)->post_title; ?></a></span>
		<span class="poststatus">
			<?php
				if(isFinish($entry->ID)){
					if(isBidderEvaluated($entry->ID)){
					echo "取引完了";
					}else{
					echo "落札者未評価";
					}
				}elseif(isConfirm($entry->ID)){
					echo "取引未完了";
				}elseif(isGiveme($entry->ID)){
					echo "落札者未確定";
				}else{
					echo "ください待ち";
				}
			?>
		</span>
		<span class="posttodo">
			<?php
				if(isFinish($entry->ID)){
					if(isBidderEvaluated($entry->ID)){
					echo "";
					}else{
					echo "<a href='" . get_permalink($entry->ID) . "'>落札者を評価してください。";
					}
				}elseif(isConfirm($entry->ID)){
					echo "<a href='" . get_permalink($entry->ID) . "'>商品を受渡し、取引を完了させてください。";
				}elseif(isGiveme($entry->ID)){
					echo "<a href='" . get_giveme_from_others_url() . "'>落札者を確定してください。";
				}else{
					echo "";
				}
			?>
		</a></span>
	</div>
<?php
	}
?>