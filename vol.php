<?php

$radio = '/usr/local/bin/piradio';

if (isset($_GET['adj'])) {
	// Volume adjustment may consist of digits or be + or -
	$adj = preg_replace('/[^0-9+-]+/', '', $_GET['adj']);
	if (strlen($adj))
		if (!strcmp($adj, '-') || !strcmp($adj, '+') || is_numeric($adj))
			// Adjust volume
			exec($radio . ' vol ' . $adj);
}

// Get status
unset($output);
exec($radio, $output);
foreach ($output as $line) {
	$trimmed = trim($line);
	if (strlen($trimmed))
		echo $trimmed . PHP_EOL;
}

?>
