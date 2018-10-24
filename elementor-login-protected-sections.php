<?php
/**
 * Created by PhpStorm.
 * User: Matteo
 * Date: 23/10/2018
 * Time: 16:42
 */

/*
Plugin Name:  Elementor Login Protected Sections
Plugin URI:   https://github.com/teolaz/elementor-login-protected-sections
Description:  This plugin allows to have a protection layer in Elementor content and show it only if a visitor is logged in or logged out from your WP site.
Version:      1.0.0
Author:       Matteo Lazzarin
Author URI:   https://github.com/teolaz/
License:      GPL3
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
Text Domain:  teolaz-elps
Domain Path:  /languages
*/

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

use Teolaz\ELPS\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* load textdomain and init plugin */
add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'teolaz-elps' );
	new Plugin();
} );



