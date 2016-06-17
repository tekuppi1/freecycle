<?php get_header(); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_archive' ); ?>

		<div class="page" id="blog-archives" role="main">

			<h3 class="pagetitle"><?php printf('「%1$s」の商品一覧', wp_title( false, false ) ); ?></h3>

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
		<?php }
				if(count_books(get_the_ID())>0){ 
		?>
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

						<div class="post-content">

							<div class="entry">
								<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(array(150, 150)) ?></a>
								<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
								<span class="index-item-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
							</div>
						</div>
						<?php $count++; ?>
					</div>
		<?php 
				}
			if($count%$row == 0) {
				$is_closed = true;
		?>
				</div><!-- posts-row -->
		<!-- <hr class="hr-posts-row"> -->
		<?php } ?>
					<?php do_action( 'bp_after_blog_post' ); ?>
				<?php endwhile; ?>
		<?php if(!$is_closed){ ?>
			</div><!-- posts-row -->
		<!-- <hr class="hr-posts-row"> -->
		<?php } ?>
				<?php bp_dtheme_content_nav( 'nav-below' ); ?>

			<?php else : ?>

				<h2 class="center"><?php _e( 'Not Found', 'buddypress' ); ?></h2>
				<?php get_search_form(); ?>

			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_archive' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar(); ?>

<?php get_footer(); ?>
