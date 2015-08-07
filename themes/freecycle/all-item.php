<?php
    $url = explode("/",$_SERVER["REQUEST_URI"]);
    if(is_numeric(end($url))){
        $page = end($url);
    }else{
        $page = 1;
    }
    $arg = array(
        'posts_per_page' => 10,
        'paged' => $page
    );
    $items_query = new WP_Query($arg);
?>
<h4 id="post-list-h4">商品一覧(<?php echo $items_query->found_posts;?>件)
		</h4>
		<div class="page" id="blog-latest" role="main">

			<?php if ( $items_query->have_posts() ) : ?>

				<?php bp_dtheme_content_nav( 'nav-above' ); ?>
				<?php $count = 1; ?>
				<?php $row = 2; ?>
				<?php $is_closed = false; ?>
				<?php while ($items_query->have_posts()) : $items_query->the_post(); ?>
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
            <?php  pagenation($page, $items_query->max_num_pages); ?>
		</div><!-- page ? -->

		<?php do_action( 'bp_after_blog_home' ); ?>

<?php
    function pagenation($page, $max_num_pages){
        if($max_num_pages - $page < 3){
            $range = $max_num_pages;
        }else{
            $range = $page + 3;
        }
        if(empty($page)){
            $page = 1;
        }

        $back = true;
        //ページネーション表示
        echo '<div id="pagenations">';
        for($i = $page - 1; $i <= $range; $i++){
            //１ページ目の時
            if($i == 0){
                $back = false;
                continue;
            }
            if($back == true){
                echo '<div class="pagenation-char" ><a href="'.home_url().'/all-item/'.$i.'">< Back</a></div>';
                $back = false;
            }
            if($page == $i){
                echo '<div class="pagenation" ><span>'.$i.'</span></div>';
            }else{
                echo '<div class="pagenation" ><a href="'.home_url().'/all-item/'.$i.'">'.$i.'</a></div>';
            }
        }
        $next = $page + 1;
        echo '<div class="pagenation-char" ><a href="'.home_url().'/all-item/'.$next.'">Next > </a></div>';
        echo '<div class="pagenation-char" ><a href="'.home_url().'/all-item/'.$max_num_pages.'">Last >></a></div>';
        echo "</div>";
    }
 ?>
