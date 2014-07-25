<?php get_header(); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_blog_home' ); ?>

		<?php do_action( 'template_notices' ); ?>
		<?php if(endsWith(home_url() . '/', $_SERVER['REQUEST_URI'])) : ?>
			<div class="page" id="topic-items">
			<?php
				$static_condition = '&orderby=rand'; // random display
				$topics_items_condition = '';
				$topics_showposts = 4;
				if(get_option('topic-items-condition-category') && get_option('use-topic-items-condition-category') == 'on'){
					$topics_items_condition .= '&cat=' . get_option('topic-items-condition-category');
				}
				if(get_option('topic-items-condition-title') && get_option('use-topic-items-condition-title') == 'on'){
					$topics_items_condition .= '&s=' . get_option('topic-items-condition-title');
				}
				$topics_query = new WP_Query($static_condition . $topics_items_condition . '&showposts=' . $topics_showposts);
			?>
			<?php if ($topics_query->have_posts() && get_option('use-topic-items')=='on') : ?>
			<h4 id="topic-h4">注目の商品：<?php echo get_option('topic-items-name')?></h4>
				<?php $count = 1; ?>
				<?php $row = 2; ?>
				<?php $is_closed = false; ?>
				<?php while ($topics_query->have_posts()) : $topics_query->the_post(); ?>
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
				<?php $count++; ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
				<?php if(!$is_closed): ?>
						<!-- dummy -->
						<div <?php post_class(); ?> class="entry-on-index">
								<div class="post-content">
									<div class="entry">	
									</div>							
								</div><!-- post-content -->					
						</div><!-- post名 -->
					</div><!-- posts-row -->
					<hr class="hr-posts-row">
				<?php endif; ?>
				<a href="<?php echo home_url() . '/?' . $topics_items_condition ?>"><p>注目の商品をもっと見る</p></a>
			<?php endif; ?>
		</div><!-- page -->

		<!-- latest items -->
		<h3 id="latest-topic">最新の出品</h3>
		<?php
			$latestitems_showposts = 6;
			$latestitems_query =
				new WP_Query(
					'&showposts=' . $latestitems_showposts);
			if($latestitems_query->have_posts()) :
		?>
		<div class="page" id="blog-latest" role="main">
			<div class="posts-row">
			<?php
				while ($latestitems_query->have_posts()) : $latestitems_query->the_post();
			?>
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?> class="entry-on-index">
					<div class="post-content">
						<div class="entry">	
							<a href="<?php the_permalink(); ?>" class="post-img-contents"><?php the_post_thumbnail(array(150, 150)) ?></a>					
							<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
							<span class="index-item-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
						</div>							
					</div><!-- post-content -->					
				</div><!-- post名 -->
			<?php
				endwhile;
			?>
			</div>
			<hr class="hr-posts-row">
			<a href="<?php echo home_url() . "/page/1" ?>">すべての商品を見る(<?php global $wp_query; echo $wp_query->found_posts;?>件)</a>
		</div>
		<?php
				endif;
		?>


		<?php endif; ?>

		<?php if(!endsWith(home_url() . '/', $_SERVER['REQUEST_URI'])) : ?>
		<h4 id="post-list-h4">商品一覧(<?php global $wp_query; echo $wp_query->found_posts;?>件)
		</h4>
		<div class="page" id="blog-latest" role="main">

			<?php if ( have_posts() ) : ?>

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
		<?php endif; ?>

		<?php do_action( 'bp_after_blog_home' ); ?>

		</div><!-- .padder -->

	</div><!-- #content -->

	<?php get_sidebar(); ?>

<?php get_footer(); ?>
