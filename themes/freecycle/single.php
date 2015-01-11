<?php get_header(); ?>
	<script type="text/javascript">
		/**
		 This function is called when finish button is clicked.
		 */
		function onFinish(){
			disableButtons();
			if(confirm("商品の受け渡しが完了していますか？")){
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
						alert("取引が完了しました。落札者の評価を行ってください！");
						enableButtons();
					}
				});
			}else{
				enableButtons();
			}
		}
		
		/**
		 This function is called when giveme button is clicked.
		 */
		function onGiveme(){
			disableButtons();
			if(confirm("くださいリクエストをします。よろしいですか？")){
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
						alert(msg);
						enableButtons();
					}
				});
			}else{
				enableButtons();
			}
		}

		/**
		 This function is called when cancelGiveme button is clicked.
		 */
		function onCancelGiveme(){
			disableButtons();
			if(confirm("くださいリクエストを取消します。よろしいですか？")){
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
						alert(msg);
						enableButtons();
					}
				});
			}else{
				enableButtons();
			}
		}
		
		function onExhibiterEvaluation(){
			var score = jQuery("#score").val();
			var comment = jQuery("#trade_comment").val();

			disableButtons();
			// check input values
			if(score === "invalid"){
				alert("評価を選択してください。");
				enableButtons();
				return;
			}else if(comment.length > 100){
				alert("コメントは100文字以内で記入してください。");
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
					alert("取引評価を行いました！");
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
				alert("評価を選択してください。");
				enableButtons();
				return;
			}else if(comment.length > 100){
				alert("コメントは100文字以内で記入してください。");
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
					alert("取引評価を行いました！");
					enableButtons();
				}
			});
		}
		
		function onDeletePost(){
			if(confirm("取り消した出品は復活できません。よろしいですか？")){
				// send values
				jQuery.ajax({
					type: "POST",
					url: '<?php echo admin_url('admin-ajax.php'); ?>',
					data: {
						"action": "delete_post",
						"postID": "<?php echo $post->ID ?>",
						"userID": "<?php echo $user_ID ?>"
					},
					success: function(msg){
						alert("出品を取り消しました。");
						location.href = "<?php echo home_url(); ?>";
					},
					false: function(msg){
						alert("取り消しに失敗しました。");
					}
				});
			}else{
				return false;
			}
		}

		function onCancelTradeFromExhibitor(){
			// 確認ダイアログを表示
			if(confirm('現在の相手との取引をキャンセルします。よろしいですか？')){
			　　　　jQuery.ajax({
				　　　type: "POST",
					url: '<?php echo admin_url('admin-ajax.php'); ?>',
					data: {
						"action": "cancel_trade_from_exhibitor",
						"postID": "<?php echo $post->ID ?>"
					},
					success: function(msg){
						jQuery('<a href="javaScript:onDeletePost();">出品取り消し</a>').replaceAll(jQuery("#cancelTradeFromExhibitor"));
						alert(msg);						
					}
				});　　
			}
			// OKが押されたら取り消し処理(Ajax)を動かす。	
		}
		
		function onCancelTradeFromBidder(){
			// 確認ダイアログを表示
			if(confirm('取引をキャンセルします。よろしいですか？')){
			　　　　jQuery.ajax({
				　　　　type: "POST",
					url: '<?php echo admin_url('admin-ajax.php'); ?>',
					data: {
						"action": "cancel_trade_from_bidder",
						"postID": "<?php echo $post->ID ?>"
					},
					success: function(msg){
						alert(msg);	
                        location.href = "<?php echo home_url(); ?>";					
					}
				});　　
			}
			// OKが押されたら取り消し処理(Ajax)を動かす。	
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

		function onEdit(itemStatus){
			//表示内容の削除
			var content = document.getElementById("post-content-edit");
			while(content.firstChild){
				content.removeChild(content.firstChild);
			}

			//編集内容の表示
			var templete = document.getElementById("edit");
			var newNode = templete.cloneNode(true);
			newNode.style.display = '';
			newNode.id = 'edit_content';
			content.appendChild(newNode);

			//出品物の状態を保持
			var displayMap = {
				"verygood" : 0,
				"good"     : 1,
				"bad"      : 2
			};
			var targetOption = document.getElementById("eval" + displayMap[itemStatus]);
			targetOption.setAttribute("selected", "selected");
		}

		function onFinishEdit(){
			jQuery.ajax({
				type : "POST",
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					action : "edit_item"
				},
				success : function(msg){
					location.reload();
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
							

						<div class="author-box" >
						<?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
						<p><?php printf( _x( 'by %s', 'Post written by...', 'buddypress' ), str_replace( '<a href=', '<a rel="author" href=', bp_core_get_userlink( $post->post_author ) ) ); ?></p>
						</div>			
					

					<div class="post-content" id="post-content-edit">
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
									この商品は「ください」待ちです。<br>
									<input type="button" id="edit" value="編集" onClick='onEdit("<?php echo $item_status[0]; ?>");'>
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

	<div id="edit" style="display : none">
	<form id="edit_form" method="post">
	<?php if($attachments){
			foreach($attachments as $attachment){
				echo wp_get_attachment_image( $attachment->ID, $size);
			}
	} ?>
	<br>
	<label>商品名</label><br><input type="text" name="item_name" value="<?php echo get_the_title(); ?>" ><br>
	<label>状態</label><br><select name="item_status" >
				<option id="eval0" value="verygood"><?php echo get_display_item_status("verygood"); ?></option>
				<option id="eval1" value="good" ><?php echo get_display_item_status("good"); ?></option>
				<option id="eval2" value="bad"><?php echo get_display_item_status("bad"); ?></option>
			</select><br>
	<label>商品説明</label><br><textarea rows="5" cols="40" name="item_exp" ><?php remove_filter('the_content', 'wpautop'); the_content(); ?></textarea></br>
	<input type="button" value="編集完了" onClick="onFinishEdit();">
	</form>
	</div><!-- hidden_content -->

	<?php get_sidebar(); ?>

<?php get_footer(); ?>