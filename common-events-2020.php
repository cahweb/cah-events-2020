<?
/*
    Plugin Name: Common - Events 2020
    Description: Displays CAH events.
    Version:     0
    Author:      Rachel Tran
    License:     GPL2
    License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

wp_enqueue_style('events-styles', get_stylesheet_directory_uri() . '/styles/events.css');

// Essentially the function's main, except this is the actual main.
include "functions/events.php";

// Helper functions for events.php; esp. for printing.
include "functions/events_print.php";
// Helper functions for events.php; esp. for filtering.
include "functions/events_filter.php";
// Helper functions for events.php; esp. for generating pagination and links.
include "functions/events_pagination.php";

// // Developer functions for testing and debugging. Remove in production.
// include "functions/dev.php";

?>