if(typeof jQuery!="undefined") {
var $ = jQuery
	
$(document).ready(function(){
	var redirect = $(".redirect");
	var themelist = $(".theme_template");
	var len = themelist.length;

	themelist.each(function(i){
		$(this).change(function(){
			if(this.value != "") {
				$(redirect[i]).attr("disabled", "disabled");
				$(redirect[i]).css("background", "#EBEBE4");
				$(redirect[i]).val("");
			} else {
				$(redirect[i]).removeAttr("disabled");
				$(redirect[i]).css("background", "#FFFFFF");
			}
		});
	});

	themelist.each(function(i){
		if(this.value != "") {
			$(redirect[i]).attr("disabled", "disabled");
			$(redirect[i]).css("background", "#EBEBE4");
		} else {
			$(redirect[i]).removeAttr("disabled");
			$(redirect[i]).css("background", "#FFFFFF");
		}
	});

	redirect.each(function(i) {
		$(this).focus(function(){
			if($(this).val() == '')
				$(this).val('http://');
		});

		$(this).blur(function(){
			if($(this).val() == 'http://')
				$(this).val('');
		});
	})
});
}

