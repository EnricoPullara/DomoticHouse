<?php
/* calculate the sunset time for Lisbon, Portugal
Latitude: 38.4 North
Longitude: 9 West
Zenith ~= 90
offset: +1 GMT
*/
	// ----------inclusione del file di classe
    include "funzioni_mysql.php";
    // ----------istanza della classe   
    $data = new MysqlClass();
    // ----------chiamata alla funzione di connessione
    $data->connetti(); 
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
			$_flag_rec = false;	//flag record minuti non trovato (false)
			if (mysql_num_rows($t) == 1){				
				//Se ha trovato almeno un record
				$ti = $data->estrai($t);
				$min_rec = ($ti->Ora * 60) + $ti->Minuti;
				echo '<br>record trovato: id '.$ti->ID_Timer.
					' out '.$ti->Uscita.
					' - ora '.$ti->Ora.
					':'.$ti->Minuti.
					'<br>';
				$_flag_rec = true;	//flag record minuti trovato 		
			}

			//Controlla se c'è un flag crepuscolo impostato
			$_flag_cr = false;			
			$t = $data->query("SELECT * 
							FROM `ardu1_domo_tbl_Timer` 
							WHERE (Uscita = ".$i." AND Crepuscolare = 1)
							LIMIT 1;");	
			if (mysql_num_rows($t) == 1){	
				//C'è un flag crepuscolo impostato
				//Calcolo orario crepuscolo				
				$x = date_sunset(time(), SUNFUNCS_RET_STRING, 1, 2, 91, 1);	
				$min_cre=(substr ($x,0,2)*60) + substr ($x,3,2);
				echo '<br>crepuscolo'.$ti_cr->ID_Timer.'minuti cre cal php'.$min_cre.'<br>';
				
				$ti_cr = $data->estrai($t);				
				$min_cr = $min_cre + $ti_cr->Offset;
				echo '<br>ID '.$ti_cr->ID_Timer.' minuti cre+offset'.$min_cr.'<br>';
				//Calcola i munuti con l'ora locale
				$min_now = (date('H') * 60) + date('i');
				echo '<br> minutes now'.$min_now.'<br>';
				//controlla se è stata superata l'ora di attivazione
				if ($min_now > $min_cr){
					//L'ora è stata superata quindi deve attivare l'uscita verificando orari di spegnimento
					echo '<br> if $min_now > $min_cr <br>';
					echo '<br> minutes rec'.$min_rec.'<br>';
					echo '<br> if $flag_rec && !(($min_rec > $min_cr) && ($min_rec < $min_now))<br>';
					//Verifica che non ci sono spegnimenti dopo l'ora del crepuscolo
					// /*&& !(($min_rec > $min_cr) && ($min_rec < $min_now))*/
					if($_flag_rec && !(($min_rec > $min_cr) && ($min_rec < $min_now))){
						$flag_cr = true;
						echo '<br> Uscita crepuscolare attivata! <br>';						
					}
				}
				
				
			}
			
			//Verifica e attivazione uscita
			if (!$flag_cr){
				echo '<br> Uscita come da record! <br>';	
			}
			
		}//end if	timer
	}//end for
		 //fclose($fp);//Chiude file
    $data->disconnetti();
/*	
	
	

echo '<br>';
$O = substr ($x,0,2);
echo 'Ora '.$O;
echo '<br>';
$M = substr ($x,3,2);
echo 'Min '.$M;
$min_cre=(substr ($x,0,2)*60) + substr ($x,3,2);
echo '<br>';
echo $min_cre;
	//Apro il file
	//$fp = fopen("io.dat", "a");	
	//fwrite($fp, "--x5bgkl\n");
	//fclose($fp);
	
	$s="ciao";
echo '<select id="id_uscita" onchange="test()">';
echo '<option value="opt1">opt1</option>';
echo '<option value="opt2">opt2</option>';
echo '<option value="opt3">opt3</option>';
echo '</select>';

*/

?>