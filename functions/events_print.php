<?
/*
    Helper functions for events.php.
    Specifically, for printing events.
*/

function print_handler($format, $filter, $number_events_to_show) {
    $events = parsed_events_index();
    $num_of_events = count($events);
    $page_number = page_number();

    // Where there is no filter set, so display all events.
    if ($filter === '' || strcasecmp($filter, "all") === 0) {
        $filter = '';
    }

    if (empty($events)) {
        ?>
            <p class="text-center text-muted my-5"><em>There are currently no active or upcoming events listed.</em></p>
        <?
    } else {
        if ($num_of_events < $number_events_to_show) {
            $number_events_to_show = $num_of_events;
        }

        // Prints all events in a category only for format 2.
        if ($format == 2 && $filter !== '') {
            for ($i = 0; $i < $number_events_to_show; $i++) {
                event_item_template($events[$i]);
            }
        } else {
            // Pagination
            
            // Great names, I know. This is just to make writing the for loop simpler.
            // Includes logic that prints the number of events specified, divided into pages.
            $x = ($page_number - 1) * $number_events_to_show;
            $y = $number_events_to_show * $page_number;
    
            for ($i = $x; $i < $y; $i++) {
                if ($i >= $num_of_events) {
                    // Break added for the last page, where the number of events might not equal to the amount needed to print.
                    // Out of bounds conditional.
                    break;
                } else {
                    event_item_template($events[$i]);
                }
            }
        }
    }

}

// Handles individual event's html. Description length is shorted to 300 characters.
function event_item_template($event) {
    $link = $event->url;
    $start = $event->starts;
    $end = $event->ends;
    $title = $event->title;
    $category = parse_event_category($event->tags);
    $description = $event->description;
    $day_range = $event->day_range;

    // Determines whether or not to print a date range.
    if ($event->day_range > 0) {
        date_modify($end, "+" . $day_range . " days");

        $event_datetime = date_format($start, "F j") . " &ndash; " . date_format($end, "j, Y") . ", " . "<span>" .  date_format($start, "g A") . " &ndash; " . date_format($end, "g A") . "</span>";
    } else {
        $event_datetime = date_format($start, "F j, Y") . ", " . "<span>" .  date_format($start, "g A") . " &ndash; " . date_format($end, "g A") . "</span>";
    }

    ?>
        <a class="cah-event-item" href=<?= $link ?>>
            <li class="cah-event-item">
                <p name="date-range" class="h5 text-primary cah-event-item-date">
                    <?= $event_datetime ?>
                </p>

                <p name="title" class="h5 text-secondary"><?= $title ?></p>
        
                <p name="description" class="text-muted mb-0"><?= strlen($description) > 300 ? strip_tags(substr($description, 0, 300) . " . . . ") : strip_tags($description) ?></p>
            </li>
        </a>

    <?
}

?>