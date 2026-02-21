<?php
ob_start();
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Jakarta');

$host 			= "localhost";
$user 			= "USER_DATABASE";
$password 		= "PASS_DATABASE";
$database 		= "NAMA_DATABASE";

$koneksi = mysqli_connect($host, $user, $password, $database) or die(mysqli_error());

?>