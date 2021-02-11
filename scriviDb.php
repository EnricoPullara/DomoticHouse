<?php
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

    $_pin = (int)$_GET['pin'];
    $_stato = (int)$_GET['stato'];
    
    $data->fnWrDb_out_pre($_pin, $_stato);
    
    $data->disconnetti();
?>