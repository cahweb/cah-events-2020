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
                    $start = strtotime($events[$i]->starts);
                    $end = strtotime($events[$i]->ends);
    
                    event_item_template($events[$i]->url, $start, $end, $events[$i]->title, $category, $events[$i]->description);
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
                    $start = strtotime($events[$i]->starts);
                    $end = strtotime($events[$i]->ends);
       
                    $category = parse_event_category($events[$i]->tags);
    
                    event_item_template($events[$i]->url, $start, $end, $events[$i]->title, $category, $events[$i]->description);

                    test_cont(array(
                        test_str_h("\$events[$i]->event_id", $events[$i]->event_id),
                    ));
                }
            }
        }
    }

}

// Handles individual event's html. Description length is shorted to 300 characters.
function event_item_template($link, $start, $end, $title, $category, $description) {
    ?>
        <a class="cah-event-item" href=<?= $link ?>>
            <li class="cah-event-item">
                <p name="date-range" class="h5 text-primary cah-event-item-date">
                    <?= date("F j, Y", $start) ?><span>,</span> <span><?= date("g A", $start) . " &ndash; " . date("g A", $end) ?></span>
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