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
	jQuery("input[type=button]").attr("disabled",true);
	// ラジオボタンのチェック確認
	// 取引相手のユーザIDを取得
	var userID = jQuery("input[name='sendto_user_"+postID+"']:checked").val();
	if(!userID){
		alert("ユーザが選択されていません。");
		jQuery("input[type=button]").attr("disabled",false);
		return false;
	}

	// TODO 日付の厳密なチェックを再実装する。
	var tradedates = new Array();

	for (var i=1; i<=3; i++) {
		var month = document.getElementById("month_" + postID + "_" + i).value;
		var date  = document.getElementById("date_" + postID + "_" + i).value;
		var time  = document.getElementById("tradetime_" + postID + "_" + i).value;
		if(month && date && time){
			tradedates[i] = month + "/" + date + " " + time;
		}

		if(!tradedates[i-1] && tradedates[i] && i != 1){
			alert("第" + (i-1) + "希望が入力されていません。");
			jQuery("input[type=button]").attr("disabled",false);
			return false;
		}
	}

	if(!jQuery("#place_" + postID).val() && jQuery("#tradeway_" + postID).val() === "handtohand"){
		alert("受渡希望場所を記入してください。");
		jQuery("input[type=button]").attr("disabled",false);
		return false;
	}

	var confirmText = "取引相手を確定させます。変更やキャンセルはできません。よろしいですか？\n";
	confirmText += "商品:"+ jQuery("#post_"+ postID + " .posttitle").text() + "\n";
	confirmText += "取引相手:"+ jQuery("[name=sendto_user_"+postID+"]:checked+label").text() + "\n";
	confirmText += "受渡希望日時\n"
	for(var i=1; i<tradedates.length; i++){
		confirmText += "第" + i + "希望:" + tradedates[i] + "\n";
	}
	confirmText += "受渡希望場所:" + jQuery("#place_" + postID).val();

	if(confirm(confirmText)){
					// 取引相手でないユーザID一覧を取得
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
						"userID": userID,
						"uncheckedUserIDs": uncheckedUserIDs.join(),
						"tradeway": jQuery("#tradeway_" + postID).val(),
						"tradedates": tradedates.join(),
						"place": jQuery("#place_" + postID).val(),
						"message": jQuery("#message_" + postID).val(),
					},
					success: function(msg){
						jQuery("#post_"+postID).hide(1000,function(){
						alert("取引相手を確定し、受渡希望条件を通知しました！\n取引相手からの返信をお待ちください。");
						jQuery("input[type=button]").attr("disabled",false);
					});
				}
			});
	}else{
		jQuery("input[type=button]").attr("disabled",false);
	}
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

