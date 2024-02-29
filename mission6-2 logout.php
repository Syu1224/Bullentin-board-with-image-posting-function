<?php
session_start();
$_SESSION = array();
session_destroy();
header("location: mission6-2 login.php");
exit();
?>