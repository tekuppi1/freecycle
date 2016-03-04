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
				closeOnConfirm: true,   
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

		/**
		 This function is called when giveme button is clicked.
		 */
		function onGiveme(){
			disableButtons();
			var firstGivemeText = "";
			swal({   
				title: "くださいリクエストをします。",   
				text: "よろしいですか？",     
				showCancelButton: true,   
				confirmButtonColor: "#AEDEF4",   
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
							"action": "giveme",
							"postID": "<?php echo $post->ID ?>",
							"userID": "<?php echo $user_ID ?>"
						},
						success: function(msg){
							switchGiveme();
							swal({
								title : msg,
								html: true
							});
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
				closeOnConfirm: true,   
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
					closeOnConfirm: true
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
					closeOnConfirm: true
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
						// type: "success",   
						showCancelButton: false,   
						confirmButtonColor: "#AEDEF4", 
						confirmButtonText: "OK",      
						closeOnConfirm: true
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
					closeOnConfirm: true
				}); 
				enableButtons();
				return;
			}else if(comment.length > 100){
				swal({   
					title: "コメントは100文字以内で記入してください。",  
					// type: "error",   
					showCancelButton: false,   
					confirmButtonColor: "#AEDEF4", 
					confirmButtonText: "OK",      
					closeOnConfirm: true
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
						closeOnConfirm: true
					});
					enableButtons();
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
				closeOnConfirm: true,   
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
				closeOnConfirm: true,   
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
		
/*
		function switchGiveme(){
			if(jQuery("#giveme").size() > 0){
				jQuery('<input type="button" id="cancelGiveme" value="ください取消" onClick="onCancelGiveme();">').replaceAll(jQuery("#giveme"));

			}else{
				jQuery('<input type="button" id="giveme" value="ください" onClick="onGiveme();">').replaceAll(jQuery("#cancelGiveme"));
			}
		}
*/

		function afterEvaluation(){
			jQuery("#evaluation").replaceWith("この商品は評価済です。");
		}

		function afterFinish(){
			// show a bidder evaluation form
			jQuery("#finish").replaceWith('<div id="evaluation">落札者の評価:</br><select name="score" id="score"><option value="invalid" selected>--選択--</option><option value="5" >とても良い</option><option value="4" >良い</option><option value="3" >普通</option><option value="2" >悪い</option><option value="1" >とても悪い</option></select></br>コメント(任意 100字以内)</br><textarea name="trade_comment" id="trade_comment" rows="5" cols="40"></textarea></br><input type="button" id="evaluation" value="評価する" onClick="onBidderEvaluation();"></div>');
			// hide a cancel trading link
			jQuery('#cancelTradeFromExhibitor').hide();
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

		function getDisplayItemStatus(status){
			var displayStatus = {
				"verygood" : "良",
				"good" : "可",
				"bad" : "悪"
			}
			return displayStatus[status];
		}

	</script>
	
	