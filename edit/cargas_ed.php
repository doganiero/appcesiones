<?

include_once('../functions/db.php');
$link = conectar();
mysql_set_charset('utf8');

$sig_carga = mysql_result((mysql_query(" SELECT MAX(CODCARGA) as max FROM tcargas ")), 0, 'max') + 1;
$cifras = strlen($sig_carga);
$ceros = "";
for ($i = $cifras; $i < 6; $i++) {
    $ceros.="0";
}
$sig_carga = $ceros . $sig_carga;


$codcarga = $sig_carga;
$descarga = $_POST['FDESCARGA'];
$codcanal = $_POST['FCODCANAL'];
$ejercicio = $_POST['EJERCICIO'];
$mes = $_POST['MES'];
$observaciones = $_POST['OBSERVACIONES'];

if ($_POST['opcion'] == "consolidarm") {
    $codcarga = $_POST['idCarga'];

    $nom_canal = mysql_result(mysql_query("SELECT a.DESCANAL as DESCR FROM tcanales a, tcargas b WHERE a.CODCANAL=b.CODCANAL AND b.CODCARGA='$codcarga' "), 0, 'DESCR');
    $codcanal = mysql_result(mysql_query("SELECT CODCANAL FROM tcargas WHERE CODCARGA='$codcarga' "), 0, 'CODCANAL');
    set_time_limit(0);
    mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas 
    if (strpos($nom_canal, "XCOM") !== false) {
        
        mysql_query("CALL PROC_CONSOLIDACION_MASCARA('$codcarga','$webseal','00002',@P_RESULTADO);");
        $error = mysql_error();

        if ($error!="")
            echo $error;
    }else

    if (strpos($nom_canal, "FTP") !== false) {

        mysql_query("CALL PROC_CONSOLIDACION_MASCARA('$codcarga','$webseal','00004',@P_RESULTADO);");
        $error = mysql_error();

        mysql_query("CALL PROC_CONSOLIDACION_MASCARA('$codcarga','$webseal','00005',@P_RESULTADO);");

        $error = mysql_error();

        mysql_query("CALL PROC_CONSOLIDACION_MASCARA('$codcarga','$webseal','00006',@P_RESULTADO);");

        $error = mysql_error();

        mysql_query("CALL PROC_CONSOLIDACION_MASCARA('$codcarga','$webseal','00007',@P_RESULTADO);");

        $error = mysql_error();

        if ($error)
            echo $error;
    } else
        mysql_query("CALL PROC_CONSOLIDACION_MASCARA('$codcarga','$webseal','$codcanal',@P_RESULTADO);");

//se desconoce el usuario hasta tanto no se tome con WebSeal. Terminar
    $error = mysql_error();
    if ($error)
        echo $error;
    echo "CONSOLIDACIÓN DE MÁSCARAS CARGA Nº $codcarga ejecutado correctamente <br><br>";
    mysql_query("SET foreign_key_checks = 1"); // Activa claves foráneas 
}
else if ($_POST['opcion'] == "consolidar") {
    $codcarga = $_POST['idCarga'];
    $codcanalgepp = $_POST['idCanalSelect'];
    set_time_limit(0);
    mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas
    $esgepp = mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM V_CARGAS WHERE CODCARGA='$codcarga' AND CARGA_DESCANAL LIKE '%GEPP-%' "), 0, 'cuenta');
    if ($esgepp > 0) {
        if ($codcanalgepp != "") {
            mysql_query("CALL PROC_CONSOLIDACION_GEPP_CANAL('$codcarga','$codcanalgepp','$webseal',@P_RESULTADO);");
        } else {
            mysql_query("CALL PROC_CONSOLIDACION_GEPP('$codcarga','$webseal',@P_RESULTADO);");
        }
    } else {
        mysql_query("CALL PROC_CONSOLIDACION('$codcarga','$webseal',@P_RESULTADO);");
    }
//se desconoce el usuario hasta tanto no se tome con WebSeal. Terminar
    $error = mysql_error();
    if ($error)
        echo $error;
    echo "Resultado CONSOLIDACIÓN CARGA Nº $codcarga ejecutado correctamente <br><br>";
    mysql_query("SET foreign_key_checks = 1"); // Activa claves foráneas 
}
else if ($_POST['opcion'] == "borrar") {
    // Hacemos las consultas...
    $codcarga = $_POST['idCarga'];
    mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas 

    $fichero = mysql_result(mysql_query("SELECT FICHERO_LOG FROM tcargas WHERE CODCARGA='$codcarga' "), 0, 'FICHERO_LOG');
    $codcanal = mysql_result(mysql_query("SELECT CODCANAL FROM tcargas WHERE CODCARGA='$codcarga' "), 0, 'CODCANAL');

    mysql_query("DELETE FROM tcargas WHERE CODCARGA = '$codcarga'");
    mysql_query("DELETE FROM tbuffer WHERE CODCARGA = '$codcarga'");

    $my_error = mysql_error($link);
    if ($my_error == "") {
        // Reiniciaremos si se borra el usuario actual.
        echo "Alerta. Carga Eliminada.";
    } else
        echo "No se puede eliminar la carga " . $my_error;
} // Fin de la opción borrar. 
else if ($_POST['opcion'] == "archivar") {
    // Hacemos las consultas...
    $codcarga = $_POST['idCarga'];
    mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas 

    $fichero = mysql_result(mysql_query("SELECT FICHERO_LOG FROM tcargas WHERE CODCARGA='$codcarga' "), 0, 'FICHERO_LOG');
    $codcanal = mysql_result(mysql_query("SELECT CODCANAL FROM tcargas WHERE CODCARGA='$codcarga' "), 0, 'CODCANAL');
    
    
    mysql_query("INSERT INTO tcargas_archivo SELECT * FROM tcargas WHERE CODCARGA = '$codcarga'");
    mysql_query("INSERT INTO tbuffer_archivo SELECT * FROM tbuffer WHERE CODCARGA = '$codcarga'");
    
    mysql_query("DELETE FROM tcargas WHERE CODCARGA = '$codcarga'");
    mysql_query("DELETE FROM tbuffer WHERE CODCARGA = '$codcarga'");

    $my_error = mysql_error($link);
    if ($my_error == "") {
        // Reiniciaremos si se borra el usuario actual.
        echo "Alerta. Carga Archivada.";
    } else
        echo "No se puede archivar la carga " . $my_error;
} // Fin de la opción borrar. 
else if ($_POST['opcion'] == "nuevo") { // Código para crear.
    if ($codcanal == "00001")
        $directorio = $directorio_raiz . "logs/editran/";
    else if ($codcanal == "00002")
        $directorio = $directorio_raiz . "logs/xcom/";
    else if ($codcanal == "00003")
        $directorio = $directorio_raiz . "logs/gepp/";

    $fichero_log = $_POST['FFICHERO_LOG'];
    $ruta_fichero = $directorio . $fichero_log;

    set_time_limit(0);
    mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas 
    //$ruta_fichero=  str_replace('\','"/", $ruta_fichero);

    if ($codcanal == "00001") {

        // CARGANDO EL lOG DE EDITRAN EN TABLA temp_editran BY TREMENDINI
        $carpetayruta = "/Applications/MAMP/tmp/logs/editran/" . $_POST['FFICHERO_LOG'];
        $file = fopen($carpetayruta, "r") or exit("Unable to open file!");
//Output a line of the file until the end is reached
        while (!feof($file)) { //////////////////////////// aquí cambio editran JCL
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
            mysql_query("CALL PROC_CARGAR_EDITRAN('$codcarga','$descarga','$fichero_log','$codcanal','$ejercicio','$mes','$webseal',@P_RESULTADO)");
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
    } else if ($codcanal == "00002") {
        //$load='LOAD DATA INFILE "' . $ruta_fichero . '" INTO TABLE TEMP_XCOM ';
        //mysql_query($load);
        // CARGANDO EL lOG DE xCOM EN TABLA temp_xcom BY TREMENDINI
        $carpetayruta = "../../tmp/logs/xcom/" . $_POST['FFICHERO_LOG'];
        $file = fopen($carpetayruta, "r") or exit("Unable to open file!");
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


            mysql_query("CALL PROC_CARGAR_XCOM('$codcarga','$descarga','$fichero_log','$codcanal','$ejercicio','$mes','$webseal',@P_RESULTADO)");
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
    } else if ($codcanal == "00003") {
        // CARGANDO EL lOG DE gepp EN TABLA temp_gepp BY TREMENDINI
        $carpetayruta = "../../tmp/logs/gepp/" . $_POST['FFICHERO_LOG'];
        $file = fopen($carpetayruta, "r") or exit("Unable to open file!");
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
                    $aux_carga = $codcarga;
                    $codcarga = $sig_carga;
                }
                mysql_query("CALL PROC_CARGAR_GEPP('$codcarga','$descarga','$fichero_log','$sig_canal','$ejercicio','$mes','$webseal','$canalorig',@P_RESULTADO)");



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
                $x++;
            }
            //------------FIN CARGA DE GEPP
            //} //fin si nombre de fichero no está vacío
        }
        mysql_query('DELETE FROM TEMP_GEPP');  //<----Aquí vaciar tabla temp_gepp
    }



    if ($my_error == "" && $error == "" && $error_p == "" && $error_l == "") {


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
        // 
        // Sustituir valor de dif_reg para nueva cuenta de registros de la carga 
        if ($codcanal != "00003")
            $dif_reg = mysql_result(mysql_query(" SELECT COUNT(*) as dif_reg FROM TBUFFER WHERE CODCARGA='$codcarga' "), 0, 'dif_reg');
        if (!$error_lf)
            echo "Fichero Log:  \"$codcarga\" cargado correctamente.<br /> $dif_reg  Nuevos Registros ";
        else
            echo "Fichero Log:  \"$codcarga\" cargado correctamente.<br /> $dif_reg  Nuevos Registros ";
    }
    else {
        echo "Error en la creación de la carga <br />" . $my_error . " " . $error_p . " " . $error_l;

        //$fich_error = unlink($ruta_fichero);
        mysql_query("DELETE FROM tcargas WHERE CODCARGA = '$codcarga'");
        mysql_query("DELETE FROM tbuffer WHERE CODCARGA = '$codcarga'");
    }
    mysql_query("SET foreign_key_checks = 1"); // Activa claves foráneas 
} // Fin de la opción nuevo.
//echo $_FILES['FFICHERO_LOG']['error'];
?>
