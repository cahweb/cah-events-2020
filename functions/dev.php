<?

/*
    Developer Functions
    -------------------
	Used for testing and debugging.
    Remove when done.
*/

// Displays error messages and saves lives.
ini_set('display_errors', 1);
// error_reporting(E_ALL);

function space() {
    echo "<br>";
}

function d_space() {
    space();
    space();
}

function sb_spaced($string) {
    echo $string;
    space();
}

function spaced($string) {
    d_space();

    echo $string;

    d_space();
}

// Renamed because it interferes with some other function and crashes cah.ucf.edu.
function test123() {
    spaced("TEST");
}

function test_str($string) {
    if ($string === '') {
        spaced("String is empty.");
    } else {
        spaced($string);
    }
}

function test_str_h($label, $data) {
    if ($data === '' || $data === NULL || $data === 0) {
        return "<strong>" . $label . "</strong>: <em class='text-muted'>Data does not exist.</em>";
    } else {
        return "<strong>" . $label . "</strong>: " . $data;
    }
}

function s_spaced_array($strings) {
	if ($strings == '') {
		spaced("EMPTY ARRAY GIVEN");
	} else {
		foreach ($strings as $string) {
			sb_spaced($string);
		}
	}
}

function test_cont($data_array) {
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
                    sb_spaced($data);
                }
            ?>
        </div>
    </div>
    <?
}

?>