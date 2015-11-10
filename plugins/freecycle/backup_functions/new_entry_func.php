<?php

/**
 * 新規出品時に呼ばれる関数
 */
function new_entry(){
	global $bp;
	$exhibitor_id = $_POST['exhibitor_id'];
	$msg = "";

	$insert_id = exhibit(array(
		'exhibitor_id' => $exhibitor_id,
		'item_name' => $_POST['field_1'],
		'item_description' => $_POST['field_2'],
		'item_category' => isset($_POST['subcategory'])?$_POST['subcategory']:"1",
		'tags' => $_POST['field_4']
	));

	if($insert_id){
		// success
		// add custom field
		add_post_meta($insert_id, "item_status", $_POST["item_status"], true);
		add_post_meta($insert_id, "department", xprofile_get_field_data('学部' ,$exhibitor_id), true);
		add_post_meta($insert_id, "course", xprofile_get_field_data('学科' ,$exhibitor_id), true);
		$msg = "商品を出品しました。";

		if($_POST['wanted_item_id']){
			add_post_meta($insert_id, "wanted_item_id", $_POST['wanted_item_id'], true);
		}

		// image upload
		global $post;
		upload_itempictures($insert_id);

		// if first new entry
		if(!get_user_meta($exhibitor_id, "is_first_new_entry")){
			$todo_row = get_todo_row($exhibitor_id, TODOID_NEWENTRY);
			$todoID = $todo_row->todo_id;
			change_todo_status_finished($todoID);
			update_user_meta($exhibitor_id, "is_first_new_entry", 1);
			$msg = '<div class="first-todo-header">チュートリアル</div><div class="first-todo-title">【新規出品をしてみよう】</div><div class="first-todo-complete">Complete!!</div>';
		}
	}else{
	// failure
	}
	
	echo $msg;
	die;
}

function callOnNewEntry(){
        
		disableButtons();
		if(jQuery("#field_1").val().length == 0){
			swal({   
				title: "商品名が未入力です。",  
				type: "error",    
			}); 			
			enableButtons();
			return false;
		}

		if(jQuery("#field_2").val().length == 0){
			swal({
				title: "商品説明が未入力です。",  
				type: "error",    
			}); 
			enableButtons();
			return false;
		}

		var mainCategory = jQuery('[name=main_category]').val();
		if(mainCategory == ""){
			swal({
				title: "大学名が未入力です。",  
				type: "error",    
			}); 
			enableButtons();
			return false;
		}

		var subCategory = jQuery('[name=subcategory]').val();
		if(subCategory == ""){
			swal({
				title: "学部が未入力です。",  
				type: "error",    
			}); 
			enableButtons();
			return false;
		}
		
		var isAttachedFlg = false;
		for (var i = jQuery(".multi").length - 1; i >= 0; i--) {
			var fileName = jQuery(".multi").get(i).value;
			if(fileName){
				isAttachedFlg = true;
			}
			if(fileName && !fileName.match(/\.(jpeg|jpg|png)$/i)){
				swal({   
					title: "不正なファイルです。",
					text: ".jpeg,.jpg,.png ファイルのみアップロードできます。",
					// type: "error",    
				}); 
				enableButtons();
				return false;
			}
		}

		if(!isAttachedFlg){
			swal({   
				title: "写真を添付してください。",  
				type: "error",    
			}); 
			enableButtons();
			return false;
		}

    jQuery("#newentry").after("\
        	<div id='dialog'>\
            <table border='1'>\
            <tr style='font-size:150%;'><td align='center'><b>商品名</b></td><td><b>"+jQuery('#field_1').val()+"</b></td></tr>\
            <tr><td  align='center'>商品説明</td><td>"+jQuery('#field_2').val()+"</td></tr>\
            <tr><td  align='center'>カテゴリ</td><td>"
                +jQuery('[name=main_category] option:selected').text()+" "
                +jQuery('[name=subcategory] option:selected').text()+"</td></tr>\
            <tr><td  align='center'>状態</td><td>"+jQuery('[name=item_status] option:selected').text()+"</td></tr>\
            <tr><td  align='center'>タグ</td><td>"+jQuery('#field_4').val()+"</td></tr>\
            <tr><td  align='center'>写真</td><td><img id='picture1' width='50px'><img id='picture2' width='50px'><img id='picture3' width='50px'></td></tr>\
            </table>\
			<p style='font-size:75%;'>写真は一枚目のみ表示されています。</p>\
            </div>\
		");
		
		var file = jQuery('[name="upload_attachment[]"]').prop('files')[0];
		var fileReader = new FileReader();
		fileReader.onload = function(event){
			jQuery("#picture1").attr('src', event.target.result);
		};
		fileReader.readAsDataURL(file);

        jQuery('#dialog').dialog({
          modal: true,
          title: "こちらを新しく出品します",
          draggable: false,
          show: "fade",
          hide: "fade",
          buttons:[
            {
                text:'出品',
                class:'btn-enter',
                click: function() {
                    jQuery(this).dialog('close');
					jQuery('#dialog').remove();
                    callOnNewEntryFinish();
                }
            },{
                text:'キャンセル',
                class:'btn-close',
                click: function() {
					jQuery('#dialog').remove();
                    jQuery(this).dialog('close');
                }
          }
        ]});
        jQuery(document).on("click", ".ui-widget-overlay", function(){
            jQuery(this).prev().find(".ui-dialog-content").dialog("close");
        });
        enableButtons();
        callOnNewEntryToChangeCSS();
	}
    
function callOnNewEntryToChangeCSS(){
        jQuery(".ui-dialog-titlebar-close").hide();
        jQuery('.ui-widget-overlay').css('background','#FFFFFF');
        jQuery('.ui-widget-overlay').css('opacity','0.7');
        jQuery('.btn-close').css('background','#EA5549');
        jQuery('.btn-close').css('border','#FF6666');
        jQuery('.btn-enter').css('background','#228b22');
        jQuery('.btn-enter').css('border','#66FF66');
    }
    
function callOnNewEntryFinish(){
            var form = jQuery("#newentry").get()[0];
			var fd = new FormData(form);
			var firstEntryText = "";
			fd.append("action", "new_entry");
			jQuery.ajax({
				type: "POST",
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				processData: false,
				contentType: false,
				mimeType:"multipart/form-data",
				data: fd,
				success: function(msg){
					swal({   
						title: msg,
						html: true
					}); 
					// reload new entry page
					enableButtons();
					jQuery("input[type='text'], input[type='file'], textarea").val("");
					jQuery('select[name="item_status"] option').attr("selected", false);
				}
			});
    }

?>