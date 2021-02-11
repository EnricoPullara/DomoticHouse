<?
//Arduino manda tramite POST lo stato delle uscite ed il reset prenotazioni comandi
//Se almeno qualche dato supera il checksum resetta il ts virtuale collegamento ok
//La pagina restituisce ad arduino le evuntali prenotazioni ed i 4 byte di checksum
    // ----------inclusione del file di classe
    include "funzioni_mysql.php";
    // ----------istanza della classe   
    $data = new MysqlClass();
    // ----------chiamata alla funzione di connessione
    $data->connetti(); 
	//Recupero dati da metodo POST --------------------------------
	$myPost = array_values($_POST);
	
	//Apro il file
//	$fp = fopen("ioz.dat", "a");
//	fwrite($fp, "\n\nInizio");
	/*for ($_i = 0; $_i < 27; $_i++){
		fwrite($fp, $_i." - ".(int)$myPost[$_i]."\n");
	}
	fclose($fp);
	*/
//Legge da POST inviato da Arduino
	//Stato delle uscite 0-11
	for ($_i = 0; $_i < 11; $_i++){
        $_O[$_i] = (int)$myPost[$_i];
    }
	//Byte di controllo uscite
	$_O_CHK[1] = (int)$myPost[11];
	$_O_CHK[2] = (int)$myPost[12];
	//Stato dei reset prenotazioni web 0-11
	for ($_i = 13; $_i < 25; $_i++){
        $_R[$_i-13] = (int)$myPost[$_i];
    }
	//Byte di controllo uscite
	$_R_CHK[1] = (int)$myPost[25];
	$_R_CHK[2] = (int)$myPost[26];
//Calcola i checksum per il confronto con quelli ricevuti
	//Checksum Uscite 0-7
    $bit = 1;
    $_O_CHK_cal[1] = 0;
	for ($_i = 0; $_i < 8; $_i++){
        if ( $_O[$_i] == 1) $_O_CHK_cal[1] = $_O_CHK_cal[1] | $bit;
        $bit = $bit << 1;
    }
	//Checksum Uscite 8-10
    $bit = 1;
    $_O_CHK_cal[2] = 0;
	for ($_i = 8; $_i < 11; $_i++){
        if ( $_O[$_i] == 1) $_O_CHK_cal[2] = $_O_CHK_cal[2] | $bit;
        $bit = $bit << 1;
    }
	//Checksum Reset 0-7
    $bit = 1;
    $_R_CHK_cal[1] = 0;
	for ($_i = 0; $_i < 8; $_i++){
        if ( $_R[$_i] == 1) $_R_CHK_cal[1] = $_R_CHK_cal[1] | $bit;
        $bit = $bit << 1;
    }
	//Checksum Reset 8-10
    $bit = 1;
    $_R_CHK_cal[2] = 0;
	for ($_i = 8; $_i < 11; $_i++){
        if ( $_R[$_i] == 1) $_R_CHK_cal[2] = $_R_CHK_cal[2] | $bit;
        $bit = $bit << 1;
    }
//Controlla i dati ricevuti 
	//scrive lo stato delle uscite 0-7 nel db
	if ($_O_CHK[1] == $_O_CHK_cal[1]){
		$data->fnWrDb_out(255, 0);	//Azzera il ts virtuale, connessione arduino OK
		for ($_i = 0; $_i < 8; $_i++){
			$data->fnWrDb_out((int)$_i, (int)$_O[$_i]);
		}
	}
	//scrive lo stato delle uscite 8-10 nel db
	if ($_O_CHK[2] == $_O_CHK_cal[2]){
		$data->fnWrDb_out(255, 0);	//Azzera il ts virtuale, connessione arduino OK
		for ($_i = 8; $_i < 11; $_i++){
			$data->fnWrDb_out((int)$_i, (int)$_O[$_i]);
		}
	}
	//resetta lo stato delle prenotazioni comandi 0-7 nel db
	if ($_R_CHK[1] == $_R_CHK_cal[1]){
		$data->fnWrDb_out(255, 0);	//Azzera il ts virtuale, connessione arduino OK
		for ($_i = 0; $_i < 8; $_i++){
			if (((int)$_R[$_i]) == 1){ 
				$data->fnWrDb_res($_i);
			}
		}
	}
	//resetta lo stato delle prenotazioni comandi 8-10 nel db
	if ($_R_CHK[2] == $_R_CHK_cal[2]){
		$data->fnWrDb_out(255, 0);	//Azzera il ts virtuale, connessione arduino OK
		for ($_i = 8; $_i < 11; $_i++){
			if (((int)$_R[$_i]) == 1){ 
				$data->fnWrDb_res((int)$_i);
			}
		}
	}
//Invio dati dal db ad Arduino	
//Legge dal db lo stato delle richieste di variazione (stato web)
	//Legge solo i pin da 0 a 10
	$a = $data->query("SELECT `stato_web` 
                            FROM ardu1_domo_tbl_OUT
							WHERE (`dig_pin`<11);");
    $t_Or[11]; 
    $count = 0;
    $r = $data->estrai($a);
    while ($r) {
        $t_Or[$count] = $r->stato_web;
        $r = $data->estrai($a);
		$count++;
	}
	//Calcola i quattro byte di checksum
	//[0-7]bit 1 byte di checksum per lo stato ed un byte di checksum per abilitare la richiesta 
	$bit = 1;
    $_Or_chk_1 = 0;	//Stato bit abilitazione web
	$_Or_chk_1a = 0;//Abilitazione web
    for ($_i = 0; $_i < 8; $_i++){
		if ( $t_Or[$_i] == 1) $_Or_chk_1 = $_Or_chk_1 | $bit;
		if ( $t_Or[$_i] != 3) $_Or_chk_1a = $_Or_chk_1a | $bit;
        $bit = $bit << 1;
    }
	//[8-11]bit 1 byte di checksum per lo stato ed un byte di checksum per abilitare la richiesta 
	$bit = 1;
    $_Or_chk_2 = 0;	//Stato bit abilitazione web
	$_Or_chk_2a = 0;//Abilitazione web
    for ($_i = 8; $_i < 11; $_i++){
		if ( $t_Or[$_i] == 1) $_Or_chk_2 = $_Or_chk_2 | $bit;
		if ( $t_Or[$_i] != 3) $_Or_chk_2a = $_Or_chk_2a | $bit;
        $bit = $bit << 1;
    }	
	//Risposta da web ad Arduino
	//Invia le richieste variazioni uscite (Or)
	// 0 = richiesta spegnimento
	// 1 = richiesta accensione
	// 3 = nessuna variazione
	echo "--x5bgkl";
	echo "Or0:". $t_Or[0]."Or1:". $t_Or[1]."Or2:".  $t_Or[2]."Or3:".  $t_Or[3].
		 "Or4:". $t_Or[4]."Or5:". $t_Or[5]."Or6:".  $t_Or[6]."Or7:".  $t_Or[7].
		 "Or8:". $t_Or[8]."Or9:".$t_Or[9]."Or10:".$t_Or[10];
	echo "Or_chk_1:".$_Or_chk_1."Or_chk_1a:".$_Or_chk_1a.
		 "Or_chk_2:".$_Or_chk_2."Or_chk_2a:".$_Or_chk_2a."end";                 
						
	//Gestione timer ----------------------------------
	//Imposta l'orario del crepuscolo nei campi con l'opzione abilitata
	$x = date_sunset(time(), SUNFUNCS_RET_STRING, 37.631347, 13.478733, 91, 1);
	$min_cre=(substr ($x,0,2)*60) + substr ($x,3,2);
	$O = substr ($x,0,2);
	$M = substr ($x,3,2);

	//Calcola i munuti con l'ora locale
	$min_now = (date('H') * 60) + date('i');
//fwrite($fp, "\nmin now:".$min_now);				
	//Gestione uscite
	for ($i = 2; $i < 11; $i++){
		//Controlla se nell'uscita è abilitato il timer
		$a = $data->query("SELECT Timer 
							FROM ardu1_domo_tbl_OUT	
							WHERE dig_pin=".$i);
		$r = $data->estrai($a);
		if ((int)$r->Timer == 1){
			//Timer impostato
			//Preleva l'ultimo record utile			
			//non tiene conto di un eventuale flag crepuscolo impostato
			$_flag_rec = false;	//flag record minuti non trovato (false)
			$t = $data->query("SELECT * 
								FROM `ardu1_domo_tbl_Timer` 
								WHERE (((Ora * 60 + Minuti) <= (EXTRACT(HOUR FROM now())*60 + EXTRACT(MINUTE FROM now()))) 
										AND Uscita = ".$i." AND Crepuscolare = 0)
								ORDER BY Ora DESC, Minuti DESC 
								LIMIT 1;");		
			if (mysql_num_rows($t) == 0){				
			//Se non ha trovato records				
			//Preleva l'ultimo record utile partendo da mezzanotte
				$t = $data->query("SELECT * 
								FROM `ardu1_domo_tbl_Timer` 
								WHERE (((Ora * 60 + Minuti) <= 1440) 
										AND Uscita = ".$i." AND Crepuscolare = 0)
								ORDER BY Ora DESC, Minuti DESC 
								LIMIT 1;");
			}						
			if (mysql_num_rows($t) == 1){				
				//Se ha trovato almeno un record
				$ti_rec = $data->estrai($t);
				$min_rec = ($ti_rec->Ora * 60) + $ti_rec->Minuti;
				$_flag_rec = true;	//flag record minuti trovato 

				//Normalizza i tempi con orario now come 0 (inizio)
				if($min_rec > $min_now){
					$min_rec = $min_rec - $min_now;
				} else {
					$min_rec = (1440 - $min_now) + $min_rec;
				}
			}
//fwrite($fp, "\nout:".$i." rec:".$min_rec." ora:".$ti_rec->Ora);
			//Controlla se c'è un flag crepuscolo impostato
			$_flag_cr = false;			
			$t = $data->query("SELECT * 
							FROM `ardu1_domo_tbl_Timer` 
							WHERE (Uscita = ".$i." AND Crepuscolare = 1)
							LIMIT 1;");	
			if (mysql_num_rows($t) == 1){	
				//C'è un flag crepuscolo impostato
				$_flag_cr = true;
				$ti_cr = $data->estrai($t);				
				$min_cr = $min_cre + $ti_cr->Offset;

				//Normalizza i tempi con orario now come 0 (inizio)
				if($min_cr > $min_now){
					$min_cr = $min_cr - $min_now;
				} else {
					$min_cr = (1440 - $min_now) + $min_cr;
				}
//fwrite($fp, "\nout:".$i." crep:".$min_cr." ora:".$ti_cr->Ora);
			}//End crepuscolo
			
//fwrite($fp, "\nflag_rec:".$_flag_rec." flag_cr:".$_flag_cr);	

			//Esecuzione timer in base al record e al crepuscolare
			//Se non c'è nessun un record e crepuscolare impostato
			if(!$_flag_rec && !$_flag_cr){}
			
			//Se c'è il record ma non il crepuscolare impostato
			if($_flag_rec && !$_flag_cr){
				$ti_c = (int)$ti_rec->Comando;				
				$data->fnWrDb_out_pre($i, $ti_c);
			}
			
			//Se non c'è nessun un record ma il crepuscolare è impostato
			if(!$_flag_rec && $_flag_cr){
				$data->fnWrDb_out_pre($i, 1);
			}
			
			//Se c'è un record e crepuscolare impostato
			if($_flag_rec && $_flag_cr){
				if($min_rec < $min_cr){
					$data->fnWrDb_out_pre($i, 1);
				} else {
					$ti_c = (int)$ti_rec->Comando;				
					$data->fnWrDb_out_pre($i, $ti_c);
				}
			}
			
		}//end if timer==1 timer impostato
		//fwrite($fp, "\nfine for out:".$i);
	}//end for	 

    $data->disconnetti();
	
		//fclose($fp);
?>