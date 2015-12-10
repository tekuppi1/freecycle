<?php
global $current_user;
get_currentuserinfo();

include_once get_stylesheet_directory().DIRECTORY_SEPARATOR."/head.php";

// DELETE TEMPORARILY
// because of unable to login from smartphone apps
if(!is_user_logged_in() || $current_user->user_level != ADMIN_LEVEL){
	header('Location:' . home_url() . '/renewal.php');
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<?php head_load(); ?>

<body <?php body_class(); ?> id="bp-default">
<?php do_action( 'bp_before_header' ); ?>
	
<!--ヘッダー------------------->
<div id="header_menu_ber" role=”banner”>
	<ul id="dropmenu" class="dropmenu">
		<li id="logo">
			<a title="ホーム"><div id="logo_icon" alt="ロゴ"></div></a>
		</li>
		<li id="menu_button"><a id="menu_button_a"><div id="home_icon" alt="ホーム"></div></a>
			<ul>
			<li>予約確認<br><br>
				<span id="check">あなたの<br>予約している本は
					<img src="">○○です<br></span></li>
			<li><a href="">予約キャンセル</a></li>
			<li><a href="">検索</a></li>
			<li><a href="">商品一覧</a></li>
			</ul>
		</li>
	</ul>
</div>
<!--ヘッダー------------------->

<?php if(is_archive() || is_search() || is_single()){ ?>
<?php } ?>
	
	

<!--コンテンツ------------------->
<div id="header_container">
