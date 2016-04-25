<?php

//Parametres de connexion à la base de données Weewx
$db_host='localhost';
$db_user='user';
$db_pass='password';
$db_name='meteo';
$db_table='table';

//parametre ftp infoclimat
$ftp_server = "ftp.infoclimat.fr";
$ftp_username   = "ftpuser";
$ftp_password   =  "ftppasswd";

//Fréquence de mise à jour du fichier static en secondes.
$frequence = 600;

//informations relatioves à la stations
$id_station = "fort-mahon";

//nothing to change after this...
$con = mysql_connect($db_host,$db_user,$db_pass) or die(mysql_error());
mysql_select_db($db_name);

$res=mysql_query("select * from $db_name.$db_table order by dateTime desc limit 1;") or die(mysql_error());
$row = mysql_fetch_row($res);
$dateTime = $row[0];
$date=date('d/m/Y',$dateTime);
$heure=date('H\hi',$dateTime);

//conditions currentes
$rose = round($row[16],1);
$temp = round($row[7],1);
$vent = round($row[10],1);
$winddir = $row[11]+30;
$rafales = round($row[12],1);
$rafalesdir = $row[13];
$hygro = $row[9];
$pression = round($row[3],1);
if(!$row[20]) {$solar=0;} else {$solar=$row[20];}
if(!$row[21]) {$uv=0;} else {$uv=$row[21];}
$rainrate = round($row[14]*10,1);
$today = strtotime('today midnight');
$rain = mysql_query("select sum(rain) from $db_name.$db_table where dateTime>'$today';");
$he = mysql_fetch_row($rain);
$cumul = round($he[0]*10,1);

//get maxtemp
$res = mysql_query("select * from ".$db_table."_day_outTemp order by dateTime DESC limit 1;") or die(mysql_error());
$row = mysql_fetch_row($res);
$mintemptime = date('H\hi',$row[2]);
$mintemp = round($row[1],1);
$maxtemp = round($row[3],1);
$maxtemptime = date('H\hi',$row[4]);

//creation du fichier
$static=
"# INFORMATIONS\n"
."id_station=$id_station\n"
."date_releve=$date\n"
."heure_releve_utc=$heure\n"
."# PARAMETRES TEMPS REEL\n"
."temperature=$temp\n"
."pression=$pression\n"
."humidite=$hygro\n"
."point_de_rosee=$rose\n"
."vent_dir_moy=$winddir\n"
."vent_moyen=$vent\n"
."vent_rafales=$rafales\n"
."pluie_intensite=$rainrate\n"
."pluie_cumul=$cumul\n"
."tn_heure_utc=$mintemptime\n"
."tn_deg_c=$mintemp\n"
."tx_heure_utc=$maxtemptime\n"
."tx_deg_c=$maxtemp\n"
."# ENSOLEILLEMENT\n"
."radiations_solaires_wlk=$solar\n"
."uv_wlk=$uv\n";


$file = "/var/www/html/static.txt";
$fp=fopen($file,'w');
fwrite($fp,$static);
fclose($fp);

//upload ftp static
$conn_id = ftp_connect($ftp_server) or die("could not connect to $ftp_server");
if (!@ftp_login($conn_id, $ftp_username, $ftp_password)) { die("could not connect to infoclimat");}
$remote="static.txt";
ftp_put($conn_id, $remote, $file, FTP_ASCII);
ftp_close($conn_id);

?>
