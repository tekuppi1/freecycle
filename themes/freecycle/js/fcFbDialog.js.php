<script>
var loadFunc = window.onload;
var myLoadFunc = function(){
	window.fbAsyncInit = function() {
		FB.init({
		  appId      : '523987747717891',
		  xfbml      : true,
		  version    : 'v2.1'
		});
	};

	(function(d, s, id){
	 var js, fjs = d.getElementsByTagName(s)[0];
	 if (d.getElementById(id)) {return;}
	 js = d.createElement(s); js.id = id;
	 js.src = "//connect.facebook.net/en_US/sdk.js";
	 fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));

	sweetAlert({
		title: "登録ありがとう<br/>ございます!",
		text: '<p>よろしければ<b>「いいね」</b>でテクスチェンジを応援してください！</p><br/><div class="fb-like" data-href="http://texchg.com/" data-layout="button_count" data-action="like" data-show-faces="true" style="transform:scale(1.5);-webkit-transform:scale(1.5);-moz-transform:scale(1.5);"></div><br/><br/><p><h6><a href="javascript:swal.close()">閉じる</a></h6></p>',
		html: true,
		showConfirmButton: false
	});

	FB.XFBML.parse();
	FB.Event.subscribe('edge.create', onLike);
};

function onLike(){
	swal({
		title: "応援ありがとう<br/>ございます！",
		text: "引き続きテクスチェンジをお楽しみください。",
		html: true
	});
}

if(typeof loadFunc == "function"){
	window.onload = function(){
		loadFunc();
		myLoadFunc();
	}
}else{
	window.onload = myLoadFunc;
}
</script>