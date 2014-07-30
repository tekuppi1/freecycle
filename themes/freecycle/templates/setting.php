<div class="wrap">
    <h2>テクスチェンジ設定</h2>
    <form method="post" action="options.php">
    <?php wp_nonce_field('update-options'); ?>
    <h2 class='nav-tab-wrapper'>
 	<a class="nav-tab<?php if(!isset($_REQUEST['view'])) echo ' nav-tab-active'; ?>" href="<?php echo admin_url('options-general.php?page=texchange');?>">
		トピックス
	</a>
	<?php
		foreach(array(
			'point-setting' => 'ポイント',
			'external-serveces' => '外部連携',
			'application' => 'アプリ'
		) as $key => $val):
	?>
	<a class="nav-tab<?php if(isset($_REQUEST['view']) && $_REQUEST['view'] == $key) echo ' nav-tab-active'; ?>" href="<?php echo admin_url('options-general.php?page=texchange&view='.$key);?>">
		<?php echo $val; ?>
	</a>
	<?php endforeach; ?>
	</h2>
	<?php
	$view = isset($_REQUEST['view']) ? (string)$_REQUEST['view'] : '';
	switch($view):
		case 'setting':
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'setting.php';
			break;
		case 'point-setting':
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'point-setting.php';
			break;
		case 'external-serveces':
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'external-serveces-setting.php';
			break;
		case 'application':
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'application-setting.php';
			break;
		default:
    ?>
    <table class="form-table">
    	<h3>お知らせ</h3>
		<tr valign="top">
		<th>お知らせを表示する</th>
		<td>
			<label>表示<input type="radio" name="use-topics" value="on" <?php if(get_option('use-topics')==='on'){ ?> checked <?php } ?>></label>
			<label>非表示<input type="radio" name="use-topics" value="off" <?php if(get_option('use-topics')==='off'){ ?> checked <?php } ?>></label>
		</td>
		<tr valign="top">
		<th scope="row">テキスト</th>
		<td><input type="text" name="topics-text" value="<?php echo get_option('topics-text'); ?>" /></td>
		</tr>
		<tr valign="top">
		<th scope="row">リンク</th>
		<td><input type="text" name="topics-link" value="<?php echo get_option('topics-link'); ?>" /></td>
		</tr>
		</table>
		<h3>注目商品</h3>	
		<table class="form-table">
		<tr valign="top">
		<th>注目商品を表示する</th>
		<td>
			<label>表示<input type="radio" name="use-topic-items" value="on" <?php if(get_option('use-topic-items')=='on'){ ?> checked <?php } ?>></label>
			<label>非表示<input type="radio" name="use-topic-items" value="off" <?php if(get_option('use-topic-items')=='off'){ ?> checked <?php } ?>></label>
		</td>
		</tr>
		<tr valign="top">
		<th scope="row">表示名</th>
		<td><input type="text" name="topic-items-name" value="<?php echo get_option('topic-items-name'); ?>" /></td>
		</tr>
		<tr>
		<th scope="row">検索条件</th>
		<td></td>
		</tr>
		<tr>
		<th scope="row">カテゴリ</th>
		<td>
		<label>有効<input type="radio" name="use-topic-items-condition-category" value="on" <?php if(get_option('use-topic-items-condition-category')=='on'){ ?> checked <?php } ?>></label>
		<label>無効<input type="radio" name="use-topic-items-condition-category" value="off" <?php if(get_option('use-topic-items-condition-category')=='off'){ ?> checked <?php } ?>></label>
		<ul>
		<li>カテゴリ:
	<?php
		wp_dropdown_categories(array(
			'orderby'		=> 'ID',
			'hide_empty'	=> 0,
			'exclude'		=> '1',
			'name'			=> 'topic-items-condition-category',
			'selected'		=> get_option('topic-items-condition-category')
		));
	?>
		</li>
		</ul>
		</td>
		<tr>
		<th scope="row">キーワード</th>
		<td>
		<label>有効<input type="radio" name="use-topic-items-condition-title" value="on" <?php if(get_option('use-topic-items-condition-title')=='on'){ ?> checked <?php } ?>></label>
		<label>無効<input type="radio" name="use-topic-items-condition-title" value="off" <?php if(get_option('use-topic-items-condition-title')=='off'){ ?> checked <?php } ?>></label>
		<ul>
		<li>キーワード:<input type="text" name="topic-items-condition-title" value="<?php echo get_option('topic-items-condition-title'); ?>" /></li>
		</ul>
		</tr>
    </table>
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options"
    	value="
    		use-topics, topics-text, topics-link,
    	    use-topic-items, topic-items-name, 
    		use-topic-items-condition-category, topic-items-condition-category,
    		use-topic-items-condition-title, topic-items-condition-title
    		" />
    <p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	<?php 
			break;
	endswitch;
	?>
    </form>
</div>