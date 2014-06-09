<?php

include_once('/usr/local/pr/aut/www/htdocs/functions/db.php');
//include_once('../../functions/db.php');

$link = conectar();
set_time_limit(0);
mysql_set_charset('utf8');
mysql_query("SET foreign_key_checks = 0");
$inicio = date('d-m-Y H:i:s');
echo "Eliminando REGISTROS DE ENVÍOS A PRODUCCIÓN (CMDB) EN LOGS TBUFFER
Inicio: $inicio     
<br><br>";

//PATCH DE IPS DE PRODUCCIÓN SEGÚN CMDB
$cons_temp_cmdb = mysql_query("SELECT COL_A, COL_B, COL_C FROM TEMP_CMDB WHERE COL_E='PRODUCCION' ");
while ($fila_cmdb = mysql_fetch_object($cons_temp_cmdb)) {

    $sql_prod = "DELETE FROM TBUFFER WHERE  ";
    if (trim($fila_cmdb->COL_A)) {
        $where++;
        $sql_prod.=" UPPER(NOMBREMAQUINA_DESTINO) LIKE '" . strtoupper($fila_cmdb->COL_A) . "%' ";
        
    }
    $array_ipsB = explode("/", $fila_cmdb->COL_B);
    if (count($array_ipsB)) {
        foreach ($array_ipsB as $ipprincipal) {
            if (trim($ipprincipal)) {
                if (!$where)
                    $sql_prod.=" (IP_DESTINO='" . $ipprincipal . "' OR  NOMBREMAQUINA_DESTINO='" . $ipprincipal . "') ";
                else
                    $sql_prod.=" OR (IP_DESTINO='" . $ipprincipal . "' OR  NOMBREMAQUINA_DESTINO='" . $ipprincipal . "') ";
                $where++;
            }
        }
    }


    $array_ips = explode("/", $fila_cmdb->COL_C);
    if (count($array_ips)) {
        foreach ($array_ips as $ipsecundario) {
            if (trim($ipsecundario) != "") {
                if (!$where)
                    $sql_prod.= " (IP_DESTINO='" . $ipsecundario . "' OR NOMBREMAQUINA_DESTINO='" . $ipsecundario . "') ";
                else
                    $sql_prod.= " OR (IP_DESTINO='" . $ipsecundario . "' OR NOMBREMAQUINA_DESTINO='" . $ipsecundario . "') ";
                $where++;
            }
        }
    }
    if ($where)
        mysql_query($sql_prod);
    $where = 0;
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
