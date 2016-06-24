<?php
/*------------------------------------------------------------
   Template Name: show_reservations 
   Description: 予約情報確認画面のテンプレです。
 ------------------------------------------------------------*/
?>

<?php
function delete_searchform($text) {
  // この関数は検索フォームを表示させるためのもののはずだけど。。。変数にぶっこんでいいんでしょうかねぇ
  $ngwords = 'get_search_form();';
  str_replace($ngwords, "", $text);    // $text 内に NGワード($ngwords) が見つかったら、該当部分を $text から削除する
}

delete_searchform(get_header());
?>

<div id="content">
  <div class="padder one-column">

    <?php do_action('bp_before_blog_page'); ?>
    
    <div class="page" id="blog-page" role="main">
      
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				
				<h2 class="pagetitle"><?php the_title(); ?></h2>
				
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="page-entry">
						<?php the_content( __( '<p class="serif">Read the rest of this page &rarr;</p>', 'buddypress' ) ); ?>
						<?php
						wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ),
																	'after' => '</p></div>',
																	'next_or_number' => 'number' ) );
						?>
						<?php edit_post_link( __( 'Edit this page.', 'buddypress' ), '<p class="edit-link">', '</p>'); ?>
					</div>
				</div>

				<!-- blog-page って領域の中に書いてるんだから、そりゃ初期位置は本文の中になるでしょうよ -->
				<div class="a_reserved_book"></div>
				
      <?php endwhile; endif; ?>

    </div> <!-- end page -->

    <?php do_action( 'bp_after_blog_page' ); ?>
    
  </div>   <!-- end padder -->
</div>     <!-- end content -->

<?php get_footer(); ?>
