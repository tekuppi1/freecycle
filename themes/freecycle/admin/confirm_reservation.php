<script>
function onDeleteReserve(){
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
					"action": "delete_reservation",
					'reserveID': "<?php echo $reserveID ?>",
				},
				success: function(msg){
					swal({
						title: "予約を取り消しました。",
						type: "success",
						showCancelButton: false,
						confirmButtonColor: "#AEDEF4",
						confirmButtonText: "OK",
						closeOnConfirm: true
					},
					function(){
						location.href = "<?php echo_entry_list_url(); ?>";
					});
				},
				false: function(msg){
					swal({
						title: "予約の取り消しに失敗しました。",
						type: "error",
						showCancelButton: false,
						confirmButtonColor: "#AEDEF4",
						confirmButtonText: "OK",
						closeOnConfirm: true
					});
				}
			});
		}else{return false;}
		});
}

function testt(){
	alert("ppp");
}
</script>
<?php confirm_reserve(); ?>

