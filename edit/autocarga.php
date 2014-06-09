<?

include_once('/usr/local/pr/aut/www/htdocs/functions/db.php');
//include_once('../functions/db.php');
$link = conectar();
mysql_set_charset('utf8');
include_once('autocmdb.php');
echo "</br>Inicio de Proceso de AutoCarga: " . date('d-m-Y H:i:s') . "</br></br>";

//linux -----------------------
/*

  $directorio_editran="../../tmp/logs/editran";

  $directorio_xcom="../../tmp/logs/xcom";

  $directorio_gepp="../../tmp/logs/gepp";

  //-------------------------------------------
 */
/*
  //Mac -----------------------

  $directorio_editran = "/Applications/MAMP/tmp/logs/editran";
  $directorio_xcom = "/Applications/MAMP/tmp/logs/xcom";
  $directorio_gepp = "/Applications/MAMP/tmp/logs/gepp";

  //----------------------------------------------
 */

//Windows y linux-----------------------


$directorio_editran = $directorio_raiz . "logs/editran";

$directorio_xcom = $directorio_raiz . "logs/xcom";

$directorio_gepp = $directorio_raiz . "logs/gepp";

//-------------------------------------------


$descarga = "CARGA AUTOMÁTICA";
$ejercicio = date('Y');
$mes = date('m');
$observaciones = "CARGA EJECUTADA AUTOMÁTICAMENTE";

//scandir   
$ficheros_editran = scandir($directorio_editran);
$ficheros_xcom = scandir($directorio_xcom);
$ficheros_gepp = scandir($directorio_gepp);



set_time_limit(0);
mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas 
//$ruta_fichero=  str_replace('\','"/", $ruta_fichero);
mysql_query("TRUNCATE TABLE temp_xcom");
mysql_query("TRUNCATE TABLE temp_editran");
mysql_query("TRUNCATE TABLE temp_gepp");

foreach ($ficheros_editran as $fichero) {

    $fichero_log = $fichero;
    $codcanal = "00001";
    $directorio = $directorio_raiz . "logs/editran/";
    $ruta_fichero = $directorio . $fichero_log;
    $existecarga = mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM tcargas WHERE CODCANAL='$codcanal' AND FICHERO_LOG='$fichero_log' "), 0, 'cuenta');
    if (!$existecarga && $fichero != "." && $fichero != "..") {
        // CARGANDO EL lOG DE EDITRAN EN TABLA temp_editran BY TREMENDINI

        $file = fopen($ruta_fichero, "r") or exit("Unable to open file!");
//Output a line of the file until the end is reached
        while (!feof($file)) {
            $linea = fgets($file);
            $cola = substr($linea, 0, 9);
            $colb = substr($linea, 10, 9);
            $colc = substr($linea, 20, 6);
            $cold = substr($linea, 27, 45);
            $cole = substr($linea, 72, 45);
            $colf = substr($linea, 117, 21);
            $colg = substr($linea, 138, 62);
            mysql_query("INSERT INTO TEMP_EDITRAN (COL_A,COL_B,COL_C,COL_D,COL_E,COL_F,COL_G) values ('" . $cola . "','" . $colb . "','" . $colc . "','" . utf8_encode($cold) . "','" . utf8_encode($cole) . "','" . utf8_encode($colf) . "','" . utf8_encode($colg) . "')");
            $error_l = mysql_error();
            $error_lf = mysql_num_rows(mysql_query("SHOW WARNINGS")) + $error_lf;
        }
        fclose($file);

        //PATCH DE NOMBRES DE EMPRESA SEGÚN JCL
        $cons_temp_editran = mysql_query("SELECT COL_C, COL_F FROM TEMP_EDITRAN");
        while ($fila_patch = mysql_fetch_object($cons_temp_editran)) {
            if (trim($fila_patch->COL_F) == "") {
                $sql_jcl = "SELECT COL_F FROM TEMP_EDITRAN WHERE COL_C='" . $fila_patch->COL_C . "' AND COL_F IS NOT NULL AND COL_F<>'' ";
                $txt_emp = mysql_result(mysql_query($sql_jcl), 0, "COL_F"); // < ---hay que insertar este texto en el registro
                $upemp_tmp_editran = "UPDATE TEMP_EDITRAN SET COL_F='" . $txt_emp . "' WHERE COL_C='" . $fila_patch->COL_C . "' AND (COL_F IS NULL OR COL_F='') ";
                mysql_query($upemp_tmp_editran);
            }
        }

        //////////////////////////////////////////////////////////////////////////////
        //-------fin CARGA DE EDITRAN-------------------------------------------------
        $num_reg_ant = mysql_result(mysql_query('SELECT COUNT(*) as suma FROM TBUFFER'), 0, 'suma');
        if ($error_l == "") {
            $sig_carga = mysql_result((mysql_query(" SELECT MAX(CODCARGA) as max FROM tcargas ")), 0, 'max') + 1;
            $cifras = strlen($sig_carga);
            $ceros = "";
            for ($i = $cifras; $i < 6; $i++) {
                $ceros.="0";
            }
            $sig_carga = $ceros . $sig_carga;


            $codcarga = $sig_carga;
            mysql_query("CALL PROC_CARGAR_EDITRAN('$codcarga','$descarga','$fichero_log','$codcanal','$ejercicio','$mes','AUTOMAT',@P_RESULTADO)");
            $error_p = mysql_error();
            $num_reg_act = mysql_result(mysql_query('SELECT COUNT(*) as suma FROM TBUFFER'), 0, 'suma');
            $dif_reg = $num_reg_act - $num_reg_ant;
            if ($dif_reg <= 0)
                $error++;
            if (!$error) {
                $sql_newcarga = "UPDATE tcargas SET OBSERVACIONES='$observaciones', FICHERO_LOG='$fichero_log' WHERE CODCARGA='$codcarga' ";
                mysql_query($sql_newcarga);
                $error = mysql_error();
            }
            $my_error = mysql_error($link);
            $error = 0;
        }

        if ($my_error == "" && $error == "" && $error_p == "" && $error_l == "") {
            if (!$error_lf)
                echo "Fichero Log EDITRAN - $fichero_log :  \"$codcarga\" cargado correctamente.<br /> $dif_reg  Nuevos Registros <br> ";
            else
                echo "Fichero Log EDITRAN - $fichero_log :  \"$codcarga\" cargado correctamente.<br /> $dif_reg  Nuevos Registros <br>";


            // Eliminando registros repetidos  ///// 
            $q = 0;
            $sql_repetidos = "SELECT COUNT(*) as cuenta, BUFFER_FICHERO_ORIGEN, DESEMPRESA_DESTINO, BUFFER_NOMBREMAQUINA_DESTINO, BUFFER_IP_DESTINO FROM V_BUFFER WHERE CODCARGA='$codcarga' GROUP BY BUFFER_DESCANAL, BUFFER_FICHERO_ORIGEN, DESEMPRESA_DESTINO,BUFFER_NOMBREMAQUINA_DESTINO, BUFFER_IP_DESTINO HAVING (cuenta>1) ";
            $cons_rep = mysql_query($sql_repetidos);
            while ($fila_rep = mysql_fetch_object($cons_rep)) {
                $DESEMPRESA_DESTINO = "='" . $fila_rep->DESEMPRESA_DESTINO . "' ";
                if ($fila_rep->DESEMPRESA_DESTINO == '')
                    $DESEMPRESA_DESTINO = " IS NULL ";
                $BUFFER_NOMBREMAQUINA_DESTINO = "='" . $fila_rep->BUFFER_NOMBREMAQUINA_DESTINO . "' ";
                if ($fila_rep->BUFFER_NOMBREMAQUINA_DESTINO == '')
                    $BUFFER_NOMBREMAQUINA_DESTINO = " IS NULL ";
                $BUFFER_IP_DESTINO = "='" . $fila_rep->BUFFER_IP_DESTINO . "' ";
                if ($fila_rep->BUFFER_IP_DESTINO == '')
                    $BUFFER_IP_DESTINO = " IS NULL ";
                $sql_codbuffer_rep = "SELECT CODBUFFER FROM TBUFFER WHERE CODCARGA='$codcarga' AND FICHERO_ORIGEN='$fila_rep->BUFFER_FICHERO_ORIGEN' AND DESEMPRESA_DESTINO$DESEMPRESA_DESTINO AND NOMBREMAQUINA_DESTINO$BUFFER_NOMBREMAQUINA_DESTINO AND IP_DESTINO$BUFFER_IP_DESTINO ";
                $cons_codbuffer_rep = mysql_query($sql_codbuffer_rep);
                while ($fila_codbuffer_rep = mysql_fetch_object($cons_codbuffer_rep)) {
                    if ($q >= 1) {
                        mysql_query("DELETE FROM TBUFFER WHERE CODCARGA='$codcarga' AND CODBUFFER='$fila_codbuffer_rep->CODBUFFER' ");
                    }
                    $q++;
                }
                $q = 0;
            }
            // Fin patch eliminar registrops repetidos por carga
            //PATCH DE EXCEPCIONES DE TBUFFER
            $sql_exc = "SELECT MASCARA FROM TEXCEPCIONES WHERE CODCANAL='$codcanal' ";
            $cons_exc = mysql_query($sql_exc);
            while ($fila_exc = mysql_fetch_object($cons_exc)) {
                mysql_query("DELETE FROM TBUFFER WHERE CODCARGA='$codcarga' AND (FICHERO_ORIGEN LIKE '" . $fila_exc->MASCARA . "' OR NOMBREMAQUINA_DESTINO LIKE '" . $fila_exc->MASCARA . "' OR IP_DESTINO LIKE '" . $fila_exc->MASCARA . "') ");
            }
            // FIN PATCH Excepciones de TBUFFER

            mysql_query("CALL PROC_CONSOLIDACION('$codcarga','AUTOMAT',@P_RESULTADO);");
            $error = mysql_error();
            if ($error)
                echo $error;
            echo "Resultado CONSOLIDACIÓN CARGA Nº $codcarga ejecutado correctamente <br><br>";
            $error = 0;

            mysql_query("CALL PROC_CONSOLIDACION_MASCARA('$codcarga','AUTOMAT','00001',@P_RESULTADO);");
            $error = mysql_error();
            if ($error)
                echo $error;
            echo "Resultado CONSOLIDACIÓN MÁSCARAS CARGA Nº $codcarga ejecutado correctamente <br><br>";
            $error = 0;

            unlink($ruta_fichero);
        }
        else {

            echo "Error en la creación de la carga <br />" . $my_error . " " . $error_p . " " . $error_l;

            //$fich_error = unlink($ruta_fichero);
            mysql_query("DELETE FROM tcargas WHERE CODCARGA = '$codcarga'");
            mysql_query("DELETE FROM tbuffer WHERE CODCARGA = '$codcarga'");
        }

        $my_error = 0;
        $error = 0;
        $error_p = 0;
        $error_l = 0;
    }//fin existe carga 
} // fin foreach fichero

foreach ($ficheros_xcom as $fichero) {

    $fichero_log = $fichero;
    $codcanal = "00002";
    $directorio = $directorio_raiz . "logs/xcom/";
    $ruta_fichero = $directorio . $fichero_log;
    $existecarga = mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM tcargas WHERE CODCANAL='$codcanal' AND FICHERO_LOG='$fichero_log' "), 0, 'cuenta');
    if (!$existecarga && $fichero != "." && $fichero != "..") {
        //$load='LOAD DATA INFILE "' . $ruta_fichero . '" INTO TABLE TEMP_XCOM ';
        //mysql_query($load);
        // CARGANDO EL lOG DE xCOM EN TABLA temp_xcom BY TREMENDINI

        $file = fopen($ruta_fichero, "r") or exit("Unable to open file!");
//Output a line of the file until the end is reached
        while (!feof($file)) {
            $linea = fgets($file);
            $colb = substr($linea, 7, 2);
            $cold = substr($linea, 19, 40);
            $colf = substr($linea, 71, 64);
            $colk = substr($linea, 177, 1);
            $cole = substr($linea, 60, 11);
            $colj = substr($linea, 168, 9);
            mysql_query("INSERT INTO TEMP_XCOM (COL_B,COL_D,COL_F,COL_K,COL_E,COL_J) values ('" . $colb . "','" . $cold . "','" . $colf . "','" . $colk . "','" . $cole . "','" . $colj . "')");
            $error_l = mysql_error();
            $error_lf = mysql_num_rows(mysql_query("SHOW WARNINGS")) + $error_lf;
        }
        fclose($file);

        //PATCH DE IPS DE PRODUCCIÓN SEGÚN CMDB
        $cons_temp_cmdb = mysql_query("SELECT COL_A, COL_B, COL_C FROM TEMP_CMDB WHERE COL_E='PRODUCCION' ");
        while ($fila_cmdb = mysql_fetch_object($cons_temp_cmdb)) {

            $sql_prod = "DELETE FROM TEMP_XCOM WHERE  ";
            if (trim($fila_cmdb->COL_A)) {
                $where++;
                $sql_prod.=" UPPER(COL_D) LIKE '" . strtoupper($fila_cmdb->COL_A) . "%' ";
            }

            $array_ipsB = explode("/", $fila_cmdb->COL_B);
            if (count($array_ipsB)) {
                foreach ($array_ipsB as $ipprincipal) {
                    if (trim($ipprincipal)) {
                        if (!$where)
                            $sql_prod.=" COL_D='" . $ipprincipal . "' ";
                        else
                            $sql_prod.=" OR COL_D='" . $ipprincipal . "' ";
                        $where++;
                    }
                }
            }



            $array_ips = explode("/", $fila_cmdb->COL_C);
            if (count($array_ips)) {
                foreach ($array_ips as $ipsecundario) {
                    if (trim($ipsecundario) != "") {
                        if (!$where)
                            $sql_prod.= " COL_D='" . $ipsecundario . "' ";
                        else
                            $sql_prod.= " OR COL_D='" . $ipsecundario . "' ";
                        $where++;
                    }
                }
            }
            if ($where)
                mysql_query($sql_prod);
            $where = 0;
        }

        //////////////////////////////////////////////////////////////////////////////
        //-------fin CARGA DE xcom-------------------------------------------------

        $num_reg_ant = mysql_result(mysql_query('SELECT COUNT(*) as suma FROM TBUFFER'), 0, 'suma');
        if ($error_l == 0)
            $error_l = "";
        if ($error_l == "") {

            $cons_explode = mysql_query('SELECT COL_F FROM TEMP_XCOM');
            while ($fila = mysql_fetch_object($cons_explode)) {

                $fichero = explode(" ", ltrim($fila->COL_F));
                $nombre = $fichero[0];
                if ($fichero[17] != "")
                    $nombre = $fichero[0] . "." . $fichero[17];
                mysql_query("UPDATE TEMP_XCOM SET COL_F='" . $nombre . "' WHERE COL_F='" . $fila->COL_F . "' ");
            }

            $sig_carga = mysql_result((mysql_query(" SELECT MAX(CODCARGA) as max FROM tcargas ")), 0, 'max') + 1;
            $cifras = strlen($sig_carga);
            $ceros = "";
            for ($i = $cifras; $i < 6; $i++) {
                $ceros.="0";
            }
            $sig_carga = $ceros . $sig_carga;


            $codcarga = $sig_carga;


            mysql_query("CALL PROC_CARGAR_XCOM('$codcarga','$descarga','$fichero_log','$codcanal','$ejercicio','$mes','AUTOMAT',@P_RESULTADO)");
            $error_p = mysql_error();

            $num_reg_act = mysql_result(mysql_query('SELECT COUNT(*) as suma FROM TBUFFER'), 0, 'suma');
            $dif_reg = $num_reg_act - $num_reg_ant;
            if ($dif_reg <= 0)
                $error++;
            if (!$error) {
                $sql_newcarga = "UPDATE tcargas SET OBSERVACIONES='$observaciones', FICHERO_LOG='$fichero_log' WHERE CODCARGA='$codcarga' ";
                mysql_query($sql_newcarga);
                $error = mysql_error();
            }
            $my_error = mysql_error($link);
            $error = 0;
        }

        if ($my_error == "" && $error == "" && $error_p == "" && $error_l == "") {
            if (!$error_lf)
                echo "Fichero Log XCOM - $fichero_log :  \"$codcarga\" cargado correctamente.<br /> $dif_reg  Nuevos Registros <br> ";
            else
                echo "Fichero Log XCOM - $fichero_log :  \"$codcarga\" cargado correctamente.<br /> $dif_reg  Nuevos Registros <br>";


            // Eliminando registros repetidos  ///// 
            $q = 0;
            $sql_repetidos = "SELECT COUNT(*) as cuenta, BUFFER_FICHERO_ORIGEN, DESEMPRESA_DESTINO, BUFFER_NOMBREMAQUINA_DESTINO, BUFFER_IP_DESTINO FROM V_BUFFER WHERE CODCARGA='$codcarga' GROUP BY BUFFER_DESCANAL, BUFFER_FICHERO_ORIGEN, DESEMPRESA_DESTINO,BUFFER_NOMBREMAQUINA_DESTINO, BUFFER_IP_DESTINO HAVING (cuenta>1) ";
            $cons_rep = mysql_query($sql_repetidos);
            while ($fila_rep = mysql_fetch_object($cons_rep)) {
                $DESEMPRESA_DESTINO = "='" . $fila_rep->DESEMPRESA_DESTINO . "' ";
                if ($fila_rep->DESEMPRESA_DESTINO == '')
                    $DESEMPRESA_DESTINO = " IS NULL ";
                $BUFFER_NOMBREMAQUINA_DESTINO = "='" . $fila_rep->BUFFER_NOMBREMAQUINA_DESTINO . "' ";
                if ($fila_rep->BUFFER_NOMBREMAQUINA_DESTINO == '')
                    $BUFFER_NOMBREMAQUINA_DESTINO = " IS NULL ";
                $BUFFER_IP_DESTINO = "='" . $fila_rep->BUFFER_IP_DESTINO . "' ";
                if ($fila_rep->BUFFER_IP_DESTINO == '')
                    $BUFFER_IP_DESTINO = " IS NULL ";
                $sql_codbuffer_rep = "SELECT CODBUFFER FROM TBUFFER WHERE CODCARGA='$codcarga' AND FICHERO_ORIGEN='$fila_rep->BUFFER_FICHERO_ORIGEN' AND DESEMPRESA_DESTINO$DESEMPRESA_DESTINO AND NOMBREMAQUINA_DESTINO$BUFFER_NOMBREMAQUINA_DESTINO AND IP_DESTINO$BUFFER_IP_DESTINO ";
                $cons_codbuffer_rep = mysql_query($sql_codbuffer_rep);
                while ($fila_codbuffer_rep = mysql_fetch_object($cons_codbuffer_rep)) {
                    if ($q >= 1) {
                        mysql_query("DELETE FROM TBUFFER WHERE CODCARGA='$codcarga' AND CODBUFFER='$fila_codbuffer_rep->CODBUFFER' ");
                    }
                    $q++;
                }
                $q = 0;
            }
            // Fin patch eliminar registrops repetidos por carga
            //PATCH DE EXCEPCIONES DE TBUFFER
            $sql_exc = "SELECT MASCARA FROM TEXCEPCIONES WHERE CODCANAL='$codcanal' ";
            $cons_exc = mysql_query($sql_exc);
            while ($fila_exc = mysql_fetch_object($cons_exc)) {
                mysql_query("DELETE FROM TBUFFER WHERE CODCARGA='$codcarga' AND (FICHERO_ORIGEN LIKE '" . $fila_exc->MASCARA . "' OR NOMBREMAQUINA_DESTINO LIKE '" . $fila_exc->MASCARA . "' OR IP_DESTINO LIKE '" . $fila_exc->MASCARA . "') ");
            }
            // FIN PATCH Excepciones de TBUFFER


            mysql_query("CALL PROC_CONSOLIDACION('$codcarga','AUTOMAT',@P_RESULTADO);");
            $error = mysql_error();
            if ($error)
                echo $error;
            echo "Resultado CONSOLIDACIÓN CARGA Nº $codcarga ejecutado correctamente <br><br>";
            $error = 0;

            mysql_query("CALL PROC_CONSOLIDACION_MASCARA('$codcarga','AUTOMAT','00002',@P_RESULTADO);");
            $error = mysql_error();
            if ($error)
                echo $error;
            echo "Resultado CONSOLIDACIÓN MÁSCARAS CARGA Nº $codcarga ejecutado correctamente <br><br>";
            $error = 0;


            unlink($ruta_fichero);
        }
        else {
            echo "Error en la creación de la carga <br />" . $my_error . " " . $error_p . " " . $error_l;

            //$fich_error = unlink($ruta_fichero);
            mysql_query("DELETE FROM tcargas WHERE CODCARGA = '$codcarga'");
            mysql_query("DELETE FROM tbuffer WHERE CODCARGA = '$codcarga'");
        }

        $my_error = 0;
        $error = 0;
        $error_p = 0;
        $error_l = 0;
    }//fin existe carga
} // fin foreach fichero

foreach ($ficheros_gepp as $fichero) {
    $fichero_log = $fichero;
    $codcanal = "00003";
    $directorio = $directorio_raiz . "logs/gepp/";
    $ruta_fichero = $directorio . $fichero_log;
    $existecarga = mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM tcargas WHERE  FICHERO_LOG='$fichero_log' "), 0, 'cuenta');
    if (!$existecarga && $fichero != "." && $fichero != "..") {
        // CARGANDO EL lOG DE gepp EN TABLA temp_gepp BY TREMENDINI

        $file = fopen($ruta_fichero, "r") or exit("Unable to open file!");
//Output a line of the file until the end is reached
        while (!feof($file)) {
            $linea = fgets($file);
            list($cola, $colb, $colc, $cold, $cole, $colf, $colg, $colh, $coli, $colj, $colk, $coll, $colm, $coln, $colo, $colp, $colq, $colr, $cols, $colt) = explode(";", $linea);

            mysql_query("INSERT INTO TEMP_GEPP (COL_A,COL_B,COL_C,COL_D,COL_E,COL_F,COL_G,COL_H,COL_I,COL_J,COL_K,COL_L,COL_M,COL_N,COL_O,COL_P,COL_Q,COL_R,COL_S,COL_T)
                                values ('" . $cola . "','" . $colb . "','" . $colc . "','" . $cold . "','" . $cole . "','" . $colf . "','" . $colg . "','" . $colh . "','" . $coli . "','" . $colj . "','" . $colk . "','" . $coll . "','" . $colm . "','" . $coln . "','" . $colo . "','" . $colp . "','" . $colq . "','" . $colr . "','" . $cols . "','" . $colt . "')");
            $error_l = mysql_error();
            $error_lf = mysql_num_rows(mysql_query("SHOW WARNINGS")) + $error_lf;
        }
        fclose($file);


        //-------fin CARGA DE gepp-------------------------------------------------
        $num_reg_ant = mysql_result(mysql_query('SELECT COUNT(*) as suma FROM TBUFFER'), 0, 'suma');
        if ($error_l == "") {

            //Consulta de los canales que vienen en el log
            //$cons_tmp_canal = mysql_query("SELECT DISTINCT COL_M AS canalgepp FROM TEMP_GEPP WHERE COL_M UNLIKE '%GET%' AND COL_M UNLIKE '%get%' AND (COL_M LIKE '%PUT%' OR COL_M LIKE '%put%') ");
            $cons_tmp_canal = mysql_query("SELECT DISTINCT COL_M AS canalgepp FROM TEMP_GEPP WHERE COL_D<>'' AND COL_M NOT LIKE '%get%' AND COL_M NOT LIKE '%GET%' ");
            //--------PROCESO DE CARGA DE GEPP--------------------------------
            $x = 0;
            while ($fila_canal = mysql_fetch_object($cons_tmp_canal)) {// Loop para cargar diferentes canales en distintas cargas para GEPP
                $sig_carga = mysql_result((mysql_query(" SELECT MAX(CODCARGA) as max FROM tcargas ")), 0, 'max') + 1;
                $cifras = strlen($sig_carga);
                $ceros = "";
                for ($i = $cifras; $i < 6; $i++) {
                    $ceros.="0";
                }
                $sig_carga = $ceros . $sig_carga;


                $codcarga = $sig_carga;

                //if(trim($fila_canal->nomfichero)!=""){
                $canalorig = trim($fila_canal->canalgepp);
                if (trim($fila_canal->canalgepp) == "")
                    $canal_gepp = "GEPP-NULL";
                else
                    $canal_gepp = "GEPP-" . trim($fila_canal->canalgepp); //aquí hacer substring para quitar partes del string que sobran
                $canal_gepp = trim(str_ireplace("-put", "", $canal_gepp));
                $existecanal = mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM tcanales WHERE DESCANAL='$canal_gepp'"), 0, 'cuenta');
                if (!$existecanal) {
                    $sig_canal = mysql_result((mysql_query(" SELECT MAX(CODCANAL) as max FROM tcanales ")), 0, 'max') + 1;
                    $cifras = strlen($sig_canal);
                    $ceros = "";
                    for ($i = $cifras; $i < 5; $i++) {
                        $ceros.="0";
                    }
                    $sig_canal = $ceros . $sig_canal;
                    mysql_query("INSERT INTO tcanales (CODCANAL, DESCANAL, CHKACTIVO) values ('$sig_canal','$canal_gepp','S')");
                } else
                    $sig_canal = mysql_result(mysql_query("SELECT CODCANAL FROM tcanales WHERE DESCANAL='$canal_gepp'"), 0, 'CODCANAL');
                if ($x > 0) {
                    $sig_carga = mysql_result((mysql_query(" SELECT MAX(CODCARGA) as max FROM tcargas ")), 0, 'max') + 1;
                    $cifras = strlen($sig_carga);
                    $ceros = "";
                    for ($i = $cifras; $i < 6; $i++) {
                        $ceros.="0";
                    }
                    $sig_carga = $ceros . $sig_carga;
                    //$aux_carga = $codcarga;
                    $codcarga = $sig_carga;
                }
                mysql_query("CALL PROC_CARGAR_GEPP('$codcarga','$descarga','$fichero_log','$sig_canal','$ejercicio','$mes','AUTOMAT','$canalorig',@P_RESULTADO)");



                // Eliminando registros repetidos  ///// 
                $q = 0;
                $sql_repetidos = "SELECT COUNT(*) as cuenta, BUFFER_FICHERO_ORIGEN, DESEMPRESA_DESTINO, BUFFER_NOMBREMAQUINA_DESTINO, BUFFER_IP_DESTINO FROM V_BUFFER WHERE CODCARGA='$codcarga' GROUP BY BUFFER_DESCANAL, BUFFER_FICHERO_ORIGEN, DESEMPRESA_DESTINO,BUFFER_NOMBREMAQUINA_DESTINO, BUFFER_IP_DESTINO HAVING (cuenta>1) ";
                $cons_rep = mysql_query($sql_repetidos);
                while ($fila_rep = mysql_fetch_object($cons_rep)) {
                    $DESEMPRESA_DESTINO = "='" . $fila_rep->DESEMPRESA_DESTINO . "' ";
                    if ($fila_rep->DESEMPRESA_DESTINO == '')
                        $DESEMPRESA_DESTINO = " IS NULL ";
                    $BUFFER_NOMBREMAQUINA_DESTINO = "='" . $fila_rep->BUFFER_NOMBREMAQUINA_DESTINO . "' ";
                    if ($fila_rep->BUFFER_NOMBREMAQUINA_DESTINO == '')
                        $BUFFER_NOMBREMAQUINA_DESTINO = " IS NULL ";
                    $BUFFER_IP_DESTINO = "='" . $fila_rep->BUFFER_IP_DESTINO . "' ";
                    if ($fila_rep->BUFFER_IP_DESTINO == '')
                        $BUFFER_IP_DESTINO = " IS NULL ";
                    $sql_codbuffer_rep = "SELECT CODBUFFER FROM TBUFFER WHERE CODCARGA='$codcarga' AND FICHERO_ORIGEN='$fila_rep->BUFFER_FICHERO_ORIGEN' AND DESEMPRESA_DESTINO$DESEMPRESA_DESTINO AND NOMBREMAQUINA_DESTINO$BUFFER_NOMBREMAQUINA_DESTINO AND IP_DESTINO$BUFFER_IP_DESTINO ";
                    $cons_codbuffer_rep = mysql_query($sql_codbuffer_rep);
                    while ($fila_codbuffer_rep = mysql_fetch_object($cons_codbuffer_rep)) {
                        if ($q >= 1) {
                            mysql_query("DELETE FROM TBUFFER WHERE CODCARGA='$codcarga' AND CODBUFFER='$fila_codbuffer_rep->CODBUFFER' ");
                        }
                        $q++;
                    }
                    $q = 0;
                }
                // Fin patch eliminar registrops repetidos por carga
                //PATCH DE EXCEPCIONES DE TBUFFER EN GEPP
                if (strpos($canal_gepp, "XCOM") !== false) {
                    $sql_exc = "SELECT MASCARA FROM TEXCEPCIONES WHERE CODCANAL='00002' ";
                } else if (strpos($canal_gepp, "FTP") !== false) {
                    $sql_exc = "SELECT MASCARA FROM TEXCEPCIONES WHERE CODCANAL='00004' OR CODCANAL='00005' OR CODCANAL='00006' OR CODCANAL='00007' ";
                } else
                    $sql_exc = "SELECT MASCARA FROM TEXCEPCIONES WHERE CODCANAL='$sig_canal' ";

                $cons_exc_gepp = mysql_query($sql_exc);
                while ($fila_exc = mysql_fetch_object($cons_exc_gepp)) {
                    mysql_query("DELETE FROM TBUFFER WHERE CODCARGA='$codcarga' AND (FICHERO_ORIGEN LIKE '" . $fila_exc->MASCARA . "' OR NOMBREMAQUINA_DESTINO LIKE '" . $fila_exc->MASCARA . "' OR IP_DESTINO LIKE '" . $fila_exc->MASCARA . "') ");
                }
                // FIN PATCH Excepciones de TBUFFER


                if ($aux_carga)
                    $codcarga = $aux_carga;
                $error_p = mysql_error();
                $num_reg_act = mysql_result(mysql_query('SELECT COUNT(*) as suma FROM TBUFFER '), 0, 'suma');
                $dif_reg = $num_reg_act - $num_reg_ant;
                $num_reg_ant = $num_reg_ant + $dif_reg;
                if ($dif_reg <= 0)
                    $error++;
                if (!$error) {
                    $sql_newcarga = "UPDATE tcargas SET OBSERVACIONES='$observaciones', FICHERO_LOG='$fichero_log' WHERE CODCARGA='$codcarga' ";
                    mysql_query($sql_newcarga);
                    $error = mysql_error();
                }
                $my_error = mysql_error($link);

                if ($my_error == "" && $error == "" && $error_p == "" && $error_l == "") {
                    if (!$error_lf)
                        echo "Fichero Log $canal_gepp - $fichero_log :  \"$codcarga\" cargado correctamente.<br /> $dif_reg  Nuevos Registros <br> ";
                    else
                        echo "Fichero Log $canal_gepp - $fichero_log :  \"$codcarga\" cargado correctamente.<br /> $dif_reg  Nuevos Registros <br>";





                    if (strpos($canal_gepp, "XCOM") !== false) {
                        mysql_query("CALL PROC_CONSOLIDACION_GEPP_CANAL('$codcarga','00002','AUTOMAT',@P_RESULTADO);");
                        $error = mysql_error();

                        if ($error)
                            echo $error;
                        echo "Resultado CONSOLIDACIÓN CARGA Nº $codcarga ejecutado correctamente <br><br>";
                        $error = 0;

                        mysql_query("CALL PROC_CONSOLIDACION_MASCARA('$codcarga','AUTOMAT','00002',@P_RESULTADO);");
                        $error = mysql_error();
                        if ($error)
                            echo $error;
                        echo "Resultado CONSOLIDACIÓN MÁSCARAS CARGA Nº $codcarga ejecutado correctamente <br><br>";
                        $error = 0;
                    }

                    if (strpos($canal_gepp, "FTP") !== false) {
                        mysql_query("CALL PROC_CONSOLIDACION_GEPP_CANAL('$codcarga','00004','AUTOMAT',@P_RESULTADO);");
                        $error = mysql_error();
                        mysql_query("CALL PROC_CONSOLIDACION_MASCARA('$codcarga','AUTOMAT','00004',@P_RESULTADO);");
                        $error = mysql_error();
                        mysql_query("CALL PROC_CONSOLIDACION_GEPP_CANAL('$codcarga','00005','AUTOMAT',@P_RESULTADO);");
                        $error = mysql_error();
                        mysql_query("CALL PROC_CONSOLIDACION_MASCARA('$codcarga','AUTOMAT','00005',@P_RESULTADO);");
                        $error = mysql_error();
                        mysql_query("CALL PROC_CONSOLIDACION_GEPP_CANAL('$codcarga','00006','AUTOMAT',@P_RESULTADO);");
                        $error = mysql_error();
                        mysql_query("CALL PROC_CONSOLIDACION_MASCARA('$codcarga','AUTOMAT','00006',@P_RESULTADO);");
                        $error = mysql_error();
                        mysql_query("CALL PROC_CONSOLIDACION_GEPP_CANAL('$codcarga','00007','AUTOMAT',@P_RESULTADO);");
                        $error = mysql_error();
                        mysql_query("CALL PROC_CONSOLIDACION_MASCARA('$codcarga','AUTOMAT','00007',@P_RESULTADO);");
                        $error = mysql_error();

                        if ($error)
                            echo $error;
                        echo "Resultado CONSOLIDACIÓN CARGA Nº $codcarga ejecutado correctamente <br><br>";
                        echo "Resultado CONSOLIDACIÓN MÁSCARAS CARGA Nº $codcarga ejecutado correctamente <br><br>";
                        $error = 0;
                    }

                    unlink($ruta_fichero);
                }
                else {
                    echo "Error en la creación de la carga <br />" . $my_error . " " . $error_p . " " . $error_l;

                    //$fich_error = unlink($ruta_fichero);
                    mysql_query("DELETE FROM tcargas WHERE CODCARGA = '$codcarga'");
                    mysql_query("DELETE FROM tbuffer WHERE CODCARGA = '$codcarga'");
                }

                $my_error = 0;
                $error = 0;
                $error_p = 0;
                $error_l = 0;


                $x++;
            }
            //------------FIN CARGA DE GEPP
            //} //fin si nombre de fichero no está vacío
        }
        mysql_query('DELETE FROM TEMP_GEPP');  //<----Aquí vaciar tabla temp_gepp
    } // fin existe carga
} // fin foreach fichero


mysql_query("TRUNCATE TABLE temp_xcom");
mysql_query("TRUNCATE TABLE temp_editran");
mysql_query("TRUNCATE TABLE temp_gepp");
mysql_query("SET foreign_key_checks = 1"); // Activa claves foráneas 
echo "</br></br>Fin de Proceso de AutoCarga: " . date('d-m-Y H:i:s') . "</br></br>";
?>
