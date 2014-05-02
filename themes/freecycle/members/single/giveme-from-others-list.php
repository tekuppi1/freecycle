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
			$post = get_post($giveme->post_id);
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
						<input type="text" id="place_<?php echo $last_post_id; ?>" name="place_<?php echo $last_post_id; ?>" placeholder="大学構内の場所を指定" size=30 maxlength=30></br>
					</br>
					</div><!-- #handtohand-option_$postID -->
						<label for="message_<?php echo $last_post_id; ?>">メッセージ:</label></br>
						<textarea id="message_<?php echo $last_post_id; ?>" name="message_<?php echo $last_post_id; ?>" rows=3 cols=30></textarea></br>
						<input type="button" value="確定" onClick="callOnConfirmGiveme(<?php echo $last_post_id; ?>)">
					</div><!-- #post_(id) -->
				</div><!-- .posts-row -->
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
		取引相手:
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
							<input type="text" id="place_<?php echo $last_post_id; ?>" name="place_<?php echo $last_post_id; ?>" placeholder="大学構内の場所を指定" size=30 maxlength=30></br>
							</br>
						</div><!-- #handtohand-option_$postID -->
						<label for="message_<?php echo $last_post_id; ?>">メッセージ:</label></br>
						<textarea id="message_<?php echo $last_post_id; ?>" name="message_<?php echo $last_post_id; ?>" rows=3 cols=30></textarea></br>
						<input type="button" value="確定" onClick="callOnConfirmGiveme(<?php echo $last_post_id; ?>);">
					</div><!-- #post_(id) -->
				</div><!-- .posts-row -->
		<hr>
		<?php } ?>
</div>