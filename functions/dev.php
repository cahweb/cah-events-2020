<?

/*
    Developer Functions
    -------------------
	Used for testing and debugging.
*/

// Displays error messages and saves lives.
ini_set('display_errors', 1);
// error_reporting(E_ALL);

// Single space.
function space() {
    echo "<br>";
}

// Double space.
function d_space() {
    space();
    space();
}

// Single spaced string.
function ss($string) {
    echo $string;
    space();
}

// Double-spaced string.
function ds($string) {
    d_space();

    echo $string;

    d_space();
}

// Simple test string.
// Renamed because it interferes with some other function and crashes cah.ucf.edu.
function test_s() {
    ds("TEST");
}

// Tests if string is empty.
function test_str($string) {
    if ($string === '') {
        ds("String is empty.");
    } else {
        ds($string);
    }
}

// Test string with header.
function tsh($label, $data) {
    if ($data === '' || $data === NULL) {
        return "<strong>" . $label . "</strong>: <em class='text-muted'>Data does not exist.</em>";
    } else {
        return "<strong>" . $label . "</strong>: " . $data;
    }
}

// Single-spaced array.
function ss_arr($strings) {
	if ($strings == '') {
		spaced("EMPTY ARRAY GIVEN");
	} else {
		foreach ($strings as $string) {
			sb_spaced($string);
		}
	}
}

// Header for dev_cont.
function dev_cont_h($string) {
    return '<p class="m-0 text-uppercase font-weight-bold letter-spacing-5">' . $string . '</p><hr class="mb-0">';
}

function dev_cont($data_array) {
    ?>
    <div class="" style="margin: 5% 0;">
        <div class="" style="   width: 75%;
                                padding: 2%;
                                margin: auto auto;
                                background-color: #f9e7c9;
                                border-color: #eddaba;
                                border-style: solid;
                                border-radius: 8px;
        ">
            <?
                foreach ($data_array as $data) {
                    ss($data);
                }
            ?>
        </div>
    </div>
    <?
}

?>