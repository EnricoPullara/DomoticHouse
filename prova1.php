<?php
	// ----------inclusione del file di classe
    include "funzioni_mysql.php";
    // ----------istanza della classe   
    $data = new MysqlClass();
    // ----------chiamata alla funzione di connessione
    $data->connetti(); 
	
	
?>