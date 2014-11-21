<?php
/*
Plugin Name: Freecycle
Plugin URI: http://www.hoge.net/
Description: for TexChange core system
Author: Hashikuchi Kazunori
Version: 0.1
Author URI: http://moyasystemengineer.hatenablog.com/
*/

// Don't allow plugin to be loaded directory
defined( 'ABSPATH' ) OR exit;

// Add action after plugins are loaded.
add_action( 'plugins_loaded', '_freecycle_setup_after_plugins_loaded');


/**
 * Instanciate plugin
 * 
 * @global WP_Gianism $gianism
 */
function _freecycle_setup_after_plugins_loaded(){
	// DB settings
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."freecycle-meta-table.php";

	// Load global functions
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."functions.php";
	
}
