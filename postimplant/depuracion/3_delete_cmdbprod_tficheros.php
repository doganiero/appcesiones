<?php

include_once('/usr/local/pr/aut/www/htdocs/functions/db.php');
//include_once('../../functions/db.php');

$link = conectar();
set_time_limit(0);
mysql_set_charset('utf8');
mysql_query("SET foreign_key_checks = 0");
$inicio = date('d-m-Y H:i:s');
echo "Eliminando FICHEROS DE ENVÍOS A PRODUCCIÓN (CMDB) EN TFICHEROS
Inicio: $inicio     
<br><br>";

//PATCH DE IPS DE PRODUCCIÓN SEGÚN CMDB
        $cons_temp_cmdb = mysql_query("SELECT COL_A, COL_B, COL_C FROM TEMP_CMDB WHERE COL_E='PRODUCCION' ");
        while ($fila_cmdb = mysql_fetch_object($cons_temp_cmdb)) {
           
            $sql_prod = "SELECT CODENVIO, CODFICHERO FROM V_FICHEROS WHERE  ";
            if (trim($fila_cmdb->COL_A)) {
                $where++;
                $sql_prod.=" UPPER(FILE_IP_DESTINO) LIKE '" .  strtoupper($fila_cmdb->COL_A) . "%' ";
            }
            
            $array_ipsB = explode("/", $fila_cmdb->COL_B);
            if (count($array_ipsB)) {
                foreach ($array_ipsB as $ipprincipal) {
                    if (trim($ipprincipal)) {
                        if (!$where)
                            $sql_prod.=" FILE_IP_DESTINO='" . $ipprincipal . "' ";
                        else
                            $sql_prod.=" OR FILE_IP_DESTINO='" . $ipprincipal . "' ";
                        $where++;
                    }
                }
            }
            
            
            
            $array_ips = explode("/", $fila_cmdb->COL_C);
            if (count($array_ips)) {
                foreach ($array_ips as $ipsecundario) {
                    if (trim($ipsecundario) != "") {
                        if (!$where) $sql_prod.= " FILE_IP_DESTINO='" . $ipsecundario . "' ";
                           else  $sql_prod.= " OR FILE_IP_DESTINO='" . $ipsecundario . "' "; 
                        $where++;
                    }
                }
            }
            if($where) $consulta=mysql_query($sql_prod);
            $where = 0;
            while($fila_del=mysql_fetch_object($consulta)){
                mysql_query("DELETE FROM TFICHEROS WHERE CODENVIO='$fila_del->CODENVIO' AND (CODFICHERO='$fila_del->CODFICHERO' OR CODFICHEROPADRE='$fila_del->CODFICHERO') ");
            }
        }

        //////////////////////////////////////////////////////////////////////////////



echo "Registros Eliminados: $eliminados<br>
      Errores: $error. $err <br> 
   
<br><br>
        ";



$fin = date('d-m-Y H:i:s');
echo "Fin: $fin
     <br>";
mysql_query("SET foreign_key_checks = 1");
?>
