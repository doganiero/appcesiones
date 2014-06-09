<?php

include_once('/usr/local/pr/aut/www/htdocs/functions/db.php');
//include_once('../../functions/db.php');

$link = conectar();
set_time_limit(0);
mysql_set_charset('utf8');
mysql_query("SET foreign_key_checks = 0");
$inicio = date('d-m-Y H:i:s');
echo "Eliminando ENVÍOS Y FICHEROS DE BAJA, MÁSCARAS SIN RELACIONAR y ENVÍOS SIN FICHEROS
Inicio: $inicio     
<br><br>";

// Eliminar todos los ficheros y envíos de baja

mysql_query("DELETE FROM TFICHEROS WHERE CODUSUARIO_BAJA IS NOT NULL ");
mysql_query("DELETE FROM TENVIOS WHERE CODUSUARIO_BAJA IS NOT NULL ");
mysql_query("DELETE from tenvios  WHERE CODENVIO NOT IN (SELECT CODENVIO FROM TFICHEROS WHERE CODFICHEROPADRE IS NULL)");
mysql_query("DELETE from tficheros  WHERE CODENVIO NOT IN (SELECT CODENVIO FROM TENVIOS)");




echo "Registros Eliminados: $eliminados<br>
      Errores: $error. $err <br> 
   
<br><br>
        ";



$fin = date('d-m-Y H:i:s');
echo "Fin: $fin
     <br>";
mysql_query("SET foreign_key_checks = 1");
?>
