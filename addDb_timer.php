<?php
//Aggiunge record tabella timer
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
    
    $data->fnAddDb_timer(1);
    
    $data->disconnetti();
?>