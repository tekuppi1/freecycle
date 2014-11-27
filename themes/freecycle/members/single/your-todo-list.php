<div id="todo-list">
	<?php
		global $user_ID;
		$todo_asc_list = get_todo_list($user_ID, "unfinished");
		$todo_list = array_reverse($todo_asc_list);
		if(!$todo_list){
	?>
		<p>your next actionはありません</p>
	<?php 
		}
		foreach($todo_list as $todo_item){
	?>
		<div id="todo-item" style="height:10px;margin:2px 2px 2px 2px;">
			<ul>
			<li ><?php echo $todo_item->message; ?>
			<ul>
			<ul style="float:right">
			<li ><?php 
					$deal_user_ID = deal_user($todo_item->item_id ,$user_ID);
					if($deal_user_ID){
						$deal_user = get_userdata($deal_user_ID)->display_name;
						echo "取引者　：　".$deal_user;
					}
				?>
			<li><?php echo "商品名　：　".get_post($todo_item->item_id)->post_title; ?>
			<li><?php echo date("g:i A 　　Y年m月d日",strtotime($todo_item->created)); ?>
			<ul>
		</div>
		<hr>
	<?php
		}
	?>
</div>