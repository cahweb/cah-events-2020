<?
/*
    Helper functions for events.php.
    Specifically, for generating pagination and links.
*/

// Navigation for event pages.
function events_pagination($num_events_to_show) {
    // !WARNING: Pagination is not responsive!
    // So beyond 20 or so pages, it'll run out of its containing div, and quite possibly the screen.

    $GLOBALS['events'] = parsed_events_index();
    $GLOBALS['num_total_events'] = count($GLOBALS['events']);

    // Number of pages to generate for pagination.
    // MOD to determine if there are remainders, no need to generate an extra page then.
    if ($GLOBALS['num_total_events'] == 0) {
        // If there are no events, still show 1 page.
        $GLOBALS['num_of_pages'] = 1;
    } else if ($GLOBALS['num_total_events'] % $num_events_to_show == 0) {
        $GLOBALS['num_of_pages'] = intdiv($GLOBALS['num_total_events'], $num_events_to_show);
    } else {
        $GLOBALS['num_of_pages'] = intdiv($GLOBALS['num_total_events'], $num_events_to_show) + 1;
    }
    // For ease of typing.
    $num_of_pages = $GLOBALS['num_of_pages'];

    $page_number = page_number();

    // Generates each page number's link.
    if (isset($_GET['sort'])) {
        $base_page_link = get_permalink() . "?sort=" . $GLOBALS['activeCat'];
    } else {
        $base_page_link = get_permalink();
    }

    ?>
        <div class="row my-3">
            <div class="mx-auto">
                <nav aria-label="page-navigation">
                    <ul class="pagination justify-content-center">
                        <? // Previous ?>
                        <li class="page-item<? if ($page_number == 1) { echo ' disabled'; } else { echo ''; } ?>">
                            <a href="<?= $base_page_link . '?page=' . ($page_number - 1) ?>" class="page-link" tabindex="-1">«</a>
                        </li>

                        <?
                            $i = 1;
                                
                            while ($i <= $num_of_pages) {
                                ?>
                                    <li class="page-item<? if ($page_number == $i) { echo ' active'; } else { echo ''; } ?>">
                                        <a href="<?= $base_page_link .  "?page=" . $i ?>" name="page" class="page-link"><?= $i ?></a>
                                    </li>
                                <?

                                $i++;
                            }
                            ?>

                        <? // Next ?>
                        <li class="page-item<? if ($page_number == $num_of_pages) { echo ' disabled'; } else { echo ''; } ?>">
                            <a href="<?= $base_page_link . '?page=' . ($page_number + 1) ?>" class="page-link" >»</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    <?
}

function page_number() {
    // Changes base link based on whether or not a category is chosen.
    if (isset($_GET['sort'])) {
        $base_page_link = get_permalink() . "?sort=" . $GLOBALS['activeCat'];
    } else {
        $base_page_link = get_permalink();
    }

    // Actual link displayed in the browser.
    $uri = $_SERVER['REQUEST_URI'];
    
    // Stripping everything except for the page number.
    if (isset($_GET['sort'])) {
        $page_number = substr($uri, strripos($uri, "=") + 1);
    } else {
        $page = str_replace("events", "", $uri);
        $page_number = str_replace("/", "", $page);
    }

    // If there was no page number, then it's probably on the first page.
    if (is_numeric($page_number)) {
        return $page_number;
    } else {
        return 1;
    }
}
?>