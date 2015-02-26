/**
 * detail-settings.js
 */
function onChangeUniversity(){
	var form = document.getElementById("settings-form");
	var map_id = form.trade_location_university.value;

	form.trade_location.disabled = "true";

	form.trade_location.length = 1;
	form.trade_location[0].value = "";
	form.trade_location[0].text = "-- 取引場所を選択 --";

	if(map_id === ""){
		form.trade_location.disabled = "";
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: ADMIN_URL,
		data: {
			"action": "get_child_trade_maps",
			"map_id": map_id
		},
		success: function(res){
				var locations = jQuery.parseJSON(res);
				if(locations.length < 1){
					return;
				}
				locations.forEach(function(location){
					form.trade_location.length++;
					form.trade_location[form.trade_location.length-1].value = location.map_id;
					form.trade_location[form.trade_location.length-1].text = location.name;					
				});
				form.trade_location.disabled = "";
			}
	});
}

document.getElementById("trade_location_university").onchange = onChangeUniversity;