
<?

/*
    -------------------
        Arts Events
    -------------------
    Handler for custom `event` post type for arts.cah.ucf.edu.
*/

function handle_arts_events($show_more_format, $num_events_to_show) {
    global $event_venues;
    $events = array();

    $raw_events = get_posts([
        'post_type' => 'event',
        'post_status' => 'publish',
        'numberposts' => -1,
    ]);

    foreach ($raw_events as $event) {
        $id = $event->ID;
        $meta = get_post_meta($id);

        if (!empty($meta['start_date'][0]) && !empty($meta['start_time'][0])) {
            array_push($events, array(
                'id' => $id,
                'title' => $event->post_title,
                'subtitle' => $meta['wps_subtitle'][0],
                'excerpt' => $event->post_excerpt,
                'url' => $event->guid,
                'thumbnail_url' => get_the_post_thumbnail_url($id, "thumbnail"),
                'start_date' => date_if_empty($meta['start_date'][0]),
                'end_date' => date_if_empty($meta['end_date'][0]),
                'start_time' => time_if_empty($meta['start_time'][0]),
                'end_time' => time_if_empty($meta['end_time'][0]),
                'venue' => $event_venues[$meta['venue'][0]],
                'custom_venue_name' => $event_venues[$meta['offsite_venue'][0]],
                'custom_venue_url' => $event_venues[$meta['offsite_url'][0]],
            ));
        }

    }

    usort($events, function($a, $b) {
        return strtotime($a['start_time'] . $a['start_date']) <=> strtotime($b['start_time'] . $b['start_date']);
    });

    // echo "<pre>";
    // // print_r($events);
    // print_r(get_post_meta(6185));
    // echo "</pre>";

    return render_arts_events($events, $show_more_format, $num_events_to_show);

}

function date_if_empty($value) {
    if (empty($value)) {
        return "";
    } else {
        return date("F j, Y", strtotime($value));
    }
}

function time_if_empty($value) {
    if (empty($value)) {
        return "";
    } else {
        return str_replace(array('am','pm'), array('a.m.','p.m.'), date("g:i a", strtotime(str_replace(["(", ")"], "", $value))));
    }
}

function render_arts_events($events, $show_more_format, $num_events_to_show) {

    ?>

        <div id="arts-events-app">
            <div class="d-flex flex-column">
                <div v-if="eventsPerPage > 0 && events.length === 0" class="mb-3">
                    <p class="font-serif" style="font-style: italic">There are currently no upcoming Arts at UCF events.</p>
                </div>

                <ul v-else class="list-unstyled">
                    <a class="cah-event-item"
                        v-for="(event, index) in events"
                        v-bind:href="event.url.replace('#038;', '')"
                        v-show="(eventsPerPage > 0) ? pageShow(index, getIndexRangeForPage) : getTotalEvents"
                    >
                        <li :class="isLight ? 'cah-event-item-light' : 'cah-event-item-dark'" class="media">
                            <div class="bg-primary mr-3" style="width: 150px; height: 125px;">
                            <img v-if="event.thumbnail_url" v-bind:src="event.thumbnail_url" class="d-flex align-self-center h-100" style="object-fit: cover;" width="150">
                            </div>

                            <div class="media-body">
                                <p name="date-range" class="h5 mb-1 text-secondary font-weight-normal cah-event-item-date">
                                    <span>{{ event.start_date }}</span><span v-if="event.end_date"> &ndash; {{ event.end_date }}</span><span v-if="event.start_time">, {{ event.start_time }}</span><span v-if="event.end_time"> &ndash; {{ event.end_time }}</span><span v-if="event.venue"> • {{ event.venue[0] }}</span>
                                </p>

                                <p name="title" v-html="event.title" class="h5" :class="isLight ? 'text-secondary' : 'text-inverse'"></p>

                                <p name="description" class="mb-0" :class="isLight ? 'text-muted' : 'text-inverse font-weight-light'" v-html="printDescription(event.excerpt)"></p>
                            </div>
                        </li>
                    </a>
                </ul>
            </div>

            <div v-if="eventsPerPage > 0" class="row my-3 mx-0">
                <div v-if="showMoreFormat === 'paged' && events.length !== 0"  class="mx-auto">
                    <nav aria-label="page-navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item cah-event-filter-button"
                                v-bind:class="{ disabled: currentPage === 1, 'disabled-hover': currentPage === 1 }"
                            >
                                <span class="page-link" tabindex="-1"
                                    v-on:click="currentPage--; indexRange = []; appendToIndexRange = false" 
                                >
                                    «
                                </span>
                            </li>

                            <li class="page-item cah-event-filter-button"
                                v-for="i in numberOfPages(events, eventsPerPage)"
                                v-on:click="currentPage = i; indexRange = []; appendToIndexRange = false"
                                v-bind:class="{ active: i === currentPage }"
                            >
                                <span class="page-link">{{ i }}</span>
                            </li>

                            <li class="page-item cah-event-filter-button"
                                v-bind:class="{ disabled: currentPage === numberOfPages(events, eventsPerPage), 'disabled-hover': currentPage === numberOfPages(events, eventsPerPage) }"
                            >
                                <span class="page-link"
                                    v-on:click="currentPage++; indexRange = []; appendToIndexRange = false" 
                                >
                                    »
                                </span>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div v-if="(showMoreFormat === 'btn' || showMoreFormat === 'button') && events.length !== 0" class="mx-auto">
                    <button class="btn btn-primary"
                        v-bind:disabled="events.length - 1 <= indexRange.slice(-1)[0]"
                        v-on:click="currentPage++; appendToIndexRange = true"
                    >
                        Show More
                    </button>
                </div>
            </div>
        </div>

        <?
            // Production version of Vue.js: 2.6.11.
            echo '<script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>';
        ?>

        <script>
            const v = new Vue({
                el: "#arts-events-app",
                data: {
                    events: <? print json_encode($events) ?>,
                    showMoreFormat: "<?= normalize_string($show_more_format) ?>",

                    pagination: true,
                    eventsPerPage: <?= $num_events_to_show ?>,
                    currentPage: 1,
                    currentPageStart: 0,
                    indexRange: [],
                    appendToIndexRange: false,
                },
                computed: {
                    getTotalEvents: function() {
                        return this.events.length
                    },
                    getStartingIndexForPage: function() {
                        let currentPage = this.currentPage
                        let currentPageStart = this.currentPageStart
                        let eventsPerPage = this.eventsPerPage

                        if (currentPage !== 1) {
                            currentPageStart = (eventsPerPage * currentPage) - eventsPerPage
                        }
                        
                        return currentPageStart
                    },
                    getIndexRangeForPage: function() {
                        let currentPage = this.currentPage
                        let currentPageStart = this.getStartingIndexForPage
                        let eventsPerPage = this.eventsPerPage
                        let totalEvents = this.getTotalEvents
                        let appendToIndexRange = this.appendToIndexRange
                        let indexRange = this.indexRange

                        for (let i = currentPageStart; i < currentPageStart + eventsPerPage; i++) {
                            if (i < totalEvents) {
                                indexRange.push(i)
                            }
                        }

                        return indexRange
                    },
                    isLight() {
                        // Finds out if there's a .bg-inverse somewhere in the app's parentage, so we can apply a conditional
                        // class to <li> elements.
                        const app = document.querySelector('#arts-events-app')
                        const inverseParent = app.closest('.bg-inverse')
                        if (inverseParent === null ) {
                            return true
                        }
                        return false
                    },
                },
                methods : {
                    printDescription: function(description) {
                        var str = description.replace(/(\n|<br>|<p>|<\/p>|<span>|<\/span>|<li>|<\/li>)/igm, " ").trim()
                        str = str.replace(/(\s\s+)/igm, " ").trim()
                        str = str.replace(/(<a.*?>|<\/a>|<strong>|<\/strong>|<ul>|<\/ul>)/igm, "").trim()

                        var strLen = str.length
                        var preferredStrLen = 275

                        if (strLen >= preferredStrLen) {
                            return str.substr(0, preferredStrLen) + " . . ."
                        } else {
                            // If the last sentence does not contain a period, add one.
                            if (str.substr(str.length - 1, str.length).trim() !== ".") {
                                str += "."
                            }

                            return str
                        }
                    },
                    numberOfPages: function(events, numEventsToShow) {
                        let pagesTotal = Math.ceil(events.length / numEventsToShow)

                        return pagesTotal
                    },
                    pageShow: function(index, indexRange) {
                        return indexRange.includes(index)
                    }
                }
            })
        </script>

    <?

}

?>