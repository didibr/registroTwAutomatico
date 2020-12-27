<?php
//set_time_limit (10000);
 $ADMINLOGIN='666';
 ini_set('default_charset','UTF-8');
 $DATABASE='data.db3';
 $query = new PDO('sqlite:'.$DATABASE) or die("cannot open the database");

 $json = json_decode(file_get_contents('php://input'), true);
 $outP="";
 $inL="";
 
  //adiciona nova info de cookie
 if(isset($json["grava"]) && isset($json["pas"])&& isset($json["data"]))AddData();
 
  if(isset($_GET["login"]))$inL=$_GET["login"];
 if($inL!=$ADMINLOGIN)die("<br><br>
 <div align='center'><h1>LOGIN INVALIDO</h1><br>
 <input id='login' type='password'></input><input type='button' onClick='ll();' value='Login'></input>
 </div><script>
    function ll(){
    document.location.href=document.location.href+'?login='+document.getElementById('login').value;
    }    
 </script>");
 
  //gera banco de dados
 if(isset($_GET['geradb']) && isset($_GET['login']))GenerateDb(); 
 //WEB mostra infos
 if(isset($_GET['verdb']) && isset($_GET['login']))ShowData();
 
 //-------------------------------------------------------------------------------------------------------------------------------------------
function AddData(){
    global $query,$ADMINLOGIN,$json,$outP;	
	//if($json["login"]!=$ADMINLOGIN)die('Login Inválido2');	
	$nome=$json["grava"];
	$pass=$json["pas"];
	$email=$json["mail"];
	$cookie=$json["data"];
    $commands = <<<COMAND
                INSERT INTO dados VALUES('{$nome}','{$pass}','{$email}','{$cookie}');              
COMAND;
    $query->exec($commands);
    
    die("Dados Inseridos");
}


//-------------------------------------------------------------------------------------------------------------------------------------------
function GenerateDb(){
	global $query,$ADMINLOGIN,$outP;	
	if($_GET['login']!=$ADMINLOGIN)die('Login Inválido1');	
	
	$comand = "SELECT * FROM dados";
	$stmt=$query->prepare($comand );
	$jacriado=false;
    if($stmt==false){
		$jacriado=false;
	}else{
		$jacriado=true;
	}	
	if($jacriado==false){
 	$commands = <<<COMAND
                CREATE TABLE IF NOT EXISTS dados (
                    login VARCHAR (255),
					pass VARCHAR (255),
					mail VARCHAR (255),
					cookie TEXT
					);					
COMAND;
  	$query->exec($commands);
    $outP.="DB dados Criado<br>";
	}
	
    $comand = "SELECT * FROM conf";
	$stmt=$query->prepare($comand );
	$jacriado=false;
    if($stmt==false){
		$jacriado=false;
	}else{
		$jacriado=true;
	}	
	if($jacriado==false){
 	$commands = <<<COMAND
                CREATE TABLE IF NOT EXISTS conf (
                    sala    VARCHAR (255),
					owner   VARCHAR (255),
					seguir  VARCHAR (255),
					ligado  VARCHAR (255),
					talk    VARCHAR (255),
					rpname  VARCHAR (255),
					projeto VARCHAR (255),
					maxbots VARCHAR (255),
					maxlurk VARCHAR (255),
					login   VARCHAR (255),
					e0      VARCHAR (255),
					e1      VARCHAR (255),
					e2      VARCHAR (255),
					e3      VARCHAR (255),
					e4      VARCHAR (255),
					e5      VARCHAR (255),
					e6      VARCHAR (255),
					e7      VARCHAR (255),
					e8      VARCHAR (255),
					e9      VARCHAR (255)
					);					
COMAND;
//e0 contador de linha do chat
  	$query->exec($commands);
  	$commands = <<<COMAND
                INSERT INTO conf VALUES('twitch','softwaresdidi','false','false','false','','','1','0','{$ADMINLOGIN}','','','','','','','','','','');              
COMAND;
    $query->exec($commands);
    $outP.="DB conf Criado<br>";
	}

$comand = "SELECT * FROM bot";
	$stmt=$query->prepare($comand );
	$jacriado=false;
    if($stmt==false){
		$jacriado=false;
	}else{
		$jacriado=true;
	}	
	if($jacriado==false){
 	$commands = <<<COMAND
                CREATE TABLE IF NOT EXISTS bot (
                    id      VARCHAR (255),
                    sala    VARCHAR (255),
					hora    VARCHAR (255),
					pedido  VARCHAR (255),
					nick    VARCHAR (255),
					b0      VARCHAR (255),
					b1      VARCHAR (255),
					b2      VARCHAR (255),
					b3      VARCHAR (255),
					b4      VARCHAR (255),
					b5      VARCHAR (255),
					b6      VARCHAR (255),
					b7      VARCHAR (255),
					b8      VARCHAR (255),
					b9      VARCHAR (255)
					);					
COMAND;
//-b0 usado para Falar
//-b1 usado para desligar bot
  	$query->exec($commands);
    $outP.="DB bot Criado";
	}	
}

//-------------------------------------------------------------------------------------------------------------------------------------------
function ShowData(){
   global $query,$ADMINLOGIN,$outP,$inL;	
   if($_GET['login']!=$ADMINLOGIN)die('Login Inválido3');		
   $cc=1;
   $projeto="";
   $rpname="";
   
    $comandos = "SELECT projeto,rpname FROM conf";
    $tabelas = $query->query($comandos);
    foreach ($tabelas as $row){
        $projeto=$row["projeto"];
        $rpname=$row["rpname"];
    }
   
   $comandos = "SELECT id,login,pass,mail FROM dados LEFT JOIN bot ON login = nick";
   $tabelas = $query->query($comandos);
   
   $outP = '<table style="border-collapse: collapse; width: 390px;text-align: center;" border="1"><tbody>
            <tr style="height: 18px;">
            <td style="height: 18px;">NUM</td>
            <td style="height: 18px;">NICK</td>
            <td style="height: 18px;">PASS</td>
            <td style="height: 18px;">MAIL</td>
            <td style="height: 18px;">ACTION</td>
            <td style="height: 18px;">SOURCE</td>
            </tr>';
   foreach ($tabelas as $row){
    $botao="botON('{$cc}');";
    $bval="ON";
    $classe="";
    if($row["id"]!=null){
        $botao="botOFF('{$cc}');";
        $bval="OFF";
        $classe="ativo";
    }
    $source="window.open('https://repl.it/@{$rpname}/{$projeto}-{$cc}','N_{$cc}')";
    
   $outP.= <<<COMAND
    <tr style="height: 18px;" class="{$classe}">
    <td style="height: 18px;">{$cc}</td>
    <td style="height: 18px;">{$row["login"]}</td>
    <td style="height: 18px;">{$row["pass"]}</td>
    <td style="height: 18px;"><a href='https://generator.email/{$row["mail"]}'>VER</a></td>
    <td style="height: 18px;"><input type="button" onClick="{$botao}" value="{$bval}"></input></td>
    <td style="height: 18px;"><input type="button" onClick="{$source}" value="Ver"></input></td>
    </tr>
COMAND;
$cc+=1;
   }
   
    $outP.="</tbody>
            </table>  
            </form>
            <br>
            <a class='ahf' onClick='update();' href='#'>Update</a>
            <script>
            function fsubmit(){
                document.location.reload();
            }
            function botON(id){
                $.post( '?rcv=boton&login={$inL}', { valor: id } );
            }
            function botOFF(id){
                $.post( '?rcv=botoff&login={$inL}', { valor: id } );
            }
            function update(){
               $.post( '?rcv=bupdate&login={$inL}', { valor: 0 } );
               document.location.reload();
            }
            </script>";
   
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>SoftBot</title>
 <style>
    .menor{
        margin-left:10px;
    } 
    .linha{
        display:inline-flex;
    }
    .ahf{
    margin-left: 10px;
    text-decoration: none;
    border: 2px solid #9190ce;
    padding: 4px;
    background-color: #3029a2;
    color: #cacdde;
    display: inline-block;
    width: 100px;  
    }
    .ativo{
    background-color: #b5f7c2;
    }
 </style>
 <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
</head>
<body>
    <input id='login' type='text' value='<?php echo $inL;?>' style='display:none'></input>
    <div align="center">
        <br><b>MENU</b><br><br>
        <a class="ahf" href="./?geradb=1&login=<?php echo $inL;?>">Gerar Db</a>
        <a class="ahf" href="./?verdb=1&login=<?php echo $inL;?>">Ver Db</a>
    </div>
<br>
<div align="center">
    <?php 
        echo $outP;
     ?>
</div>
<script>
    var login='';
    $(document).ready(function(){
    login=document.getElementById('login').value;
    });
 </script>
</body>
</html>
