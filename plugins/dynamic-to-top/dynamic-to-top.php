<?php
/*
Plugin Name: Dynamic To Top
Version: 3.4.2
Plugin URI: http://www.mattvarone.com/featured-content/dynamic-to-top/
Description: Adds an automatic and dynamic "To Top" button to scroll long pages back to the top.
Author: Matt Varone
Author URI: http://www.mattvarone.com

Copyright 2011-2012 ( email: contact@mattvarone.com )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
( at your option ) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
|--------------------------------------------------------------------------
| DYNAMIC TO TOP CONSTANTS
|--------------------------------------------------------------------------
*/

define( 'MV_DYNAMIC_TO_TOP_VERSION', '3.4.2' );

/*
|--------------------------------------------------------------------------
| DYNAMIC INITIALIZATION
|--------------------------------------------------------------------------
*/

/**
 * Plugins Loaded
 *
 * Launches on plugins_loaded. Loads internationalization,
 * requires the necessary files.
 *
 * @package  Dynamic To Top
 * @since    3.2
 * @return   void
*/

if ( ! function_exists( 'mv_dynamic_to_top_plugins_loaded' ) ) {
    function mv_dynamic_to_top_plugins_loaded() {

        // translation
        add_action( 'init', 'mv_dynamic_to_top_load_textdomain' );

        // require files
        if ( is_admin() )
            require_once( plugin_dir_path( __FILE__ ) . 'inc/dynamic-to-top-options.php' );
        else {
            if ( !class_exists('CssMin') ) require_once( plugin_dir_path( __FILE__ ) . 'inc/cssmin-v3.0.1.php' );
            require_once( plugin_dir_path( __FILE__ ) . 'inc/dynamic-to-top-class.php' );
        }
    }
}
add_action( 'plugins_loaded', 'mv_dynamic_to_top_plugins_loaded' );

/*
|--------------------------------------------------------------------------
| INTERNATIONALIZATION
|--------------------------------------------------------------------------
*/

/**
 * Load Textdomain
 *
 * @access      private
 * @since       3.3
 * @return      void
*/

if ( ! function_exists( 'mv_dynamic_to_top_load_textdomain' ) ) {
function mv_dynamic_to_top_load_textdomain() {
        // load textdomain
        load_plugin_textdomain( 'dynamic-to-top', false, dirname( plugin_basename( __FILE__ ) ) . '/lan' );
    }
}

/*
|--------------------------------------------------------------------------
| ON ACTIVATION
|--------------------------------------------------------------------------
*/

/**
 * Activation
 *
 * @package  Dynamic To Top
 * @since    3.1.5
 * @return   void
*/

if ( ! function_exists( 'mv_dynamic_to_top_activation' ) ) {
    function mv_dynamic_to_top_activation() {

        // check compatibility
        if ( version_compare( get_bloginfo( 'version' ), '3.3' ) >= 0 )
        deactivate_plugins( basename( __FILE__ ) );

        // refresh cache
        delete_transient( 'dynamic_to_top_transient_css' );

    }
}
register_activation_hook( __FILE__, 'mv_dynamic_to_top_activation' );
