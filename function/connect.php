<?php
ob_start();
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Jakarta');

$host 			= "localhost";
$user 			= "mimintop";
$password 		= "mimintop";
$database 		= "database";

$koneksi = mysqli_connect($host, $user, $password, $database) or die(mysqli_error());


?>
