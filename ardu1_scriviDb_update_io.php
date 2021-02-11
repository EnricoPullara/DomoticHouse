<?
//Arduino manda tramite GET il valore da aggiornare

    // ----------inclusione del file di classe
    include "funzioni_mysql.php";
    // ----------istanza della classe   
    $data = new MysqlClass();
    // ----------chiamata alla funzione di connessione
    $data->connetti(); 
    
	//Recupero dati da metodo GET --------------------------------
	
	$myPost = array_values($_POST);
	
	$id = $myPost[0];
	$descr = $myPost[1];
	
	$data->query("UPDATE `ardu1_domo_tbl_OUT` SET `descr` = '".$descr."' WHERE (`ID` = ".$id.")");
	
	/*
	//Apro il file
	$fp = fopen("io.dat", "a");
	
	fwrite($fp, "\nInizio\n");
	fwrite($fp, $id . "\n");
	fwrite($fp, $descr . "\n");
	
	fclose($fp);
	*/
?>	