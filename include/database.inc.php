<?php

if($_SERVER["SERVER_NAME"] == "benjichaz.scienceontheweb.net")
	$_MYSQLI = new mysqli("fdb14.runhosting.com", "1972947_db", "kenya777", "1972947_db");
else if($_SERVER["SERVER_NAME"] == "webfront.olympe.in")
	$_MYSQLI = new mysqli("sql2.olympe.in", "s453wvj0", "kenya777", "s453wvj0");
else
	$_MYSQLI = new mysqli("localhost", "root", "root", "iut_swm");

if ($_MYSQLI->connect_errno) {
	printf("Échec de la connexion : %s\n", $_MYSQLI->connect_error);
	exit();
}

?>