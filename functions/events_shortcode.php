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
    $filter = '';
    $filter_format = '';
    $show_more_format = '';
    $hide_recurrence = false;
    $num_events_to_show = 5;
    $front = false;
    $show_all_when_none = false;
    $arts_filter = '';
    $arts_show_all = false;

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
    if ($atts['arts-show-all'] && strtolower($atts['arts-show-all']) === "true") {
        $arts_show_all = true;
    }

    // For enabling and disabling dev features and Vuejs modes.
    // $dev = false;
    $dev = $atts['dev'];
    // Convert the string: "false", to a boolean.
    if ($dev === "false") {
        $dev = false;
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
    
    ob_start();

    render_events($filter, $filter_format, $show_more_format, $hide_recurrence, $num_events_to_show, $dev, $front, $show_all_when_none);

    return ob_get_clean();
}

?>