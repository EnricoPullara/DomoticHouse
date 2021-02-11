<?
//Legge lo stato delle uscite dal database
//Incrementa e scrive il ts virtuale 255 collegamento con Arduino
//Restituisce (ts virtuale); uscite(campo0...campo13); prenot(campo0...campo13); timer(campo0...campo13)

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
    
    //Connessione Arduino ts virtuale azzerato da arduino
    $a = $data->query("SELECT `stato` 
                            FROM ardu1_domo_tbl_OUT 
                            WHERE `ID` = 255 ;");
    $r = $data->estrai($a);
    $_coll = $r->stato;

    //Aumento di 1 il valore nel db del ts virtuale collegamento
    if($r->stato < 254){
        //Scrivo nel db un valore
        $data->fnWrDb_out(255, ($r->stato + 1));
    }
        
//Outputs
    $a = $data->query("SELECT `stato` 
                            FROM ardu1_domo_tbl_OUT 
                            WHERE `ID` < 255 ;");
    $t_OUT = ""; 
    $r = $data->estrai($a);
    while ($r) {
        $t_OUT = $t_OUT . $r->stato . ",";

        $r = $data->estrai($a);
    }
    //$t_OUT = $t_OUT . ",";
    $t_OUT = substr($t_OUT,0,(strlen($t_OUT)-1));	//Toglie la virgola finale
	
//Outputs prenotate
    $a = $data->query("SELECT `stato_web` 
                            FROM ardu1_domo_tbl_OUT 
                            WHERE `ID` < 255 ;");
    $t_OUT_pre = ""; 
    $r = $data->estrai($a);
    while ($r) {
        $t_OUT_pre = $t_OUT_pre . $r->stato_web . ",";

        $r = $data->estrai($a);
    }
    //$t_OUT = $t_OUT . ",";
    $t_OUT_pre = substr($t_OUT_pre,0,(strlen($t_OUT_pre)-1));	//Toglie la virgola finale
	
//Timer
    $a = $data->query("SELECT `Timer` 
                            FROM ardu1_domo_tbl_OUT 
                            WHERE `ID` < 255 ;");
    $t_Timer = ""; 
    $r = $data->estrai($a);
    while ($r) {
        $t_Timer = $t_Timer . $r->Timer . ",";

        $r = $data->estrai($a);
    }
    $t_Timer = substr($t_Timer,0,(strlen($t_Timer)-1));	//Toglie la virgola finale
	
    $data->disconnetti();
    
    echo $_coll . ";" . $t_OUT. ";" . $t_OUT_pre. ";" . $t_Timer;//valore restituito dalla funzione php */
?>