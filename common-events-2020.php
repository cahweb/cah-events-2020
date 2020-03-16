<?
/*
    Plugin Name: Common - Events 2020
    Description: Displays CAH events.
    Version:     0
    Author:      Rachel Tran
    License:     GPL2
    License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Where the shortcode is contained and where the other functions are called.
include "functions/events_shortcode.php";

// Contains PHP specific functions mostly used to index events.
include "functions/events_php-functions.php";

// Actual rendering of the plugin.
include "functions/events_html-vuejs.php";

// Developer functions for testing and debugging.
// NOTE: This is active wherever the plugin is used.
include "functions/dev.php";

// Custom styles.
wp_enqueue_style('events-styles', plugin_dir_url( __FILE__ ) . '/styles/events.css');

?>