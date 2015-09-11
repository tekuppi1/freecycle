<script>
var ADMIN_URL = '<?php echo admin_url('admin-ajax.php'); ?>';

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
	var userID = jQuery("#postID_" + postID + " option:selected").val();
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

	if(jQuery("#map_search_" + postID).val() === ""){
		swal({
			title: "取引場所が選択されていません。",
			type: "error",
			showCancelButton: false,
			confirmButtonColor: "#AEDEF4",
			confirmButtonText: "OK",
			closeOnConfirm: true
		});
		enableButtons();
		return false;
	}

	var cutFigureTitle = 15;
	var title = jQuery("#post_"+ postID + " .index-item-title").text();
	var titleTrim = title.substr(0, (cutFigureTitle));
	var confirmText = "商品:"

	if(cutFigureTitle < title.length){
		confirmText += titleTrim + "...";
	}else{
		confirmText += title;
	}

	swal({
		title: "取引相手を確定させます。",
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
			var map = document.getElementById("map_canvas_" + postID);
			var lat = map.getAttribute("lat")?map.getAttribute("lat"):""; // latitude
			var lng = map.getAttribute("lng")?map.getAttribute("lng"):""; // longitude
			// 取引相手でないユーザID一覧を取得
			jQuery("[name=sendto_user_"+ postID + "]" + ":not(:selected)").each(function(){
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
				"mapID": jQuery("#map_search_" + postID).val()
			},
			success: function(msg){
				jQuery("#post_"+postID).hide(1000,function(){
				swal({
					title: "取引相手を確定し、メッセージを送信しました！",
					text: "取引相手からの返信をお待ちください。",
					// type: "success",
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
	jQuery('#top_slide').bxSlider({
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

/**
*	formID
*	0->新規出品
*	1->商品編集
**/
function onChangeMainCategory(formID){
	// create subcategories select menu
	var maincategory = jQuery("[name='main_category']").val();
	var subcategories = [];
	for (var i = categories.length - 1; i >= 0; i--) {
		if(categories[i].parent == maincategory){
			subcategories.push(categories[i]);
		}
	};

	switch(formID){
		case 0: formID = newentry; break;
		case 1: formID = edit_form; break;
		default : return;
 	}

	formID.subcategory.length = 1;
	formID.subcategory[0].value = "1";
	formID.subcategory[0].text = "-- 学部 --";

	if(!subcategories){
		return;
	}

	subcategories.forEach(function(subcategory){
		formID.subcategory.length++;
		formID.subcategory[formID.subcategory.length-1].value = subcategory.term_id;
		formID.subcategory[formID.subcategory.length-1].text = subcategory.name;
	});
}

function displayImages(){
		jQuery.ajax({
		type: "POST",
		url: '<?php echo admin_url('admin-ajax.php'); ?>',
		data: {
			"action" : "top_images",
			"req" : "top_page"	// 一覧ページには取引相手確定済の記事を表示しない。
		},
		success: function(data){
			var image_urls = JSON.parse(data);
			var id = image_urls[0];
			var url = image_urls[1];
			var elm;
			for(var i = 0; i < id.length; i++){
				elm = 
					"<a href='<?php echo home_url(); ?>/archives/"+id[i]+"' class='image_link'><img src='"+url[i]+"' height='100'></a>";
	
				jQuery("#top_image").append(elm);
			}
		jQuery("#top_image").owlCarousel({
			items : 6,
			itemsDesktop : [1199,6],
			itemsDesktopSmall : [980,5],
			itemsTablet : [768, 4],
			itemsMobile : [481, 3],
			autoPlay: 3700,
			slideSpeed : 1000,
			paginationSpeed : 1000,
			rewindSpeed : 1000,
		});
	}
	});
}
function displayImages1(){
	jQuery.ajax({
		type: "POST",
		url: '<?php echo admin_url('admin-ajax.php'); ?>',
		data: {
			"action" : "top_images",
			"req" : "top_page"	// 一覧ページには取引相手確定済の記事を表示しない。
		},
		success: function(data){
			var image_urls = JSON.parse(data);
			var id = image_urls[0];
			var url = image_urls[1];
			var elm;
			for(var i = 0; i < id.length; i++){
				elm = 
					"<a href='<?php echo home_url(); ?>/archives/"+id[i]+"' class='image_link'><img src='"+url[i]+"' height='100'></a>";
	
				jQuery("#top_image1").append(elm);
			}
		jQuery("#top_image1").owlCarousel({
			items : 6,
			itemsDesktop : [1199,6],
			itemsDesktopSmall : [980,5],
			itemsTablet : [768, 4],
			itemsMobile : [481, 3],
			autoPlay: 3700,
			slideSpeed : 1000,
			paginationSpeed : 1000,
			rewindSpeed : 1000,
		});
	}
	});
	return true;
}
function displayImages2(){
	jQuery.ajax({
		type: "POST",
		url: '<?php echo admin_url('admin-ajax.php'); ?>',
		data: {
			"action" : "top_images",
			"req" : "top_page"	// 一覧ページには取引相手確定済の記事を表示しない。
		},
		success: function(data){
			var image_urls = JSON.parse(data);
			var id = image_urls[0];
			var url = image_urls[1];
			var elm;
			for(var i = 0; i < id.length; i++){
				elm = 
					"<a href='<?php echo home_url(); ?>/archives/"+id[i]+"' class='image_link'><img src='"+url[i]+"' height='100'></a>";
				jQuery("#top_image2").append(elm);
			}
		jQuery("#top_image2").owlCarousel({
			items : 6,
			itemsDesktop : [1199,6],
			itemsDesktopSmall : [980,5],
			itemsTablet : [768, 4],
			itemsMobile : [481, 3],
			autoPlay: 3600,
			slideSpeed : 1000,
			paginationSpeed : 1000,
			rewindSpeed : 1000,
		});
		}
	});
	return true;
}
function displayImages3(){
	jQuery.ajax({
		type: "POST",
		url: '<?php echo admin_url('admin-ajax.php'); ?>',
		data: {
			"action" : "top_images",
			"req" : "top_page"	// 一覧ページには取引相手確定済の記事を表示しない。
		},
		success: function(data){
			var image_urls = JSON.parse(data);
			var id = image_urls[0];
			var url = image_urls[1];
			var elm;
			for(var i = 0; i < id.length; i++){
				elm = 
					"<a href='<?php echo home_url(); ?>/archives/"+id[i]+"' class='image_link'><img src='"+url[i]+"' height='100'></a>";
				jQuery("#top_image3").append(elm);
			}
		jQuery("#top_image3").owlCarousel({
			items : 6,
			itemsDesktop : [1199,6],
			itemsDesktopSmall : [980,5],
			itemsTablet : [768, 4],
			itemsMobile : [481, 3],
			autoPlay: 3500,
			slideSpeed : 1000,
			paginationSpeed : 1000,
			rewindSpeed : 1000,
		});
		}
	});
	return true;
}

function linkToOthersProfile(itemID){
			var nicename = jQuery("#postID_"+itemID+" option:selected").data('nicename');
			if(nicename != ""){
				location.href = "<?php echo home_url(); ?>"+"/members/"+nicename;
			}
}

function switchProfileButtonDisabled(itemID){
			var nicename = jQuery("#postID_"+itemID+" option:selected").data('nicename');
			var buttonID = "profile_"+itemID;
			if(nicename.length > 0){
				jQuery('#'+buttonID).removeAttr('disabled');
			}else{
				jQuery('#'+buttonID).attr('disabled','disabled');
			}
}

</script>
