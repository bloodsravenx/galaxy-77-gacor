<?php

	include '../../function/connect.php';
	
	session_destroy();

	header("Location: ../index.php?pesan=8");
?>