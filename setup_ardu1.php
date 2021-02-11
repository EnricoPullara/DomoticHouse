<?php
	session_start();  

    if (!isset($_SESSION['loginklmafg'])) { 
        header("Location: login.php");  
    } 
	if ($_SESSION['dir'] != 0){
		header("Location: login.php");
	}
	echo "Crepuscolo: ".date_sunset(time(), SUNFUNCS_RET_STRING, 1, 2, 91, 1);
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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Setup arduino 1 Out corridoio 1 piano</title>
		</head>
		<body>
		<body onLoad="fn_init()">
		
<script type="text/javascript">
	function fn_init(){
		xmlhttp = new XMLHttpRequest();
        wr_http = new XMLHttpRequest();
    }
	//Salva il valore nel db
	function scriviDb_update(id){
		var elem = 'text' + id;
		var el = document.getElementById(elem);
		var descr = el.value;
		//alert("ardu1_scriviDb_update_io.php?id=" + id + "&descr=" + descr);
		//wr_http.open("POST","ardu1_scriviDb_update_io.php?id=" + id + "&descr='" + descr + "'", true);
		wr_http.open("POST","ardu1_scriviDb_update_io.php", true);
		wr_http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		wr_http.send("id=" + id + "&descr=" + descr);
        //wr_http.send(null);
	}
</script>
<?php
	// Includo la connessione al database
	require('config.php');

	$query="SELECT ID, dig_pin, descr FROM ardu1_domo_tbl_OUT ORDER BY dig_pin";
	$risultati=mysql_query($query);
 
	mysql_close();

	echo '
		<input type="button" onclick="document.location.href=`domotic.php`;" value="Home">
		<br><br>
	';

	// visualizza i dati in tabella
	echo "<table border='1' cellpadding='10'>";
	echo "<tr> <th>Uscita pin</th> <th>Descrizione</th> <th></th></tr>";
	// loop tra i risultati della query del database, visualizzandoli in tabella
	while($row = mysql_fetch_array( $risultati )) {
		// emissione del contenuto di ogni riga in una tabella
		echo "<tr>";
			echo '<td align="center">' . $row['dig_pin'] . '</td>';
			echo '<td><input id="text'.$row['ID'].'" type="text" style="width: 200px;" value="' . $row['descr'] . '"></td>';
			echo '<td><input id='. $row['ID'] .' type="button" value="save" onClick="scriviDb_update('.$row['ID'].')"></a></td>';
		echo "</tr>";
	}
	// chiude la tabella>
	echo "</table>";
 	echo '
		</body>
		</html>
	';
?>
