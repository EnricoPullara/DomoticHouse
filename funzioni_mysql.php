<?php
class MysqlClass
{
    // variabili per la connessione al database
    private $nomehost = "localhost";     
    private $nomeuser = "root";          
    private $password = "password";
    private $nomedb = "db";

    // controllo sulle connessioni attive
    private $attiva = false;

// --------- funzione per la connessione a MySQL
    public function connetti() {
        if(!$this->attiva) {
            if($connessione = mysql_connect($this->nomehost,$this->nomeuser,$this->password) or die (mysql_error())) {
		$selezione = mysql_select_db($this->nomedb,$connessione) or die (mysql_error());
            }//end if connessione
        }else{
            return true;
        }//end if this
    }//end function

// ---------- funzione per l'esecuzione delle query 
    public function query($sql) {
        if(isset($this->attiva)) {
            $sql = mysql_query($sql) or die (mysql_error());
            return $sql; 
        }else{
            return false; 
        }//end if
    }//end function
 

// ---------- funzione per la scrittura nel db lo stato delle uscite
    // in base al numero ingresso/uscita (non pin)
    //$num ID tabella 255=telesegnale virtuale controllo connessione
    //$stato 1=alto 0=basso
    public function fnWrDb_out($num, $stato) {	
    	if(isset($this->attiva)) {
            $this->query("UPDATE `ardu1_domo_tbl_OUT` SET `stato` = ".$stato." WHERE (`ID` = ".$num.")");
        }//end if
    }//end function

// ---------- funzione per la scrittura nel db lo stato delle prenotazioni comandi
    // in base al numero ingresso/uscita (non pin)
    //$num ID tabella 255=telesegnale virtuale controllo connessione
    //$stato 1=alto 0=basso
    public function fnWrDb_out_pre($num, $stato) {	
    	if(isset($this->attiva)) {
			$a = $this->query("SELECT `stato` 
                            FROM ardu1_domo_tbl_OUT
							WHERE (`dig_pin` = ".$num.");");
			$r = $this->estrai($a);
			if ((int)$r->stato != $stato){
				$this->query("UPDATE `ardu1_domo_tbl_OUT` SET `stato_web` = ".$stato." WHERE (`ID` = ".$num.")");
			}        
        }//end if
    }//end function

// ---------- funzione per la scrittura nel db lo stato delle prenotazioni web
    // in base al numero ingresso/uscita (non pin)
    //$stato 1=alto 0=basso 3=nessuna variazione
    public function fnWrDb_res($num) {	
    	if(isset($this->attiva)) {
            $this->query("UPDATE `ardu1_domo_tbl_OUT` SET `stato_web` = 3 WHERE (`ID` = ".$num.")");
        }//end if
    }//end function
  
// ---------- funzione per la scrittura nel db lo stato del timer abilitazione
    // in base al numero ingresso/uscita (non pin)
    //$num ID tabella 255=telesegnale virtuale controllo connessione
    //cambia lo stato
    public function fnWrDb_timer($num) {
    	if(isset($this->attiva)) {
			$a = $this->query("SELECT `Timer` 
                            FROM ardu1_domo_tbl_OUT
							WHERE (`dig_pin` = ".$num.");");
			$r = $this->estrai($a);
			if ((int)$r->Timer == 0){
				$r = 1;
			} else {
				$r = 0;
			}
            $this->query("UPDATE `ardu1_domo_tbl_OUT` SET `Timer` = ".$r." WHERE (`dig_pin` = ".$num.")");
        }//end if
    }//end function

// ---------- funzione per aggiungere un nuovo record nel db timer 
    // in base al numero ingresso/uscita (non pin)
    //$num ID tabella 255=telesegnale virtuale controllo connessione
    //
    public function fnAddDb_timer($num) {
    	if(isset($this->attiva)) {
			$this->query("INSERT INTO ardu1_domo_tbl_Timer (`ID_Timer`, `Uscita`, `Comando`, `Ora`, `Minuti`, `Crepuscolare`, `Offset`, `Descrizione`) 
					VALUES (NULL, '".$num."', '0', '0', '0', '0', '0', '');");
        }//end if
    }//end function
	
// ---------- funzione per l'estrazione dei record 
    public function estrai($risultato) {
        if(isset($this->attiva)) {
            $r = mysql_fetch_object($risultato);
            //mysql_free_result($risultato);
            return $r;
        }else{
            mysql_free_result($risultato);
            return false; 
        }//end if
    }


    // ---------- funzione per la chiusura della connessione
    public function disconnetti() {
        if($this->attiva) {
            if(mysql_close()) {
                $this->attiva = false; 
                return true; 
            }else{
                return false; 
            }//end if mysql
		}//end if
    }
	

}//end class

function IndirizzoIpReale() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else {
		$ip=$_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}
?>