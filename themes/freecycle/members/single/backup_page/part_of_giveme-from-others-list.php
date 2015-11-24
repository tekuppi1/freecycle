<div id="giveme-from-others" class="giveme-from-others standard-form">
	<?php if(get_count_giveme_from_others() > 0 ){ ?>
	<!-- 以下の商品にくださいリクエストが来ています。取引相手、取引場所を選んで確定させてください。 -->
	現在、ユーザ同士での取引は停止されています。
	<?php }else{ ?>
	<!-- くださいリクエストがきている商品はありません。 -->
	現在、ユーザ同士での取引は停止されています。
	<?php }?>
	<?php
		$givemes = get_giveme_from_others_list("post_id,insert_timestamp");
		$last_post_id = "";
		foreach($givemes as $giveme){
			$post = get_post($giveme->post_id);
			if($last_post_id != $giveme->post_id){
				if($last_post_id != ""){
	?>

	<?php
	echo "</select></br>";
	echo '<input type="button" class="profilebutton" id="profile_'.$last_post_id.'" value="↑プロフィール確認" onclick="linkToOthersProfile('.$last_post_id.');" disabled="disabled"></br>';
	echo '<script>switchProfileButtonDisabled('.$last_post_id.');</script>';
	echo '上のボタンを押すと、選ぼうとしている取引相手のプロフィールを、確認することができます。</br>';
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
				
		<span class="labels">1.取引相手を選ぶ</span></br>

		<?php
			$last_post_id = $giveme->post_id;

		?>
		<select id='postID_<?php echo $last_post_id;?>' onchange="switchProfileButtonDisabled(<?php echo $last_post_id; ?>);" disabled>
		<?php
				echo '<option label=" -- 取引相手(くださいされた日) -- " value="" data-nicename="">';
			}
				echo '<option name="sendto_user_' . $giveme->post_id . '" value="' . $giveme->user_id . '" data-nicename = "'.$giveme->user_nicename.'">';
				if (mb_strlen($giveme->display_name) > 10) {
					echo mb_substr($giveme->display_name,0,10).'...';
				}else{
					echo $giveme->display_name;
				}
				echo '('.date('Y/n/j',strtotime($giveme->insert_timestamp)).')</option>';
		}
		?>


		<?php if($last_post_id != ""){ ?>
	</div> <!-- 取引相手 -->
	<?php
	echo "</select></br>";
	echo '<input type="button" class="profilebutton" id="profile_'.$last_post_id.'" value="↑プロフィール確認" onclick="linkToOthersProfile('.$last_post_id.');" disabled="disabled"></br>';
	echo '<script>switchProfileButtonDisabled('.$last_post_id.');</script>';
	echo '上のボタンを押すと、選ぼうとしている取引相手のプロフィールを、確認することができます。</br>';
	echo_map_section($last_post_id);
	echo_message_section($last_post_id);
	?>
		<hr>
		<?php } ?>
</div>
