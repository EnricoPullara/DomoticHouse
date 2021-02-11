<?php  
    session_start();  
    if (!isset($_SESSION['loginklmafg'])) { 
        header("Location: login.php");  
    } 
?> 
<!DOCTYPE html>
 <html>
    <head>
        <title>Home control</title>
        <style>
            @import url(tile.css);
        </style>
        <script type="text/javascript">
            setInterval("temporFn()", 500); //Aggiorna ogni 1/2 secondo
            //------ Funzione che viene richiamata ogni x secondi
            function temporFn() {
                orologio();
                read_io();
            }			
            function fn_init(){
                xmlhttp = new XMLHttpRequest();
                wr_http = new XMLHttpRequest();
                read_io();
            }			
         //------ Legge i dati IO dal file read_data.php
            function read_io(){
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        var msg = xmlhttp.responseText;
                        var arr_row_response = msg.split(";");//Divide i records coll - Out - prenotaz
                        var arr_row_OUT = arr_row_response[1].split(",");//Divide i records Out
						var arr_row_OUT_pre = arr_row_response[2].split(",");//Divide i records prenotaz
						var arr_row_timer = arr_row_response[3].split(",");//Divide i records timer
                        page_update(arr_row_response[0], arr_row_OUT, arr_row_OUT_pre, arr_row_timer);
                    }
                }
                xmlhttp.open("GET","read_data.php",true);
                xmlhttp.send();
            }
         //------ Aggiorna le immagini           
            function page_update(coll, arr_row_OUT, arr_row_OUT_pre, arr_row_timer){
            //Stato collegamento
                //Ingresso virtuale 255 pin 255
                if (coll < 20)
                    document.img_coll.src='images/link-ok.jpg';
                else
                    document.img_coll.src='images/link-ko.jpg';
                document.getElementById('coll').textContent = coll;
			//Test
				//var el = document.getElementById('timer_2');
                //el.src = "images/timer_On.gif";
				//document.timer_2.src='images/timer_On.gif';
            //Stato uscite     
                //Uscita 2 pin 2
                if (arr_row_OUT[2] == 1)
                    document.img_out1.src='images/lamp_on.gif';
                else
                    document.img_out1.src='images/lamp_off.gif';
				//gestione prenotazioni
				var el = document.getElementById('out2');
				if (arr_row_OUT_pre[2] == 0)
                    el.textContent = "->OFF";
                if (arr_row_OUT_pre[2] == 1)
                    el.textContent = "->ON";
				if (arr_row_OUT_pre[2] == 3)
                    el.textContent = "";		
				//gestione timer
				var el = document.getElementById('timer_2');
				if (arr_row_timer[2] == 1) {
					el.src='images/timer_On.gif';
				} else{
					el.src='images/timer_Off.gif';
				}
                //Uscita 3 pin 3
                if (arr_row_OUT[3] == 1)
                    document.img_out2.src='images/lamp_on.gif';
                else
                    document.img_out2.src='images/lamp_off.gif';		
				//gestione prenotazioni
				var el = document.getElementById('out3');
				if (arr_row_OUT_pre[3] == 0)
                    el.textContent = "->OFF";
                if (arr_row_OUT_pre[3] == 1)
                    el.textContent = "->ON";
				if (arr_row_OUT_pre[3] == 3)
                    el.textContent = "";
				//gestione timer
				var el = document.getElementById('timer_3');
				if (arr_row_timer[3] == 1) {
					el.src='images/timer_On.gif';
				} else{
					el.src='images/timer_Off.gif';
				}		
                //Uscita 4 pin 4
                if (arr_row_OUT[4] == 1)
                    document.img_out3.src='images/lamp_on.gif';
                else
                    document.img_out3.src='images/lamp_off.gif';
				
				//gestione prenotazioni
				var el = document.getElementById('out4');
				if (arr_row_OUT_pre[4] == 0)
                    el.textContent = "->OFF";
                if (arr_row_OUT_pre[4] == 1)
                    el.textContent = "->ON";
				if (arr_row_OUT_pre[4] == 3)
                    el.textContent = "";
                //Uscita 5 pin 5
                if (arr_row_OUT[5] == 1)
                    document.img_out4.src='images/lamp_on.gif';
                else
                    document.img_out4.src='images/lamp_off.gif';
				//gestione prenotazioni
				el = document.getElementById('out5');
				if (arr_row_OUT_pre[5] == 0)
                    el.textContent = "->OFF";
                if (arr_row_OUT_pre[5] == 1)
                    el.textContent = "->ON";
				if (arr_row_OUT_pre[5] == 3)
                    el.textContent = "";
            }

         //------ Scrive pin nel db
            function scriviDb(pin, stato){
                wr_http.open("GET","scriviDb.php?pin=" + pin + "&stato=" + stato,true);
                wr_http.send(null);
            }
		 //------ Cambia stato pin timer nel db
            function scriviDb_timer(pin){
                wr_http.open("GET","scriviDb_timer.php?pin=" + pin, true);
                wr_http.send(null);
            }
         //------ Scrive pin multipli nel db
         // accetta pin, stato, .... 
            function scriviDb_m(){
				var i = 0;
				var p = 0;
				var get_str = "scriviDb_m.php?";
				do {
					if( i == 0){
						get_str = get_str + "pin" + p + "=" + arguments[i];
					} else {
						get_str = get_str + "&pin" + p + "=" + arguments[i];
					}
					i++;
					if(arguments[i] == 'H'){
						get_str = get_str + "&stato" + p + "=" + '1';
					} else {
						get_str = get_str + "&stato" + p + "=" + '0';
					}
					i++;
					p++;
				} while (i < arguments.length);
				get_str = get_str + "&num=" + p;
				wr_http.open("GET",get_str,true);
				wr_http.send(null);
            }
         //------ Orologio
            function orologio() {
                var el = document.getElementById('orologio');
                el.textContent = new Date().toLocaleString();
            }
	</script>
    </head>
		<?php
			echo "Crepuscolo: ".date_sunset(time(), SUNFUNCS_RET_STRING, 37.631347, 13.478733, 91, 1);
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
	echo '
		<body onLoad="fn_init()">
	';
	echo'
			<div class="box-1">
					<img src="images/Pianta_top.jpg" alt=""/>
	';
	echo'
			<! Test >
				<div id="test" class="box-2" style="left: 18px; top: 434px;">
					Test. . .
				</div>
	';
	echo'		
			<!Lampada fanalone strada OUT 2 pin 2>
				<div class="box-2" style="left: 76px; top: 290px; color: yellow;">
					Fanalone strada
				</div>
				<div id="out2" class="box-2" style="font-size: 10px; left: 74px; top: 310px;color: yellow;">
				</div>
				<div class="box-2" style="left: 120px; top: 312px;">
					<img name="img_out1" src="images/lamp_off.gif"/>
				</div>
				<div class="box-2" style="left: 150px; top: 304px;">
				';
				if ($_SESSION['dir'] < 2){
					echo '
					<button type="button" class="btn" id="OUT1on" onClick="scriviDb(2,1)">ON</button><br>
					<button type="button" class="btn" id="OUT1off" onClick="scriviDb(2,0)">OFF</button>
					';
				}
	echo'
				</div>
				<!Timer pin 2>
				<div class="box-2" style="left: 102px; top: 312px;">
					<input id="timer_2" type="image" src="" onClick="scriviDb_timer(2)">
				</div>
	';
	echo'
			 <!Lampada Ingresso 1 piano OUT 3 pin 3>
				<div class="box-2" style="left: 146px; top: 188px; color: yellow;">
					Portone 1P
				</div>
				<div id="out3" class="box-2" style="font-size: 10px; left: 144px; top: 208px; color: yellow;">
				</div>
				<div class="box-2" style="left: 190px; top: 210px;">
					<img name="img_out2" src="images/lamp_off.gif"/>
				</div>
				<div class="box-2" style="left: 220px; top: 202px;">
	';
				if ($_SESSION['dir'] < 2){
					echo '
					<button type="button" class="btn" id="OUT2on" onClick="scriviDb(3,1)">ON</button><br>
					<button type="button" class="btn" id="OUT2off" onClick="scriviDb(3,0)">OFF</button>
					';
				}
	echo'
				</div>
				<!Timer pin 3>
				<div class="box-2" style="left: 172px; top: 210px;">
					<input id="timer_3" type="image" src="" onClick="scriviDb_timer(3)">
				</div>
	';
	echo'
			 <!Lampada sala pranzo OUT 4 pin 4>
				<div class="box-2" style="left: 120px; top: 460px;">
					Out 4
				</div>
				<div id="out4" class="box-2" style="font-size: 10px; left: 120px; top: 475px;">
				</div>
				<div class="box-2" style="left: 164px; top: 460px;">
					<img name="img_out3" src="images/lamp_off.gif"/>
				</div>
				<div class="box-2" style="left: 194px; top: 452px;">
	';
				if ($_SESSION['dir'] < 2){
					echo '
					<button type="button" class="btn" id="OUT3on" onClick="scriviDb(4,1)">ON</button><br>
					<button type="button" class="btn" id="OUT3off" onClick="scriviDb(4,0)">OFF</button>
					';
				}
	echo'
				</div>
	';
	echo'
			 <!Lampada ext tettoia OUT 5 pin 5>
				<div class="box-2" style="left: 120px; top: 510px;">
					Out 5
				</div>
				<div id="out5" class="box-2" style="font-size: 10px; left: 120px; top: 525px;">
				</div>
				<div class="box-2" style="left: 164px; top: 510px;">
					<img name="img_out4" src="images/lamp_off.gif"/>
				</div>
				<div class="box-2" style="left: 194px; top: 502px;">
	';
				if ($_SESSION['dir'] < 2){
					echo '
					<button type="button" class="btn" id="OUT4on" onClick="scriviDb(5,1)">ON</button><br>
					<button type="button" class="btn" id="OUT4off" onClick="scriviDb(5,0)">OFF</button>
					';
				}
	echo'

				</div>
	';
	echo'
			 <!Lampade ext OUT 1-2-4 pin 6-7-9>
				<div class="box-2" style="left: 80px; top: 35px;">
							Luci esterne
				</div>
				<div class="box-2" style="left: 50px; top: 22px;">
	';
				if ($_SESSION['dir'] < 2){
					echo '
					<button type="button" class="btn" id="OUTs124_on"  onClick="scriviDb_m(6,"H", 7,"H", 9,"H")">ON</button><br>
					<button type="button" class="btn" id="OUTs124_off" onClick="scriviDb_m(6,"L", 7,"L", 9,"L")">OFF</button>
					';
				}
	echo'
				</div>
	';
	echo'
				<!Simbolo Arduino>
				<div class="box-2" style="left: 400px; top: 0px;">
					<img name="img_coll" src="images/link-ko.jpg " width="80" height="57"/>
					<div id="coll" class="box-2" style="left: 33px; top: 42px;">
					</div>
				</div> 
	';
	echo'		
				</div>
			</body>   
			<div id="orologio">
			  Sincronizzazione. . .
			</div>
		<form name="logout" action="logout.php" method="post"> 
			<input type="hidden" name="logout" value="esci"/> 
			<input type="submit" value="Logout"/> 
		</form> 
		</html>
	';
?>
