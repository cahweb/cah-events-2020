<?

/*
    ----------------------------    
        PHP Helper Functions
    ----------------------------
*/

// // Sets timezone to EST.
// date_default_timezone_set("America/New_York");

// // Indexes all events into an array, formatted, and returned to Vue.js to be encoded into a JSON object again.
// function index_events() {
//     $events = array();

//     $current_year = date_create('Y');
//     $current_month = date_create('m');

//     // Tracks if this is the initial loop where date looping would not apply.
//     $i = 0;

//     $path = "https://events.ucf.edu/calendar/4310/arts-at-ucf/";
    
//     // Initializes the conditional below. It's repeated again to output the correct path.
//     $events_json_contents = json_decode(file_get_contents($path . date_format($current_year, 'Y') . "/" . date_format($current_month, 'n') . "/" . "feed.json"));

//     while (!empty($events_json_contents)) {
//         // Loop around to next year if the current month is December and the loop as already gone through once.
//         if ($i > 0) {
//             if (date_format($current_month, 'n') == 12) {
//                 $current_year->modify("+1 year");
//             }

//             // This just for readability's sake
//             $Y = date_format( $current_month, 'Y' );
//             $m = date_format( $current_month, 'm' ) + 1;
//             $d = date_format( $current_month, 'd' ) >= 28 ? 28 : date_format( $current_month, 'd' );

//             $current_month = date_create_from_format( 'Y-m-d', "$Y-$m-$d" );
//         }

//         // Not DRY, I know.
//         $events_json_contents = json_decode(file_get_contents($path . date_format($current_year, 'Y') . "/" . date_format($current_month, 'n') . "/" . "feed.json"));

//         foreach ($events_json_contents as $event) {
//             // The date/time when each event ends.
//             $end = strtotime($event->ends);
            
//             // Ensures that the events are active or upcoming:
//             if ($end >= time()) {
//                 $event->filtered_category = strtolower(parse_event_category($event->tags));
//                 array_push($events, $event);
//             }
//         }

//         $i++;
//     }

//     return $events;
// }

// // Helper function for event_end_dates(). Returns an array of every unique event id.
// function get_unique_event_ids() {
//     $original_events_array = index_events();
//     $unique_events = array();
//     $unique_event_ids = array();
    
//     foreach ($original_events_array as $event) {
//         if (empty($unique_event_ids)) {
//             array_push($unique_event_ids, $event->event_id);
//         } else {
//             foreach ($unique_event_ids as $unique_event_id) {
//                 if (!in_array($event->event_id, $unique_event_ids)) {
//                     array_push($unique_event_ids, $event->event_id);
//                 }
//             }
//         }
//     }

//     return $unique_event_ids;
// }

// // Indexes each unique event and their end dates if they occur multiple times.
// function event_end_dates() {
//     $original_events_array = index_events();
//     $unique_event_ids = get_unique_event_ids();
//     $ids_end_dates = array();
    
//     if (!empty($unique_event_ids)) {
//         foreach ($original_events_array as $event) {
//             if (in_array($event->event_id, $unique_event_ids)) {
//                 array_push($ids_end_dates, array("event_id" => $event->event_id, "end_date" => $event->ends));
//             }
//         }
//     }

//     $reversed_ids_end_dates = array_reverse($ids_end_dates);

//     return $reversed_ids_end_dates;
// }

// // Properly formats category tags for printing.
// function parse_event_category($tags) {
//     $categories = array("Gallery", "Music", "SVAD", "Theatre");

//     if (strtolower($tags[0]) == "music") {
//         return "$categories[1]";
//     } else if (strtolower($tags[0]) == "theatre ucf") {
//         return $categories[3];
//     } else {
//         // It'll be SVAD. Seems like "art gallery" always goes with SVAD.
//         // This else statement depends on "art gallery" always being a tag with SVAD.
//         // !WARNING: This might not be true in the future. I'm just too lazy to future-proof this.

//         // Checks for "art gallery" tag.
//         $gallery = false;
        
//         // If statement only needed to remove warning about providing an invalid input, since PHP wants you to check for empty arrays before looping them.
//         if (!empty($tags)) {
//             foreach ($tags as $tag) {
//                 if (strpos(strtolower($tag), "gallery") !== false) {
//                     $gallery = true;
//                 }
//             }
//         }

//         if ($gallery === true) {
//             return $categories[0];
//         } else {
//             return $categories[2];
//         }
//     }
    
// }

// Normalizes strings by removing spaces and converting to lowercase to make comparisons easier.
function normalize_string($string) {
    return strtolower(str_replace(' ', '', $string));
}

?>