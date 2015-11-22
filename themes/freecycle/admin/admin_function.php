
<?php 
/**管理者用カテゴリ検索フォーム**/
function get_catgories_list(){ 
?>
	<!----------------------------->
<style type="text/css">
.ad_categories{
    width: auto;
		float : left;
		margin-right : 10px;
}
.main_categories.ad_{
		width : auto;
		line-height: 25px;
		height: auto;
	  padding: 0 20px 0 10px;
}
</style>
<!----------------------------->
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
<!----------------------------->
<style type="text/css">
.ad_input{
	float : left;
	height  :calc( 27px - 4px );
	width : 100px;
}
#searchsubmit{
	border: 0 !important;
	padding: 0 !important;
	margin-left : 0px;
	margin-right :auto;
	width: 45px !important;
	height: 27px !important;
	border-radius: 0px !important;
	background-size: contain;
	background-repeat: no-repeat;
	text-indent: 100%;
}
</style>
<!----------------------------->
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
<!----------------------------->
<style type="text/css">
.ad_input{
	float : left;
	height  :calc( 27px - 4px );
	width : 100px;
}
#searchsubmit{
	border: 0 !important;
	padding: 0 !important;
	margin-left : 0px;
	margin-right :auto;
	width: 45px !important;
	height: 27px !important;
	border-radius: 0px !important;
	background-size: contain;
	background-repeat: no-repeat;
	text-indent: 100%;
}
</style>
<!----------------------------->
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
jQuery(function() {
	var $Inputs = jQuery("#ad_search_form").find("#searchsubmit");
	$Inputs.on("click",function(){
	console.log("ajax");
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
<style type="text/css">
.ad-post-content{
	width : 150px;
}
.grid_center{
	position: relative;
 	max-width: 700px;
  margin: 0 auto;   /*全体の中央寄せ*/
}
.grid{
		width: 300px;
	/*margin:10px;*/
	border-bottom:1px solid #ccc;
}
.colm1{
	float:left;
	margin: 3px;
}
.colm2{
	margin-top: 10px;
	margin-left: 110px;
	height: 100px;
	width :150px;
}
.ad_cancel{
	width : 30px;
	padding : 2px;
	background-color:#ff2029;
	color : #FFF;
	border-radius : 5px;
	text-align: center;
	margin-left : auto;
}
.ad_pagenation{
	width : 90%;
}
</style>
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
			
<div class="grid_center">
<!--div class="page" id="blog-latest" role="main"-->

<?php 
	if($items_query->have_posts()){
			bp_dtheme_content_nav( 'nav-above' );
			$count = 1;
			while($items_query->have_posts()) {
				$items_query->the_post();
				do_action( 'bp_before_blog_post' );
?>
	
<!---------------------------------------------------------------------------------------------->
<!--表示-->	
<!---------------------------------------------------------------------------------------------->
<div class="grid">
<div class="colm1">
	<a href="<?php the_permalink(); ?>" class="post-img-contents"><?php the_post_thumbnail(array(100, 100)) ?></a>
</div>
<!------------------------------------->
<div class="colm2">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		<div class="ad_cancel">取消</div>
</div>
</div>
<!---------------------------------------------------------------------------------------------->
	
<?php 
			do_action( 'bp_after_blog_post' );
			$count++;
	}
	bp_dtheme_content_nav( 'nav-below' ); 
	} else {
?>
<h2 class="center"><?php _e( '商品が見つかりませんでした', 'buddypress' ); ?></h2>
<p class="center"><?php _e( 'お探しの商品は見つかりませんでした。', 'buddypress' ); ?></p>
<?php } ?>
	<div class="grid ad_pagenation">
  <?php pagenation($page, $items_query->max_num_pages); ?>
	</div>
	
<?php
do_action( 'bp_after_blog_home' );
}
/**ページネーション**/
function pagenation($page, $max_num_pages){
	if($page > $max_num_pages){
		echo_pagenation_link("pagenation_top", "all-items", 1, "商品トップへ");
		return;
	}
	$next = $page + 1;
	$back = $page - 1;
	echo '<div class="pagenation">';
	//back
	if($page == 1){
		echo_pagenation_span("pagenation_back", "< 戻る");
	}else{
		echo_pagenation_link("pagenation_back", "all-items", $back, "< 戻る");
	}
	
	//pages
	pagenation_select_number($page, $max_num_pages);

	//next
	if($page == $max_num_pages){
		echo_pagenation_span("pagenation_next", "次へ >");
	}else{
		echo_pagenation_link("pagenation_next", "all-items", $next, "次へ >");
	}
	echo '</div>';
}
function pagenation_select_number($page, $max_num_pages){
	echo '<select id="pagenation_number" onChange="top.location.href=value" >';
	for($i = 1; $i <= $max_num_pages; $i++){
		if($i == $page){
			echo '<option value="'.home_url().'/all-items/'. $i .'" selected>'.$i.'</option>';
		}else{
			echo '<option value="'.home_url().'/all-items/'. $i .'">'.$i.'</option>';
		}
	}
	echo '</select>';
}
function echo_pagenation_link($html_class, $link, $page, $value){
	echo '<div class="'. $html_class .'" ><a href="'.home_url().'/' . $link . '/'.$page.'">'. $value .'</a></div>';
}
function echo_pagenation_span($html_class, $value){
	echo '<div class="' . $html_class . '" ><span>' . $value .'</span></div>';
}
/**管理者用商品一覧**/
?>