<?php
	session_start();
	unset($_SESSION['user']);	
	unset($_SESSION['active']);	
	session_destroy();
    //echo "logout";
	header("Location: ../index.php");
?>