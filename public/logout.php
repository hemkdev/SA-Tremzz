<?php
require_once("../config/bd.php");

session_start();
session_unset();
session_destroy();
header("location: login.php");
exit;
?>