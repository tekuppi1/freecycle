<script>
function disableButtons(){
	jQuery("input[type=button]").attr("disabled",true);
}

function enableButtons(){
	jQuery("input[type=button]").attr("disabled",false);
}

function onClickMenuIcon(){
	jQuery(".grobal_nav_div_sp").slideToggle();
}

function onChangeTradeWay(postID){
	var tradeway = jQuery("#tradeway_"+postID).val();
	if(tradeway == "handtohand"){
		jQuery("#handtohand-option_"+postID).show();
	}else if(tradeway == "delivery"){
		jQuery("#handtohand-option_"+postID).hide();
	}
}

function onConfirmGiveme(postID, url){
	disableButtons();
	// ラジオボタンのチェック確認
	// 取引相手のユーザIDを取得
	var userID = jQuery("input[name='sendto_user_"+postID+"']:checked").val();
	if(!userID){
		swal({   
			title: "ユーザーが選択されていません。",  
			type: "error",   
			showCancelButton: false,   
			confirmButtonColor: "#AEDEF4", 
			confirmButtonText: "OK",      
			closeOnConfirm: true
		});
		enableButtons();
		return false;
	}

	var confirmText = "変更やキャンセルはできません。よろしいですか？\n";
	confirmText += "商品:"+ jQuery("#post_"+ postID + " .index-item-title").text() + "\n";
	confirmText += "取引相手:"+ jQuery("[name=sendto_user_"+postID+"]:checked+label").text() + "\n";
	confirmText += "メッセージ:"+ jQuery("#message_" + postID).val();
	swal({   
		title: "取引相手を確定させます。",     
		text: confirmText,
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
			var uncheckedUserIDs = new Array();
			jQuery("[name=sendto_user_"+ postID + "]" + ":not(:checked)").each(function(){
				uncheckedUserIDs.push(this.value);
			});
			jQuery.ajax({
			type: "POST",
			url: url,
			data: {
				"action": "confirmGiveme",
				"postID": postID,
				"userID": userID,//落札者相手ユーザーＩＤ
				"uncheckedUserIDs": uncheckedUserIDs.join(),
				"message": jQuery("#message_" + postID).val(),
			},
			success: function(msg){
				jQuery("#post_"+postID).hide(1000,function(){
				swal({   
					title: "取引相手を確定し、メッセージを送信しました！",  
					text: "取引相手からの返信をお待ちください。",
					type: "success",   
					showCancelButton: false,   
					confirmButtonColor: "#AEDEF4", 
					confirmButtonText: "OK",      
					closeOnConfirm: true
				});
				enableButtons();
				});
			},
			error:function(){
				swal({   
					title: "error!",  
					type: "error",   
					showCancelButton: false,   
					confirmButtonColor: "#AEDEF4", 
					confirmButtonText: "OK",      
					closeOnConfirm: true
				});
			}
		});

		}else{
		enableButtons();
		} 
	});
}

function onClickEditCommentButton(commentID){
	var commentArea = jQuery("#comment-" + commentID + " .comment-entry");
	var currentComment = jQuery.trim(commentArea.text());
	var editForm = '<textarea name="comment" area-required="true">' + currentComment + '</textarea>'
	var editButton = jQuery("#comment-" + commentID + " .comment-options .edit-button");
	var confirmButton = '<a class="button comment-edit-link bp-secondary-action confirm-button" onClick="onClickConfirmCommentButton(' + commentID + ')" title="編集確定">確定</a>'

	commentArea.hide();
	commentArea.after(editForm);
	editButton.hide();
	editButton.after(confirmButton);
}

function onClickConfirmCommentButton(commentID){
	var commentArea = jQuery("#comment-" + commentID + " .comment-entry");
	var editForm = jQuery("#comment-" + commentID + " .comment-content textarea");
	var updatedComment = editForm.val();
	var editButton = jQuery("#comment-" + commentID + " .comment-options .edit-button");
	var confirmButton = jQuery("#comment-" + commentID + " .comment-options .confirm-button");

	commentArea.text(updatedComment);

	editForm.remove();
	confirmButton.remove();

	commentArea.show();
	editButton.show();
	updateComment(commentID, updatedComment);
}

function onClickSearchWantedList(page,keyword){
	disableButtons();
	jQuery('#wanted_list').html('<div class="loader" align=center><img src="<?php echo get_stylesheet_directory_uri() ?>/images/ajax-loader.gif"></div>');
	jQuery.ajax({
		type: "POST",
		url: '<?php echo admin_url('admin-ajax.php'); ?>',
		data: {
			"action": "search_wantedlist",
			"user_id": "<?php echo $user_ID; ?>",
			"keyword": keyword,
			"page":page
		},
		success: function(result){
			if(!result){
				jQuery('#wanted_list').html("ほしいものリストが見つかりません。");
				enableButtons();
				return;
			}
			jQuery('#wanted_list').html(result);
			jQuery('.button_exhibit_to_wanted').click(function(){
				exhibitToWanted(jQuery(this).attr('wanted_item_id'), jQuery(this).attr('asin'));
			});
			jQuery('.button_del_exhibition_to_wanted').click(function(){
				delExhibitionToWanted(jQuery(this).attr('post_id'), jQuery(this).attr('wanted_item_id'), jQuery(this).attr('asin'));
			});
			jQuery('.item_detail').hover(function(){
				jQuery(this).css('background-color', '#ffffe0');
			},
			function(){
				jQuery(this).css('background-color', '#ffffff');
			});
			enableButtons();
		}
	});
}

function exhibitToWanted(wanted_item_id, asin){
	disableButtons();
	jQuery.ajax({
		type: "POST",
		url: '<?php echo admin_url('admin-ajax.php'); ?>',
		data: {
			"action": "exhibit_to_wanted",
			"exhibitor_id": <?php global $bp; echo $bp->loggedin_user->id ?>,
			"field_1": jQuery('#title_' + wanted_item_id).text(),
			"item_status": jQuery('#' + wanted_item_id + ' [name="item_status"]').val(),
			"image_url":jQuery('#' + wanted_item_id + ' img').attr('src'),
			"wanted_item_id": wanted_item_id,
			"asin": asin
		},
		success: function(insert_id){
			jQuery("#button_" + wanted_item_id).val("出品取消");
			jQuery("#button_" + wanted_item_id)
				.unbind('click')
				.click(function(){
					delExhibitionToWanted(insert_id, wanted_item_id, asin);
				});
			enableButtons();
		},
		error: function(){
			swal({   
				title: "出品できませんでした。",  
				text: "しばらくしてからもう一度おためしください。",
				type: "error",   
				showCancelButton: false,   
				confirmButtonColor: "#AEDEF4", 
				confirmButtonText: "OK",      
				closeOnConfirm: true
			});
			enableButtons();
		}
	});
 }

function delExhibitionToWanted(post_id, wanted_item_id, asin){
 	disableButtons();
	jQuery.ajax({
		type: "POST",
		url: '<?php echo admin_url('admin-ajax.php'); ?>',
		data: {
			"action": "delete_post",
			"postID": post_id
		},
		success: function(){
			jQuery("#button_" + wanted_item_id).val("出品");
			jQuery("#button_" + wanted_item_id)
				.unbind('click')
				.click(function(){
					exhibitToWanted(wanted_item_id, asin);
				});
			enableButtons();
		},
		false: function(msg){
			swal({   
				title: "取り消しに失敗しました。",  
				text: "しばらくしてからもう一度おためしください。",
				type: "error",   
				showCancelButton: false,   
				confirmButtonColor: "#AEDEF4", 
				confirmButtonText: "OK",      
				closeOnConfirm: true
			});
			enableButtons();
		}
	});
 }

 //Enter押下時読み込まれる関数
function go(f){
	if(window.event.keyCode == 13){
		f();
	}
}

function todo_dealing(user_ID, item_ID){
		jQuery.ajax({
			type: "POST",
			url:  '<?php echo admin_url('admin-ajax.php'); ?>',
			data: {
				action: "todo_dealing",
				userID: user_ID,
				itemID: item_ID
			},
			success:function(result){
				return;
			}
		});
	}

//トップページのスライドショー表示
function topSlide(){
	$('#top_slide').bxSlider({
		auto:true,
		pause: 6500,
		speed: 700,
		captions: true,
		infiniteLoop: false,
		hideControlOnEnd: true,
		autoDelay: 3000,
		controls: true
	});
}

var categories = jQuery.parseJSON('<?php echo get_freecycle_category_JSON(array('hide_empty' => 0)); ?>');

function onChangeMainCategory(){
	// create subcategories select menu
	var maincategory = jQuery("[name='main_category']").val();
	var subcategories = [];
	for (var i = categories.length - 1; i >= 0; i--) {
		if(categories[i].parent == maincategory){
			subcategories.push(categories[i]);
		}
	};

	newentry.subcategory.length = 1;
	newentry.subcategory[0].value = "";
	newentry.subcategory[0].text = "-- 子カテゴリ --";

	if(!subcategories){
		return;
	}

	subcategories.forEach(function(subcategory){
		newentry.subcategory.length++;
		newentry.subcategory[newentry.subcategory.length-1].value = subcategory.term_id;
		newentry.subcategory[newentry.subcategory.length-1].text = subcategory.name;
	});
}
</script>