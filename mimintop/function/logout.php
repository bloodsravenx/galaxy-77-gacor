<?php

	include '../../function/connect.php';
	
	session_destroy();

	header("Location:../index.php");
?>