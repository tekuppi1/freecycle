<div id="todo-list">
	<?php
		global $user_ID;
		$todo_list = get_todo_list($user_ID, "unfinished");
		if(!$todo_list){
	?>
		<p>TODOはありません</p>
	<?php 
		}
		foreach($todo_list as $todo_item){
	?>
		<div id="todo-item" >
			<ul>
			<li><?php echo $todo_item->message; ?>
			<ul>
		</div>
		<hr>
	<?php
		}
	?>
</div>