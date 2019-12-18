<?
/*
    Upcoming Events
*/

// Shortcode used in Wordpress is the first parameter.
add_shortcode("events", "events_handler");

// Sets timezone to EST.
date_default_timezone_set("America/New_York");

global $events;
global $num_total_events;
global $num_of_pages;

function events_handler($atts = [], $content = null) {
    // Attributes given in the shortcode call in Wordpress
    $attributes = shortcode_atts([
        "number-of-events-to-show-per-page" => 5,
        "format" => 0,
    ], $atts);

    $num_events_to_show = $attributes["number-of-events-to-show-per-page"];
    // $format = $attributes["format"];
    $format = 0; // for dev purposes only

    // Flag for no events in a month.
    // !WARNING: Not sure if this is needed, it's not in global scope.
    global $isEmpty;
    $isEmpty = FALSE;

    /*
        Format is given by the Wordpress shortcode attribute "format".

        0 - (Default) Item list side bar
        1 - Drop down menu

        NOTE: Could probably merge this with all of the event child functions, but that's for future you or me. DRY means nothing to me lol.
    */
    switch ($format) {
        // TODO: Edit case 1 to match default.
        case 1:
            ?>
            <div class="d-flex flex-column">
                <div class="col-sm-9 mx-auto">
                    <? // Filters ?>
                    <section class="col-sm-3 my-5">
                        <?
                            filter_handler($format)
                        ?>
                    </section>

                    <? // Events ?>
                    <section class="mt-3">
                        <?
                            // events_pagination();
                        ?>

                        <ul class="cah-events">
                            <?
                                // First parameter = however many months you want to show.
                                // e.g. 0 or 1, shows 1 month, 2 = 2 months, etc.
                                print_handler($num_months_to_show);
                            ?>
                        </ul>

                        <?
                            show_more_events_handler();
                        ?>
                    </section>
                </div>
            </div>
            <?
            break;
        default:
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
                    <?
                        events_pagination($num_events_to_show);
                    ?>

                    <ul class="cah-events">
                        <?
                            print_handler($num_events_to_show);
                        ?>
                    </ul>

                    <?
                        events_pagination($num_events_to_show);
                    ?>
                </section>
            </div>
            <?

    }
}

// Function to index all events into an array for pagnination. This indexing function can possibly be merged with total_number_of_months();
// TODO: Add consideration for current active category.
function events_index() {
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
            $category = parse_event_category($event->tags);
            
            // Ensures that the events are active or upcoming:
            if ($end >= time()) {
                // Pushes each event into an array depending on which category is currently active.
                if ($activeCat == "All") {
                    array_push($events, $event);
                } else if (strpos($activeCat, $category) !== FALSE) {
                    array_push($events, $event);
                }
            }
        }

        $i++;
    }

    return $events;
}

?>