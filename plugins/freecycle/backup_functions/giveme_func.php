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
							location.href = "<?php echo home_url(); ?>";
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

		function getDisplayItemStatus(status){
			var displayStatus = {
				"verygood" : "良",
				"good" : "可",
				"bad" : "悪"
			}
			return displayStatus[status];
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
		function zoom(i){
			switch(i){
				case 0:jQuery("#zoom1").hide();jQuery("#zoom2").hide();jQuery("#zoom0").show();break;
				case 1:jQuery("#zoom2").hide();jQuery("#zoom0").hide();jQuery("#zoom1").show();break;
				case 2:jQuery("#zoom0").hide();jQuery("#zoom1").hide();jQuery("#zoom2").show();break;
			}
		}

	</script>
	
	