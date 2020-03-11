<?

/*
    ------------
        TODO
    ------------

    - Consider what to do when the number of pages exceed the containing HTML element and runs off screen.
        - Consider mobile devices as well.

    ----------------------
        Considerations
    ----------------------

    * Chrome sometimes does not immediately render correctly. Try:
        - running it in Guest mode with no extensions,
        - wating a while before refreshing,
        - or check in Firefox, as I don't seem to have a problem there.
*/

add_shortcode('dev-pagination', 'dev_pagination_handler');

function dev_pagination_handler($atts = []) {
    $attributes = shortcode_atts([
        'filter' => 'all',
        'filter-format' => '',
        'hide-recurrence' => false,
        'num-events' => 5,
    ], $atts);

    $filter = $atts['filter'];
    // Takes into account that an empty string defaults to "all"
    if (str_replace(' ', '', $filter) === "") {
        $filter = "all";
    }

    $filter_format = $atts['filter-format'];

    $hide_recurrence = $atts['hide-recurrence'];
    // Needed for $hide_recurrence to be processed correctly in JavaScript. PHP renders the false as an empty string otherwise.
    if ($hide_recurrence === false) {
        $hide_recurrence = "false";
    }

    // $num_events_to_show = $atts['num-events'];
    $num_events_to_show = 5;
    
    dev_cont(array(
        tsh("Filter", $filter),
        tsh("Filter format", $filter_format),
        tsh("Hide recurrence", $hide_recurrence),
        tsh("Number of events to show", $num_events_to_show),
    ));

    ?>

        <div id="app" class="demo">
            <component v-bind:is="currentPageComponent" class="page"></component>
            
            <button
                v-for="page in pages"
                v-bind:key="page"
                v-bind:class="['page-button', { active: currentPage === page }]"
                v-on:click="currentPage = page"
            >
                {{ page }}
            </button>
        </div>

        <div id="mess" class="mt-5">
            <h1>This is a mess.</h1>

            <div class="dropdown w-50 my-4 mx-auto">
                <a v-if="currentFilter === ''" class="btn btn-primary dropdown-toggle w-100" href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ getCurrentFilter }}
                </a>
                <a v-else class="btn btn-primary dropdown-toggle w-100" href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ currentFilter }}
                </a>

                <div class="dropdown-menu w-100" aria-labelledby="dropdownMenuLink">
                    <button class="dropdown-item cah-event-filter-button"
                        v-for="filter in filters"
                        v-bind:disabled="isCurrentFilter(currentFilter, givenFilter, filter)"
                        v-on:click="currentFilter = filter; currentPage = 1"
                    >
                        {{ filter }}
                    </button>
                </div>
            </div>

            <div class="row my-3">
                <div class="mx-auto">
                    <nav aria-label="page-navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item cah-event-filter-button"
                                v-bind:class="{ disabled: currentPage === 1, 'disabled-hover': currentPage === 1 }"
                            >
                                <span class="page-link" tabindex="-1"
                                    v-on:click="currentPage--" 
                                >
                                    «
                                </span>
                            </li>

                            <li class="page-item cah-event-filter-button"
                                v-for="i in numberOfPages(filteredEvents, eventsPerPage)"
                                v-on:click="currentPage = i"
                                v-bind:class="{ active: i === currentPage }"
                            >
                                <span class="page-link">{{ i }}</span>
                            </li>

                            <li class="page-item cah-event-filter-button"
                                v-bind:class="{ disabled: currentPage === numberOfPages(filteredEvents, eventsPerPage), 'disabled-hover': currentPage === numberOfPages(filteredEvents, eventsPerPage) }"
                            >
                                <span class="page-link"
                                    v-on:click="currentPage++" 
                                >
                                    »
                                </span>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <h4>FilteredEvents length: {{ getTotalEvents }}</h4>
            <h4>Current filter: {{ getCurrentFilter }}</h4>
            <h4>Index start: {{ getStartingIndexForPage }}</h4>
            <h4>Index range: {{ getIndexRangeForPage }}</h4>
            <h4>currentPage: {{ currentPage }}</h4>
            <h4>currentPageStart: {{ currentPageStart }}</h4>

            <ul class="list-unstyled"
                v-for="(event, index) in filteredEvents"
            >
                <a class="cah-event-item"
                    v-bind:href="event.url"
                    v-show="pageShow(index, getIndexRangeForPage)"
                >
                    <li class="cah-event-item-light">
                        <p name="date-range" class="h5 text-primary cah-event-item-date">
                            {{ printDate(event, hideRecurrence, endDateArray) }}, {{ printTime(event.starts) }} &ndash; {{ printTime(event.ends) }} 
                        </p>

                        <p name="title" class="h5 text-secondary">
                            {{ index }} &mdash; {{ event.title }} &mdash; {{ event.filtered_category }}
                        </p>

                        <p name="description" class="mb-0 text-muted" v-html="printDescription(event.description)"></p>
                    </li>
                </a>
            </ul>
        </div>

        <script src="https://unpkg.com/vue"></script>
        <script>
            Vue.component("page-home", {
                template: "<div>Home component</div>"
            });
            Vue.component("page-posts", {
                template: "<div>Posts component</div>"
            });
            Vue.component("page-archive", {
                template: "<div>Archive component</div>"
            });

            new Vue({
                el: "#app",
                data: {
                    currentPage: "Home",
                    pages: ["Home", "Posts", "Archive"]
                },
                computed: {
                    currentPageComponent: function() {
                        return "page-" + this.currentPage.toLowerCase();
                    }
                }
            });
            
            new Vue({
                el: "#mess",
                data: {
                    json: <? print json_encode(index_events()) ?>,
                    endDateArray: <? print json_encode(event_end_dates()) ?>,
                    currentFilter: "",
                    givenFilter: "<?= $filter ?>",
                    uniqueEventIds: [],
                    filters: [
                        "All",
                        "Gallery",
                        "Music",
                        "SVAD",
                        "Theatre",
                    ],
                    hideRecurrence: <?= $hide_recurrence ?>,
                    pagination: true,
                    eventsPerPage: <?= $num_events_to_show ?>,
                    currentPage: 1,
                    currentPageStart: 0,
                },
                computed: {
                    getCurrentFilter: function() {
                        let givenFilter = this.givenFilter
                        let filters = this.filters

                        for (var i = 0; i < filters.length; i++) {
                            if (givenFilter.toLowerCase().trim() === filters[i].toLowerCase()) {
                                return filters[i]
                            }
                        }
                    },
                    noRepeatedEvents: function() {
                        let uniqueIds = this.uniqueEventIds

                        return this.json.filter(function (event) {
                            if (uniqueIds.length === 0) {
                                    uniqueIds.push(event.event_id)
                                    return event
                                } else {
                                    for (id in uniqueIds) {
                                        if (!uniqueIds.includes(event.event_id)) {
                                            uniqueIds.push(event.event_id)
                                            return event
                                        }
                                    }
                                }
                        })
                    },
                    filteredEvents: function() {
                        let givenFilter = this.givenFilter
                        let currentFilter = this.currentFilter
                        let hideRecurrence = this.hideRecurrence
                        
                        function filterShow(givenFilter, currentFilter, eventFilter) {
                            normalizedGivenFilter = givenFilter.toLowerCase().trim()
                            normalizedCurrentFilter = currentFilter.toLowerCase().trim()
                            normalizedEventFilter = eventFilter.toLowerCase().trim()

                            // Takes into account the given preferred filter in the Wordpress shortcode.
                            if (normalizedGivenFilter !== "" && normalizedCurrentFilter === "") {
                                normalizedCurrentFilter = normalizedGivenFilter
                            }

                            if (normalizedCurrentFilter === "" || normalizedCurrentFilter === "all") {
                                return true
                            } else {
                                if (normalizedCurrentFilter === normalizedEventFilter) {
                                    return true
                                } else {
                                    return false
                                }
                            }
                        }

                        if (hideRecurrence) {
                            return this.noRepeatedEvents.filter(function (event) {
                                if (filterShow(givenFilter, currentFilter, event.filtered_category)) {
                                    return event
                                }
                            })
                        } else {
                            return this.json.filter(function (event) {
                                if (filterShow(givenFilter, currentFilter, event.filtered_category)) {
                                    return event
                                }
                            })
                        }

                    },
                    getTotalEvents: function() {
                        return this.filteredEvents.length
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

                        let indexRange = []

                        for (let i = currentPageStart; i < currentPageStart + eventsPerPage; i++) {
                            if (i < totalEvents) {
                                indexRange.push(i)
                            }
                        }

                        // return [currentPage, currentPageStart, outOfBounds]
                        return indexRange
                    }
                },
                methods : {
                    printDescription: function(description) {
                        // return description.replace(/<[^>]*>?/gm, '')
                        var str = description.replace(/(\n|<br>|<p>|<\/p>|<span>|<\/span>|<li>|<\/li>)/igm, " ").trim()
                        str = str.replace(/(\s\s+)/igm, " ").trim()
                        str = str.replace(/(<a.*?>|<\/a>|<strong>|<\/strong>|<ul>|<\/ul>)/igm, "").trim()

                        var strLen = str.length

                        if (strLen >= 300) {
                            return str.substr(0, 300) + " . . ."
                        } else {
                            // If the last sentence does not contain a period, add one.
                            if (str.substr(str.length - 1, str.length).trim() !== ".") {
                                str += "."
                            }

                            return str
                        }
                    },
                    printDate: function(date, hideRecurrence, endDateArray) {
                        var d = Date.parse(date.starts)
                        d = new Date(d)

                        var month = d.toLocaleDateString('en-US', { month: 'long' })
                        var day = d.toLocaleDateString('en-US', { day: 'numeric' })
                        var year = d.toLocaleDateString('en-US', { year: 'numeric' })
                        
                        var formattedDate = ""
                        
                        if (hideRecurrence) {
                            function printEndDate(event_id, endDateArray) {
                                for (var i = 0; i < endDateArray.length; i++) {
                                    if (event_id === endDateArray[i].event_id) {
                                        return endDateArray[i].end_date
                                    }
                                }
                            }
                            
                            var rawEndDate = printEndDate(date.event_id, endDateArray)
                            var endDate = Date.parse(rawEndDate)
                            endDate = new Date(endDate)

                            var endMonth = endDate.toLocaleDateString('en-US', { month: 'long' })
                            var endDay = endDate.toLocaleDateString('en-US', { day: 'numeric' })
                            var endYear = endDate.toLocaleDateString('en-US', { year: 'numeric' })
                            
                            if (endYear === year) {
                                if (endMonth === month) {
                                    if (endDay === day) {
                                        formattedDate = month + " " + day + ", " + year    
                                    } else {
                                        formattedDate =  month + " " + day + " – "+ endDay + ", " + year
                                    }
                                } else {
                                    formattedDate =  month + " " + day + " – "+ endMonth + " " +  endDay + ", " + year
                                }
                            } else {
                                formattedDate =  month + " " + day + ", " + year + " – "+ endMonth + " " +  endDay + ", " + endYear
                            }
                        } else {
                            formattedDate = month + " " + day + ", " + year
                        }

                        return formattedDate
                    },
                    printTime: function(time) {
                        var t = Date.parse(time)
                        t = new Date(t)

                        var timePeriod, formattedHour, formattedMinutes
                        var hour = t.getHours()
                        var minutes = t.getMinutes()

                        if (hour > 12) {
                            formattedHour = hour - 12
                            timePeriod = " p.m."
                        } else {
                            formattedHour = hour
                            if (hour === 12) {
                                timePeriod = " p.m."
                            } else {
                                timePeriod = " a.m."
                            }
                        }

                        if (minutes === 0) {
                            formattedMinutes = ""
                        } else {
                            formattedMinutes = ":" + minutes;
                        }

                        return formattedHour + formattedMinutes + timePeriod
                    },
                    isCurrentFilter: function (currentFilter, givenFilter, filter) {
                        if (currentFilter !== "" && currentFilter.toLowerCase().trim() === filter.toLowerCase()) {
                            return true
                        } else if (currentFilter.toLowerCase() === "" && givenFilter.toLowerCase().trim() === filter.toLowerCase()) {
                            return true
                        } else {
                            return false
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