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
	
// Includo la connessione al database
require('config.php');

// Se il modulo viene inviato...
if(isset($_POST['registra']))
{
    
    // Dati Inviati dal modulo
    $user = (isset($_POST['user'])) ? trim($_POST['user']) : '';    // Metto nella variabile 'user' il dato inviato dal modulo, se non viene inviato d� di default ''
    $pass = (isset($_POST['pass'])) ? trim($_POST['pass']) : '';    // Metto nella variabile 'pass' il dato inviato dal modulo, se non viene inviato d� di default ''
    $mail = (isset($_POST['mail'])) ? trim($_POST['mail']) : '';    // Metto nella variabile 'mail' il dato inviato dal modulo, se non viene inviato d� di default ''
	$rights = (isset($_POST['Rights'])) ? trim($_POST['Rights']) : '';    // Metto nella variabile 'mail' il dato inviato dal modulo, se non viene inviato d� di default ''
    
    // Filtro i dati inviati se i magic_quotes del server sono disabilitati per motivi di sicurezza
    if (!get_magic_quotes_gpc()) {
        $user = addslashes($user);
        $pass = addslashes($pass);
        $mail = addslashes($mail);
    }
    
    // Controllo il Nome Utente
    if(strlen($user) < 4 || strlen($user) > 12)
        die('<br><br>Nome Utente troppo corto, o troppo lungo');
    // Controllo la Password
    elseif(strlen($pass) < 4 || strlen($pass) > 12)
        die('<br><br>Password troppo corta, o troppo lunga');
    // Controllo l'email
    elseif(!eregi("^[a-z0-9][_\.a-z0-9-]+@([a-z0-9][0-9a-z-]+\.)+([a-z]{2,4})", $mail))
        die('<br><br>Email non valida');
    // Controllo il nome utente non sia gi� occupato
    elseif(mysql_num_rows(mysql_query("SELECT user FROM domo_tbl_user WHERE user = '$user' LIMIT 1")) == 1)
        die('<br><br>Nome Utente non disponibile');
    // Controllo l'indirizzo email non sia gi� registrato
    elseif(mysql_num_rows(mysql_query("SELECT mail FROM domo_tbl_user WHERE mail = '$mail' LIMIT 1")) == 1)
        die('<br><br>Questo indirizzo email risulta gi&agrave; registrato ad un altro utente');
    // Registrazione dell'utente nel database
    else
    {
        
        // Crypt della password per garantire una miglior sicurezza
        $pass = md5($pass);
        
        // Query per l'inserimento dell'utente nel database
        $strSQL = "INSERT INTO domo_tbl_user (user,pass,mail,Diritti)";
        $strSQL .= "VALUES('$user', '$pass', '$mail', '$rights')";
        mysql_query($strSQL) OR die("Errore 003, contattare l'amministratore ".mysql_error());
        
        // Reindirizzo l'utente ad una pagina di conferma della registrazione
        header('Location: registrato.php');
        exit;
    }
}
echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Gestione utenti</title>
	</head>
	<body onLoad="fn_init()">
		<input type="button" onclick="document.location.href=`domotic.php`;" value="Home">
		<p align="left">
            <img name="img_logo" src="images/utenti_1.jpg" width="102" height="102" />
        </p>
		<H1>Nuovo utente</H1>
		<form action="" method="post">
			<p>
				Nome utente: 
				<input name="user" type="text" id="user";" /><br />
			</p>
			<p>
				Password: 
				<input name="pass" type="password" id="pass";" /><br />
			</p>
			<p>
				Email: 
				<input name="mail" type="text" id="mail";" /><br />
			</p>
			<p>
				Diritti:
				<input type="radio" name="Rights" value="0" /> Admin &nbsp;&nbsp;&nbsp;
				<input type="radio" name="Rights" value="1" checked="checked" /> User &nbsp;&nbsp;&nbsp;
				<input type="radio" name="Rights" value="2" /> Guest
			</p>
			<input name="registra" type="submit" value="Registra" /><br />
		</form>
		<br>
		';
	echo "<table border='1' cellpadding='10'>";
	echo "<tr>   
				<th>Nome utente</th> 
				<th>email</th> 
				<th>Diritti</th> 
				<th></th>
		</tr>";
	//echo "<pre><b>ID&#9;Nome utente&#9;email&#9;&#9;Diritti</b></pre>";
$query="SELECT * FROM domo_tbl_user ORDER BY Diritti, user";
$risultati=mysql_query($query);
$num=mysql_numrows($risultati);
	$i=0;
	while ($i < $num) {
		$ID_user=mysql_result($risultati,$i,"ID_user");
		$user=mysql_result($risultati,$i,"user");
		$mail=mysql_result($risultati,$i,"mail");
		$diritti=mysql_result($risultati,$i,"Diritti");
		if ($diritti == 0)
			$d="Admin";
		else if ($diritti == 1)
			$d="User";
		else
			$d="Guest";
		
		if ($diritti == 0){
			echo '<tr id="riga'.$ID_user.'" style="color: red; font-weight: bold;">';
		} else {
			echo '<tr id="riga'.$ID_user.'">';
		}
		
		echo '<td>'.$user.'</td>';
		echo '<td>'.$mail.'</td>';
		echo '<td>'.$d.'</td>';
		/*
		if ($diritti == 0)
			echo "<pre><font color=�FF0000�><b>$ID_user&#9;$user&#9;&#9;$mail&#9;Admin</b></font>";
		else if ($diritti == 1)
			echo "<pre><font color=�008800�>$ID_user&#9;$user&#9;&#9;$mail&#9;User</font></pre>";
		else
			echo "<pre><font color=�008800�>$ID_user&#9;$user&#9;&#9;$mail&#9;Guest</font></pre>";
		*/
		echo '<td>
			<input id="delete'. $ID_user .'" type="button" value="delete" onClick="delete_rec_Db('.$ID_user.')">
			</td>';
		
		$i++;
		echo '</tr>';
		//echo "<hr align=�left� size=�1? width=�100? color=�#FEFEFE� noshade>";
	}	
	// chiude la tabella>
	echo "</table>";
	
	echo "</body>
</html>";
?>
<script type="text/javascript">
	function fn_init(){
		xmlhttp = new XMLHttpRequest();
        wr_http = new XMLHttpRequest();
    }
	//Cancella il record nel db
	function delete_rec_Db(id){
		var sicuro = confirm('Sei sicuro di voler procedere?');
		if (sicuro != true){
			return;
		}
		wr_http.open("POST","Db_wr.php", true);
		wr_http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		wr_http.send("funzione=delete_tbl_user" +
					"&tabella=domo_tbl_user" +
					"&id=" + id);
		//Fa partire il redirect dopo 1/2 secondo da quando l'intermprete JavaScript ha rilevato la funzione
		window.setTimeout("location.href = 'registrati.php';", 500);
 	}
</script>