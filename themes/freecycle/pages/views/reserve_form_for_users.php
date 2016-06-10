<?php get_header(); ?>
<?php
$reserve_infos = get_bookfair_info_all_you_want(1);	
	foreach($reserve_infos as $reserve_info){
		$datetime = strtotime($reserve_info->date);
		$date = date('Y/m/d',$datetime);
		$starttime = strtotime($reserve_info->starting_time);
		$start = date('H:i',$starttime);
		$endtime = strtotime($reserve_info->ending_time);
		$end = date('H:i',$endtime);
		$time = $start .' ～ '.$end;
		$venue = $reserve_info->venue;
		$room = $reserve_info->classroom;
		$bookfair_id = $reserve_info->bookfair_id;
	}

	$before_url=$_SERVER['HTTP_REFERER'];
	$filename = strrchr( $before_url, "/" );// /postname.html
	$post_id = substr( $filename, 1 );// postname.html
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

	$point=get_point($post_id);
?>

<script>
// 予約確定ボタンを押した時の反応（予約内容を予約テーブルに挿入する）
function postReserveInfo(){
	var user_nicename = jQuery('#user_id').val();
	if(user_nicename==""){
         	swal("入力していない情報があります");
    }else{
		<?php
			insert_user_nicename($user_nicename); 
			$user_count = get_user_count_of_nicename($user_nicename);
			if($user_count<1):
				$user_id = get_user_id_by_nicename($user_nicename);
			 	$reserve_count = get_reserve_count_of_postid($user_id);
			 	$book_count = count_books($post_id);
		?>
			
	     	jQuery.ajax({
				type: "POST",
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					"action": "insert_reserve_info_by_ajax",
					"bookfairID": <?php echo $bookfair_id; ?>,
					"userID": <?php echo $user_id; ?>,
					"postID": "<?php echo $post_id ?>"
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
						jQuery('#user_id').val('');
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
		<?php elseif($reserve_count=$book_count): ?>
				swal({
					title: "申し訳ございません。この本はすでに予約されています。",
					type: "error",
					showCancelButton: false,
					confirmButtonColor: "#AEDEF4",
					confirmButtonText: "OK",
					closeOnConfirm: true
				});
		<?php else: ?>
				swal({
					title: "申し訳ございません。これ以上予約できません。",
					type: "error",
					showCancelButton: false,
					confirmButtonColor: "#AEDEF4",
					confirmButtonText: "OK",
					closeOnConfirm: true
				});
		<?php endif; ?>
	}
}

</script>

<div class="confirm_reserve_info">
	<p>タイトル：<?php echo $title; ?></p>
	<p>受取日：<?php echo $date; ?></p>
	<p>受け取り時間：<?php echo $time; ?></p>
	<p>受け取り場所：<?php echo $venue.' '.$room; ?></p>
	<p>必要ポイント数：<?php echo $point; ?></p>
</div>
<?php
	var_dump(get_user_count_of_nicename('2015sc079'));
 	var_dump(get_reserve_count_of_postid('43'));
 	var_dump(count_books('43'));
 	var_dump(get_user_id_by_nicename('2015sc079'));
?>

<div style="margin-top:10px;text-align:center;">
	ポイントは、読み終わった本を持ってくるとためることができます
</div>

	↓本人確認のために入力をお願いします。本をお渡しするときに確認します。</br>
<div class="user_id_form">
	<input type="text" id="user_id" placeholder="学生：学生番号,一般：カタカナで名字"/>
</div>

<div class="reserve_button">
		<input type="button" value="予約を確定する" onclick="postReserveInfo()"/>
</div>