<?php get_header(); ?>
	<script type="text/javascript">
		/**
		 This function is called when finish button is clicked.
		 */
		function onFinish(){
			disableButtons();
			swal({   
				title: "商品の受け渡しが完了していますか？",      
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "はい",   
				cancelButtonText: "いいえ",   
				closeOnConfirm: false,   
				closeOnCancel: true 
			}, 
			function(isConfirm){
				if (isConfirm) {
					jQuery.ajax({
						type: "POST",
						url: '<?php echo admin_url('admin-ajax.php'); ?>',
						data: {
							"action": "finish",
							"postID": "<?php echo $post->ID ?>",
							"userID": "<?php echo $user_ID ?>"
						},
						success: function(msg){
							afterFinish();
							swal({   
								title: "取引が完了しました。",  
								text: "落札者の評価を行ってください！",
								type: "success",   
								showCancelButton: false,   
								confirmButtonColor: "#AEDEF4", 
								confirmButtonText: "OK",      
								closeOnConfirm: true
							});
							enableButtons();
						}
					});
				}else{
				enableButtons();
				} 
			});
		}

		function onGiveme(){
			disableButtons();
			swal({   
				title: "くださいリクエストをします。",   
				text: "よろしいですか？",     
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "はい",   
				cancelButtonText: "いいえ",   
				closeOnConfirm: false,   
				closeOnCancel: true 
			}, 
			function(isConfirm){
				if (isConfirm) {
					jQuery.ajax({
						type: "POST",
						url: '<?php echo admin_url('admin-ajax.php'); ?>',
						data: {
							"action": "giveme",
							"postID": "<?php echo $post->ID ?>",
							"userID": "<?php echo $user_ID ?>"
						},
						success: function(msg){
							switchGiveme();
							swal(msg);
							enableButtons();
						}
					});
				} else {
					enableButtons();   
				} 
			});
		}
		
		/**
		 This function is called when cancelGiveme button is clicked.
		 */
		function onCancelGiveme(){
			disableButtons();
			swal({   
				title: "くださいリクエストを取消します。",   
				text: "よろしいですか？",   
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "はい",   
				cancelButtonText: "いいえ",   
				closeOnConfirm: false,   
				closeOnCancel: true 
			}, 
			function(isConfirm){
				if (isConfirm) {
					jQuery.ajax({
						type: "POST",
						url: '<?php echo admin_url('admin-ajax.php'); ?>',
						data: {
							"action": "cancelGiveme",
							"postID": "<?php echo $post->ID ?>",
							"userID": "<?php echo $user_ID ?>"
						},
						success: function(msg){
							switchGiveme();
							swal(msg);
							enableButtons();
						}
					});
				}else{
					enableButtons();
				}
			});
		}	
		
		function onExhibiterEvaluation(){
			var score = jQuery("#score").val();
			var comment = jQuery("#trade_comment").val();

			disableButtons();
			// check input values
			if(score === "invalid"){
				swal({   
					title: "評価を選択してください。",  
					type: "error",   
					showCancelButton: false,   
					confirmButtonColor: "#AEDEF4", 
					confirmButtonText: "OK",      
					closeOnConfirm: false
				}); 
				enableButtons();
				return;
			}else if(comment.length > 100){
				swal({   
					title: "コメントは100文字以内で記入してください。",  
					type: "error",   
					showCancelButton: false,   
					confirmButtonColor: "#AEDEF4", 
					confirmButtonText: "OK",      
					closeOnConfirm: false
				}); 
				enableButtons();
				return;
			}
			// send values
			jQuery.ajax({
				type: "POST",
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					"action": "exhibiter_evaluation",
					"postID": "<?php echo $post->ID ?>",
					"userID": "<?php echo $user_ID ?>",
					"score" : score,
					"comment" : comment
				},
				success: function(msg){
					afterEvaluation();
					swal({   
						title: "取引評価を行いました！",  
						type: "success",   
						showCancelButton: false,   
						confirmButtonColor: "#AEDEF4", 
						confirmButtonText: "OK",      
						closeOnConfirm: false
					}); 
					enableButtons();
				}
			});
		}
		
		function onBidderEvaluation(){
			disableButtons();

			var score = jQuery("#score").val();
			var comment = jQuery("#trade_comment").val();
			// check input values
			if(score === "invalid"){
				swal({   
					title: "評価を選択してください。",  
					type: "error",   
					showCancelButton: false,   
					confirmButtonColor: "#AEDEF4", 
					confirmButtonText: "OK",      
					closeOnConfirm: false
				}); 
				enableButtons();
				return;
			}else if(comment.length > 100){
				swal({   
					title: "コメントは100文字以内で記入してください。",  
					type: "error",   
					showCancelButton: false,   
					confirmButtonColor: "#AEDEF4", 
					confirmButtonText: "OK",      
					closeOnConfirm: false
				}); 
				enableButtons();
				return;
			}
			// send values
			jQuery.ajax({
				type: "POST",
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					"action": "bidder_evaluation",
					"postID": "<?php echo $post->ID ?>",
					"userID": "<?php echo $user_ID ?>",
					"score" : score,
					"comment" : comment
				},
				success: function(msg){
					afterEvaluation();
					swal({   
						title: "取引評価を行いました！",  
						type: "success",   
						showCancelButton: false,   
						confirmButtonColor: "#AEDEF4", 
						confirmButtonText: "OK",      
						closeOnConfirm: false
					});
					enableButtons();
				}
			});
		}
		
		function onDeletePost(){
						swal({   
				title: "取り消した出品は復活できません。",   
				text: "よろしいですか？",   
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "はい",   
				cancelButtonText: "いいえ",   
				closeOnConfirm: false,   
				closeOnCancel: true 
			}, 
			function(isConfirm){
				if (isConfirm) {
					jQuery.ajax({
						type: "POST",
						url: '<?php echo admin_url('admin-ajax.php'); ?>',
						data: {
							"action": "delete_post",
							"postID": "<?php echo $post->ID ?>",
							"userID": "<?php echo $user_ID ?>"
						},
						success: function(msg){
							swal({   
								title: "商品を取り消しました。",  
								type: "success",   
								showCancelButton: false,   
								confirmButtonColor: "#AEDEF4", 
								confirmButtonText: "OK",      
								closeOnConfirm: false
							}); 
							location.href = "<?php echo home_url(); ?>";
						},
						false: function(msg){
							swal({   
								title: "商品を取り消しに失敗しました。",  
								type: "error",   
								showCancelButton: false,   
								confirmButtonColor: "#AEDEF4", 
								confirmButtonText: "OK",      
								closeOnConfirm: false
							}); 
						}
					});
				}else{
				return false;
				}
			});
		}

		function onCancelTradeFromExhibitor(){
			// 確認ダイアログを表示
			swal({   
				title: "取引をキャンセルします。",   
				text: "よろしいですか？",   
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "はい",   
				cancelButtonText: "いいえ",   
				closeOnConfirm: false,   
				closeOnCancel: true,
			}, 
			function(isConfirm){   
				if (isConfirm) {     
					jQuery.ajax({
				　　　type: "POST",
					url: '<?php echo admin_url('admin-ajax.php'); ?>',
					data: {
						"action": "cancel_trade_from_exhibitor",
						"postID": "<?php echo $post->ID ?>"
					},
					success: function(msg){
						jQuery('<a href="javaScript:onDeletePost();">出品取り消し</a>').replaceAll(jQuery("#cancelTradeFromExhibitor"));
						swal(msg);						
					}
				})
				} 
			});
		}
		
		function onCancelTradeFromBidder(){
			// 確認ダイアログを表示
			swal({   
				title: "取引をキャンセルします。",   
				text: "よろしいですか？",   
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "はい",   
				cancelButtonText: "いいえ",   
				closeOnConfirm: false,   
				closeOnCancel: true,
			}, 
			function(isConfirm){   
				if (isConfirm) {     
			　		jQuery.ajax({
				　　　	type: "POST",
						url: '<?php echo admin_url('admin-ajax.php'); ?>',
						data: {
							"action": "cancel_trade_from_bidder",
							"postID": "<?php echo $post->ID ?>"
						},
						success: function(msg){
							swal(msg);	
							location.href = "<?php echo home_url(); ?>";					
						}
					});
				} 
			});	
		}
		
		function switchGiveme(){
			if(jQuery("#giveme").size() > 0){
				jQuery('<input type="button" id="cancelGiveme" value="ください取消" onClick="onCancelGiveme();">').replaceAll(jQuery("#giveme"));
				
			}else{
				jQuery('<input type="button" id="giveme" value="ください" onClick="onGiveme();">').replaceAll(jQuery("#cancelGiveme"));
			}
		}
        
	
		
		function afterEvaluation(){
			jQuery("#evaluation").replaceWith("この商品は評価済です。");
		}

		function afterFinish(){
			jQuery("#finish").replaceWith('<div id="evaluation">落札者の評価:</br><select name="score" id="score"><option value="invalid" selected>--選択--</option><option value="5" >とても良い</option><option value="4" >良い</option><option value="3" >普通</option><option value="2" >悪い</option><option value="1" >とても悪い</option></select></br>コメント(任意 100字以内)</br><textarea name="trade_comment" id="trade_comment" rows="5" cols="40"></textarea></br><input type="button" id="evaluation" value="評価する" onClick="onBidderEvaluation();"></div>');
		}
		
		function updateComment(commentID, updatedComment){
			jQuery.ajax({
				type: "POST",
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					"action": "update_comment",
					"comment_ID": commentID,
					"comment_content": updatedComment
				},
				success: function(msg){

				}
			});
		}

	</script>
	
	
	
	<div id="content">
		<div class="padder">

			<?php do_action( 'bp_before_blog_single_post' ); ?>

					<div class="page" id="blog-single" role="main">

					　<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
							

						<div class="author-box">
						<?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
						<p><?php printf( _x( 'by %s', 'Post written by...', 'buddypress' ), str_replace( '<a href=', '<a rel="author" href=', bp_core_get_userlink( $post->post_author ) ) ); ?></p>
						</div>			
					

					<div class="post-content">
						<h2 class="posttitle"><?php the_title(); ?></h2>	
								
							
						<div class="item_status">状態:
						<?php
							$item_status = get_post_custom_values("item_status");
							echo get_display_item_status($item_status["0"]);
						?>
						</div>
						<div>
							学部,学科: <?php echo get_post_custom_values("department")["0"] ?>,<?php echo get_post_custom_values("course")["0"] ?>
						</div>
						<?php
						/*
						  display finish button or giveme button
						  if watching user doesn't log in, button is not shown
						 */
						?>
						
						<!-- when login user is author -->
						<?php if($user_ID == $authordata->ID){
								if(isFinish($post->ID)){
									if(isBidderEvaluated($post->ID)){ ?>
							<!-- when status is finish -->
									この商品は評価済です。
								<?php }else{ ?>
								
								<div id="evaluation">
									落札者の評価:</br>
									<select name="score" id="score">
										<option value="invalid" selected>--選択--</option>
										<option value="5" >とても良い</option>
										<option value="4" >良い</option>
										<option value="3" >普通</option>
										<option value="2" >悪い</option>
										<option value="1" >とても悪い</option>
									</select>
									</br>
									コメント(任意 100字以内)</br>
									<textarea name="trade_comment" id="trade_comment" rows="5" cols="40"></textarea></br>
									<input type="button" id="evaluation" value="評価する" onClick="onBidderEvaluation();">
								</div>
								
								<?php } ?>
							<?php }elseif(isConfirm($post->ID)){ ?>
							<!-- when status is confirm -->
							
						<input type="button" id="finish" value="取引完了" onClick="onFinish();">
							<?php }elseif(isGiveme($post->ID)){ ?>
							<!-- when status is giveme -->
						<a href="<?php echo get_giveme_from_others_url(); ?>">取引相手を確定させてください。</a>
							<?php }else{ ?>
									この商品は「ください」待ちです。
						<?php     } ?>
						
						<!-- when login user is not author -->
						<?php }elseif(!is_user_logged_in()){?>
						<!-- information is hidden for un-login users -->
						<?php }elseif(isConfirm($post->ID)){
								if(get_bidder_id($post->ID) == $user_ID){
									if(isExhibiterEvaluated($post->ID)){ ?>
										この商品は評価済です。
								<?php }elseif(isFinish($post->ID)){ ?>
								<div id="evaluation">
									出品者の評価:</br>
									<select name="score" id="score">
										<option value="invalid" selected>--選択--</option>
										<option value="5" >とても良い</option>
										<option value="4" >良い</option>
										<option value="3" >普通</option>
										<option value="2" >悪い</option>
										<option value="1" >とても悪い</option>
									</select>
									</br>
									コメント(任意 100字以内 改行も1文字と数えます)</br>
									<textarea name="trade_comment" id="trade_comment" rows="5" cols="40"></textarea></br>
									<input type="button" id="evaluation" value="評価する" onClick="onExhibiterEvaluation();">
								</div>
								<?php }else{ ?>
									取引完了待ちです。
								<?php } ?>
							<?php }else{ ?>
									この商品は取引相手が決まったため、「ください」はできません
							<?php } ?>
						<?php }elseif(is_user_logged_in()){
								if(doneGiveme($post->ID, $user_ID)){ ?>
						<input type="button" id="cancelGiveme" value="ください取消" onClick="onCancelGiveme();">
							<?php }elseif(get_usable_point($user_ID) > 0){ ?>
						<input type="button" id="giveme" value="ください" onClick="onGiveme();">
							<?php }else{ ?>
						使用可能なポイントが無いため「ください」できません。
							<?php } ?>
						<?php } ?>
						
						<p class="date">
							<!-- <span></span>がないと次の<span>がイタリックになる -->
							<?php printf( __( '%1$s <span></span>', 'buddypress' ), get_the_date()); ?>
							<!-- edit entry is not available -->
							<!-- <span class="post-utility alignright"><?php edit_post_link( __( 'Edit this entry', 'buddypress' ) ); ?></span> -->
							<?php if($user_ID == $authordata->ID && !isGiveme($post->ID)){ ?><span class="post-utility alignright"><a href="javaScript:onDeletePost();">出品取り消し</a></span><?php } ?>
							<?php if($user_ID == $authordata->ID && isConfirm($post->ID) && !isFinish($post->ID)){ ?><span class="post-utility alignright"><a href="javaScript:onCancelTradeFromExhibitor();" id="cancelTradeFromExhibitor">取引キャンセル</a></span><?php } ?>
							<?php if($user_ID == get_bidder_id($post->ID) && isConfirm($post->ID) && !isFinish($post->ID)){ ?><span class="post-utility alignright"><a href="javaScript:onCancelTradeFromBidder();" id="cancelTradeFromBidder">取引キャンセル</a></span><?php } ?>
						</p>

						<div class="entry">
						
							<p class="author_name"><?php printf( _x( 'by %s', 'Post written by...', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ); ?></p>
						
							<?php
							$args = array(
								'post_type' => 'attachment',
								'post_parent' => $post->ID
							);
							//define size
							$size = array(200, 200);
							$attachments = array_reverse(get_posts($args));
							if($attachments){
								foreach($attachments as $attachment){
									echo wp_get_attachment_image( $attachment->ID, $size);
								}
							}
							?>
							<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
							<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
							
							
						</div>
						
							<p class="postmetadata"><?php the_tags( '<span class="tags">' . __( 'Tags: ', 'buddypress' ), ', ', '</span>' ); ?>&nbsp;</p>

						<div class="alignleft"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'buddypress' ) . '</span> %title' ); ?></div>
						<div class="alignright"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'buddypress' ) . '</span>' ); ?></div>
					</div>

				</div>

			<?php if(is_user_logged_in()) {
						comments_template();
					}
			?>

			<?php endwhile; else: ?>

				<p><?php _e( 'Sorry, no posts matched your criteria.', 'buddypress' ); ?></p>

			<?php endif; ?>
				
				
		</div>

		<?php do_action( 'bp_after_blog_single_post' ); ?>
		
			
			
		</div><!-- .padder -->
	</div><!-- #content -->
	

	<?php get_sidebar(); ?>

<?php get_footer(); ?>