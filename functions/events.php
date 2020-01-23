<?
/*
    Upcoming Events

    TODO: Add shortcode to toggle pagination. Also for format 2.
    TODO: Add shortcode to toggle filters dropdown.
    TODO: Think of a more elegant way to accomplish this.
*/

// Shortcode used in Wordpress is the first parameter.
add_shortcode('events', 'events_handler');

// Sets timezone to EST.
date_default_timezone_set("America/New_York");

global $events;
global $num_total_events;
global $num_of_pages;
global $hide_recurrence;
global $dev;
global $filter;

function events_handler($atts = []) {
    // Attributes given in the shortcode call in Wordpress
    $attributes = shortcode_atts([
        'dev' => false,
        'hide-recurrence' => false,
        'filter' => '',
        'format' => 0,
        'num-events' => 5,
    ], $atts);

    $hide_recurrence = $attributes['hide-recurrence'];
    $GLOBALS['hide_recurrence'] = $hide_recurrence;
    $filter = $attributes['filter'];
    $GLOBALS['filter'] = strtolower($filter);
    $format = $attributes['format'];
    $num_events_to_show = $attributes['num-events'];

    // // Allows changes to dev site without affecting other live sites.
    // $GLOBALS['dev'] = $attributes['dev'];

    // Flag for no events in a month.
    // !WARNING: Not sure if this is needed, it's not in global scope.
    global $isEmpty;
    $isEmpty = FALSE;

    /*
        Format is given by the Wordpress shortcode attribute "format".

        0 - (Default) Item list side bar
        1 - Drop down menu
        2 - No filter shown
        3 - With custom background for front page, with 'more events' button.

        NOTE: Could probably merge this with all of the event child functions, but that's for future you or me. DRY means nothing to me lol.
    */
    switch ($format) {
        case 1:
            ob_start();
            ?>
            <div class="d-flex flex-column">
                <div class="mx-auto">
                    <? // Filters ?>
                    <section class="col-sm-5 mb-5 mx-auto">
                        <?
                            filter_handler($format)
                        ?>
                    </section>

                    <? // Events ?>
                    <section class="mt-0">
                        <ul class="list-unstyled">
                            <?
                                print_handler($format, $filter, $num_events_to_show);
                            ?>
                        </ul>

                        <?
                            events_pagination($num_events_to_show);
                        ?>
                    </section>
                </div>
            </div>
            <?
            return ob_get_clean();
        case 2:
            ob_start();
            ?>
            <div class="d-flex flex-column">
                <div class="mx-auto">
                    <? // Events ?>
                    <section class="mt-0">
                        <ul class="list-unstyled">
                            <?
                                print_handler($format, $filter, $num_events_to_show);
                            ?>
                        </ul>
                    </section>
                </div>
            </div>
            <?
            return ob_get_clean();
        case 3:
            // To access background image.
            // Had to do it this way because I didn't want to redo the CSS file as a PHP.
            $bg_img = plugin_dir_url(__DIR__, 1) . "imgs/knight.jpg";

            ob_start();
            ?>
            <div class="p-5" style="background-image: url('<?= $bg_img ?>'); background-repeat: no-repeat; background-size: cover;">
                <div class="container">
                    <h1 class="text-inverse mb-4">Events</h1>

                    <div class="d-flex flex-column">
                        <div class="mx-auto">
                            <? // Events ?>
                            <section class="mt-0 col-9">
                                <ul class="list-unstyled">
                                    <?
                                        print_handler($format, $filter, $num_events_to_show);
                                    ?>
                                </ul>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
            <?
            return ob_get_clean();
        default:
            ob_start();
            ?>
            <div class="row">
                <? // Filters ?>
                <section class="col-sm-3 my-3">
                    <?
                        filter_handler($format)
                    ?>
                </section>

                <? // Events ?>
                <section class="col-sm-9 mt-0">
                    <ul class="list-unstyled">
                        <?
                            print_handler($format, $filter, $num_events_to_show);
                        ?>
                    </ul>

                    <?
                        events_pagination($num_events_to_show);
                    ?>
                </section>
            </div>
            <?
            return ob_get_clean();
    }
}

// Parses each category from event tags for each event.
function parse_event_category($tags) {
    $categories = array("Gallery", "Music", "SVAD", "Theatre");

    if (strtolower($tags[0]) == "music") {
        return "$categories[1]";
    } else if (strtolower($tags[0]) == "theatre ucf") {
        return $categories[3];
    } else {
        // Checks for "art gallery" or any tags containing that string.
        $gallery = false;

        // Same thing, but for SVAD.
        $svad = false;
        
        // If statement only needed to remove warning about providing an invalid input, since PHP wants you to check for empty arrays before looping them.
        if (!empty($tags)) {
            foreach ($tags as $tag1) {
                // Normalize the tag to lowercase.
                $tag = strtolower($tag1);

                if (strpos($tag, "art gallery") !== false) {
                    $gallery = true;
                }

                if (strpos($tag, "svad") !== false || strpos($tag, "visual arts")) {
                    $svad = true;
                }
            }
        }

        if ($gallery === true && $svad === true) {
            return $categories[0] . ", " . $categories[2];
        } else if ($gallery === true) {
            return $categories[0];
        } else if ($svad === true) {
            return $categories[2];
        }
    }
}

// Indexes all events into an array for pagnination. This indexing function can possibly be merged with total_number_of_months();
// TODO: Add consideration for current active category.
function index_events() {
    $events = array();

    $current_year = date_create('Y');
    $current_month = date_create('m');

    // For ease of typing.
    $activeCat = $GLOBALS['activeCat'];

    // Tracks if this is the initial loop where date looping would not apply.
    $i = 0;

    $path = "https://events.ucf.edu/calendar/4310/arts-at-ucf/";
    
    // Initializes the conditional below. It's repeated again to output the correct path.
    $events_json_contents = json_decode(file_get_contents($path . date_format($current_year, 'Y') . "/" . date_format($current_month, 'n') . "/" . "feed.json"));

    while (!empty($events_json_contents)) {
        // Loop around to next year if the current month is December and the loop as already gone through once.
        if ($i > 0) {
            if (date_format($current_month, 'n') == 12) {
                $current_year->modify("+1 year");
            }
            
            $current_month->modify("+1 month");
        }

        // Not DRY, I know.
        $events_json_contents = json_decode(file_get_contents($path . date_format($current_year, 'Y') . "/" . date_format($current_month, 'n') . "/" . "feed.json"));

        foreach ($events_json_contents as $event) {
            // The date/time when each event ends.
            $end = strtotime($event->ends);

            // The actual tag from the JSON file.
            $category = strtolower(parse_event_category($event->tags));
            
            // Ensures that the events are active or upcoming:
            if ($end >= time()) {
                // Pushes each event into an array depending on which category is currently active.
                // Added comparison to empty string for format 2 for filters.
                if ($activeCat == "All" || $activeCat == "") {
                    if ($i > 0 && $hide_recurrence && $previous_id !== $event->event_id) {
                    } else {
                        $previous_id = $events->event_id;
                        $event->parsed_category = $category;
                        array_push($events, $event);
                    }
                } else if (strpos($activeCat, $category) !== FALSE) {
                    $event->parsed_category = $category;
                    array_push($events, $event);
                }
            }
        }

        $i++;
    }

    return $events;
}

// Checks for recurrences in events if option is activated and returns the parsed array of events.
function parsed_events_index() {
    $original_events_array = index_events();
    $num_of_events = count($original_events_array);
    $parsed_events_array = array();

    if ($GLOBALS['hide_recurrence']) {
        // To keep track of the previous event id in the array.
        $previous_event_id = 0;
        $day_range = 0;

        for ($i = 0; $i < $num_of_events; $i++) {
            // Converts start and ending date and times to datetime format for easier parsing.
            $original_events_array[$i]->starts = date_create($original_events_array[$i]->starts);
            $original_events_array[$i]->ends = date_create($original_events_array[$i]->ends);

            if ($GLOBALS['filter'] == $original_events_array[$i]->parsed_category || $GLOBALS['filter'] == "all") {
                if ($i === 0) {
                    array_push($parsed_events_array, $original_events_array[$i]);
    
                    $previous_event_id = $original_events_array[$i]->event_id;
                } else {
                    $current_event_id = $original_events_array[$i]->event_id;
                        
                    if ($previous_event_id !== $current_event_id) {
                        if ($day_range > 0) {
                            $last_parsed = count($parsed_events_array) - 1;
        
                            $parsed_events_array[$last_parsed]->day_range = $day_range;
    
                            $day_range = 0;
                        }
    
                        $original_events_array[$i]->day_range = 0;
                        
                        array_push($parsed_events_array, $original_events_array[$i]);
                            
                        $previous_event_id = $current_event_id;
                    } else {
                        $day_range++;
                        }
                }
            }
        }
    
        return $parsed_events_array;
    } else {
        return $original_events_array;
    }
}

?>