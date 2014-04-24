	<?php 
		$givemes = get_your_giveme_list();
		if($givemes){
	?>
	あなたは以下の商品に「ください」中です。
	<?php bp_dtheme_content_nav( 'nav-above' ); ?>
	<?php $count = 1; ?>
	<?php $row = 2; ?>
	<?php $is_closed = false; ?>
	<?php foreach($givemes as $giveme){
			$post = get_post($giveme->post_id);
	?>
		<?php if($count%$row == 1) {
				$is_closed = false;
		?>
		<div class="posts-row">
		<?php } ?>
			<div id="post-<?php echo $post->ID; ?>" <?php post_class('post'); ?> class="entry-on-index">
				<div class="post-content">		
					<div class="entry">				
					<a href="<?php echo get_permalink($post->ID); ?>" class="post-img-contents"><?php echo get_the_post_thumbnail($post->ID, array(150, 150)) ?></a>					
					<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
					<span class="index-item-title"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></span>
					</div>							
				</div><!-- post-content -->					
			</div><!-- post名 -->
		<?php if($count%$row == 0) {
				$is_closed = true;
		?>
		</div><!-- posts-row -->
		<hr class="hr-posts-row">
		<?php } ?>
		<?php $count++;
		} // foreach roop end
		if($count > 1 && !$is_closed){
		?>
			<div id="post-dummy" <?php post_class('post'); ?> class="entry-on-index">
				<div class="post-content">
					<div class="entry">	
					</div>
				</div><!-- post-content -->					
			</div><!-- post名 -->
		</div><!-- posts-row -->
		<hr class="hr-posts-row">
	<?php
		}
	} else {
	?>
	くださいしている商品はありません。
	<?php
		}
	?>