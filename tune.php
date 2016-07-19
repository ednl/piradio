<?php

$radio = '/usr/local/bin/piradio';

if (isset($_GET['station'])) {
	// Station IDs may consist of digits or lowercase letters, max 7 characters
	$station = substr(preg_replace('/[^a-z0-9]+/', '', $_GET['station']), 0, 7);
	if (strlen($station))
		// Tune to station, discard output
		exec($radio . ' ' . $station);
}

// Get status, display output
system($radio);

?>
