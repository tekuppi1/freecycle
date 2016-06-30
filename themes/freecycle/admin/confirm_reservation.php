<?php get_header(); ?>



<?php
	global $wpdb;
	global $table_prefix;
	$allreserve = $wpdb->get_results(
		"SELECT * FROM " . $table_prefix."fmt_reserve"
	);
	foreach ( $allreserve as $reservation ) {
	$bookfair_info = $wpdb->get_results($wpdb->prepare(
		"SELECT " . $table_prefix . "fmt_book_fair.date
		FROM " . $table_prefix . "fmt_book_fair
		WHERE " . $table_prefix . "fmt_book_fair.bookfair_id = %s",$reservation->bookfair_id
	));
	$start= $bookfair_info[0]->date;
	$time = strtotime("$start");
		echo '<div class="resbox">';
		echo '<div>ユーザーid：：',$reservation->user_id, '</div>';
		echo '<div>本のタイトル：：',get_the_title($reservation->item_id), '</div>';
		echo '<div>受け取り予定の古本市：：：',date("Y/m/d H:i",$time), '</div>';
		$reserveID = $reservation->reserve_id;
		echo '<div>予約id：：：',$reserveID, '</div>';
		echo'<input type="button" class="reserve_delete" onclick="testt()" value="予約完了する">';
		echo '</div>';
		}
?>

<script>
function testt(){
	swal({   
		title: "ホントに大丈夫？",  
		text: "予約データを取り消します。一度消したらもとには戻せませんよ？",   
		type: "warning",   
		showCancelButton: true,   
		confirmButtonColor: "#DD6B55",   
		confirmButtonText: "けす！", 
		cancelButtonText: "やっぱやめる" , 
		closeOnConfirm: false },
		function(){   
			jQuery.ajax({
				type: "POST",
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					"action": "delete_reservation",
					'reserveID': "<?php echo $reserveID ?>",
				},

		success: function(res){
					var result = JSON.parse(res);
					if(result.error === "true"){
						swal({
							title: "取引完了に失敗しました。",
							text: result.message,
							type: "error",
							showCancelButton: false,
							confirmButtonColor: "#AEDEF4",
							confirmButtonText: "OK",
							closeOnConfirm: true
						});
					}else{
						swal({
							title: "取引を完了しました。",
							type: "success",
							showCancelButton: false,
							confirmButtonColor: "#AEDEF4",
							confirmButtonText: "OK",
							closeOnConfirm: true
						},
						function(){
							location.reload();
						});
					}
				},
				false: function(){
					swal({
						title: "取引完了に失敗しました。",
						text: "システム管理者に連絡してください。",
						type: "error",
						showCancelButton: false,
						confirmButtonColor: "#AEDEF4",
						confirmButtonText: "OK",
						closeOnConfirm: true
					});
				}
			}); 
			});
}
</script>