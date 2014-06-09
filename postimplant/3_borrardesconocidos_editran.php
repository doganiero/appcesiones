<?php

//include_once('/usr/local/pr/aut/www/htdocs/functions/db.php');
include_once('../functions/db.php');

$link = conectar();
set_time_limit(0);
mysql_set_charset('utf8');
mysql_query("SET foreign_key_checks = 0");
$inicio = date('d-m-Y H:i:s');
echo "Actualización de Empresas en Envíos EDITRAN
Inicio: $inicio     
<br><br>";


$sql_ficheros = "SELECT E.CODENVIO AS CODENV FROM TFICHEROS F, V_ENVIOS E WHERE F.CODCANAL='00001' AND F.CODENVIO=E.CODENVIO AND F.CODUSUARIO_BAJA IS NULL AND F.CODFICHEROPADRE IS NULL AND E.CODEMPRESA='00000'  ";
$result = mysql_query($sql_ficheros);
while ($fila = mysql_fetch_object($result)) {

   
    //insertar  en tficheros
    $sql_del = "DELETE FROM TENVIOS WHERE CODENVIO=".$fila->CODENV."";
    $sql_delf = "DELETE FROM TFICHEROS WHERE CODENVIO=".$fila->CODENV."";
    mysql_query($sql_delf);
    mysql_query($sql_del);
    if (mysql_error())
        echo mysql_error() . "</br>";
    else
        $elim++;
}

$fin = date('d-m-Y H:i:s');
echo "Fin: $fin
     <br>Envíos Eliminados: $elim     <br>";
mysql_query("SET foreign_key_checks = 1");
?>
