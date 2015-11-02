<?php
$_tests_dir = getenv('WP_TESTS_DIR');
//if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';
require_once $_tests_dir . '/includes/functions.php';

// The BP_TESTS_DIR constant is a helpful shorthand for accessing assets later
// on. By defining in a constant, we allow for setups where the BuddyPress
// tests may be located in a non-standard location.
if (getenv( 'BP_TESTS_DIR' ) ) {
    define( 'BP_TESTS_DIR', getenv('BP_TESTS_DIR'));
}
echo WP_TESTS_DIR;
echo BP_TESTS_DIR;

// Checking for the existence of tests/bootstrap.php ensures that your version
// of BuddyPress supports this kind of automated testing
if ( file_exists( BP_TESTS_DIR . '/bootstrap.php' ) ) {
    // The functions.php file from the WP test suite needs to be defined early,
    // because it gives us access to the tests_add_filter() function
    require_once $_tests_dir . '/includes/functions.php';

    // Hooked to muplugins_loaded, this function is responsible for bootstrapping
    // BuddyPress, as well as your own plugin
    function _bootstrap_plugins() {
        // loader.php will ensure that BP gets installed at the right time, and
        // that BP is initialized before your own plugin
        require BP_TESTS_DIR . '/includes/loader.php';

        // Change this path to point to your plugin's loader file
        require __DIR__ . '/../freecycle.php';

    }
    tests_add_filter( 'muplugins_loaded', '_bootstrap_plugins' );

    // Start up the WP testing environment
    require $_tests_dir . '/includes/bootstrap.php';

    // Requiring this file gives you access to BP_UnitTestCase
    require BP_TESTS_DIR . '/includes/testcase.php';

    // Optional: If your plugin needs its own _UnitTestCase class, include it
    // here so that it's available when your testcases are loaded
    //require __DIR__ . '/bp-cli-testcase.php';
}