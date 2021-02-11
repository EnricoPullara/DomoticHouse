<!//Tabella timer arduino 1>
<?php
	session_start();
    if (!isset($_SESSION['loginklmafg'])) {  
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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Timer Arduino 1 Out corridoio 1 piano</title>
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
		var elem = 'id_uscita' + id;
		var el = document.getElementById(elem);
		var uscita = el.value;
		
		elem = 'comando' + id;
		el = document.getElementById(elem);
		comando = 1;
		if (el.checked) {comando = 0;}
		
		elem = 'ora' + id;
		el = document.getElementById(elem);
		ora = el.value;
		
		elem = 'minuti' + id;
		el = document.getElementById(elem);
		minuti = el.value;
		elem = 'crepuscolare' + id;
		el = document.getElementById(elem);
		crepuscolare = 1;
		if (el.checked) {crepuscolare = 0;}
		
		elem = 'offset' + id;
		el = document.getElementById(elem);
		offset = el.value;
		elem = 'descrizione' + id;
		el = document.getElementById(elem);
		descrizione = el.value;
		//Controlla valori immessi
		if (ora < 0 | ora > 23){
			alert("Ora fuori range");
			return;
		}
		if (minuti < 0 | minuti > 59){
			alert("Minuti fuori range");
			return;
		}
		if (offset < -120 | offset > 120){
			alert("Offset fuori range min -120 max 120 minuti");
			return;
		}
		if (descrizione.length > 49){
			alert("Descrizione troppo lunga max 50 caratteri");
			return;
		}
		//Scrive nel database
		wr_http.open("POST","Db_wr.php", true);
		wr_http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		wr_http.send("funzione=update_tbl_timer" +
					"&tabella=ardu1_domo_tbl_Timer" +
					"&id=" + id + 
					"&uscita=" + uscita +
					"&comando=" + comando +
					"&ora=" + ora +
					"&minuti=" + minuti +
					"&crepuscolo=" + crepuscolare +
					"&offset=" + offset +
					"&descr=" + descrizione);
		//Ripristina il colore di sfondo della riga
		elem = 'riga' + id;
		el = document.getElementById(elem);
		el.style.backgroundColor = "white";
		//Fa partire il redirect dopo 1/2 secondo da quando l'intermprete JavaScript ha rilevato la funzione
		window.setTimeout("location.href = 'ardu1_timer.php';", 500);
	}
	//Cancella il record nel db
	function delete_rec_Db(id){
		var sicuro = confirm('Sei sicuro di voler procedere?');
		if (sicuro != true){
			return;
		}
		wr_http.open("POST","Db_wr.php", true);
		wr_http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		wr_http.send("funzione=delete_tbl_timer" +
					"&tabella=ardu1_domo_tbl_Timer" +
					"&id=" + id);
		//Fa partire il redirect dopo 1/2 secondo da quando l'intermprete JavaScript ha rilevato la funzione
		window.setTimeout("location.href = 'ardu1_timer.php';", 500);
 	}
	//Aggiunge nuovo record nel db
	function add_new_rec_Db(){
		wr_http.open("POST","addDb_timer.php", true);
		wr_http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		wr_http.send(null);
		//Fa partire il redirect dopo 1/2 secondo da quando l'intermprete JavaScript ha rilevato la funzione
		window.setTimeout("location.href = 'ardu1_timer.php';", 500);
 	}
	//Cambia sfondo alla riga quando un valore non è salvato
	function cng_bkcl(id){
		var elem = 'riga' + id;
		var el = document.getElementById(elem);
		el.style.backgroundColor = "yellow";
	}
</script>
<?php
	// Includo la connessione al database
	require('config.php');
	$query="SELECT * 
			FROM `ardu1_domo_tbl_Timer` 
			INNER JOIN `ardu1_domo_tbl_OUT`
			ON Uscita = dig_pin
			ORDER BY Uscita, Ora, Minuti";
	$risultati=mysql_query($query);
	echo '
		<input type="button" onclick="document.location.href=`domotic.php`;" value="Home">
		<br>
		<p align="left">
            <img name="img_logo" src="images/timer.jpg" width="102" height="102" />
        </p>
	';
	// visualizza i dati in tabella
	echo "<table border='1' cellpadding='10'>";
	echo "<tr>  <th>Uscita</th> 
				<th>Comando</th> 
				<th>Ora</th> 
				<th>Minuti</th> 
				<th style='border-right:0px none'>Crepuscolo</th> 
				<th style='border-left:0px none'>Offset</th> 
				<th>Descrizione</th> 
				<th></th> 
				<th></th>
		</tr>";
	// loop tra i risultati della query del database, visualizzandoli in tabella
	while($row = mysql_fetch_array( $risultati )) {
		// emissione del contenuto di ogni riga in una tabella
		echo '<tr id="riga'.$row["ID_Timer"].'">';
		//Uscita
			echo '<td><select id="id_uscita'.$row["ID_Timer"].'" onchange="cng_bkcl(\''.$row["ID_Timer"].'\')">';
				$query="SELECT dig_pin, descr 
					FROM `ardu1_domo_tbl_OUT`
					ORDER BY dig_pin";
				$out_sel=mysql_query($query);
				while($row_out = mysql_fetch_array( $out_sel )) {
					if ($row_out["dig_pin"] == $row["Uscita"]){
						$sel = 'selected="selected"';
					} else {
						$sel = '';
					}
					echo '<option value="'.$row_out["dig_pin"].'" '.$sel.'>'.$row_out["dig_pin"].' - '.$row_out["descr"].' </option>';
				}
			echo '	</select></td>';
		//Comando
			if($row['Comando'] == 0){
				$ck0 = "checked";
				$ck1 = "";
			} else {
				$ck0 = "";
				$ck1 = "checked";
			}
			//Controlla se il crepuscolare è attivo
			if($row['Crepuscolare'] == 0){
				echo '<td>
					<div style="visibility: visible;">
					<input type="radio" id="comando'.$row["ID_Timer"].'" style="visibility: visible;" name="comando'.$row["ID_Timer"].'" onchange="cng_bkcl('.$row['ID_Timer'].')" value="'.$row["Comando"].'" '.$ck0.'/> Off
					<br>
					<input type="radio" id="comando'.$row["ID_Timer"].'" style="visibility: visible;" name="comando'.$row["ID_Timer"].'" onchange="cng_bkcl('.$row['ID_Timer'].')" value="'.$row["Comando"].'" '.$ck1.'/> On
					</div>
				</td>';
			} else {
				echo '<td>
					<div style="visibility: hidden;">
					<input type="radio" id="comando'.$row["ID_Timer"].'" style="visibility: hidden;" name="comando'.$row["ID_Timer"].'" onchange="cng_bkcl('.$row['ID_Timer'].')" value="'.$row["Comando"].'" '.$ck0.'/> Off
					<input type="radio" id="comando'.$row["ID_Timer"].'" style="visibility: hidden;" name="comando'.$row["ID_Timer"].'" onchange="cng_bkcl('.$row['ID_Timer'].')" value="'.$row["Comando"].'" '.$ck1.'/> On
					</div>
				</td>';
			}
			
		//Ora
			//Controlla se il crepuscolare è attivo
			if($row['Crepuscolare'] == 0){
				echo '<td><input id="ora'.$row['ID_Timer'].'" type="text" style="width: 30px; visibility: visible;" 
				onchange="cng_bkcl('.$row['ID_Timer'].')" value="' . $row['Ora'] . '"></td>';
			} else {
				echo '<td><input id="ora'.$row['ID_Timer'].'" type="text" style="width: 30px; visibility: hidden;" 
				onchange="cng_bkcl('.$row['ID_Timer'].')" value="' . $row['Ora'] . '"></td>';
			}
		//Minuti
			//Controlla se il crepuscolare è attivo
			if($row['Crepuscolare'] == 0){
				echo '<td><input id="minuti'.$row['ID_Timer'].'" type="text" style="width: 30px; visibility: visible;" 
				onchange="cng_bkcl('.$row['ID_Timer'].')" value="' . $row['Minuti'] . '"></td>';
			} else {
				echo '<td><input id="minuti'.$row['ID_Timer'].'" type="text" style="width: 30px; visibility: hidden;" 
				onchange="cng_bkcl('.$row['ID_Timer'].')" value="' . $row['Minuti'] . '"></td>';
			}
		//Crepuscolare
			if($row['Crepuscolare'] == 0){
				$ckcr0 = "checked";
				$ckcr1 = "";
			} else {
				$ckcr0 = "";
				$ckcr1 = "checked";
				//cng_bkcl_crep(44);
				//f('document.getElementById("id_cell_comando'.$row["ID_Timer"].'").style.backgroundColor = "red"');
			}
			echo '<td style="border-right:0px none">
					<input type="radio" id="crepuscolare'.$row["ID_Timer"].'" name="crepuscolare'.$row["ID_Timer"].'" onchange="cng_bkcl('.$row['ID_Timer'].')" value="'.$row["crepuscolare"].'" '.$ckcr0.'/> Dis
					<br>
					<input type="radio" id="crepuscolare'.$row["ID_Timer"].'" name="crepuscolare'.$row["ID_Timer"].'" onchange="cng_bkcl('.$row['ID_Timer'].')" value="'.$row["crepuscolare"].'" '.$ckcr1.'/> Abil
				</td>';
			//echo '<td><input id="crepuscolare'.$row['ID_Timer'].'" type="text" style="width: 30px;" onchange="cng_bkcl('.$row['ID_Timer'].')" value="' . $row['Minuti'] . '"></td>';
		//Offset
			echo '<td style="border-left:0px none"><input id="offset'.$row['ID_Timer'].'" type="text" style="width: 30px;" onchange="cng_bkcl('.$row['ID_Timer'].')" value="' . $row['Offset'] . '"></td>';
		//Descrizione
			echo '<td><input id="descrizione'.$row['ID_Timer'].'" type="text" style="width: 200px;" onchange="cng_bkcl('.$row['ID_Timer'].')" value="' . $row['Descrizione'] . '"></td>';
						echo '<td><input id="save'. $row['ID_Timer'] .'" type="button" value="save" onClick="scriviDb_update('.$row['ID_Timer'].')"></a></td>';
			echo '<td><input id="delete'. $row['ID_Timer'] .'" type="button" value="delete" onClick="delete_rec_Db('.$row['ID_Timer'].')"></a></td>';
		echo "</tr>";
	}
	// chiude la tabella>
	echo "</table>";
	echo '<br><input id="add" type="button" value="add" onClick="add_new_rec_Db()">';
 	echo '
		</body>
		</html>
	';
	mysql_close();
?>
