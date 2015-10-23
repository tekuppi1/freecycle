<?php get_header(); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_blog_search' ); ?>

		<div class="page" id="blog-search" role="main">

			<h2 class="pagetitle"><?php _e( 'Site', 'buddypress' ); ?></h2>

			<?php if (have_posts()) : ?>

				<h3 class="pagetitle"><?php _e( 'Search Results', 'buddypress' ); ?></h3>

				<?php bp_dtheme_content_nav( 'nav-above' ); ?>
				<?php $count = 1; ?>
				<?php $row = 2; ?>
				<?php $is_closed = false; ?>
				<?php while (have_posts()) : the_post(); ?>
					<?php do_action( 'bp_before_blog_post' ); ?>
		<?php if($count%$row == 1) { 
				$is_closed = false;
		?>
				<div class="posts-row">
		<?php } ?>
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

						<div class="post-content">

							<div class="entry">
								<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(array(150, 150)) ?></a>
								<span class="index-item-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
							</div>
						</div>
					</div>
		<?php if($count%$row == 0) { 
				$is_closed = true;
		?>
				</div><!-- posts-row -->
		<hr class="hr-posts-row">
		<?php } ?>
					<?php do_action( 'bp_after_blog_post' ); ?>
				<?php $count++; ?>
				<?php endwhile; ?>
		<?php if(!$is_closed){ ?>
			</div><!-- posts-row -->
		<hr class="hr-posts-row">
		<?php } ?>
				<?php bp_dtheme_content_nav( 'nav-below' ); ?>

			<?php else : ?>

				<h2 class="center"><?php _e( 'No posts found. Try a different search?', 'buddypress' ); ?></h2>
				<?php 
					// get_search_form();
				 ?> 

			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_blog_search' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar(); ?>

<?php get_footer(); ?>
