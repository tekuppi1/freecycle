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
        <?php if($is_closed == false): ?>
                </div><!-- posts-row -->
        <hr class="hr-posts-row">
        <?php endif; ?>

				<?php bp_dtheme_content_nav( 'nav-below' ); ?>

			<?php else : ?>

				<h2 class="center"><?php _e( '商品が見つかりませんでした', 'buddypress' ); ?></h2>
				<p class="center"><?php _e( 'お探しの商品は見つかりませんでした。', 'buddypress' ); ?></p>

			<?php endif; ?>
            <?php  pagenation($page, $items_query->max_num_pages); ?>
		</div><!-- page ? -->

		<?php do_action( 'bp_after_blog_home' ); ?>

<?php
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
 ?>
