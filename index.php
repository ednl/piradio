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
		<meta name="viewport" content="width=738px" />
		<meta name="author" content="E. Dronkert" />
		<link rel="icon" type="image/png" href="raspi.png" />
		<style type="text/css">
			* { border: 0; margin: 0; padding: 0; }
			body { margin: 10px; background-color: #151d26; width: 738px; }
			img {
				width: 72px;
				height: 72px;
				margin-left: 10px;
				float: left;
				border-radius: 7px;
			}
			img.first { margin-left: 0; }
			a { text-decoration: none; color: #950; }
			div#clr { clear: both; margin-bottom: 10px; }
			pre {
				background-color: #369;
				width: 723px;
				margin-top: 10px;
				padding: 5px 5px 5px 0;
			}
			div#lcd {
				color: #ccc;
				overflow-x: hidden;
				background-repeat: no-repeat;
				background-position: center right;
			}
			div#listall {
				font: 16px Helvetica;
				width: 728px;
				margin-top: 10px;
				overflow-x: hidden;
			}
			div.station { padding: 10px 15px; }
			div.row1 { background-color: #1f2a34; }
			div#stop, div#reboot, div#poweroff {
				font: 24px Helvetica;
				color: #ccc;
				width: 698px;
				margin-top: 10px;
				padding: 10px 15px;
				text-align: center;
				border-radius: 7px;
			}
			div#stop     { background-color: #030; margin-top: 0; }
			div#reboot   { background-color: #c60; }
			div#poweroff { background-color: #900; }
		</style>
		<script type="text/javascript">
			var re_station = /Station : (.*)/;
			function ajax(url) {
				var req = new XMLHttpRequest();
				req.open("GET", url);
				req.onload = function() {
					var e;
					if (e = document.getElementById("lcd")) {
						e.innerHTML = req.responseText;
						e.style.backgroundImage = "";
						var station = req.responseText.match(re_station);
						if (null !== station) {
							if (station.length == 2) {
								if (station[1].length) {
									e.style.backgroundImage = "url(logo-" + station[1] + ".png)";
								}
							}
						}
					}
				}
				req.send();
			}
			function tune(station) {
				ajax("tune.php?station=" + encodeURIComponent(station));
				if (station != "off") {
					var e;
					if (e = document.getElementById("stop")) {
						e.style.backgroundColor = "#090";
						e.style.cursor = "pointer";
						e.addEventListener("click", shutdown);
					}
				}
				return false;
			}
			function vol(adj) {
				ajax("vol.php?adj=" + encodeURIComponent(adj));
				return false;
			}
			function shutdown(e) {
				e.target.removeEventListener("click", shutdown);
				e.target.style.cursor = "default";
				var arg = false;
				if (e.target.id == "stop") {
					e.target.style.backgroundColor = "#030";
					return tune("off");
				} else if (e.target.id == "reboot") {
					e.target.style.backgroundColor = "#930";
					arg = "r";
				} else if (e.target.id == "poweroff") {
					e.target.style.backgroundColor = "#300";
					arg = "h";
				}
				if (arg) {
					ajax("shutdown.php?arg=" + encodeURIComponent(arg));
				}
				return false;
			}
			function init() {
				tune();
				var id, e;
				for (id of ["reboot", "poweroff"]) {
					if (e = document.getElementById(id)) {
						e.addEventListener("click", shutdown);
						e.style.cursor = "pointer";
					}
				}
			}
		</script>
	</head>
	<body onload="init()">
		<div id="favs">
<?php

// Give link and button image for the appropriate fav preset, depending on position and ID.
// Nine buttons on each row, two volume buttons at the end of the first row.
// $pos = 0+, $id = string or false
function favlink($pos, $id) {
	$mod = $pos % 9;
	$class = $mod == 0 ? ' class="first"' : '';       // start of row
	$clear = $mod == 8 ? '<div id="clr"></div>' : ''; // end of row
	// At position 7+8, give volume buttons (regardless of ID)
	if ($pos == 7) {
		return '<a href="#dec" onclick="return vol(\'-\')" title="Volume Down"><img src="cmd-dec.png" alt="dec"' . $class . ' /></a>' . $clear;
	}
	if ($pos == 8) {
		return '<a href="#inc" onclick="return vol(\'=\')" title="Volume Up"><img src="cmd-inc.png" alt="inc"' . $class . ' /></a>' . $clear;
	}
	// If no preset ID at this position, give placeholder button without link
	if ($id === false) {
		return '<img src="logo-none.png" alt="none"' . $class . ' />' . $clear;
	}
	// In all other cases, give station image+link
	return '<a href="#' . urlencode($id) . '" onclick="return tune(\'' . urlencode($id) . '\')" title="' . htmlspecialchars($id) . '"><img src="logo-' . urlencode($id) . '.png" alt="' . htmlspecialchars($id) . '"' . $class . ' /></a>' . $clear;
}

$pos = 0;
foreach ($station as $id => $url) {
	echo "\t\t\t" . favlink($pos++, $id) . PHP_EOL;
	// Stop after three rows
	if ($pos == 27)
		break;
	// Skip over volume buttons without losing presets from this loop
	while ($pos == 7 || $pos == 8)
		echo "\t\t\t" . favlink($pos++, false) . PHP_EOL;
}
while ($pos < 27)
	echo "\t\t\t" . favlink($pos++, false) . PHP_EOL;

?>
		</div>
		<div id="stop">Stop</div>
		<pre><div id="lcd"></div></pre>
		<div id="reboot">Reboot</div>
		<div id="poweroff">Power Off</div>
<?php

if (count($station)) {
	echo "\t\t" . '<div id="listall">' . PHP_EOL;
	$rowid = 0;
	$pos = 0;
	foreach ($station as $id => $url) {
		if (++$pos > 25) {  // skip stations already listed in the logo rows
			echo "\t\t\t" . '<div class="station row' . $rowid . '"><a href="#' . urlencode($id) . '" onclick="return tune(\'' . urlencode($id) . '\')">' . htmlspecialchars($id . ' | ' . $url) . '</a></div>' . PHP_EOL;
			$rowid = 1 - $rowid;
		}
	}
	echo "\t\t" . '</div>' . PHP_EOL;
}

?>
	</body>
</html>
