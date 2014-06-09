<?php

include_once('/usr/local/pr/aut/www/htdocs/functions/db.php');
//include_once('../../functions/db.php');

$link = conectar();
set_time_limit(0);
mysql_set_charset('utf8');
mysql_query("SET foreign_key_checks = 0");
$inicio = date('d-m-Y H:i:s');
echo "Eliminando Repetidos en TBUFFER
Inicio: $inicio     
<br><br>";
$sql_ficheros = "SELECT 
                    count(*) as cuenta, 
                    CODCARGA, 
                    CODBUFFER,
                    BUFFER_FICHERO_ORIGEN, 
                    DESEMPRESA_DESTINO,BUFFER_NOMBREMAQUINA_DESTINO, 
                    BUFFER_IP_DESTINO 
                FROM V_BUFFER 
                GROUP BY 
                    CODCARGA, 
                    BUFFER_FICHERO_ORIGEN, 
                    DESEMPRESA_DESTINO,
                    BUFFER_NOMBREMAQUINA_DESTINO, 
                    BUFFER_IP_DESTINO 
                HAVING(cuenta>1) ";

$buffresult = mysql_query($sql_ficheros);
while ($fila = mysql_fetch_object($buffresult)) {
    
    $DESEMPRESA_DESTINO=" DESEMPRESA_DESTINO='$fila->DESEMPRESA_DESTINO' ";  
    if(trim($fila->DESEMPRESA_DESTINO)==='' || $fila->DESEMPRESA_DESTINO===null ) $DESEMPRESA_DESTINO=" (DESEMPRESA_DESTINO IS NULL OR DESEMPRESA_DESTINO='') "; 
    
    $NOMBREMAQUINA_DESTINO=" NOMBREMAQUINA_DESTINO='$fila->BUFFER_NOMBREMAQUINA_DESTINO' ";
    if(trim($fila->BUFFER_NOMBREMAQUINA_DESTINO)==='' || $fila->BUFFER_NOMBREMAQUINA_DESTINO===null ) $NOMBREMAQUINA_DESTINO=" (NOMBREMAQUINA_DESTINO IS NULL OR NOMBREMAQUINA_DESTINO='') "; 
    
    $IP_DESTINO=" IP_DESTINO='$fila->BUFFER_IP_DESTINO' "; 
     if(trim($fila->BUFFER_IP_DESTINO)==='' || $fila->BUFFER_IP_DESTINO===null ) $IP_DESTINO=" (IP_DESTINO IS NULL OR IP_DESTINO='') "; 
   
   $sql_del="DELETE FROM TBUFFER WHERE CODCARGA='$fila->CODCARGA' AND CODBUFFER<>'$fila->CODBUFFER' AND FICHERO_ORIGEN='$fila->BUFFER_FICHERO_ORIGEN' AND $DESEMPRESA_DESTINO AND $NOMBREMAQUINA_DESTINO AND $IP_DESTINO  ";  
   mysql_query($sql_del);
   $err=  mysql_error();
   if(!$err) $eliminados=$eliminados+(($fila->cuenta)-1);
   else $error++;
   
}

echo "Registros Eliminados: $eliminados<br>
      Errores: $error. $err <br> 
   
<br><br>
        ";



$fin = date('d-m-Y H:i:s');
echo "Fin: $fin
     <br>";
mysql_query("SET foreign_key_checks = 1");
?>
