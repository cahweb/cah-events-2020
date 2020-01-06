<?
/*
    Helper functions for events.php.
    Specifically, for printing events.
*/

function print_handler($number_events_to_show) {
    $events = events_index();
    $num_of_events = count($events);
    $page_number = page_number();

    if (empty($events)) {
        ?>
            <p class="text-center text-muted my-5"><em>There are currently no active or upcoming events listed.</em></p>
        <?
    } else {
        // // Prints all events
        // foreach (events_index() as $event) {
        //     $start = strtotime($event->starts);
        //     $end = strtotime($event->ends);
            
        //     $category = parse_event_category($event->tags);
            
        //     event_item_template($event->url, $start, $end, $event->title, $category, $event->description);
        // }

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
            }
        }
    }

}

// Handles individual event's html. Description length is shorted to 300 characters.
function event_item_template($link, $start, $end, $title, $category, $description) {
    ?>
        <a class="cah-event-item" href=<?= $link ?>>
            <li class="mb-4 pt-2 cah-event-item">
                <p name="date-range" class="h5 text-primary">
                    <?= date("F j, Y", $start) ?><span class="ml-1">,</span> <span class="ml-2"><?= date("g A", $start) . " &ndash; " . date("g A", $end) ?></span>
                </p>

                <p name="title" class="h5 text-secondary"><?= $title ?></p>

                <!-- <h4 name="category"><?// $category ?></h3> -->
        
                <p name="description" class="text-muted"><?= strip_tags(substr($description, 0, 300) . " . . . ") ?></p>
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