<script>
		onClickSearchWantedList(0);
</script>
<input type="text" name="keyword" id="keyword" placeholder="書名検索" size="30">
<input type="button" name="btn_search" value="検索" onClick="onClickSearchWantedList(0)"></br>
<div id="wanted_list" style="height:auto !important;">
<div align=center><img src="<?php echo get_stylesheet_directory_uri() ?>/images/ajax-loader.gif"></div>
</div>