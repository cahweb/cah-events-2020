<?

/*
    Developer Functions
    -------------------
    Used for testing and debugging. Will be removed in production.
*/

// Displays error messages and saves lives.
ini_set('display_errors', 1);
// error_reporting(E_ALL);

function spaced($string) {
    echo "<br><br>";
    echo $string;
    echo "<br><br>";
}

function spaced_array($strings) {
	if ($strings == '') {
		spaced("EMPTY ARRAY GIVEN");
	} else {
		echo "<br><br>";
		
		foreach ($strings as $string) {
			echo $string;
			echo "<br>";
		}
	
		echo "<br><br>";
	}
}

// Renamed because it interferes with some other function and crashes cah.ucf.edu.
function test123() {
    spaced("TEST");
}

?>