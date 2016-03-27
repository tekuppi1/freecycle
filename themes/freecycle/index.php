<?php get_header(); ?>

<!--
	style sheet : style/index.css
-->


<!--広告-->
<div id="huruhon">
	<img src=" <?php echo get_stylesheet_directory_uri() ?>/images/index/huru.png" alt="古本市の宣伝">
</div>
<!--広告-->

<!------------------------------------------------------>
<!--カテゴリ検索-->
<!------------------------------------------------------>
<div>
<ul id=index_category_search>
<?php
	$main_categories = get_categories(array("parent" => 0,"hide_empty" => 0,"exclude" => 1));
  foreach((array)$main_categories as $main_category){
 		$main_name = $main_category->name;
 		$main_slug = $main_category->slug;
		echo "<a href='". home_url()."/archives/category/".$main_slug."'><li>$main_name</li></a>";
	}
?>
</ul>
</div>
<script>
jQuery(window).on('load resize', function(){
	var a = jQuery('body');
	var b = jQuery('#index_category_search li');
	var count = b.length;
	if(a.width() > 450)
		b.width(((a.width()-1)/count)-2);
	else
		b.width((a.width()/2)-2);
});
</script>
<!------------------------------------------------------>
<!--div id="head">Textbook for FREE</div-->
<!------------------------------------------------------>
<!--サーチフォーム検索-->
<!------------------------------------------------------>
<li class="index_wire">
<div class="index_title">ワード検索</div>
<div id="index_searchform">
<form role="search" method="get" id="searchform_index" action="<?php echo home_url(); ?>">
<input type="text" placeholder="ほしい本を検索する" name="s" value=""/>
<input type="submit" value="検索"/>
</form>
</div>
<div style="clear:both"></div>
</li>

<!------------------------------------------------------>
<!--サムネイル表示-->
<!------------------------------------------------------>
<li class="index_wire thum">
	<div class="index_title">PICK UP!<span class="index_title_small">おすすめ</span></div>
<?php
$i =0;
$args = array( 'posts_per_page' => 100, 'paged' => $page, 'orderby' => 'rand');
$posts = get_posts( $args );
foreach( $posts as $key => $post ) {
  if($i>3) break;else $i++;
	$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
	$image = wp_get_attachment_image_src( $post_thumbnail_id,'medium');
	$returnObj[$key] = array(
		'post_title' => $post->post_title,
		'permalink' => get_permalink( $post->ID ),
		'genre' => $post->post_category,
		'image' => $image
	);
	if($returnObj[$key]['image']!=null){
		$image_src = $returnObj[$key]['image'][0];
	} else {
		$image_src = get_stylesheet_directory_uri().'/images/index/NotImage.png';
	}
?>
<div class="index_archive_grid">
	<a href="<?php echo $returnObj[$key]['permalink']; ?>">
		<img class="index_archive_entry_img" src="<?php echo $image_src ?>">
	</a>
	<div class="index_archive_cat"><span><?php echo get_category($returnObj[$key]['genre'][0])->cat_name ?></span></div>
	<div class="index_archive_title">
		<a href="<?php echo $returnObj[$key]['permalink']; ?>"><?php echo $returnObj[$key]['post_title'] ?></a>
	</div>
</div>
<?php
}
?>
</li>
<script>
jQuery(window).on('load resize', function(){
	var count = jQuery('.thum').length;
	jQuery('.thum').width((jQuery('body').width())-32-20);

	var a = jQuery('.thum');
	var b = jQuery('.index_archive_entry_img');
	var c = jQuery('.index_archive_title');
	var count = b.length;
	var _width;
	if(a.width() > 450){
		_width =(((a.width()/4))-15);
	}else{
		_width =(((a.width()/2))-15);
	}
	b.width(_width);
	b.height(_width*1.2);
	c.width(_width);
});
</script>


<!------------------------------------------------------>
<!--サムネイル表示(カテゴリ別)-->
<!------------------------------------------------------>
<?php
	$categories = get_categories('parent=0');
	foreach($categories as $category){
?>
<li class="index_wire thum2">
<a href="<?php echo get_category_link( $category->term_id ); ?>" class="index_title_link">
	<div class="index_title back_subcolor"><?php echo $category->cat_name; ?></div>
</a>
<?php
$i =0;
$args = array( 'posts_per_page' => 100, 'paged' => $page, 'orderby' => 'rand', 'category'=> $category->term_id);
$posts = get_posts( $args );
foreach( $posts as $key => $post ) {
  if($i>4) break;else $i++;
	$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
	$image = wp_get_attachment_image_src( $post_thumbnail_id,'medium');
	$returnObj[$key] = array(
		'post_title' => $post->post_title,
		'permalink' => get_permalink( $post->ID ),
		'genre' => $post->post_category,
		'image' => $image
	);
	if($returnObj[$key]['image']!=null){
		$image_src = $returnObj[$key]['image'][0];
	} else {
		$image_src = get_stylesheet_directory_uri().'/images/index/NotImage.png';
	}
?>
<div class="index_archive_grid">
	<a href="<?php echo $returnObj[$key]['permalink']; ?>">
		<img class="index_archive_entry_img2 <?php if($i==1)echo "big"; ?>" src="<?php echo $image_src ?>">
	</a>
</div>
<?php
}
echo "</li>" ;
}
?>
<script>
jQuery(window).on('load resize', function(){
	var a = jQuery('body');
	var b = jQuery('.thum2');
	var count = b.length;
	if(a.width() > 500){
		b.width((a.width()/2)-32-20);
	} else {
		b.width((a.width())-32-20);
	}

	var a = jQuery('.thum2');
	var b = jQuery('.index_archive_entry_img2');
	var count = b.length;
	var _width;
	if(a.width() > 270){
		_width =(((a.width()/4))-10);
	}else{
		_width =(((a.width()/2))-10);
	}
	b.width(_width);
	b.height(_width*1.2)

	var b = jQuery('.big');
	if(a.width() > 270){
		_width =(((a.width()/2))-20);
	}else{
		_width =(((a.width()))-20);
	}
	b.width(_width);
	b.height(_width*1.3)
});
</script>

<!------------------------------------------------------>
<!--サーチフォーム検索-->
<!------------------------------------------------------
<li class="index_wire">
<div class="index_title">カテゴリ検索</div>
<div id="searchbox">
	<div class="category">
		<img src="<?php echo get_stylesheet_directory_uri() ?>/images/index/a.png">
		<p class="main">法学部</p>
	</div>
	<div class="clear"></div>
	<div class="category">
		<img src="<?php echo get_stylesheet_directory_uri() ?>/images/index/b.png">
		<p class="main">経済学部</p>
	</div>
	<div class="clear"></div>
	<div class="category">
		<img src="<?php echo get_stylesheet_directory_uri() ?>/images/index/c.png">
		<p class="main">外国語学部</p>
		<p class="sub">英米学科</p>
		<p class="sub">スペイン・ラテンアメリカ学科</p>
		<p class="sub">フランス学科</p>
		<p class="sub">ドイツ学科</p>
		<p class="sub">アジア学科</p>
	</div>
	<div class="clear"></div>
</div>
</li>
<div class="clear"></div>
<!--カテゴリ検索-->

<!------------------------------------------------------>
<div style="clear:both"></div>
<!------------------------------------------------------>
<!--ログアウト処理-->
	<?php if( is_user_logged_in() ): ?>
	
<div class="index_profile">
	<?php bp_loggedin_user_avatar( 'type=thumb&width=30&height=30' ); ?>
	<span class="user-nicename"><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></span>
	<a class="button logout" href="<?php echo wp_logout_url( wp_guess_url() ); ?>"><?php _e( 'Log Out', 'buddypress' ); ?></a>
</div>
	
	<?php endif; ?>
<!--ログアウト処理-->

<?php get_footer(); ?>
