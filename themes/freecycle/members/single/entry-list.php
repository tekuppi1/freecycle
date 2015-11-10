<?php
function show_entry_list($user_ID, $mode){
	global $post;
	$count = 1;
	$row = 2;
	$is_closed = false;
	$entrylist_query =
			new WP_Query(array(
					'author' => $user_ID,
					'showposts' => -1,
					'order' => 'DESC'
				));
	if($entrylist_query->have_posts()){
		while($entrylist_query->have_posts()) : $entrylist_query->the_post();
?>
		<?php
			if($mode === 'notinprogress'){
				if(isGiveme($post->ID)){
					continue;
				}
			}elseif($mode === 'toconfirm'){
				if(!isGiveme($post->ID) || isConfirm($post->ID)){
					continue;
				}
			}elseif($mode === 'inprogress'){
				if(!isConfirm($post->ID) || isBidderEvaluated($post->ID)){
					continue;
				}
			}elseif($mode === 'finished'){
				if(!isBidderEvaluated($post->ID)){
					continue;
				}
			}
			if($count%$row == 1) {
			$is_closed = false;
		?>
			<div class="posts-row">
		<?php } ?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?> class="entry-on-index">
		<div class="post-content">
			<div class="entry">	
				<a href="<?php the_permalink(); ?>" class="post-img-contents"><?php the_post_thumbnail(array(150, 150)) ?></a>					
				<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
				<span class="index-item-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
			</div>
		</div><!-- post-content -->					
	</div><!-- post名 -->
	<?php if($count%$row == 0) {
			$is_closed = true;
	?>
	</div><!-- posts-row -->
	<hr class="hr-posts-row">
	<?php } ?>
<?php
		$count++;
		endwhile;
		if($count > 1 && !$is_closed){
?>
		<div id="post-dummy" <?php post_class(); ?> class="entry-on-index">
			<div class="post-content">
				<div class="entry">	
				</div>
			</div><!-- post-content -->					
		</div><!-- post名 -->
	</div><!-- posts-row -->
	<hr class="hr-posts-row">
<?php
		}
		if($count === 1){
?>
	<p>該当の出品はありません。</p>
<?php
		}	
	}else{
?>
	<p>出品がまだありません。</p>
	<!--p><a href="<?php echo get_new_entry_url(); ?>">こちらから出品してみましょう！</a></p-->
<?php
	}
}
?>