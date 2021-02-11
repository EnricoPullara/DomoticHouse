<?php

session_start();

// Includo la connessione al database
//require('config.php');

// Includo le funzioni per il recupero ip
include "funzioni_mysql.php";
// ----------istanza della classe   
$data = new MysqlClass();
// ----------chiamata alla funzione di connessione
$data->connetti();

    
// Se il modulo viene inviato...
if(isset($_POST['Login']))
{
    // Dati Inviati dal modulo
    $user = (isset($_POST['user'])) ? trim($_POST['user']) : '';    // Metto nella variabile 'user' il dato inviato dal modulo, se non viene inviato d� di default ''
    $pass = (isset($_POST['pass'])) ? trim($_POST['pass']) : '';    // Metto nella variabile 'pass' il dato inviato dal modulo, se non viene inviato d� di default ''
    
    // Filtro i dati inviati se i magic_quotes del server sono disabilitati per motivi di sicurezza
    if (!get_magic_quotes_gpc()) {
        $user = addslashes($user);
        $pass = addslashes($pass);
    }
    
    //Memorizzo la password in chiaro per il log
    $pass_ch = $pass;
    // Crypto la password e la confronto con quella nel database
    $pass = md5($pass);
    
    // Controllo l'utente esiste
    $query = mysql_query("SELECT ID_user, Diritti FROM domo_tbl_user WHERE user = '$user' AND pass = '$pass' LIMIT 1");

    // Se ha trovato un record
    if(mysql_num_rows($query) == 1)
    {
        // prelevo l'id utente dal database
        //$login = mysql_fetch_array($query);      
		$login = mysql_result($query,0,"ID_user");
		
		// prelevo i diritti dell'utente dal database     
		$diritti = mysql_result($query,0,"Diritti");
		
        // Creo una variabile di sessione
        $_SESSION['loginklmafg'] = "enricoz123";//$login['ID_user'];
        $_SESSION['user'] = $user;
        $_SESSION['ip'] = IndirizzoIpReale();
		$_SESSION['dir'] = $diritti;
        $ip = $_SESSION['ip'];

       // Query per l'inserimento dell'utente nel database log login ok
        $strSQLlog = "INSERT INTO domo_tbl_log_IP (IP_client, Utente, descr)";
        $strSQLlog .= "VALUES('$ip', '$user', 'Login OK')";
        mysql_query($strSQLlog) OR die("Errore 003, contattare l'amministratore ".mysql_error());

        // reindirizzo l'utente
        header('Location: domotic.php');
        exit;
    }
    // se non esiste da l'errore
    else {
	$ip = IndirizzoIpReale();
        // Query per l'inserimento dell'utente nel database log login ok
        $strSQLlog = "INSERT INTO domo_tbl_log_IP (IP_client, Utente, password, descr)";
        $strSQLlog .= "VALUES('$ip', '$user', '$pass_ch', 'Login KO')";
        mysql_query($strSQLlog) OR die("Errore 003, contattare l'amministratore ".mysql_error());

	header('Location: nopage.html');
    	exit;
        die('Nome Utente o Password errati');
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login</title>
	<style type="text/css">

body {
    background-color: #efefef;
}

.rounded {
    width: 400px;
    height: 330px;
    background-color: white;
    color: #100400;
    text-height: 30px;
    margin: 20px auto;
    padding: 20px;
    
    border: 5px solid #E63302;
    
    
    border-radius: 20px;
    -moz-border-radius: 20px;
    -webkit-border-radius: 20px;
    
}
    
    </style>
    
</head>

<body>
    <div class="rounded">
        <p align="center">
            <img name="img_logo" src='images/home.jpg' width="340" height="224" />
        </p>
            <form action="" method="post">
                <table>
                    <tr>
                        <font size="+2" color="black">
                        <td>
                            User
                        </td> 
                        <td>
                            <input name="user" type="text" id="user" value="Nome Utente" onfocus="if(this.value=='Nome Utente') this.value='';" /><br />
                        </td>  
                        </font>
                    </tr>
                    <tr>
                        <td>
                            Password
                        </td>
                        <td>
                            <input name="pass" type="password" id="pass" value="Password" onfocus="if(this.value=='Password') this.value='';" /><br />
                        </td>    
                    </tr>
                    <tr>
                        <td>
                            <input name="Login" type="submit" value="Login" /><br />
                        </td>
                    </tr>
                </table>
                </H2>
                </table>
            </form>
        
    </div>
</body>
</html>