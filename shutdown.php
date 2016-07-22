<?php

$shutdown = 'sudo /sbin/shutdown -%s now';

if (isset($_GET['arg']))
	if (!strcmp($_GET['arg'], 'r') || !strcmp($_GET['arg'], 'h'))
		system(sprintf($shutdown, $_GET['arg']));
	else
		echo 'Error: wrong argument.';
else
	echo 'Error: missing argument.';

?>
