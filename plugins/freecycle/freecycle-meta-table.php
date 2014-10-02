<?php
/*
Plugin Name: Freecycle
Plugin URI: http://www.hoge.net/
Description: for TexChange core system
Author: Hashikuchi Kazunori
Version: 0.1
Author URI: http://moyasystemengineer.hatenablog.com/
*/

class FreecycleMetaTable {
	// plugin custom table names
	var $fmt_giveme_state;
	var $fmt_points;
	var $fmt_trade_history;
	var $fmt_user_giveme;
	var $fmt_wanted_list;
	var $fmt_todo;
	
	public function __construct(){
		global $wpdb;
		$this->fmt_giveme_state = $wpdb->prefix . 'fmt_giveme_state';
		$this->fmt_points = $wpdb->prefix . 'fmt_points';
		$this->fmt_trade_history = $wpdb->prefix . 'fmt_trade_history';
		$this->fmt_user_giveme = $wpdb->prefix . 'fmt_user_giveme';
		$this->fmt_wanted_list = $wpdb->prefix . 'fmt_wanted_list';
		$this->fmt_todo = $wpdb->prefix . 'todo';
		// activate when plugin is activated
		register_activation_hook(__FILE__, array($this, 'fmt_activate'));
	}
	
	
	function fmt_activate(){
		global $wpdb;
		//DB version
		$fmt_db_version = '1.03';
		//current DB version
		$installed_ver = get_option( 'fmt_meta_version' );
			// if versions are different tables are created
			if( $installed_ver != $fmt_db_version ) {
				$sql = "CREATE TABLE IF NOT EXISTS `" . $this->fmt_giveme_state . "` (
						`post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
						`entry_flg` int(1) unsigned NOT NULL DEFAULT '1',
						`giveme_flg` int(1) unsigned NOT NULL DEFAULT '0',
						`confirmed_flg` int(1) unsigned NOT NULL DEFAULT '0',
						`exhibiter_evaluated_flg` int(1) unsigned NOT NULL DEFAULT '0' COMMENT 'o•iŽÒ•]‰¿Ïƒtƒ‰ƒO',
						`bidder_evaluated_flg` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '—ŽŽDŽÒ•]‰¿Ïƒtƒ‰ƒO',
						`finished_flg` int(1) unsigned NOT NULL DEFAULT '0',
						PRIMARY KEY (`post_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;
						";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			
				$sql = "ALTER TABLE `" . $this->fmt_giveme_state . "` 
						ADD `insert_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP FIRST,
						ADD `update_timestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `insert_timestamp`;
						";
				$wpdb->query($sql);
				$sql = "UPDATE `" . $this->fmt_giveme_state ."`
						SET `insert_timestamp`= CURRENT_TIMESTAMP
						WHERE `insert_timestamp` = '0000-00-00 00:00:00'";
				$wpdb->query($sql);
				$sql = "UPDATE `" . $this->fmt_giveme_state ."`
						SET `update_timestamp`= CURRENT_TIMESTAMP
						WHERE `update_timestamp` = '0000-00-00 00:00:00'";
				$wpdb->query($sql);


				$sql = "CREATE TABLE IF NOT EXISTS `" . $this->fmt_points . "` (
						`user_id` bigint(20) unsigned NOT NULL COMMENT 'ƒ†[ƒUID',
						`got_points` int(8) unsigned NOT NULL DEFAULT '0' COMMENT 'Šl“¾ƒ|ƒCƒ“ƒg',
						`temp_used_points` int(8) unsigned NOT NULL DEFAULT '0',
						`used_points` int(8) unsigned NOT NULL DEFAULT '0' COMMENT 'Žg—pÏƒ|ƒCƒ“ƒg',
						PRIMARY KEY (`user_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;
						";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				$sql = "ALTER TABLE `" . $this->fmt_points . "` 
						ADD `insert_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP FIRST,
						ADD `update_timestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `insert_timestamp`;
						";
				$wpdb->query($sql);
				$sql = "UPDATE `" . $this->fmt_points ."`
						SET `insert_timestamp`= CURRENT_TIMESTAMP
						WHERE `insert_timestamp` = '0000-00-00 00:00:00'";
				$wpdb->query($sql);
				$sql = "UPDATE `" . $this->fmt_points ."`
						SET `update_timestamp`= CURRENT_TIMESTAMP
						WHERE `update_timestamp` = '0000-00-00 00:00:00'";
				$wpdb->query($sql);

				$sql = "CREATE TABLE IF NOT EXISTS `" . $this->fmt_trade_history . "` (
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

				$sql = "ALTER TABLE `" . $this->fmt_trade_history . "` 
						ADD `insert_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP FIRST,
						ADD `update_timestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `insert_timestamp`;
						";
				$wpdb->query($sql);
				$sql = "UPDATE `" . $this->fmt_trade_history ."`
						SET `insert_timestamp`= CURRENT_TIMESTAMP
						WHERE `insert_timestamp` = '0000-00-00 00:00:00'";
				$wpdb->query($sql);
				$sql = "UPDATE `" . $this->fmt_trade_history ."`
						SET `update_timestamp`= CURRENT_TIMESTAMP
						WHERE `update_timestamp` = '0000-00-00 00:00:00'";
				$wpdb->query($sql);

				$sql = "CREATE TABLE IF NOT EXISTS `" . $this->fmt_user_giveme . "` (
						`user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
						`post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
						`confirmed_flg` int(1) unsigned NOT NULL DEFAULT '0',
						PRIMARY KEY (`user_id`,`post_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;
						";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				$sql = "ALTER TABLE `" . $this->fmt_user_giveme . "` 
						ADD `insert_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP FIRST,
						ADD `update_timestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `insert_timestamp`;
						";
				$wpdb->query($sql);
				$sql = "UPDATE `" . $this->fmt_user_giveme ."`
						SET `insert_timestamp`= CURRENT_TIMESTAMP
						WHERE `insert_timestamp` = '0000-00-00 00:00:00'";
				$wpdb->query($sql);
				$sql = "UPDATE `" . $this->fmt_user_giveme ."`
						SET `update_timestamp`= CURRENT_TIMESTAMP
						WHERE `update_timestamp` = '0000-00-00 00:00:00'";
				$wpdb->query($sql);

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

				//register dbversion to option
				update_option('fmt_meta_version', $fmt_db_version);
			}
	}
}

$exmeta = new FreecycleMetaTable;
