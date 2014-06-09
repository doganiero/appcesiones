<?php

include_once('/usr/local/pr/aut/www/htdocs/functions/db.php');
//include_once('../../functions/db.php');

$link = conectar();
set_time_limit(0);
mysql_set_charset('utf8');
mysql_query("SET foreign_key_checks = 0");
$inicio = date('d-m-Y H:i:s');
echo "Eliminando Repetidos en TFICHEROS
Inicio: $inicio     
<br><br>";
$sql_ficheros = "SELECT 
                    count(*) as cuenta,
                    CODENVIO,
                    CODFICHERO,
                    FILE_CODCANAL, 
                    FICHERO_ORIGEN, 
                    FILE_CODEMPRESA_DESTINO,
                    FILE_IP_DESTINO 
                FROM V_FICHEROS 
                WHERE CODFICHEROPADRE IS NULL
                GROUP BY 
                    FILE_CODCANAL, 
                    FICHERO_ORIGEN, 
                    FILE_CODEMPRESA_DESTINO,
                    FILE_IP_DESTINO 
                HAVING(cuenta>1) ";

$fichresult = mysql_query($sql_ficheros);
while ($fila = mysql_fetch_object($fichresult)) {

    $FILE_CODEMPRESA_DESTINO = " FILE_CODEMPRESA_DESTINO='$fila->FILE_CODEMPRESA_DESTINO' ";
    if (trim($fila->FILE_CODEMPRESA_DESTINO) === '' || $fila->FILE_CODEMPRESA_DESTINO === null)
        $FILE_CODEMPRESA_DESTINO = " (FILE_CODEMPRESA_DESTINO IS NULL OR FILE_CODEMPRESA_DESTINO='') ";

    $FILE_IP_DESTINO = " FILE_IP_DESTINO='$fila->FILE_IP_DESTINO' ";
    if (trim($fila->FILE_IP_DESTINO) === '' || $fila->FILE_IP_DESTINO === null)
        $FILE_IP_DESTINO = " (FILE_IP_DESTINO IS NULL OR FILE_IP_DESTINO='') ";

    $sql_detalle = "SELECT 
                        CODENVIO,
                        CODFICHERO,
                        FILE_CODCANAL, 
                        FICHERO_ORIGEN, 
                        FILE_CODEMPRESA_DESTINO,
                        FILE_IP_DESTINO 
                    FROM V_FICHEROS 
                    WHERE CODFICHEROPADRE IS NULL
                        
                        AND  FILE_CODCANAL='$fila->FILE_CODCANAL' 
                        AND  FICHERO_ORIGEN='$fila->FICHERO_ORIGEN'
                        AND  $FILE_CODEMPRESA_DESTINO 
                        AND  $FILE_IP_DESTINO ";

    $fichdet = mysql_query($sql_detalle);
    $i=0;
    while ($fila2 = mysql_fetch_object($fichdet)) {
        if($i>0){
        $sql_del = "DELETE FROM TFICHEROS WHERE CODENVIO='$fila2->CODENVIO' AND (CODFICHERO='$fila2->CODFICHERO' OR CODFICHEROPADRE='$fila2->CODFICHERO') ";
        mysql_query($sql_del);
        $err = mysql_error();
        if (!$err)
            $eliminados = $eliminados + ($fila->cuenta - 1);
        else
            $error++;
        }
        $i++;
    }
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
