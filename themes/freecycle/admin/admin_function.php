
<?php 
/**管理者用カテゴリ検索フォーム**/
function get_catgories_list(){ 
?>
<?php
   $main_categories = get_categories(array(
      "parent" => 0,
      "hide_empty" => 0,
      "exclude" => 1 //'uncategorized'
   ));
	echo("<div class='ad_categories'>");
  foreach ((array)$main_categories as $main_category) {
      $main_id = $main_category->term_id;
      $main_name = $main_category->name;
      $main_slug = $main_category->slug;
      echo "<div class='main_categories ad_' onclick='switchDisplay($main_id);'>$main_name</div>";

      $sub_categories = get_categories(array(
         "parent" => $main_id
      ));

      echo "<div class='sub_categories' id=sub_category_$main_id >";

      foreach((array)$sub_categories as $sub_category){
         $sub_name = $sub_category->name;
         $sub_slug = $sub_category->slug;
         echo "<div><a href='". home_url() ."/archives/category/".$main_slug."/".$sub_slug ."'>" .$sub_name. "</a></div>";
      }

      echo "</div>";
		echo "</div>";
   }
}
/**管理者用カテゴリ検索フォーム**/
?>

<?php
/**管理者用検索フォーム(old)**/
function admin_search_form_old(){ 
?>
		<form role="search" method="get" action="<?php echo home_url(); ?>">
    	<div>
				<div><input class="ad_search" type="submit" id="searchsubmit" style="float:right;"/></div>
				<input type="text" placeholder="検索" class="ad_input" name="s" id="s" value="<?php if(isset($_GET['s'])){ echo escape_html_special_chars($_GET['s']); } ?>" style="float:right;"/>
    	</div>
			<div style="clear:both;">
		<!-- 検索バー -->          
		</form>
<?php } 
/**管理者用検索フォーム(old)**/
?>
			
<?php
/**管理者用検索フォーム**/
function admin_search_form(){ 
?>
<form id="ad_search_form">
    	<div>
				<div>
				<!--input class="ad_search" type="submit" id="searchsubmit" style="float:right;"/-->
				<input class="ad_search" id="searchsubmit" style="float:right;"/></div>
				<input type="text" placeholder="検索" class="ad_input" name="s" id="s" value="<?php if(isset($_GET['s'])){ echo escape_html_special_chars($_GET['s']); } ?>" style="float:right;"/>
    	</div>
			<div style="clear:both;">
		<!-- 検索バー -->          
		</form>
			
<script type="text/javascript">
var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
console.log("Ajax");
jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
			'str' : jQuery(".ad_input").val(),
			'action' : 'get_search_json',
		},
		success: function(json){
			jQuery(".grid_center").empty();
			jQuery(".grid_center").append(json);
		}
});
jQuery(function() {
	var $Inputs = jQuery("#ad_search_form").find("#searchsubmit");
	$Inputs.on("click",function(){
		console.log("Ajax");
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
			'str' : jQuery(".ad_input").val(),
			'action' : 'get_search_json',
		},
		success: function(json){
			jQuery(".grid_center").empty();
			jQuery(".grid_center").append(json);
		}
		});
	});
});
</script>
<?php } 
/**管理者用検索フォーム**/
?>
				
<?php
/**管理者用商品一覧**/
function admin_item_list(){
?>
<!----------------------------->
<script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/3.3.2/masonry.pkgd.js"></script>
<!--可変グリッドアニメーション-->
<script type="text/javascript">
jQuery(function(){
	jQuery('.grid_center').masonry({
		itemSelector: '.grid',
		isFitWidth: true,
		isAnimated: true,
		isFitWidth : true
	});
});
</script>
<!----------------------------->
<?
    $url = explode("/",$_SERVER["REQUEST_URI"]);
    is_numeric(end($url)) ? $page = end($url) : $page = 1;
	
    $arg = array( 'posts_per_page' => 30, 'paged' => $page );
    $items_query = new WP_Query($arg);
?>
<h4 id="post-list-h4">商品一覧(<?php echo $items_query->found_posts;?>件)</h4>
<div class="grid_center">Now Loading...<div>
<?php
}
/**管理者用商品一覧**/
?>