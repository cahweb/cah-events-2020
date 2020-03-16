<?

/*
    -----------------------------
        Shortcode Declaration
    -----------------------------

    Acts like the "main" function for this plug in.

    Current available options:
    --------------------------
    filter-format:
        - 'dropdown'
        - 'list'
        - 'none' (default)

    show-more-format:
        - 'paged'
        - 'button' or 'btn'
        - 'none' (default)
    
    front:
        - true
        - false
*/

add_shortcode('events', 'events_handler');

function events_handler($atts = []) {
    $attributes = shortcode_atts([
        'filter' => 'all',
        'filter-format' => '',
        'show-more-format' => '',
        'hide-recurrence' => false,
        'num-events' => 5,
        'front' => false,
    ], $atts);

    $filter = $atts['filter'];
    $filter_format = $atts['filter-format'];
    $show_more_format = $atts['show-more-format'];
    $hide_recurrence = $atts['hide-recurrence'];
    $num_events_to_show = $atts['num-events'];
    $front = $atts['front'];

    // For enabling and disabling dev features and Vuejs modes.
    $dev = false;
    
    if ($dev) {
        // Set dev attributes manually.
        $filter = $atts['filter'];
        // $filter_format = 'dropdown';
        $show_more_format = 'btn';
        // $hide_recurrence = false;
        $num_events_to_show = 3;
        $front = true;

        dev_cont(array(
            dev_cont_h("Shortcode Attributes"),
            tsh("Filter", $filter),
            tsh("Filter format", $filter_format),
            tsh("Show more format", $show_more_format),
            tsh("Hide recurrence", $hide_recurrence),
            tsh("Number of events to show", $num_events_to_show),
            tsh("Front", $front),
        ));
    }

    // Takes into account that an empty string defaults to "all" for $filter.
    if (str_replace(' ', '', $filter) === "") {
        $filter = "all";
    }

    // Needed for $hide_recurrence to be processed correctly in JavaScript. PHP renders the false as an empty string otherwise.
    if ($hide_recurrence === false) {
        $hide_recurrence = "false";
    }

    ob_start();

    render_events($filter, $filter_format, $show_more_format, $hide_recurrence, $num_events_to_show, $dev, $front);

    return ob_get_clean();
}

?>