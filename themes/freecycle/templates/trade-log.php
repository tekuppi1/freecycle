    <?php
    	wp_nonce_field('update-options');
    ?>
    <h2>取引履歴</h2>
    <table class="wp-list-table widefat fixed">
    	<thead>
			<tr valign="top">
			<th class="manage-column"></th>
			<th class="manage-column"><strong>今日</strong></th>
			<th class="manage-column"><strong>最近1週間</strong></th>
			<th class="manage-column"><strong>最近1ヶ月</strong></th>
			</tr>
		</thead>
		<tr valign="top">
		<td><strong>ください</strong></td>
		<td><?php echo get_giveme_log([
			"count"=>true,
			"period_from"=>date_i18n('Y-m-d 00:00:00'),
			"period_to"=>date_i18n('Y-m-d 23:59:59')]); ?>件</td>
		<td><?php
			// なぜかstrtotimeで日を計算すると一日ずれる……
			echo get_giveme_log([
			"count"=>true,
			"period_from"=>date_i18n('Y-m-d 00:00:00', strtotime('- 5 day')),
			"period_to"=>date_i18n('Y-m-d 23:59:59')]); ?>件</td>
		<td><?php 
			echo get_giveme_log([
			"count"=>true,
			"period_from"=>date_i18n('Y-m-d 00:00:00', strtotime('- 1 month')),
			"period_to"=>date_i18n('Y-m-d 23:59:59')]); ?>件</td>
		</tr>
		<tr valign="top" class="">
		<td><strong>取引完了</strong></td>
		<td><?php echo get_trade_log([
			"count"=>true,
			"state"=>'finished',
			"period_from"=>date_i18n('Y-m-d 00:00:00'),
			"period_to"=>date_i18n('Y-m-d 23:59:59')]); ?>件</td>
		<td><?php echo get_trade_log([
			"count"=>true,
			"state"=>'finished',
			"period_from"=>date_i18n('Y-m-d 00:00:00', strtotime('- 5 day')),
			"period_to"=>date_i18n('Y-m-d 23:59:59')]); ?>件</td>
		<td><?php echo get_trade_log([
			"count"=>true,
			"state"=>'finished',
			"period_from"=>date_i18n('Y-m-d 00:00:00', strtotime('- 1 month')),
			"period_to"=>date_i18n('Y-m-d 23:59:59')]); ?>件</td>
		</tr>
    </table>
	<br/>
    <h2>最近の取引</h2>
    <table class="wp-list-table widefat fixed">
    	<thead>
			<tr valign="top">
			<th class="manage-column"><strong>商品名</strong></th>
			<th class="manage-column"><strong>出品者</strong></th>
			<th class="manage-column"><strong>取引相手</strong></th>
			<th class="manage-column"><strong>最終更新日時</strong></th>
			<th class="manage-column"><strong>状態</strong></th>
			</tr>
		</thead>
		<tbody>
			<?php
				$count = 0;
				$trade_logs = get_trade_log([
					"limit"=>10,
					"order"=>["update_timestamp"=>"DESC", "insert_timestamp"=>"DESC", "confirmed_flg"=>"ASC"]]);
				foreach ($trade_logs as $trade_log) {
					$class = $count%2==0?'':"class='alternate'";
					$item = get_post($trade_log->post_id);
					$item_link = home_url() . "/archives/" . $trade_log->post_id;
					$exhibitor_link = bp_core_get_userlink($item->post_author);
					$bidder_link = bp_core_get_userlink(get_bidder_id($trade_log->post_id));
					$status = get_trade_status($trade_log->post_id);
					$status_display;
					switch ($status) {
						case FC_TRADE_FINISHED:
							$status_display = '取引完了';
							break;
						case FC_TRADE_BIDDER_EVALUATED:
							$status_display = '取引相手評価済';
							break;
						case FC_TRADE_EXHIBITOR_EVALUATED:
							$status_display = '出品者評価済';
							break;
						case FC_TRADE_ITEM_PASSED:
							$status_display = '商品受け渡し済';
							break;
						case FC_TRADE_CONFIRMED:
							$status_display = '取引相手確定済';
							break;
						case FC_TRADE_GIVEMEED:
							$status_display = 'ください済';
							break;
						default:
							// 
							break;
					}
echo <<<ROW
			<tr valign="top" $class>
			<td><a href="$item_link">$item->post_title</a></td>
			<td>$exhibitor_link</td>
			<td>$bidder_link</td>
			<td>$trade_log->update_timestamp</td>
			<td>$status_display</td>
			</tr>
ROW;
					$count++;
				}
			?>
		</tbody>
    </table>