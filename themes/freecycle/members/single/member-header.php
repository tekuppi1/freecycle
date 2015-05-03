<?php

/**
 * freecycle - Users Header
 *
 * @package freecycle
 */

?>

<?php do_action( 'bp_before_member_header' ); ?>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCmhfQEie0qbsIR-F2xNVxzpV8IxzrwDBE&libraries=places&sensor=false"></script>

<!--Add(2015/5/4)-->
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/start/jquery-ui.css" rel="stylesheet">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<!--Add(2015/5/4)-->

<script type="text/javascript">

	function callOnConfirmGiveme(postID){
		onConfirmGiveme(postID, "<?php echo admin_url('admin-ajax.php'); ?>");
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
            var btnName  = document.fm.button1.value;
			return false;
		}

//Add(2015/5/4)//
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
//Add(2015/5/4)//
	}
    
    //Add(2015/5/4)//
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
			fd.append("action", "new_entry");
			jQuery.ajax({
				type: "POST",
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				processData: false,
				contentType: false,
				mimeType:"multipart/form-data",
				data: fd,
				success: function(permalink){
					swal({   
						title: "商品を出品しました。",  
						type: "success",    
					}); 
					// reload new entry page
					enableButtons();
					location.href = "<?php echo bp_loggedin_user_domain(); ?>" + "new_entry/#newentry";
					jQuery("input[type='text'], input[type='file'], textarea").val("");
					jQuery("option").attr("selected", false);
				}
			});
    }

	function onClickSearchWantedBook(){
		disableButtons();
		jQuery('#search_result').html('<div align=center><img src="<?php echo get_stylesheet_directory_uri() ?>/images/ajax-loader.gif"></div>');
		jQuery.ajax({
			type: "POST",
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			data: {
				"action": "search_wantedbook",
				"keyword": jQuery("#keyword").val()
			},
			success: function(result){
				jQuery('#search_result').html(result);
				jQuery('.button_add_wanted').click(function(){
					addWantedList(jQuery(this).attr('asin'));
				});
				jQuery('.button_del_wanted').click(function(){
					delWantedListByASIN(jQuery(this).attr('asin'));
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

	function addWantedList(asin){
		disableButtons();
		jQuery.ajax({
			type: "POST",
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			data: {
				"action": "add_wanted_item",
				"asin": asin,
				"item_name": jQuery("#name_" + asin).text(),
				"image_url": jQuery("#" + asin + " img").attr("src")
			},
			success: function(){
				jQuery("#button_" + asin).val("追加済");
				jQuery("#button_" + asin)
					.unbind('click')
					.click(function(){
						delWantedListByASIN(asin);
					});
				enableButtons();
			},
			error: function(){
				swal({   
					title: "登録できませんでした。しばらくしてからもう一度おためしください。",  
					type: "error",    
				}); 
			}
		});
	}

	function delWantedListByASIN(asin){
		disableButtons();
		jQuery.ajax({
			type: "POST",
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			data: {
				"action": "del_wanted_item_by_asin",
				"asin": asin
			},
			success: function(){
				jQuery("#button_" + asin).val("追加");
				jQuery("#button_" + asin)
					.unbind('click')
					.click(function(){
						addWantedList(asin);
					});
				enableButtons();
			},
			error: function(){
				swal({   
					title: "削除できませんでした。しばらくしてからもう一度おためしください。",  
					type: "error",    
				}); 
				enableButtons();
			}
		});
	}

	function delWantedListFromIndex(asin){
		disableButtons();
		jQuery.ajax({
			type: "POST",
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			data: {
				"action": "del_wanted_item_by_asin",
				"asin": asin
			},
			success: function(){
				jQuery("#" + asin).hide(1000);
				enableButtons();
			},
			error: function(){
				swal({   
					title: "削除できませんでした。しばらくしてからもう一度おためしください。",  
					type: "error",    
				}); 
				enableButtons();
			}
		});
	}

	function showMap(map, location){
		var mapOptions = {
	    	zoom: 17,
	    	center: location,
	    	mapTypeId: google.maps.MapTypeId.ROADMAP,
	    	mapTypeControl: false
  		}
		var newMap = new google.maps.Map(map, mapOptions);
		return newMap;
	}

	function showMarker(map, mapelm, location, draggable){
		var marker = new google.maps.Marker({
			map: map,
			position: location,
			draggable: draggable
		});

		if(draggable){
			google.maps.event.addListener(marker, 'dragend', function(ev){
				mapelm.setAttribute("lat", ev.latLng.lat()); // latitude
				mapelm.setAttribute("lng", ev.latLng.lng()); // longitude
			});
		}
		return marker;
	}

	function initializeMap(){
		var maps = document.getElementsByName("map-canvas");
		var mapsInMessages = document.getElementsByName("map-canvas-message");
		var geocoder;
		var location;
		if(maps.length > 0){
			// set the university in user profile as a default location of maps
			var mylocation = "<?php echo get_user_meta(get_current_user_id(), 'default_trade_location', true) ?>";
			var shownMap;
			var marker;
			var input;
			var searchbox;
			if(!mylocation){
				// if user default trade location is not set, set default
				mylocation = "<?php echo get_default_map()->map_id ?>";
			}
			jQuery.ajax({
				type: "POST",
				url: ADMIN_URL,
				data: {
					"action": "get_trade_map",
					"map_id": mylocation
				},
				success: function(res){
					var location = jQuery.parseJSON(res);
					var lat = location.latitude * 1;
					var lng = location.longitude * 1;
					for (var i = maps.length-1; i >= 0; i--) {
						var pos = new google.maps.LatLng(lat, lng, false);
						shownMap = showMap(maps[i], pos);
						marker = showMarker(shownMap, maps[i], pos, false);
						maps[i].setAttribute("lat", lat); // latitude
						maps[i].setAttribute("lng", lng); // longitude
						var itemID = maps[i].getAttribute("id").replace("map_canvas_", "");
						input = document.getElementById("map_search_" + itemID);
						// set the input form as a place search form of the map
						shownMap.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
						function _create_callback(map, input, marker, mapelm){
							var bounds;
							var location;
							return function(){
								var val = input.value;
								if(val.length == 0){
									return;
								}else{
									jQuery.ajax({
										type: "POST",
										url: ADMIN_URL,
										data: {
											"action": "get_trade_map",
											"map_id": val
										},
										success: function(res){
											var location = jQuery.parseJSON(res);
											var lat, lng, position;
											lat = location.latitude * 1; // parse string to number
											lng = location.longitude * 1; // parse string to number
											position = {lat: lat, lng: lng};
											marker.setPosition(position);
											map.setCenter(position);
											map.setZoom(17);

											mapelm.setAttribute("lat", lat); // latitude
											mapelm.setAttribute("lng", lng); // longitude
										}
									});
								}								
							}
						}
						input.onchange = _create_callback(shownMap, input, marker, maps[i]);
					}
				}
			});
  		}

  		if(mapsInMessages){
			for (var i = mapsInMessages.length-1; i >= 0; i--) {
				var lat = mapsInMessages[i].getAttribute('lat');
				var lng = mapsInMessages[i].getAttribute('lng');
				var location = new google.maps.LatLng(lat, lng, false);
				shownMap = showMap(mapsInMessages[i], location);
				showMarker(shownMap, mapsInMessages[i], location, false);
			}
  		}
	}
	google.maps.event.addDomListener(window, "load", initializeMap);

	//ページスクロール
	var url = location.href;
	var str = url.substr(url.indexOf("members"));
	var count = 0;
	var pos = str.indexOf("/");

	while ( pos != -1 ) {
	   count++;
	   pos = str.indexOf("/", pos + 1);
	}

	if(count >= 3){
	 location.href = "#mypage";
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
		<h3 class="show-points">使用可能ポイント:<?php echo get_usable_point($user_ID); ?>p <br>(仮払ポイント:<?php echo get_temp_used_point($user_ID); ?>p)</h3>	
	</div>
	
	<?php } ?>
	<h5 class="show-points">評価平均:<?php echo number_format(get_average_score(bp_displayed_user_id()),2); ?>(件数:<?php echo get_count_evaluation(bp_displayed_user_id()); ?>件)</h5>
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
<div id="mypage"></div>
<?php do_action( 'bp_after_member_header' ); ?>

<?php do_action( 'template_notices' ); ?>