function onConfirmGiveme(postID, url){
	// ラジオボタンのチェック確認
	// 取引相手のユーザIDを取得
	var userID = jQuery("input[name='sendto_user_"+postID+"']:checked").val();
	if(userID){
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
							"uncheckedUserIDs": uncheckedUserIDs.join()
						},
						success: function(msg){
							jQuery("#post_"+postID).hide(1000,function(){
							alert("取引相手を確定させました。確実に商品の受渡を行ってください。" + msg);
						});
					}
				});
		}
	}else{
		alert("ユーザが選択されていません。");
	}
}