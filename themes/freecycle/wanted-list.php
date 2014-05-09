<script>
		onClickSearchWantedList(0);
</script>
<ul>
	<li>
	<input type="text" name="keyword" id="keyword" placeholder="書名検索" size="30">
	<input type="button" name="btn_search" value="検索" onClick="onClickSearchWantedList(0)">
	</li>
	<li>
	<label for='department'>学部で検索</label>
	<select name='department' id='department' onChange="onClickSearchWantedList(0)"><?php echo get_department_options(); ?></select>
	</li>
</ul>
<div id="wanted_list" style="height:auto !important;">
<div align=center><img src="<?php echo get_stylesheet_directory_uri() ?>/images/ajax-loader.gif"></div>
</div>