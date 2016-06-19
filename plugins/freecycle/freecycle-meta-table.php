<?php
class FreecycleMetaTable {
	// plugin custom table names
	var $fmt_giveme_state;
	var $fmt_points;
	var $fmt_trade_history;
	var $fmt_user_giveme;
	var $fmt_wanted_list;
	var $fmt_todo;
	var $fmt_trade_maps;
	var $fmt_reserve;
	var $fmt_book_fair;
	
	public function __construct(){
		global $wpdb;
		$this->fmt_giveme_state = $wpdb->prefix . 'fmt_giveme_state';
		$this->fmt_points = $wpdb->prefix . 'fmt_points';
		$this->fmt_trade_history = $wpdb->prefix . 'fmt_trade_history';
		$this->fmt_user_giveme = $wpdb->prefix . 'fmt_user_giveme';
		$this->fmt_wanted_list = $wpdb->prefix . 'fmt_wanted_list';
		$this->fmt_todo = $wpdb->prefix . 'todo';
		$this->fmt_trade_maps = $wpdb->prefix . 'fmt_trade_maps';
		$this->fmt_reserve = $wpdb->prefix . 'fmt_reserve';
		$this->fmt_book_fair = $wpdb->prefix . 'fmt_book_fair';
		
		 register_activation_hook (__FILE__, array($this, 'cmt_activate'));
	}
	
	
	function fmt_activate(){
		global $wpdb;
		//DB version
		$fmt_db_version = '2.1111111111';
		//current DB version
		$installed_ver = get_option( 'fmt_meta_version' );
			// if versions are different tables are created
			if( $installed_ver != $fmt_db_version ) {
				$sql = "CREATE TABLE IF NOT EXISTS `" . $this->fmt_giveme_state . "` (
						`insert_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
						`update_timestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
						`post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
						`entry_flg` int(1) unsigned NOT NULL DEFAULT '1',
						`giveme_flg` int(1) unsigned NOT NULL DEFAULT '0',
						`confirmed_flg` int(1) unsigned NOT NULL DEFAULT '0',
						`exhibiter_evaluated_flg` int(1) unsigned NOT NULL DEFAULT '0',
						`bidder_evaluated_flg` int(1) unsigned NOT NULL DEFAULT '0',
						`finished_flg` int(1) unsigned NOT NULL DEFAULT '0',
						PRIMARY KEY (`post_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;
						";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			
				$sql = "CREATE TABLE IF NOT EXISTS `" . $this->fmt_points . "` (
						`insert_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
						`update_timestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
						`user_id` bigint(20) unsigned NOT NULL,
						`got_points` int(8) unsigned NOT NULL DEFAULT '0',
						`temp_used_points` int(8) unsigned NOT NULL DEFAULT '0',
						`used_points` int(8) unsigned NOT NULL DEFAULT '0',
						PRIMARY KEY (`user_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;
						";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				$sql = "CREATE TABLE IF NOT EXISTS `" . $this->fmt_trade_history . "` (
						`insert_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
						`update_timestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
						`post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
						`bidder_id` bigint(20) unsigned NOT NULL DEFAULT '0',
						`bidder_score` int(1) unsigned NOT NULL DEFAULT '0',
						`bidder_comment` varchar(200) DEFAULT NULL,
						`exhibiter_id` bigint(20) unsigned NOT NULL DEFAULT '0',
						`exhibiter_score` int(1) unsigned NOT NULL DEFAULT '0',
						`exhibiter_comment` varchar(200) DEFAULT NULL,
						PRIMARY KEY (`post_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;
						";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				$sql = "CREATE TABLE IF NOT EXISTS `" . $this->fmt_user_giveme . "` (
						`insert_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
						`update_timestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
						`user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
						`post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
						`confirmed_flg` int(1) unsigned NOT NULL DEFAULT '0',
						PRIMARY KEY (`user_id`,`post_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;
						";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				$sql = "CREATE TABLE IF NOT EXISTS `" . $this->fmt_wanted_list . "` (
  						`insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  						`update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  						`wanted_item_id` int(8) NOT NULL AUTO_INCREMENT,
  						`user_id` bigint(20) NOT NULL,
  						`item_name` varchar(200) CHARACTER SET utf8 NOT NULL,
 						`ASIN` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  						`image_url` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  						PRIMARY KEY (`wanted_item_id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
						";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				$sql = "CREATE TABLE IF NOT EXISTS `". $this->fmt_todo ."` (
						`todo_id` int(11) NOT NULL AUTO_INCREMENT,
						`status` varchar(11) NOT NULL DEFAULT 'unfinished',
						`user_id` int(11) NOT NULL,
						`item_id` int(11) NOT NULL,
						`message` longtext NOT NULL,
						`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
						`modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
						PRIMARY KEY (`todo_id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
						";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				$sql = "CREATE TABLE IF NOT EXISTS `" . $this->fmt_trade_maps . "` (
						`insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
						`update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
						`map_id` int(8) NOT NULL AUTO_INCREMENT,
						`name` varchar(200) CHARACTER SET utf8 NOT NULL,
						`parent_id` int(8) NOT NULL DEFAULT '0',
						`latitude` double(17,14) NOT NULL DEFAULT '0.00000000000000',
						`longitude` double(17,14) NOT NULL DEFAULT '0.00000000000000',
						`display_order` int(8) NOT NULL,
						`default_flg` tinyint(1) NOT NULL,
						PRIMARY KEY (`map_id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
						";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				$sql = "CREATE TABLE IF NOT EXISTS `" . $this->fmt_book_fair .
				"` (
						`bookfair_id` int(8) NOT NULL AUTO_INCREMENT,
						`starting_time` datetime NOT NULL DEFAULT
						'0000-00-00 00:00:00',
						`ending_time` datetime NOT NULL DEFAULT
						'0000-00-00 00:00:00',
						`date` datetime NOT NULL DEFAULT
						'0000-00-00 00:00:00',
						`venue` varchar(50) NOT NULL,
						`classroom` varchar(50) NOT NULL,
						`insert_timestamp` timestamp NOT NULL DEFAULT
						'0000-00-00 00:00:00',
						`update_timestamp` timestamp NOT NULL DEFAULT
						'0000-00-00 00:00:00',
						PRIMARY KEY (`bookfair_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
						";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				$sql = "CREATE TABLE IF NOT EXISTS `" . $this->fmt_reservation .
				"`(
						`item_id` int(11) NOT NULL,
						`user_id` int(11) NOT NULL,
						`bookfair_id` int(8) NOT NULL,
						`insert_timestamp` timestamp NOT NULL DEFAULT
						'0000-00-00 00:00:00',
						`update_timestamp` timestamp NOT NULL DEFAULT
						'0000-00-00 00:00:00',
						`status` int(3) NOT NULL DEFAULT 0,
						PRIMARY KEY (`item_id`, `user_id`, `bookfair_id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
						";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				$sql = "CREATE TABLE IF NOT EXISTS `" . $this->fmt_reserve .
				"`(
						`reserve_id` int(8) NOT NULL AUTO_INCREMENT,
						`item_id` int(11) NOT NULL,
						`user_id` int(11) NOT NULL,
						`bookfair_id` int(8) NOT NULL,
						`insert_timestamp` timestamp NOT NULL DEFAULT
						'0000-00-00 00:00:00',
						`update_timestamp` timestamp NOT NULL DEFAULT
						'0000-00-00 00:00:00',
						PRIMARY KEY (`reserve_id`,`item_id`, `user_nicename`, `bookfair_id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
						";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				//register dbversion to option
				update_option('fmt_meta_version', $fmt_db_version);
			}
	}
}

$exmeta = new FreecycleMetaTable;
$exmeta->fmt_activate();
