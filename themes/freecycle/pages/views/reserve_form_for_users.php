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
	<form>
		<input type="button" value="予約を確定する"/>
	</form>
</div>