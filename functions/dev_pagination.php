<?

add_shortcode('dev-pagination', 'dev_pagination_handler');

function dev_pagination_handler($atts = []) {
    $attributes = shortcode_atts([
        'hide-recurrence' => false,
        'num-events' => 5,
    ], $atts);

    $hide_recurrence = $atts['hide-recurrence'];
    // $num_events_to_show = $atts['num-events'];
    $num_events_to_show = 20;
    
    test_cont(array(
        test_str_h("hide-recurrence", $hide_recurrence),
        test_str_h("Number of events to show", $num_events_to_show),
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

            <ul v-for="(event, index) in noRepeats(json)"  v-if="index < <?= $num_events_to_show ?>" class="list-unstyled">
                <a class="cah-event-item" v-bind:href="event.url">
                    <li class="cah-event-item-light">
                        <p name="date-range" class="h5 text-primary cah-event-item-date">
                            {{ printDate(event) }}
                        </p>

                        <p name="title" class="h5 text-secondary">
                            {{ event.title }}
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
                    json: <? print json_encode(index_events()) ?>
                },
                methods : {
                    noRepeats: function (json) {
                        var uniqueIds = []
                        return json.filter(function (event) {
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
                    printDescription: function(description) {
                        // return description.replace(/<[^>]*>?/gm, '')
                        var str = description.replace(/(\n|<br>|<p>|<\/p>|<span>|<\/span>|<li>|<\/li>)/igm, " ").trim()
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
                    printDate: function(event) {
                        var d = Date.parse(event.ends)
                        d = new Date(d)

                        var month = d.toLocaleDateString('en-US', { month: 'long' })
                        var day = d.toLocaleDateString('en-US', { day: 'numeric' })
                        var year = d.toLocaleDateString('en-US', { year: 'numeric' })

                        var oneDayFormat = month + " " + day + ", " + year

                        return oneDayFormat
                    }
                }
            })
        </script>

    <?
}

?>