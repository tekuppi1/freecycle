<?php get_header(); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_blog_home' ); ?>

		<?php do_action( 'template_notices' ); ?>

		<div class="page" id="blog-latest" role="main">

			<?php if ( have_posts() ) : ?>

				<?php bp_dtheme_content_nav( 'nav-above' ); ?>
				<?php $count = 1; ?>
				<?php $row= 2; ?>
				<?php $is_closed = false; ?>
				<?php while (have_posts()) : the_post(); ?>
					<?php do_action( 'bp_before_blog_post' ); ?>
		<?php if($count%$row == 1) {
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
					<?php do_action( 'bp_after_blog_post' ); ?>
				<?php $count++; ?>
				<?php endwhile; ?>
							
				<?php bp_dtheme_content_nav( 'nav-below' ); ?>

			<?php else : ?>

				<h2 class="center"><?php _e( 'Not Found', 'buddypress' ); ?></h2>
				<p class="center"><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'buddypress' ); ?></p>

				<!-- <?php get_search_form(); ?> -->

			<?php endif; ?>
		</div><!-- page ? -->

		<?php do_action( 'bp_after_blog_home' ); ?>

		</div><!-- .padder -->
		
		
	</div><!-- #content -->

	<?php get_sidebar(); ?>

<?php get_footer(); ?>
