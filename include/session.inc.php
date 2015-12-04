<?php

$_sessid = "";

if(isset($_GET["PHPSESSID"])) {
	session_id($_GET["PHPSESSID"]);
	$_sessid = $_GET["PHPSESSID"];
	
}

session_start();

if($_sessid == "")
	$_sessid = session_id();


define("SESSIDPARAM", "PHPSESSID=" . htmlspecialchars($_sessid));

?>