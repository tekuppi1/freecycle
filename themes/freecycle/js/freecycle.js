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
	var tradeYear = jQuery("select[name='year_"+postID+"']").val();
	var tradeMonth = jQuery("select[name='month_"+postID+"']").val();
	var tradeDate = jQuery("select[name='date_"+postID+"']").val();

	var date = new Date(tradeYear, tradeMonth-1, tradeDate);
	if(isNaN(date)){
		alert("日付が無効です。");
		jQuery("input[type=button]").attr("disabled",false);
		return false;
	}

	if(!jQuery("#place_" + postID).val()){
		alert("受渡希望場所を記入してください。");
		jQuery("input[type=button]").attr("disabled",false);
		return false;
	}

	if(confirm("取引相手を確定させます。変更やキャンセルはできません。よろしいですか？\n" + 
				"商品:"+ jQuery("#post_"+ postID + " .posttitle").text()
				)){
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
						"tradedate": tradeYear + "/" + tradeMonth + "/" + tradeDate,
						"tradetime": jQuery("#tradetime_" + postID).val(),
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