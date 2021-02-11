<?php
	session_start();  

    if (!isset($_SESSION['loginklmafg'])) { 
        header("Location: login.php");  
    } 
	if ($_SESSION['dir'] != 0){
		header("Location: login.php");
	}
	echo "Crepuscolo: ".date_sunset(time(), SUNFUNCS_RET_STRING, 37.631347, 13.478733, 91, 1);
	echo "&nbsp;&nbsp;Utente: ";
	echo $_SESSION['user'];
	echo " &nbsp;&nbsp;Diritti: ";
	if ($_SESSION['dir'] == 0){
		echo "Admin &nbsp;<br>";
		echo " <input type='button' onclick='document.location.href=`log_accessi.php`;' value='Log accessi'>";
		echo " <input type='button' onclick='document.location.href=`registrati.php`;' value='Gestione utenti'>";
		echo " <input type='button' onclick='document.location.href=`setup_ardu1.php`;' value='Setup'>";
		echo " <input type='button' onclick='document.location.href=`ardu1_timer.php`;' value='Timer'>";
	} else if ($_SESSION['dir'] == 1){
		echo "User &nbsp;";
	} else {
		echo "Guest &nbsp;";
	}
	
	// Includo la connessione al database
	require('config.php');

	$query="SELECT * FROM domo_tbl_log_IP ORDER BY Data DESC";
	$risultati=mysql_query($query);
 
	$num=mysql_numrows($risultati);
 
	mysql_close();

	echo '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Log accessi</title>
		</head>
		<body>
		<input type="button" onclick="document.location.href=`domotic.php`;" value="Home">
	';

	echo "<b><center>Log accessi</center></b><br>";
	
	echo "<pre><font color=”008800”><b>Data       ora&#9&#9;IP utente&#9;Utente&#9;Password&#9;Stato</b></font></pre>";
	
	$i=0;
	while ($i < $num) {
		$data=mysql_result($risultati,$i,"Data");
		$ip_client=mysql_result($risultati,$i,"IP_client");
		$utente=mysql_result($risultati,$i,"utente");
		$password=mysql_result($risultati,$i,"password");
		$descr=mysql_result($risultati,$i,"descr");
		if ($descr == 'Login OK')
			echo "<pre><font color=”008800”>$data&#9;$ip_client&#9;$utente&#9;$password&#9&#9;$descr</font></pre>";
		else
			echo "<pre><font color=”FF0000”><b>$data&#9;$ip_client&#9;$utente&#9;$password&#9&#9;$descr</b></font></pre>";
		$i++;
		echo "<hr align=”left” size=”1″ width=”100″ color=”#FEFEFE” noshade>";
	}
 

	echo '
		</body>
		</html>
	';


?>

