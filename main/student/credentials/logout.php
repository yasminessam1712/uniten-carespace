<?php

session_start();

$_SESSION = [];
session_destroy();

header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 

echo "<script>

            window.location.replace('../../mainpage.php'); // Your desired page after logout
        </script>";

exit(); 
?>
