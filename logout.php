<?php

require_once("include/auth.class.php");

Auth::logout();
header("Location: index.php");
exit;

?>