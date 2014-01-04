<?php

/**
 * freecycle - Users Header
 *
 * @package freecycle
 */

?>

<?php do_action( 'bp_before_member_header' ); ?>
<script type="text/javascript">

	function callOnConfirmGiveme(postID){
		onConfirmGiveme(postID, "<?php echo admin_url('admin-ajax.php'); ?>");
	}
	
	function callOnNewEntry(){
		if(jQuery("#field_1").val().length == 0){
			alert("商品名が未入力です。");
			return false;
		}

		if(jQuery("#field_2").val().length == 0){
			alert("商品説明が未入力です。");
			return false;
		}
		
		var isAttachedFlg = false;
		for (var i = jQuery(".multi").length - 1; i >= 0; i--) {
			var fileName = jQuery(".multi").get(i).value;
			if(fileName){
				isAttachedFlg = true;
			}
			if(fileName && !fileName.match(/\.(jpeg|jpg|png)$/i)){
				alert("不正なファイルです。\n.jpeg,.jpg,.png ファイルのみアップロードできます。");
			}
		}

		if(!isAttachedFlg){
			alert("写真を添付してください。");
			return false;
		}

		if(confirm("出品後の記事の編集はできません。出品しますか？")){
			var form = jQuery("#newentry").get()[0];
			var fd = new FormData(form);
			fd.append("action", "new_entry");
			jQuery.ajax({
				type: "POST",
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				processData: false,
				contentType: false,
				mimeType:"multipart/form-data",
				data: fd,
				success: function(permalink){
					alert("商品を出品しました。");
					// reload new entry page
					location.href = "<?php echo bp_loggedin_user_domain(); ?>" + "new_entry/";
				}
			});
		}else{
			return false;
		}
	}
	
</script>
<div id="item-header-avatar">

	<a href="<?php bp_displayed_user_link(); ?>">
		<?php bp_displayed_user_avatar( 'type=full' ); ?>
	</a>
	
</div><!-- #item-header-avatar -->

<div id="item-header-content">

	<h2>
		<a href="<?php bp_displayed_user_link(); ?>"><?php bp_displayed_user_fullname(); ?></a>
	</h2>

	<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
		<span class="user-nicename">@<?php bp_displayed_user_username(); ?></span>
	<?php endif; ?>

	<span class="activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>
	
	<!-- display when loggin user page -->
	<?php if($user_ID == bp_displayed_user_id()){ ?>
	
	<div id="points-info">
		<h3>使用可能ポイント:<?php echo get_usable_point($user_ID); ?>p <br>(仮払ポイント:<?php echo get_temp_used_point($user_ID); ?>p)</h3>	
	</div>
	
	<?php } ?>
	<h5>出品者としての評価平均:<?php echo get_average_exhibiter_score(bp_displayed_user_id()); ?> (件数:<?php echo get_count_exhibiter_evaluation(bp_displayed_user_id()); ?>件)</h5>
	<h5>落札者としての評価平均:<?php echo get_average_bidder_score(bp_displayed_user_id()); ?> (件数:<?php echo get_count_bidder_evaluation(bp_displayed_user_id()); ?>件)</h5>
	<?php do_action( 'bp_before_member_header_meta' ); ?>

	<div id="item-meta">

		<?php if ( bp_is_active( 'activity' ) ) : ?>

			<div id="latest-update">

				<?php bp_activity_latest_update( bp_displayed_user_id() ); ?>

			</div>

		<?php endif; ?>

		<div id="item-buttons">

			<?php do_action( 'bp_member_header_actions' ); ?>

		</div><!-- #item-buttons -->

		<?php
		/***
		 * If you'd like to show specific profile fields here use:
		 * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
		 */
		 do_action( 'bp_profile_header_meta' );

		 ?>
		 
	</div><!-- #item-meta -->

</div><!-- #item-header-content -->

<?php do_action( 'bp_after_member_header' ); ?>

<?php do_action( 'template_notices' ); ?>