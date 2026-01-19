<?php

session_start();

$_SESSION = [];
session_destroy();

header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 

header("Location: ../../mainpage.php"); // Replace with your login page URL
exit;
?>
