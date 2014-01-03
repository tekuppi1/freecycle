<?php
/**
 Plugin Name: WPtap Mobile Detector
 Plugin URI: http://www.wptap.com/index.php/plugin/
 Description: This plugin automatically detects the type of mobile browser that you site is viewed from, and activates the mobile theme you have chosen for it. User can install multiple mobile themes and link it to different mobile browsers for best performance. If you have a separate WAP or mobile website, this detector also allows you to redirect your mobile traffic to the WAP/mobile site.

 Version: 1.1
 Author: WPtap Development Team
 Author URI: http://www.wptap.com/index.php
*/
define('TABLE_MOBILES', $table_prefix.'md_mobiles');
define('TABLE_MOBILEMETA', $table_prefix.'md_mobilemeta');

require(dirname(__FILE__) . '/md-includes/function.php');

$pluginversion = md_pluginversion();
$pluginname = md_pluginname();
$mobile_current_template = mobileDetect();

// Activation of plugin
if(function_exists('register_activation_hook')) {
	register_activation_hook( __FILE__, 'md_install' );
}

// Uninstallation of plugin
if(function_exists('register_uninstall_hook')) {
	register_uninstall_hook(__FILE__, 'md_uninstall');
}

if(is_admin()) {
	require(dirname(__FILE__) . '/md-admin/function.php');

	add_action('admin_menu', 'md_option_menu');
}


if($mobile_current_template) {
	add_filter('stylesheet', 'mobileDetect');
	add_filter('template', 'mobileDetect');
}
?>