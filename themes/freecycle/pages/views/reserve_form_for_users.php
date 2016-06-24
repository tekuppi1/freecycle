<?php get_header(); ?>
<?php
$bookfair_infos = get_bookfair_info_all_you_want(1);	
	foreach($bookfair_infos as $bookfair_info){
		$datetime = strtotime($bookfair_info->date);
		$date = date('Y/m/d',$datetime);
		$starttime = strtotime($bookfair_info->starting_time);
		$start = date('H:i',$starttime);
		$endtime = strtotime($bookfair_info->ending_time);
		$end = date('H:i',$endtime);
		$time = $start .' ～ '.$end;
		$venue = $bookfair_info->venue;
		$room = $bookfair_info->classroom;
		$bookfair_id = $bookfair_info->bookfair_id;
	}

	$before_url=$_SERVER['HTTP_REFERER'];
	$filename = strrchr( $before_url, "/" );// /postname.html
	$post_id = (int)substr( $filename, 1 );// postname.html
	$post = get_post($post_id);
	$title = get_the_title($post);
function get_point($post_id){
	$fake_pt = get_post_meta($post_id,"price",true)/1000;
	if($fake_pt<2){
		$true_pt = 1;
	}else if($fake_pt>=2){
		$true_pt = 2; 
	}else{
		$true_pt = 1;
	}
	return $true_pt;
}
	global $current_user;
    get_currentuserinfo();
    $user_id = $current_user->ID;
	$book_counts = count_books($post_id);
 	$book_count = (int)$book_counts;
 	$reserve_count = count(get_reserve_count_of_postid($post_id));
	$point=get_point($post_id);
?>

<script>
// 予約確定ボタンを押した時の反応（予約内容を予約テーブルに挿入する）
function postReserveInfo(){
		<?php
			if($reserve_count>=$book_count): ?>
				swal({
					title: "申し訳ありませんが、この本はすでに予約されています",
					type: "error",
					showCancelButton: false,
					confirmButtonColor: "#AEDEF4",
					confirmButtonText: "OK",
					closeOnConfirm: true
				});
			<?php else: ?>
	     	jQuery.ajax({
				type: "POST",
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					"action": "insert_reserve_info_by_ajax",
					"bookfairID": <?php echo $bookfair_id; ?>,
					"userID": <?php echo $user_id; ?>,
					"postID": <?php echo $post_id ?>
				},
				success: function(msg){
					swal({	
		                title: "予約内容受付を完了しました！",
						text: "古本市でお待ちしております",
						type: "success",
						showCancelButton: false,
						confirmButtonColor: "#AEDEF4",
						confirmButtonText: "OK",
						closeOnConfirm: true
					},
					function(){
						jQuery("#reserve_button").prop("disabled",true);
					});
				},
				false: function(msg){
					swal({
						title: "予約に失敗しました、再度予約をお願いします",
						type: "error",
						showCancelButton: false,
						confirmButtonColor: "#AEDEF4",
						confirmButtonText: "OK",
						closeOnConfirm: true
					});
	     		}			
			});
		<?php endif; ?>
}

</script>

<div class="confirm_reserve_info">
	<p>タイトル：<?php echo $title; ?></p>
	<p>受取日：<?php echo $date; ?></p>
	<p>受け取り時間：<?php echo $time; ?></p>
	<p>受け取り場所：<?php echo $venue.' '.$room; ?></p>
	<p>必要ポイント数：<?php echo $point; ?></p>
</div>

<div style="margin-top:10px;text-align:center;">
	ポイントは、読み終わった本を持ってくるとためることができます
</div>

<div class="reserve_button">
		<input id="reserve_button" type="button" value="予約を確定する" onclick="postReserveInfo()"/>
</div>