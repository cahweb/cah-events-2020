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
        // Prints all events in a category only for format 2.
        if ($format == 2 && $filter !== '') {
            for ($i = 0; $i < $number_events_to_show; $i++) {
                $category = parse_event_category($events[$i]->tags);

                if (strcasecmp($category, $filter) !== 0) {
                    $number_events_to_show++;
                } else {
                    event_item_template($events[$i]->url, $events[$i]->starts, $events[$i]->ends, $events[$i]->title, $category, $events[$i]->description, $date_range_flag);
                }
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
                    $category = parse_event_category($events[$i]->tags);

                    // Determines whether or not to print a date range.
                    $diff = date_diff($events[$i]->starts, $events[$i]->ends, true);
                    
                    if ($events[$i]->starts == $events[$i]->ends) {
                        $date_range_flag = false;
                    } else {
                        $date_range_flag = true;
                    }
    
                    event_item_template($events[$i]->url, $events[$i]->starts, $events[$i]->ends, $events[$i]->title, $category, $events[$i]->description, $date_range_flag);

                    test_cont(array(
                        test_str_h("\$events[$i]->event_id", $events[$i]->event_id),
                        test_str_h("Starts", date_format($events[$i]->starts, "D, Y m d - H:i:s")),
                        test_str_h("Ends", date_format($events[$i]->ends, "D, Y m d - H:i:s")),
                        test_str_h("Date diff", $diff->format("%R%a days")),
                    ));
                }
            }
        }
    }

}

// Handles individual event's html. Description length is shorted to 300 characters.
function event_item_template($link, $start, $end, $title, $category, $description, $date_range_flag) {
    if ($_GLOBAL['dev']) {
        if ($date_range_flag) {
            $event_datetime = date_format($start, "F j") . " &ndash; " . date_format($end, "j, Y") . ", " . "<span>" .  date_format($start, "g A") . " &ndash; " . date_format($end, "g A") . "</span>";
        } else {
            $event_datetime = date_format($start, "F j, Y") . ", " . "<span>" .  date_format($start, "g A") . " &ndash; " . date_format($end, "g A") . "</span>";
        }
    } else {
        $event_datetime = date("F j, Y", strtotime($start)) . "<span>,</span> <span>" . date("g A", strtotime($start)) . " &ndash; " . date("g A", strtotime($end)) . " </span>";
    }

    ?>
        <a class="cah-event-item" href=<?= $link ?>>
            <li class="cah-event-item">
                <p name="date-range" class="h5 text-primary cah-event-item-date">
                    <?= $event_datetime ?>
                </p>

                <p name="title" class="h5 text-secondary"><?= $title ?></p>
        
                <p name="description" class="text-muted mb-0"><?= strip_tags(substr($description, 0, 300) . " . . . ") ?></p>
            </li>
        </a>

    <?
}

// Properly formats category tags for printing.
function parse_event_category($tags) {
    $categories = array("Gallery", "Music", "SVAD", "Theatre");

    if (strtolower($tags[0]) == "music") {
        return "$categories[1]";
    } else if (strtolower($tags[0]) == "theatre ucf") {
        return $categories[3];
    } else {
        // It'll be SVAD. Seems like "art gallery" always goes with SVAD.
        // This else statement depends on "art gallery" always being a tag with SVAD.
        // !WARNING: This might not be true in the future. I'm just too lazy to future-proof this.

        // Checks for "art gallery" tag.
        $gallery = false;
        
        // If statement only needed to remove warning about providing an invalid input, since PHP wants you to check for empty arrays before looping them.
        if (!empty($tags)) {
            foreach ($tags as $tag1) {
                if (strtolower($tag1) == "art gallery") {
                    $gallery = true;
                }
            }
        }

        if ($gallery === true) {
            return $categories[0] . ", " . $categories[2];
        } else {
            return $categories[2];
        }
    }
    
}

?>