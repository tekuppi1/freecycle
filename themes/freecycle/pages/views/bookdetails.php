
<!-------個別商品情報-------->	
<div class="fake">
	<div class="bookinfo">
		<p class="booktitle">商品名: <?php echo get_the_title(); ?></p>
		<p class="bookauthor">著者: <?php echo get_post_meta($post->ID, "author", true)?get_post_meta($post->ID, "author", true):"データがありません"; ?></p>
		<img class="picture" src="/wp-content/themes/freecycle/pages/views/image/sc.png" width="50%" height="50%" alt="本の写真">
		<div class="fake">
			<table class="booksubinfo">
				<tr>
					<th class="normal">カテゴリー</th><td class="normal">?????</td>
				</tr>

				<tr>
				<th class="normal">ポイント数</th><td class="normal">？？？？？？？？</td>
				</tr>

				<tr>
				<th class="normal">Amazon価格</th><td class="normal"><?php echo get_post_meta($post->ID, "price", true)?number_format(get_post_meta($post->ID, "price", true))."円":"データがありません"; ?></td>
				</tr>

				<tr>
				<th class="normal">残り冊数</th><td class="normal"><?php echo count_books($post->ID)?count_books($post->ID):0; ?>冊</td>
				</tr>	
			</table>
		</div>
	</div>
</div>
<div class="reserve">
	<span class="this">この商品を</span><a class="button" href="#">予約する</a>
	

</div>	
	
<div class="plus">
<P >補足情報：<?php remove_filter('the_content', 'wpautop'); the_content(); ?></P>	
</div>	
	
 </body>
</html>
