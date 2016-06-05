<?php get_header(); ?>

<script type="text/javascript">
function onDeletePost(){
	swal({
		title: "取り消した出品は復活できません。",
		text: "よろしいですか？",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "はい",
		cancelButtonText: "いいえ",
		closeOnConfirm: true,
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
						closeOnConfirm: true
					},
					function(){
						location.href = "<?php echo_entry_list_url(); ?>";
					});
				},
				false: function(msg){
					swal({
						title: "商品を取り消しに失敗しました。",
						type: "error",
						showCancelButton: false,
						confirmButtonColor: "#AEDEF4",
						confirmButtonText: "OK",
						closeOnConfirm: true
					});
				}
			});
		}else{return false;}
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

	//隠していた編集内容の削除
	while(templete.firstChild){
		templete.removeChild(templete.firstChild);
	}

	//出品物の状態を保持
	var statusLabel = {
		"verygood" : 0,
		"good"     : 1,
		"bad"      : 2
	};
	var targetOption = document.getElementById("eval" + statusLabel[itemStatus]);
	targetOption.setAttribute("selected", "selected");
	}
function onUpdateEdit(){
			var form = jQuery("#edit_form")[0];
			var fd = new FormData(form);
			fd.append("action", "edit_item");
			jQuery.ajax({
				type : "POST",
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				processData: false,
				contentType: false,
				mimeType: "multipart/form-data",
				data: fd,
				success : function(msg){
					location.reload();
				}
			});
		}
  /*これが拡大の機能でしょう
  function zoom(i){
	switch(i){
		case 0:jQuery("#zoom1").hide();jQuery("#zoom2").hide();jQuery("#zoom0").show();break;
		case 1:jQuery("#zoom2").hide();jQuery("#zoom0").hide();jQuery("#zoom1").show();break;
		case 2:jQuery("#zoom0").hide();jQuery("#zoom1").hide();jQuery("#zoom2").show();break;
	}
	*/
}

// 取引完了関数
function onFinish(postID){
	swal({
		title: "取引を完了します。",
		text: "よろしいですか？",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "はい",
		cancelButtonText: "いいえ",
		closeOnConfirm: true,
		closeOnCancel: true
	},
	function(isConfirm){
		if (isConfirm) {
			jQuery.ajax({
				type: "POST",
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					"action": "finish_trade",
					"post_id": postID
				},
				success: function(res){
					var result = JSON.parse(res);
					if(result.error === "true"){
						swal({
							title: "取引完了に失敗しました。",
							text: result.message,
							type: "error",
							showCancelButton: false,
							confirmButtonColor: "#AEDEF4",
							confirmButtonText: "OK",
							closeOnConfirm: true
						});
					}else{
						swal({
							title: "取引を完了しました。",
							type: "success",
							showCancelButton: false,
							confirmButtonColor: "#AEDEF4",
							confirmButtonText: "OK",
							closeOnConfirm: true
						},
						function(){
							location.reload();
						});
					}
				},
				false: function(){
					swal({
						title: "取引完了に失敗しました。",
						text: "システム管理者に連絡してください。",
						type: "error",
						showCancelButton: false,
						confirmButtonColor: "#AEDEF4",
						confirmButtonText: "OK",
						closeOnConfirm: true
					});
				}
			});
		}else{return false;}
	});
}

</script>

<div class="fake" id="blog-single" role="main">
	
	<?php do_action( 'bp_before_blog_single_post' ); ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
	<div class="bookinfo">
		<?php $sub_category = get_the_category();?>
		<div class="category_tree">
			<a href=" <?php echo home_url();?> ">ホーム</a>&raquo;
			  <?php echo get_category_parents( $sub_category[0]->term_id, true, ' &raquo; ' ); ?>
		</div>
		<div class="">
			
		</div>
		<p class="booktitle">商品名: <?php echo get_the_title(); ?></p>
		<p class="bookauthor">著者: <?php echo get_post_meta($post->ID, "author", true)?get_post_meta($post->ID, "author", true):"データがありません"; ?></p>
		<div class="shashin">
			<?php
							$args = array(
								'post_type' => 'attachment',
								'post_parent' => $post->ID
							);
							//define size
							$size = array(200, 200);
							$attachments = array_reverse(get_posts($args));
							if($attachments){
								$i=0;
								foreach($attachments as $attachment){
									echo "<div class='zoom_image'  onclick=zoom(";
									echo $i;
									echo ")>";
									echo wp_get_attachment_image( $attachment->ID,'thumbnail');
									echo "</div>";
                	$i++;
								}
							}
							?>
							<?php /* wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
							<?php
								$i=0;
								foreach($attachments as $attachment){
									echo "<div class='zoom_in_image' id=zoom";
									echo $i;
									echo " style='display: none;'>";
									echo wp_get_attachment_image( $attachment->ID,'full');
									echo "</div>";
									$i++;
								}
						*/	?>
		</div>
		
			<div class="booksubinfo">
				<div>
					<span class="first">カテゴリー</span><span class="second"><?php
								if($sub_category[0]->term_id == '1'):
									echo "カテゴリ:未設定";
								else:
									echo $sub_category[0]->name;
							?><?php endif; ?>
					
					</span>
				</div>

				<div>
					<span class="first">ポイント数</span><span class="second"><?php
							$fake_pt = get_post_meta($post->ID,"price",true)/1000;
							if($fake_pt<2){
								$true_pt = 1;
							}else if($fake_pt>=2){
								$true_pt = 2; 
							}else{
								$true_pt = 1;
							}
							echo $true_pt;
						?>
					</span>
				</div>

				<div>
				<sapn class="first">Amazon価格</span><span class="second"><?php echo get_post_meta($post->ID, "price", true)?number_format(get_post_meta($post->ID, "price", true))."円":"データがありません"; ?></span>
				</div>

				<div>
				<span class="first">残り冊数</span><span class="second"><?php echo count_books($post->ID)?count_books($post->ID):0; ?>冊</span>
				</div>	
			</div>
		
	</div>
</div>

<?php /*予約機能はまだ未定
<div class="reserve">
	<span class="this">この商品を</span><a class="button" href="#">予約する</a>
	*/?>

</div>	
	
<div class="plus">
	<P>補足情報：<?php remove_filter('the_content', 'wpautop'); the_content(); ?></P>	
</div>	
	
<div class="reserve_button">
		<?php
			$reserve_confirm_url=home_url()."/reserve_form_for_users";
		?>
		<input type="button" value="予約する" onclick="location.href='<?php echo $reserve_confirm_url; ?>'"/>
</div>

	<?php 
	endwhile; else: 
	?>

		<p><?php _e( 'Sorry, no posts matched your criteria.', 'buddypress' ); ?></p>

	<?php
	 endif; 
	 ?>

	<?php do_action( 'bp_after_blog_single_post' ); ?>














<?php /*
	<div id="content">
		<div class="padder">


					<div class="page" id="blog-single" role="main">

						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
							<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
								
					<div class="post-content" id="post-content-edit">
						<h2 class="posttitle"><?php the_title(); ?></h2>


						<div>
							<?php
								$sub_category = get_the_category();
								if($sub_category[0]->term_id == '1'):
									echo "カテゴリ:未設定";
								else:
									$main_category = get_category($sub_category[0]->parent);
								?>
	 							大学: <?php echo $main_category->cat_name;?> 学部: <?php echo $sub_category[0]->cat_name; ?><br/>
 							<?php endif; ?>
							著者: <?php echo get_post_meta($post->ID, "author", true)?get_post_meta($post->ID, "author", true):"データがありません"; ?><br/>
							Amazon価格: <?php echo get_post_meta($post->ID, "price", true)?number_format(get_post_meta($post->ID, "price", true))."円":"データがありません"; ?><br/>
							在庫数:  <?php echo count_books($post->ID)?count_books($post->ID):0; ?>冊<br/>
						</div>
						<div>
							<?php if(current_user_can('administrator')) { ?>
								<input type="button" id="finish" value="取引完了" onClick="onFinish(<?php echo $post->ID ?>);" <?php echo count_books($post->ID)>0?:"disabled" ?>/>
							<?php } ?>
						</div>
				<?php
						/*
						  display finish button or giveme button
						  if watching user doesn't log in, button is not shown
						 */
						?>
					<?php /*
						<p class="date">
							<!-- <span></span>がないと次の<span>がイタリックになる -->
							<?php printf( __( '%1$s <span></span>', 'buddypress' ), get_the_date()); ?>
							<!-- edit entry is not available -->
							<!-- <span class="post-utility alignright"><?php edit_post_link( __( 'Edit this entry', 'buddypress' ) ); ?></span> -->
							<?php if(current_user_can('administrator')){ ?><span class="post-utility alignright"><a href="javaScript:onDeletePost();">出品取り消し</a></span><?php } ?>
							<?php if(current_user_can('administrator')){ ?><span class="post-utility alignright"><a href='javaScript:onEdit("<?php echo $item_status[0]; ?>");'>編集</a></span><?php } ?>
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
								$i=0;
								foreach($attachments as $attachment){
									echo "<div class='zoom_image'  onclick=zoom(";
									echo $i;
									echo ")>";
									echo wp_get_attachment_image( $attachment->ID,'thumbnail');
									echo "</div>";
                	$i++;
								}
							}
							?>
							<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
							<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
							<?php
								$i=0;
								foreach($attachments as $attachment){
									echo "<div class='zoom_in_image' id=zoom";
									echo $i;
									echo " style='display: none;'>";
									echo wp_get_attachment_image( $attachment->ID,'full');
									echo "</div>";
									$i++;
								}
							?>
						</div>

							<p class="postmetadata"><?php the_tags( '<span class="tags">' . __( 'Tags: ', 'buddypress' ), ', ', '</span>' ); ?>&nbsp;</p>

						
					</div>

				</div>


			<?php endwhile; else: ?>

				<p><?php _e( 'Sorry, no posts matched your criteria.', 'buddypress' ); ?></p>

			<?php endif; ?>
		
		</div>

		<?php do_action( 'bp_after_blog_single_post' ); ?>
				


		</div><!-- .padder -->
	</div><!-- #content -->
	*/ ?>

	<div id="edit" style="display : none">
	<form id="edit_form" method="post" enctype="multipart/form-data">
	<?php if($attachments){
			foreach($attachments as $attachment){
				echo wp_get_attachment_image( $attachment->ID, $size);
			}
	} ?>
	<br>
	<label>商品名</label><br><input type="text" name="item_title" value="<?php echo get_the_title(); ?>" ><br>
	<label>状態</label><br><select name="item_status" >
				<option id="eval0" value="verygood"><?php echo get_display_item_status("verygood"); ?></option>
				<option id="eval1" value="good" ><?php echo get_display_item_status("good"); ?></option>
				<option id="eval2" value="bad"><?php echo get_display_item_status("bad"); ?></option>
			</select><br>
	<label>商品説明</label><br><textarea rows="5" cols="40" name="item_content" ><?php remove_filter('the_content', 'wpautop'); the_content(); ?></textarea></br>
	<label>カテゴリ</label></br>
	<select name="main_category" onChange="onChangeMainCategory(1)">
	<?php
		$sub_category = get_the_category();
		$main;
		$sub;
		if($sub_category[0]->cat_name == 'Uncategorized'){
			$main = 0;
			$sub = 0;
		}else{
			$main = get_category($sub_category[0]->parent)->cat_name;
			$sub = $sub_category[0]->cat_name;
		}
	?>
			<option value="">-- 大学 --</option>
			<?php $item_main_category_name = output_main_category($main); ?>
			</select>
			<select name="subcategory">
			<option value="1">-- 学部 --</option>
			<?php output_sub_category($item_main_category_name,$sub); ?>
			</select><br>

	<label>写真</label><br>
		<input type="file" class="multi" name="upload_attachment[]" ></br>
		<input type="file" class="multi" name="upload_attachment[]" ></br>
		<input type="file" class="multi" name="upload_attachment[]" ></br><br>
	<input type="hidden" name="itemID" value="<?php echo $post->ID; ?>">
	<input type="button" value="編集完了" onClick="onUpdateEdit();">
	</form>
	</div><!-- hidden_content -->

<?php get_footer(); ?>
