<?php
function ajax_func($str){

$returnObj = array();
$html = "";
	
$url = explode("/",$_SERVER["REQUEST_URI"]);
is_numeric(end($url)) ? $page = end($url) : $page = 1;
$args = array( 'posts_per_page' => 50, 'paged' => $page ,'s' => $str);
$posts = get_posts( $args );   
	
foreach( $posts as $key => $post ) {
	$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
	$image = wp_get_attachment_image_src( $post_thumbnail_id, array(100, 100) );
	$returnObj[$key] = array(
		'post_title' => $post->post_title,
		'permalink' => get_permalink( $post->ID ),
		'image' => $image
	);
	
	$html .= "<!---------------------------------------------------------------------------------------------->";
	$html .= "<!--表示-->";
	$html .= "<!---------------------------------------------------------------------------------------------->";
	$html .= "<div class=\"grid\">";
	$html .= "<div class=\"colm1\">";
	$html .= "<a href=\"".$returnObj[$key]['permalink']."\" class=\"post-img-contents\">";
	$html .= "<img width=\"100\" height=\"100\" src=\" ".$returnObj[$key]['image'][0]." \" class=\"attachment-100x100 wp-post-image\">";
	$html .= "</a>";
	$html .= "</div>";
	$html .= "<!------------------------------------->";
	$html .= "<div class=\"colm2\">";
	$html .= "<a href=\"".$returnObj[$key]['permalink']."\">".$returnObj[$key]['post_title']."</a>";
	$html .= "<div class=\"ad_cancel\">取消</div>";
	$html .= "</div>";
	$html .= "</div>";
	$html .= "<!---------------------------------------------------------------------------------------------->";
}
echo $html;
}
?>