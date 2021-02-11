<?php
//Gestisce lettura scrittura db

	session_start();
    if (!isset($_SESSION['loginklmafg'])) {  
          header("Location: login.php");  
    } 
	
    // ----------inclusione del file di classe
    include "funzioni_mysql.php";
    // ----------istanza della classe
    $data = new MysqlClass();
    // ----------chiamata alla funzione di connessione
    $data->connetti();

	$myPost = array_values($_POST);
	
//Apro il file	
$fp = fopen("io.dat", "a");	
fwrite($fp, "\ninizio\n comando: ".$myPost[0].
							"\n-tabella: ".$myPost[1].
							"\n-id: ".$myPost[2].
							"\n-uscita: ".$myPost[3].
							"\n-comando: ".$myPost[4].
							"\n-ora: ".$myPost[5].
							"\n-minuti: ".$myPost[6].
							"\n-descr: ".$myPost[7]."\n");

	//Funzione da eseguire
	$_funzione = $myPost[0];
	
	//Funzione update tabella timer
	//Argomenti:
	//POST[1] = tabella
	//POST[2] = id
	//POST[3] = uscita
	//POST[4] = comando
	//POST[5] = ora
	//POST[6] = minuti
	//POST[7] = descrizione
	if ($_funzione == 'update_tbl_timer'){
		$_tabella = $myPost[1];
		$_id = (int)$myPost[2];
		$_uscita = $myPost[3];
		$_comando = $myPost[4];
		$_ora = (int)$myPost[5];	
		$_minuti = (int)$myPost[6];	
		$_crepuscolo = (int)$myPost[7];	
		$_offset = (int)$myPost[8];	
		$_descr = $myPost[9];
		
		//fwrite($fp, "\nUPDATE ".$_tabella." SET `Ora` = ".$_ora.", `Minuti` = ".$_minuti.", `Descrizione` = '".$_descr."', `Comando` = ".$_comando." WHERE (`ID_Timer` = ".$_id.")";
		fwrite($fp, "UPDATE ".$_tabella.
					" SET `Uscita` = ".$_uscita.
					", `Comando` = ".$_comando.
					", `Ora` = ".$_ora.
					", `Minuti` = ".$_minuti.
					", `Descrizione` = '".$_descr.
					"' WHERE (`ID_Timer` = ".$_id.")");
					
		$data->query("UPDATE ".$_tabella.
					" SET `Uscita` = ".$_uscita.
					", `Comando` = ".$_comando.
					", `Ora` = ".$_ora.
					", `Minuti` = ".$_minuti.
					", `Crepuscolare` = ".$_crepuscolo.
					", `Offset` = ".$_offset.
					", `Descrizione` = '".$_descr.
					"' WHERE (`ID_Timer` = ".$_id.")");
	}
    
	//Funzione delete record tabella timer
	//Argomenti:
	//POST[1] = tabella
	//POST[2] = id
	if ($_funzione == 'delete_tbl_timer'){
		$_tabella = $myPost[1];
		$_id = (int)$myPost[2];
		$data->query("DELETE FROM ".$_tabella." WHERE (`ID_Timer` = ".$_id.")");
	}

	//Funzione delete record tabella user
	//Argomenti:
	//POST[1] = tabella
	//POST[2] = id
	if ($_funzione == 'delete_tbl_user'){
		$_tabella = $myPost[1];
		$_id = (int)$myPost[2];
		$data->query("DELETE FROM ".$_tabella." WHERE (`ID_user` = ".$_id.")");
	}
fclose($fp);
	
    $data->disconnetti();
?>