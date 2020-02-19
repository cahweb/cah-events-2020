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
        'btn-format' => '',
        'format' => 0,
        'filter-format' => '',
        'btn-format' => '',
        'num-events' => 5,
    ], $atts);

    $hide_recurrence = $attributes['hide-recurrence'];
    if ($hide_recurrence == "false") {
        $hide_recurrence = "";
    }
    $GLOBALS['hide_recurrence'] = $hide_recurrence;

    $filter = $attributes['filter'];
    $GLOBALS['filter'] = strtolower($filter);
    
    $format = $attributes['format'];

    $num_events_to_show = $attributes['num-events'];

    $filter_format = strtolower($attributes['filter-format']);

    $btn_format = strtolower($attributes['btn-format']);

    // Allows changes to dev site without affecting other live sites.
    $dev = strtolower($attributes['dev']);
    $GLOBALS['dev'] = $dev;

    // Flag for no events in a month.
    // !WARNING: Not sure if this is needed, it's not in global scope.
    global $isEmpty;
    $isEmpty = FALSE;

    // Changes layout styles depending on which filter_format is chosen.
    if ($filter_format == "list") {
        $styles = "";
        $container_classes = "row";
        $filter_classes = "col-sm-3 my-3";
        $events_classes = " col-sm-9";
    } else {
        $styles = "";
        $container_classes = "mx-auto";
        $filter_classes = "col-sm-5 mb-5 mx-auto";
        $events_classes = "";

        if ($filter_format == "none") {
            $filter_format = "";
        }
    }

    ob_start();

    // "front" format.
    if ($filter_format == "front") {
        // To access background image.
        // Had to do it this way because I didn't want to redo the CSS file as a PHP.
        $bg_img = plugin_dir_url(__DIR__, 1) . "imgs/knight.jpg";

        ?> 
            <div class="py-5 pl-5 pr-4" style="background-image: url('<?= $bg_img ?>'); background-repeat: no-repeat; background-size: cover;">
                <div class="container">
                    <h1 class="text-inverse mb-4">Events</h1>

                    <div class="d-flex flex-column">
                        <div class="mx-auto">
                            <? // Events ?>
                            <section class="mt-0 col-lg-9 p-0">
                                <ul class="list-unstyled">
                                    <?
                                        print_handler($filter, $num_events_to_show);
                                    ?>
                                </ul>
                            </section>
                        </div>
                    </div>

                    <a href="https://events.ucf.edu/calendar/3611/cah-events/upcoming/" class="btn btn-primary mt-3">More Events</a>
                </div>
            </div>
        <?
    } else {
        ?>
            <div class="d-flex flex-column">
                <div class="<?= $container_classes ?>">
                    <?
                        // Filters
                        if ($filter_format !== "") {
                    ?>
                        <section class="<?= $filter_classes ?>">
                            <?
                                filter_handler($filter_format)
                            ?>
                        </section>
                    <? } ?>

                    <? // Events ?>
                    <section class="mt-0<?= $events_classes ?>">
                        <ul class="list-unstyled">
                            <?
                                print_handler($filter, $num_events_to_show);
                            ?>
                        </ul>

                        <?
                            // Button format
                            if ($btn_format !== "" || $btn_format !== "none") {
                                if ($btn_format == "paged" || $btn_format == "pagination") {
                                    events_pagination($num_events_to_show);
                                } else if ($btn_format == "showmore" || $btn_format == "show-more") {
                                    ?>
                                        <a href="https://events.ucf.edu/calendar/3611/cah-events/upcoming/" class="btn btn-primary mt-3">More Events</a>
                                    <?
                                }
                            }
                        ?>
                    </section>
                </div>
            </div>
        <?
    }
    return ob_get_clean();
}

// Function to index all events into an array for pagnination. This indexing function can possibly be merged with total_number_of_months();
function index_events() {
    $events = array();

    // These end up as identical DateTime objects. Why not just use one? - M.L. (31 JAN 2020 13:47)
    $current_year = date_create('Y');
    $current_month = date_create('m');

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
            
            // On days past 30 January, this was breaking and not showing February's events.
            // It seems the "+1 month" interval (I tried both with DateTime::modify() and 
            // with DateTime::add()) tries to give you the same date in the following month,
            // but will just roll over into the next month if you ask for a date beyond that
            // one. I tested it with 31 AUG +1 month, as well, and it gave me 1 OCT, so it's
            // not just February that's weird, in this particular use case.
            //
            // I figured the easiest fix would be to just increment the month and create a
            // new DateTime object, but you may be able to think of a less clunky solution.
            //      - M.L. (31 JAN 2020 13:56)

            // This just for readability's sake
            $Y = date_format( $current_month, 'Y' );
            $m = date_format( $current_month, 'm' ) + 1;
            $d = date_format( $current_month, 'd' ) >= 28 ? 28 : date_format( $current_month, 'd' );

            $current_month = date_create_from_format( 'Y-m-d', "$Y-$m-$d" );
        }

        // Not DRY, I know.
        $events_json_contents = json_decode(file_get_contents($path . date_format($current_year, 'Y') . "/" . date_format($current_month, 'n') . "/" . "feed.json"));

        foreach ($events_json_contents as $event) {
            // The date/time when each event ends.
            $end = strtotime($event->ends);
            
            // Ensures that the events are active or upcoming:
            if ($end >= time()) {
                array_push($events, $event);
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

    // For ease of typing.
    $activeCat = $GLOBALS['activeCat'];

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
    } else {
        foreach ($original_events_array as $event) {
            // The actual tag from the JSON file.
            $category = parse_event_category($event->tags);

            // Converts start and ending date and times to datetime format for easier parsing.
            $event->starts = date_create($event->starts);
            $event->ends = date_create($event->ends);

            
            // If no specific filter is given, just pushes same object but with converted dates to new array.
            if ($activeCat == "All" || $activeCat == "") {
                array_push($parsed_events_array, $event);
            } else if (strpos($activeCat, $category) !== false) {
                // Pushes each event into an array depending on which category is currently active.
                array_push($parsed_events_array, $event);
            }
        }
    }

    return $parsed_events_array;
}

?>