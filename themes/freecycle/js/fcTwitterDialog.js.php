<script>
var loadFunc = window.onload;
var myLoadFunc = function(){
	// ダイアログを表示
	var message = "<?php echo get_option('twitter_signin_tweet'); ?>";
	swal({
		title: "登録ありがとうございます!",
		text: "<p>テクスチェンジを始めたことを知らせましょう!</p><br/><a href='https://twitter.com/share' class='twitter-share-button' data-text='"+ message +"' data-url='http://texchg.com/' data-size='large'>Tweet</a><p><br/><h6><a href='javascript:swal.close()'>閉じる</a></h6></p>",
		showConfirmButton: false,
		showCancelButton: false,
		html: true
	});
	// ボタンのレンダリング
	twttr.widgets.load();
	// ツイート後のイベント定義
	twttr.events.bind(
		"tweet",
		function(){
			swal({
				title: "ツイートありがとうございます！",
				text: "引き続きテクスチェンジをお楽しみください。",
				html: true
			});
		}
	);

};

if(typeof loadFunc == "function"){
	window.onload = function(){
		loadFunc();
		myLoadFunc();
	}
}else{
	window.onload = myLoadFunc;
}
</script>