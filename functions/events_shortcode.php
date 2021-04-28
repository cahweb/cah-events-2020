<?

/*
    -----------------------------
        Shortcode Declaration
    -----------------------------

    Acts like the "main" function for this plug in.
    See the README for available options.
*/

add_shortcode('events', 'events_handler');

function events_handler($atts = []) {
    $attributes = shortcode_atts([
        'dev' => 'false',
        'filter' => 'all',
        'filter-format' => '',
        'show-more-format' => '',
        'hide-recurrence' => false,
        'num-events' => 5,
        'front' => false,
        'show-all-when-none' => false,
        'arts-filter' => '',
    ], $atts);

    $filter = '';
    $filter_format = '';
    $show_more_format = '';
    $hide_recurrence = false;
    $num_events_to_show = 5;
    $front = false;
    $show_all_when_none = false;
    $arts_filter = '';

    // Really janky way to enforce default values.
    if ($atts['filter']) {
        $filter = strtolower($atts['filter']);
    }
    if ($atts['filter-format']) {
        $filter_format = strtolower($atts['filter-format']);
    }
    if ($atts['show-more-format']) {
        $show_more_format = strtolower($atts['show-more-format']);
    }
    if ($atts['hide-recurrence'] && strtolower($atts['hide-recurrence']) === "true") {
        $hide_recurrence = strtolower($atts['hide-recurrence']);
    }
    if ($atts['num-events']) {
        $num_events_to_show = strtolower($atts['num-events']);
    }
    if ($atts['front'] && strtolower($atts['front']) === "true") {
        $filter_format = strtolower($atts['filter-format']);
    }
    if ($atts['show-all-when-none'] && strtolower($atts['show-all-when-none']) === "true") {
        $show_all_when_none = strtolower($atts['show-all-when-none']);
    }
    if ($atts['arts-filter']) {
        $arts_filter = strtolower($atts['arts-filter']);
    }

    // For enabling and disabling dev features and Vuejs modes.
    // $dev = false;
    $dev = $atts['dev'];
    // Convert the string: "false", to a boolean.
    if ($dev === "false") {
        $dev = false;
    }
    
    if ($dev) {
        // // Set dev attributes manually.
        // $filter = $atts['filter'];
        // $filter_format = 'dropdown';
        // $show_more_format = 'btn';
        // $hide_recurrence = false;
        // $num_events_to_show = 3;
        // $front = false;

        if ($dev && false) {
            dev_cont(array(
                dev_cont_h("(BEFORE) Shortcode Attributes"),
                tsh("Filter", $filter),
                tsh("Filter format", $filter_format),
                tsh("Show more format", $show_more_format),
                tsh("Hide recurrence", $hide_recurrence),
                tsh("Number of events to show", $num_events_to_show),
                tsh("Front", $front),
            ));
        }
    }

    // Takes into account that an empty string defaults to "all" for $filter.
    if (str_replace(' ', '', $filter) === "") {
        $filter = "all";
    }

    // Needed for $hide_recurrence to be processed correctly in JavaScript. PHP renders the false as an empty string otherwise.
    if ($hide_recurrence === false) {
        $hide_recurrence = "false";
    }

    // If the format is for the CAH front page.
    if ($front) {
        $filter_format = "";
        $show_more_format = "";
    } else {
        // Needed to be processed correctly in JavaScript. PHP renders the false as an empty string otherwise.
        $front = "false";
    }

    // If true, then show all CAH events when current filter has none.
    if ($show_all_when_none) {
        $filter_format = "";
        $show_more_format = "";
    } else {
        $show_all_when_none = false;
    }

    if ($dev && false) {
        dev_cont(array(
            dev_cont_h("(AFTER) Shortcode Attributes"),
            tsh("Filter", $filter),
            tsh("Filter format", $filter_format),
            tsh("Show more format", $show_more_format),
            tsh("Hide recurrence", $hide_recurrence),
            tsh("Number of events to show", $num_events_to_show),
            tsh("Front", $front),
        ));
    }

    if ($filter === "arts") {
        if (empty($atts['num-events']) || $num_events_to_show == "all") {
            $num_events_to_show = -1;
        }

        ob_start();
        
        handle_arts_events($show_more_format, $num_events_to_show, $arts_filter);

        return ob_get_clean();
    } else {
        ob_start();
    
        render_events($filter, $filter_format, $show_more_format, $hide_recurrence, $num_events_to_show, $dev, $front, $show_all_when_none);
    
        return ob_get_clean();
    }
}

?>