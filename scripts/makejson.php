<?php

/*-----------------------------------------------------------
Creation de fichiers json pour highchart à partir de la DB MySQL 
de Weewx - Xavier JULIE <xav@xj1.fr> 
https://fortmahon.webcam/meteo.php
This program is free software: you can redistribute it and/or
modify it under the terms of the GNU General Public License
It is distributed in the hope that it will be useful, but 
WITHOUT ANY WARRANTY;
-------------------------------------------------------------
*/

# Parametres de connexion à la bases de données Weewx
$db_host="localhost";
$db_user="user";
$db_pass="password";
$db="meteo.fmp";

#Chemin d'export des fichiers JSON
$path = "/var/www/html";

mysql_connect($db_host,$db_user,$db_pass);

//temperature
$json_temp = "[";
$res=mysql_query("select datetime,OutTemp from $db");
$i=0;
while($row=mysql_fetch_row($res)) {
	$when=$row[0];
        $temp = round($row[1],1);
        if(is_int($i/12)) {
	       $timestamp=$when*1000;
	       $json_temp.= "[$timestamp,$temp],\n";
        }
        $i++;
}
$json_temp.="]";

//temp rose
$json_rose = "[";
$res=mysql_query("select datetime,dewpoint from $db");
$i=0;
while($row=mysql_fetch_row($res)) {
		$when=$row[0];
        if(is_int($i/12)) {
        	$temp = round($row[1],0);
        	$timestamp=$when*1000;
        	$json_rose.= "[$timestamp,$temp],\n";
        }
        $i++;
}
$json_rose.="]";

//barometre
$json_baro = "[";
$res=mysql_query("select datetime,barometer from $db;");
$i=0;
while($row=mysql_fetch_row($res)) {
        $timestamp=$row[0];
        $timestamp=$timestamp*1000;
        $data=round($row[1],1);
        if (is_int($i/12)) {
        $json_baro.= "[$timestamp,$data],\n";
    	}
    	$i++;
}
$json_baro.="]";

//hygro
$json_hygro = "[";
$res=mysql_query("select datetime,outHumidity from $db;");
$i=0;
while($row=mysql_fetch_row($res)) {
        $timestamp=$row[0];
        $timestamp=$timestamp*1000;
        $data=$row[1];
        if (is_int($i/12)) {
        $json_hygro.= "[$timestamp,$data],\n";
    	}
    	$i++;
}
$json_hygro.="]";

//vent
$json_vent = "[";
$res=mysql_query("select datetime,WindSpeed from $db;");
$i=0;
while($row=mysql_fetch_row($res)) {
        $timestamp=$row[0];
        $timestamp=$timestamp*1000;
        $vent=round($row[1],0);
        if(is_int($i/12)) {
        $json_vent.= "[$timestamp,$vent],\n";
    	}
    	$i++;
}
$json_vent.="]";

$json_rafales = "[";
$res=mysql_query("select datetime,WindGust from $db");
$i=0;
while($row=mysql_fetch_row($res)) {
        $timestamp=$row[0];
        $timestamp=$timestamp*1000;
        $vent=round($row[1],0);
        if(is_int($i/12)) {
        $json_rafales.= "[$timestamp,$vent],\n";
    	}
    	$i++;
}
$json_rafales.="]";

//solar
$json_solar = "[";
$res=mysql_query("select datetime,radiation,UV from $db;");
$i=0;
while($row=mysql_fetch_row($res)) {
        $timestamp=$row[0];
        $timestamp=$timestamp*1000;
        $solar=$row[1];
        $uv = round(($row[2]),1);
        if(is_int($i/12)) {
        $json_solar.= "[$timestamp,$solar,$uv],\n";
    	}
    	$i++;
}
$json_solar.="]";


//write files
$file = $path."vent.json";
$fp=fopen($file,'w');
fwrite($fp,$json_vent);
fclose($fp);

$file = $path."rafales.json";
$fp=fopen($file,'w');
fwrite($fp,$json_rafales);
fclose($fp);

$file = $path."pression.json";
$fp=fopen($file,'w');
fwrite($fp,$json_baro);
fclose($fp);

$file = $path."hygrometrie.json";
$fp=fopen($file,'w');
fwrite($fp,$json_hygro);
fclose($fp);


$file = $path."jsonsolar.json";
$fp=fopen($file,'w');
fwrite($fp,$json_solar);
fclose($fp);

$file = $path."temperature.json";
$fp=fopen($file,'w');
fwrite($fp,$json_temp);
fclose($fp);


$file = $path."rosee.json";
$fp=fopen($file,'w');
fwrite($fp,$json_rose);
fclose($fp);

?>
