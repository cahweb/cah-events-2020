<?

/*
    ----------------------------    
        HTML/Vue.js Template
    ----------------------------

    How the plugin renders.

    ------------
        TODO
    ------------

    - Add in front-page formatting.
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

function render_events($filter, $filter_format, $show_more_format, $hide_recurrence, $num_events_to_show, $dev, $front) {

    ?>

        <div id="app" class="">

            <? if ($dev) { ?>
                <div v-show="true" style="width: 75%; padding: 2%; margin: auto auto; background-color: #f9e7c9; border-color: #eddaba; border-style: solid; border-radius: 8px;" class="mb-5">
                    <p class="m-0 text-uppercase font-weight-bold letter-spacing-5">
                        Vue.js Data
                    </p>
                    
                    <hr>

                    <ul class="mb-0">
                        <li>
                            <strong>currentFilter: </strong>
                            {{ currentFilter }}
                        </li>

                        <li>
                            <strong>givenFilter: </strong>
                            {{ givenFilter }}
                        </li>

                        <li v-show="false">
                            <strong>uniqueEventIds: </strong>
                            {{ uniqueEventIds }}
                        </li>
                        
                        <li>
                            <strong>filterFormat: </strong>
                            {{ filterFormat }}
                        </li>

                        <li>
                            <strong>showMoreFormat: </strong>
                            {{ showMoreFormat }}
                        </li>
                        
                        <li>
                            <strong>filteredEvents.length: </strong>
                            {{ filteredEvents.length }}
                        </li>

                        <li>
                            <strong>currentPage: </strong>
                            {{ currentPage }}
                        </li>

                        <li>
                            <strong>currentPageStart: </strong>
                            {{ currentPageStart }}
                        </li>

                        <li>
                            <strong>getStartingIndexForPage: </strong>
                            {{ getStartingIndexForPage }}
                        </li>

                        <li>
                            <strong>getIndexRangeForPage: </strong>
                            {{ getIndexRangeForPage }}
                        </li>

                        <li>
                            <strong>indexRange: </strong>
                            {{ indexRange }}
                        </li>

                        <li>
                            <strong>appendToIndexRange: </strong>
                            {{ appendToIndexRange }}
                        </li>
                    </ul>
                </div>
            <? } ?>

            <div v-if="filterFormat === 'dropdown'" class="dropdown my-4 mx-auto" style="width: 35%;">
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

            <div v-else class="d-flex flex-column">
                <div v-bind:class="[filterFormat === 'list' ? 'row justify-content-between' : '']">
                    <div v-show="filterFormat === 'list'" class="col-sm-2 my-3">
                        <button class="list-group-item list-group-item-action cah-event-filter-button"
                            v-for="filter in filters"
                            v-bind:class="isCurrentFilter(currentFilter, givenFilter, filter) ? 'active' : ''"
                            v-on:click="currentFilter = filter; currentPage = 1; indexRange = []; appendToIndexRange = false"
                        >
                            {{ filter }}
                        </button>
                    </div>

                    <div v-bind:class="[filterFormat === 'list' ? 'col-sm-9' : '']">
                        <ul class="list-unstyled">
                            <a class="cah-event-item"
                                v-for="(event, index) in filteredEvents"
                                v-bind:href="event.url"
                                v-show="pageShow(index, getIndexRangeForPage)"
                            >
                                <li class="cah-event-item-light">
                                    <p name="date-range" class="h5 text-primary cah-event-item-date">
                                        {{ printDate(event, hideRecurrence, endDateArray) }}, {{ printTime(event.starts) }} &ndash; {{ printTime(event.ends) }} 
                                    </p>

                                    <p name="title" class="h5 text-secondary">
                                        {{ event.title }}
                                    </p>

                                    <p name="description" class="mb-0 text-muted" v-html="printDescription(event.description)"></p>
                                </li>
                            </a>
                        </ul>
                    </div>
                </div>
            </div>         

            <div v-show="showMoreFormat === 'paged'" class="row my-3">
                <div class="mx-auto">
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
                                v-for="i in numberOfPages(filteredEvents, eventsPerPage)"
                                v-on:click="currentPage = i; indexRange = []; appendToIndexRange = false"
                                v-bind:class="{ active: i === currentPage }"
                            >
                                <span class="page-link">{{ i }}</span>
                            </li>

                            <li class="page-item cah-event-filter-button"
                                v-bind:class="{ disabled: currentPage === numberOfPages(filteredEvents, eventsPerPage), 'disabled-hover': currentPage === numberOfPages(filteredEvents, eventsPerPage) }"
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
            </div>

            <div v-show="showMoreFormat === 'btn' || showMoreFormat === 'button'" class="row my-3">
                <div class="mx-auto">
                    <button class="btn btn-primary"
                        v-bind:disabled="filteredEvents.length - 1 <= indexRange.slice(-1)[0]"
                        v-on:click="currentPage++; appendToIndexRange = true"
                    >
                        Show More
                    </button>
                </div>
            </div>
        </div>

        <?
            if ($dev) {
            // Most up-to-date production version of Vue.js.
            echo '<script src="https://unpkg.com/vue"></script>';
            } else {
            // Production version 2.6.11.
            echo '<script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>';
            }
        ?>

        <script>
            new Vue({
                el: "#app",
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
                    filterFormat: "<?= normalize_string($filter_format) ?>",
                    showMoreFormat: "<?= normalize_string($show_more_format) ?>",
                    hideRecurrence: <?= $hide_recurrence ?>,
                    pagination: true,
                    eventsPerPage: <?= $num_events_to_show ?>,
                    currentPage: 1,
                    currentPageStart: 0,
                    indexRange: [],
                    appendToIndexRange: false,
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
                        let appendToIndexRange = this.appendToIndexRange
                        let indexRange = this.indexRange

                        for (let i = currentPageStart; i < currentPageStart + eventsPerPage; i++) {
                            if (i < totalEvents) {
                                indexRange.push(i)
                            }
                        }

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