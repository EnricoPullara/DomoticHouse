<?php
   session_start(); 
	echo "Logout</br>";
	echo $_SESSION['loginklmafg'];
   echo "</br>";
	echo $_SESSION['user'];
   echo "</br>";
	echo $_SESSION['ip'];
   echo "</br>";

	if(isset($_SESSION['loginklmafg']))
	unset($_SESSION['loginklmafg']);  //Is Used To Destroy Specified Session

	header("location: login.php");
	exit;
?> 