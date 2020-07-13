# Common - Events 2020

A mostly self-contained Wordpress plugin that displays CAH events.

## External Dependencies

1. All events are pulled from https://events.ucf.edu/calendar/3611/cah-events/.
2. `events_html-vuejs.php` also calls the production version of Vue.js (2.6.11) via CDN. Specifically: https://cdn.jsdelivr.net/npm/vue@2.6.11

## Usage

Call the shortcode: `[events]` and add any of the following options.

If only given `[events]`, this plugin will assume `[events]` is:
```
[events filter="all" filter-format="" show-more-format="" hide-recurrence="false" num-events="5" front="false" show-all-when-none="false"]
```
Which will show only a list of 5 events with no filtering or show-more options.

There's no need to list an attribute if you wish to use the default option.

### Current available options:

| Name                 | Options                      | Description                                                                                                                                               | Example                                                                                                                                                              |
| -------------------- | ---------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `filter`             |                              | Determines which events to show.                                                                                                                          | `[events filter="svad"]` Which will list 5 SVAD events with no filter nor show-more options.                                                                         |
|                      | `all` or `""` (**DEFAULT**)  | Will list all upcoming events from CAH.                                                                                                                   |                                                                                                                                                                      |
|                      | `gallery`                    | List all upcoming gallery events.                                                                                                                         |                                                                                                                                                                      |
|                      | `music`                      | List all upcoming music events.                                                                                                                           |                                                                                                                                                                      |
|                      | `svad`                       | List all upcoming SVAD events.                                                                                                                            |                                                                                                                                                                      |
|                      | `theatre`                    | List all upcoming theatre events.                                                                                                                         |                                                                                                                                                                      |
| `filter-format`      |                              | Determines if there is a filtering option and which one it'll be.                                                                                         | `[events filter-format="dropdown"]` Will list 5 events from all of CAH with no show-more option. A dropdown with filtering options will be at the top of the events. |
|                      | `none` or `""` (**DEFAULT**) | List the events with no filtering option.                                                                                                                 |                                                                                                                                                                      |
|                      | `dropdown`                   | List the events with a filter dropdown option at the top.                                                                                                 |                                                                                                                                                                      |
|                      | `list`                       | List the events with all filter options listed on the left-hand side.                                                                                     |                                                                                                                                                                      |
| `show-more-format`   |                              | Determines if there will be an option to display more events and what it'll be.                                                                           | `[events show-more-format="paged"]` Will list 5 events from all of CAH with no filtering option and pagination at the bottom of the events.                          |
|                      | `none` or `""` (**DEFAULT**) | No option will be given at the end of events to display more.                                                                                             |                                                                                                                                                                      |
|                      | `button` or `btn`            | Will display a "SHOW MORE" button at the bottom of the listed events that, when clicked, will show the next however many events is set by `num-pages`.    |                                                                                                                                                                      |
|                      | `paged`                      | Will display the number of pages at bottom of the listed events which can be used to navigate.                                                            |                                                                                                                                                                      |
| `hide-recurrence`    |                              | Determines whether or not recurring events should be displayed.                                                                                           |                                                                                                                                                                      |
|                      | `false` (**DEFAULT**)        | Will not hide recurring events.                                                                                                                           |                                                                                                                                                                      |
|                      | `true`                       | Will hide recurring events and show date ranges for multi-day events.                                                                                     |                                                                                                                                                                      |
| `front`              |                              | Determines if the events are being displayed on the front page and applies appropriate styling.                                                           |                                                                                                                                                                      |
|                      | `false` (**DEFAULT**)        | Does nothing.                                                                                                                                             |                                                                                                                                                                      |
|                      | `true`                       | Applies front page styling.                                                                                                                               |                                                                                                                                                                      |
| `num-events`         |                              | Determines the amount of events to show per page. It will also determine how many events to append when using the `button` option for `show-more-format`. |                                                                                                                                                                      |
|                      | `5` (**DEFAULT**)            | 5 events will be shown.                                                                                                                                   |                                                                                                                                                                      |
|                      | Any integer                  | `n` number of events will be shown.                                                                                                                       | `[events num-events="10"]` Will list 5 events from all of CAH with no filtering nor show-more options.                                                               |
| `show-all-when-none` |                              | Determines if all events from CAH should be shown when there are no events in the current selected filter.                                                |                                                                                                                                                                      |
|                      | `false` (**DEFAULT**)        | Only displays a message saying that there are no more events in this category.                                                                            |                                                                                                                                                                      |
|                      | `true`                       | Displays a message saying that there are no more events in this category but to check out the events in all of CAH with the listed events shown.          |                                                                                                                                                                      |

The current shortcode configuration for CAH Upcoming Events located at https://www.cah.ucf.edu/newsroom/events/ is:
```
[events filter="all" filter-format="list" show-more-format="paged" hide-recurrence="true" num-events="5"]
```
The attributes `filter` and `num-events` are not necessary here since they are the default values.