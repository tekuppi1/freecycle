<?php
if(!function_exists('md_install')){
	/**
	 * Initialize the plugin.
	 *
	 * @since 1.0
	 */
	function md_install()
	{
		global $table_prefix, $wpdb;

		$table_mobiles = $table_prefix."md_mobiles";

		$table_mobilemeta = $table_prefix."md_mobilemeta";

		$sql1 = "

		CREATE TABLE IF NOT EXISTS `$table_mobiles` (
		  `mobile_id` int(11) NOT NULL auto_increment,
		  `mobile_name` varchar(255) NOT NULL default '',
		  `mobile_agent` varchar(255) NOT NULL default '',
		  `is_system_mobile` tinyint(1) NOT NULL default '0',
		  PRIMARY KEY  (mobile_id)
		);";

		$sql2 = "

		CREATE TABLE IF NOT EXISTS `$table_mobilemeta` (
		  `mobile_id` int(11) NOT NULL,
		  `theme_template` varchar(255) NOT NULL default '',
		  `redirect` varchar(255) NULL,
		  PRIMARY KEY  (mobile_id)
		);";

		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

		dbDelta($sql1);

		dbDelta($sql2);

		$insert_mobile = "INSERT INTO `$table_mobiles` (`mobile_name`, `mobile_agent`, `is_system_mobile`)VALUES
		('iPhone/iPod', 'iPhone|iPod|aspen|webmate', 1),
		('Android','android|dream|cupcake', 1),
		('BlackBerry Storm','blackberry9500|blackberry9530', 1),
		('Nokia','series60|series40|nokia|Nokia', 1),
		('Apple iPad', 'ipad|iPad', 0),	
		('Opera','opera mini|Opera',0),
		('Palm','pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine',0),
		('Windows Smartphone','iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile',0),
		('Blackberry','blackberry|Blackberry',0)";
		
		if(!$wpdb->get_results("SELECT * FROM `$table_mobiles`"))
			$wpdb->query($insert_mobile);
	}
}

if(!function_exists('md_uninstall')) {
	/**
	 * Uninstallation of plugin.
	 *
	 * @since 1.0
	 */
	function md_uninstall()
	{
		global $table_prefix, $wpdb;

		$table_mobiles = $table_prefix."md_mobiles";
		$table_mobilemeta = $table_prefix."md_mobilemeta";

		$sql1 = "DROP TABLE `$table_mobiles`";
		$sql2 = "DROP TABLE `$table_mobilemeta`";

		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

		dbDelta($sql1);
		dbDelta($sql2);
	}
}

if(!function_exists('md_pluginversion')) {
	/**
	 * The plugin version.
	 *
	 * @since 1.0
	 * @return string Plugin version
	 */
	function md_pluginversion(){
		$md_plugin_data = implode('', file(dirname(dirname(__FILE__)).'/mobiledetector.php'));
		if (preg_match("|Version:(.*)|i", $md_plugin_data, $version)) {
				$version = $version[1];
		}
		return $version;
	}
}

if(!function_exists('md_pluginname')) {
	/**
	 * The plugin name.
	 *
	 * @since 1.0
	 * @return string Plugin name
	 */
	function md_pluginname()
	{
		$md_plugin_data = implode('', file(dirname(dirname(__FILE__)).'/mobiledetector.php'));
		if (preg_match("|Plugin\sName:(.*)|i", $md_plugin_data, $pluginname)) {
				$pluginname = $pluginname[1];
		}
		return $pluginname;
	}
}

function md_get_mobiles(){
	global $wpdb;

	$sql = "SELECT * FROM `".TABLE_MOBILES."`";

	return $wpdb->get_results($sql);
}

function md_user_agent()
{
	global $wpdb;
	$user_agents = array();

	$sql = "SELECT `mobile_id`,`mobile_agent` FROM `".TABLE_MOBILES."`";
	$mobiles = $wpdb->get_results($sql);

	foreach($mobiles as $mobile) {
		$user_agents[$mobile->mobile_id] = $mobile->mobile_agent;
	}
	return $user_agents;
}

if(!function_exists('get_mobile_themes')) {
	/**
	 * Retrieve list of mobile themes with theme data in theme directory.
	 *
	 * @since 1.0
	 * @global array $wptap_mobile_themes Stores the working mobile themes.
	 *
	 * @return array Mobile Theme list with theme data.
	 */

	 function get_mobile_themes()
	 {
		if(!function_exists('get_themes'))
			return null;

		return $wp_themes = get_themes();

		/*foreach($wp_themes as $name=>$theme) {
			$stylish_dir = $wp_themes[$name]['Stylesheet Dir'];

			if(is_file($stylish_dir.'/style.css')) {
				$theme_data = get_theme_data($stylish_dir.'/style.css');
				$tags = $theme_data['Tags'];

				foreach($tags as $tag) {
					if(eregi('Mobile Theme', $tag) || eregi('WPtap', $tag)) {
						$wptap_mobile_themes[$name] = $theme;
						break;
					}
				}
			}
		}

		return $wptap_mobile_themes;*/
	 }
}


/**
 * Detect user agent.
 *
 * @since 1.0
 */
function mobileDetect()
{
	global $wpdb;

	$container = $_SERVER['HTTP_USER_AGENT'];
	$useragents = md_user_agent();
	$mobile_current_id = null;

	foreach ($useragents as $mobile_id => $useragent) {
		$useragent = explode('|', $useragent);
		
		foreach($useragent as $agent) {
			if (eregi($agent, $container)) {
				$mobile_current_id = $mobile_id;
				break;
			}
		}
	}

	$mobilemeta = $wpdb->get_row("SELECT `theme_template`,`redirect` FROM `".TABLE_MOBILEMETA."` WHERE `mobile_id`=$mobile_current_id");

	if($mobilemeta->redirect) {
		header("Location: $mobilemeta->redirect");
		exit;
	}

	return $mobilemeta->theme_template;
}
?>