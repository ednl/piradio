<?php

$stationfile = '/home/pi/radio.txt';
$stationtext = file($stationfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$station = array();
if ($stationtext !== FALSE) {
	foreach ($stationtext as $line) {
		$a = preg_split('/\s+/', $line, 2, PREG_SPLIT_NO_EMPTY);
		if (count($a) == 2) {
			list($id, $url) = $a;
			$station[$id] = $url;
		}
	}
}

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-GB">
	<head profile="http://www.w3.org/2005/10/profile">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Internet Radio</title>
		<meta name="author" content="Ewoud Dronkert" />
		<style type="text/css">
			* { border: 0; margin: 0; padding: 0; }
			body { margin: 10px; background-color: #fff; }
			img { width: 72px; height: 72px; margin-right: 10px; float: left; }
			a { text-decoration: none; }
			div#clr { clear: both; }
			pre { color: #000; background-color: #bdf; width: 616px; margin-top: 10px; padding: 10px 15px; overflow-x: hidden; }
			div#listall { font: 16px Helvetica; width: 646px; margin-top: 10px; overflow-x: hidden; }
			div.station { padding: 10px 15px; }
			div.row1 { background-color: #f0f0f0; }
			div#reboot, div#poweroff { cursor: pointer; font: 24px Helvetica; color: #fff; width: 616px; margin-top: 10px; padding: 10px 15px; text-align: center; }
			div#reboot { background-color: #0c0; }
			div#poweroff { background-color: #f00; }
		</style>
		<script type="text/javascript">
			function ajax(url) {
				var req = new XMLHttpRequest();
				req.open("GET", url);
				req.onload = function() {
					document.getElementById("lcd").innerHTML = req.responseText;
				}
				req.send();
			}
			function tune(station) {
				ajax("tune.php?station=" + encodeURIComponent(station));
				return false;
			}
			function vol(adj) {
				ajax("vol.php?adj=" + encodeURIComponent(adj));
				return false;
			}
			function shutdown(arg) {
				ajax("shutdown.php?arg=" + encodeURIComponent(arg));
				return false;
			}
		</script>
	</head>
	<body onload="tune()">
		<div id="favs">
<?php

reset($station);
for ($i = 0; $i < 5; ++$i) {
	echo "\t\t\t";
	if ($a = each($station)) {
		list($id, $url) = $a;
		echo '<a href="#' . urlencode($id) . '" onclick="return tune(\'' . urlencode($id) . '\')" title="' . htmlspecialchars($id) . '"><img src="logo-' . urlencode($id) . '.png" alt="' . htmlspecialchars($id) . '" /></a>';
	} else {
		echo '<img src="logo-none.png" alt="none" />';
	}
	echo PHP_EOL;
}

?>
			<a href="#off" onclick="return tune('off')" title="Radio Off"><img src="cmd-off.png" alt="off" /></a>
			<a href="#dec" onclick="return vol('-')" title="Volume Down"><img src="cmd-dec.png" alt="dec" /></a>
			<a href="#inc" onclick="return vol('+')" title="Volume Up"><img src="cmd-inc.png" alt="inc" /></a>
			<div id="clr"></div>
		</div>
		<pre id="lcd"></pre>
<?php

if (count($station)) {
	echo "\t\t" . '<div id="listall">' . PHP_EOL;
	$rowid = 0;
	foreach ($station as $id => $url) {
		echo "\t\t\t" . '<div class="station row' . $rowid . '"><a href="#' . urlencode($id) . '" onclick="return tune(\'' . urlencode($id) . '\')">' . htmlspecialchars($id . ' | ' . $url) . '</a></div>' . PHP_EOL;
		$rowid = 1 - $rowid;
	}
	echo "\t\t" . '</div>' . PHP_EOL;
}

?>
		<div id="reboot" onclick="return shutdown('r')">Reboot</div>
		<div id="poweroff" onclick="return shutdown('h')">Power Off</div>
	</body>
</html>
