<?

include_once('../functions/db.php');
include_once('../functions/date.php');

$link = conectar();
mysql_set_charset('utf8');



if ($_POST['opcion'] == "edremfich") {


    $edEnv = $_POST['edremEnv'];
    
    // datos de envío nuevo
    $contacto = $_POST['ERCONTACTO'];
    $codremedy = $_POST['ERCODENVIO_REMEDY'];
    $codempresa = $_POST['ERCODEMPRESA'];
    $codinterviniente = $_POST['ERCODINTERVINIENTE'];
    $coddestinatario = $_POST['ERCODDESTINATARIO'];
    $codfrecuencia = $_POST['ERCODFRECUENCIA'];
    $codcanal = $_POST['ERFILE_CODCANAL'];
    if ($codcanal == '00000')
        $err = "Se debe seleccionar un canal";
    $codautoriza = trim($_POST['ERCODAUTORIZA']);
    //if ($codautoriza == '0000000' || $codautoriza == '')
    //$err = "Se debe introducir el usuario BBVA que solicita la cesión ";
    $maquina = $_POST['ERNOMBREMAQUINA_ORIGEN'];
    $ipo = $_POST['ERIP_ORIGEN'];
    $ipd = $_POST['ERIP_DESTINO'];

    $chkcifrado = $_POST['ERCHKCIFRADO'];
    if (!$chkcifrado)
        $chkcifrado = 'D';

    $codtipoenvio = $_POST['ERCODTIPOENVIO'];
    //if ($codtipoenvio == '00000')
      //  $err = "Se debe seleccionar el origen del registro";
    $motivoenvio = $_POST['ERMOTIVOENVIO'];
    if (!$motivoenvio)
        $err = "El campo de Motivo de Envío es obligatorio";


    $eobservaciones = $_POST['EROBSERVACIONES'];


    if (!$err) {

        set_time_limit(0);
        mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas 
        //insertar en tenvios


        $sig_env = $edEnv;
        $sql_newenv = "UPDATE tenvios SET 
                         
                        CODTIPOENVIO = '$codtipoenvio',
                        CODENVIO_REMEDY = '$codremedy', 
                        CODUSUARIO = '$codautoriza',
                        CODEMPRESA = '$codempresa',
                        CODINTERVINIENTE = '$codinterviniente',
                        CODDESTINATARIO = '$coddestinatario',
                        CODFRECUENCIA = '$codfrecuencia',
                        CODCANAL = '$codcanal',
                        NOMBREMAQUINA_ORIGEN = '$maquina',
                        IP_ORIGEN = '$ipo',
                        IP_DESTINO = '$ipd',
                        CHKCIFRADO = '$chkcifrado',
                        MOTIVOENVIO = '$motivoenvio',
                        FECHA_MODIFICACION = now(),
                        CODUSUARIO_MODIFICACION = '$webseal', " . /* CODUSUARIO_MODIFICACION  el usuario se desconoce aun por WebSeal. Terminar */"
                        OBSERVACIONES = '$eobservaciones' 
                        WHERE CODENVIO='$edEnv'";
        mysql_query($sql_newenv);
        $err = mysql_error();
        //actualizar valores nulos
        if (!$codremedy)
            mysql_query("UPDATE tenvios SET CODENVIO_REMEDY=NULL WHERE CODENVIO='$sig_env'");
        if (!$maquina)
            mysql_query("UPDATE tenvios SET NOMBREMAQUINA_ORIGEN=NULL WHERE CODENVIO='$sig_env'");
        if (!$ipo)
            mysql_query("UPDATE tenvios SET IP_ORIGEN=NULL WHERE CODENVIO='$sig_env'");
        if (!$ipd)
            mysql_query("UPDATE tenvios SET IP_DESTINO=NULL WHERE CODENVIO='$sig_env'");

        if (!$eobservaciones)
            mysql_query("UPDATE tenvios SET OBSERVACIONES=NULL WHERE CODENVIO='$sig_env'");

        mysql_query("UPDATE tficheros SET CODCANAL='$codcanal' WHERE CODENVIO='$sig_env'");

        if ($err)
            $err = "Ha ocurrido un error " . mysql_error();
        else
            $responce = "Envío $edEnv editado correctamente";

        mysql_query("SET foreign_key_checks = 1"); // Activa claves foráneas
    }


    $campos = array(
        'ERROR' => $err,
        'SUCCESS' => $responce,
        'FORM' => 'edrem',
        'ENV' => $edEnv
    );

    if (!$err) {
        mysql_query("SET foreign_key_checks = 0");
        if (trim($contacto) != "") {
            $existecontacto = mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM tcontactos WHERE DESCONTACTO='$contacto' AND CODEMPRESA='$codempresa' AND CODUSUARIO_BAJA IS NULL "), 0, 'cuenta');
            if (!$existecontacto) {
                $sig_contacto = mysql_result((mysql_query(" SELECT MAX(CODCONTACTO) as max FROM tcontactos ")), 0, 'max') + 1;
                $cifras = strlen($sig_contacto);
                $ceros = "";
                for ($i = $cifras; $i < 6; $i++) {
                    $ceros.="0";
                }
                $sig_contacto = $ceros . $sig_contacto;

                mysql_query("INSERT INTO tcontactos (CODEMPRESA, CODCONTACTO, DESCONTACTO, FECHA_ALTA, CODUSUARIO_ALTA) values ('$codempresa','$sig_contacto','$contacto',now(), '$webseal')");
                // Se desconoce el usuario hasta tanto no se tome de la variable de webSeal .Terminar
                $codcontacto = $sig_contacto;
            } else {
                $codcontacto = mysql_result(mysql_query("SELECT CODCONTACTO FROM tcontactos WHERE DESCONTACTO='$contacto' AND CODEMPRESA='$codempresa'  "), 0, 'CODCONTACTO');
            }
            mysql_query("DELETE FROM tcontactos_envios WHERE CODENVIO='$edEnv' ");
            mysql_query("INSERT INTO tcontactos_envios (CODENVIO,CODCONTACTO) values ('$edEnv','$codcontacto') ");
        }
        mysql_query("SET foreign_key_checks = 1");
    }
    echo json_encode($campos);
} // Fin de la opción editar Envío. 
else
if ($_POST['opcion'] == "editfich") {

    $edFich = $_POST['edfichSel'];
    $edEnv = $_POST['edenvSel'];
    $newenv=$_POST['EDCODENVIO'];
    $forigen = $_POST['EFICHERO_ORIGEN'];
    if (trim($forigen) === "")
        $error = "El campo Fichero de Origen es obligatorio";
    $fdestino = $_POST['EFICHERO_DESTINO'];
    $rorigen = $_POST['ERUTA_ORIGEN'];
    $uuaa = $_POST['EUUAA'];
    $codclasificacion = $_POST['ECODCLASIFICACION'];
    $codlopd = $_POST['ECODNIVEL_LOPD'];
    $obs = $_POST['EDOBSERVACIONES'];
    
     $sig_mfich = mysql_result((mysql_query(" SELECT MAX(CODFICHERO) as max FROM tficheros WHERE CODENVIO='$newenv' ")), 0, 'max') + 1;
        $cifras = strlen($sig_mfich);
        $ceros = "";
        for ($i = $cifras; $i < 6; $i++) {
            $ceros.="0";
        }
        $sig_mfich = $ceros . $sig_mfich;
    
    
    mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas
    
    if (!$error)
        if($newenv!=$edEnv){
            $conssqlmenv=mysql_query("SELECT CODENVIO FROM TENVIOS WHERE CODENVIO='$newenv' ");
            $existenvio=mysql_num_rows($conssqlmenv);
        if($existenvio) mysql_query("UPDATE tficheros SET  CODENVIO='$newenv',FECHA_MODIFICACION=now(), CODUSUARIO_MODIFICACION='$webseal', FICHERO_ORIGEN='$forigen', FICHERO_DESTINO='$fdestino', RUTA_ORIGEN='$rorigen', UUAA='$uuaa', CODCLASIFICACION='$codclasificacion', CODNIVEL_LOPD='$codlopd', OBSERVACIONES='$obs',CODFICHERO='$sig_mfich' WHERE CODENVIO='$edEnv' and CODFICHERO='$edFich' AND CODCANAL IN (SELECT CODCANAL FROM TENVIOS WHERE CODENVIO='$newenv') ");
            else $error="El código de envío introducido no existe";
        }else
        mysql_query("UPDATE tficheros SET  FECHA_MODIFICACION=now(), CODUSUARIO_MODIFICACION='$webseal', FICHERO_ORIGEN='$forigen', FICHERO_DESTINO='$fdestino', RUTA_ORIGEN='$rorigen', UUAA='$uuaa', CODCLASIFICACION='$codclasificacion', CODNIVEL_LOPD='$codlopd', OBSERVACIONES='$obs' WHERE CODENVIO='$edEnv' and CODFICHERO='$edFich' ");  
// el usuario está dos veces el usuario se desconoce hasta tanto se tome la variable de Webseal. Terminar
    $err = mysql_error();

    if ($error == "" && $err == "") {
        $responce = "Fichero: $forigen con código de Envío: $edEnv editado correctamente ";
    } else
    if ($error == "")
        $error = "Error en la edición del fichero fichero: <br />" . $err;
    $campos = array(
        'ERROR' => $error,
        'SUCCESS' => $responce,
        'FORM' => 'edfich',
        'ENV' => $edEnv
    );
    echo json_encode($campos);

    mysql_query("SET foreign_key_checks = 0");
} // Fin de la opción editar fichero. 
else
if ($_POST['opcion'] == "activar" || $_GET['activar'] == "1") {
    
     if ($_GET['activar'] == "1") {
        $activarFich = $_GET['activarFich'];
        $activarEnv = $_GET['activarEnv'];
    } else {
    $activarFich = $_POST['activarFich'];
    $activarEnv = $_POST['activarEnv'];
    }
    mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas 
    mysql_query("UPDATE tficheros SET CODUSUARIO_BAJA=NULL, FECHA_BAJA='0000-00-00 00:00:00', CODMOTIVOBAJA='00000', FECHA_MODIFICACION=now(), CODUSUARIO_MODIFICACION='$webseal' WHERE CODENVIO='$activarEnv' and CODFICHEROPADRE='$activarFich' ");
    mysql_query("UPDATE tficheros SET CODUSUARIO_BAJA=NULL, FECHA_BAJA='0000-00-00 00:00:00', CODMOTIVOBAJA='00000', FECHA_MODIFICACION=now(), CODUSUARIO_MODIFICACION='$webseal' WHERE CODENVIO='$activarEnv' and CODFICHERO='$activarFich' ");
    // el usuario está dos veces el usuario se desconoce hasta tanto se tome la variable de Webseal. Terminar
    $err = mysql_error();

    if ($err == "") {


        $sql_num_fich = "SELECT * FROM tficheros f,tenvios e WHERE f.CODENVIO='$activarEnv' and f.CODUSUARIO_BAJA IS NULL AND f.CODFICHEROPADRE IS NULL AND e.CODENVIO=f.CODENVIO AND e.CODUSUARIO_BAJA IS NOT NULL";
        $result_num_env = mysql_query($sql_num_fich);
        $num_fich_env = mysql_num_rows($result_num_env);
        if ($num_fich_env) {

            $sql_bajaenv = "UPDATE tenvios SET CODUSUARIO_BAJA= NULL, FECHA_MODIFICACION= now(), FECHA_BAJA= '0000-00-00 00:00:00', CODMOTIVOBAJA='00000', CODUSUARIO_MODIFICACION='$webseal' WHERE CODENVIO='$activarEnv' ";
            // El usuario de baja y modificación se desconoce hasta tanto se capte con WEBSEAL - Terminar
            mysql_query($sql_bajaenv);


            $env++;
        }

        if ($env)
            $responce = "Fichero dado de alta correctamente. El Envío: $activarEnv se encontraba de baja y ha sido dado de alta. ";
        else
            $responce = "Fichero dado de alta correctamente con código de envío $activarEnv ";
    } else
        $err = "Error el dar de baja el fichero: <br />" . $err;
    $campos = array(
        'ERROR' => $err,
        'SUCCESS' => $responce,
        'ENV' => $activarEnv
    );
    echo json_encode($campos);

    mysql_query("SET foreign_key_checks = 1");
} // Fin de la opción activar. 
else
if ($_POST['opcion'] == "borrar" || $_GET['borrar'] == "1") {

    $mot=$_GET['mot'];
    if ($_GET['borrar'] == "1") {
        $borrarFich = $_GET['borrarFich'];
        $borrarEnv = $_GET['borrarEnv'];
    } else {
        $borrarFich = $_POST['borrarFich'];
        $borrarEnv = $_POST['borrarEnv'];
    }

    
    
    mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas 
    mysql_query("UPDATE tficheros SET CODUSUARIO_BAJA='$webseal', FECHA_BAJA=now(), CODMOTIVOBAJA='$mot', FECHA_MODIFICACION=now(), CODUSUARIO_MODIFICACION='$webseal' WHERE CODENVIO='$borrarEnv' and CODFICHEROPADRE='$borrarFich' ");
    mysql_query("UPDATE tficheros SET CODUSUARIO_BAJA='$webseal', FECHA_BAJA=now(), CODMOTIVOBAJA='$mot', FECHA_MODIFICACION=now(), CODUSUARIO_MODIFICACION='$webseal' WHERE CODENVIO='$borrarEnv' and CODFICHERO='$borrarFich' ");
    // el usuario está dos veces el usuario se desconoce hasta tanto se tome la variable de Webseal. Terminar
    $err = mysql_error();

    if ($err == "") {
        $responce = "Fichero dado de baja";

        $sql_num_fich = "SELECT * FROM tficheros WHERE CODENVIO='$borrarEnv' and CODUSUARIO_BAJA IS NULL AND CODFICHEROPADRE IS NULL";
        $result_num_env = mysql_query($sql_num_fich);
        $num_fich_env = mysql_num_rows($result_num_env);
        if (!$num_fich_env) {

            $sql_bajaenv = "UPDATE tenvios SET CODUSUARIO_BAJA= '$webseal', FECHA_MODIFICACION= now(), FECHA_BAJA= now(), CODMOTIVOBAJA='NO CONTIENE FICHEROS ACTIVOS', CODUSUARIO_MODIFICACION='$webseal' WHERE CODENVIO='$borrarEnv' ";
            // El usuario de baja y modificación se desconoce hasta tanto se capte con WEBSEAL - Terminar
            mysql_query($sql_bajaenv);
            $responce = "El Envío: $borrarEnv ha sido dado de baja por no contener ficheros activos";
        }
    } else
        $err = "Error el dar de baja el fichero: <br />" . $err;
    $campos = array(
        'ERROR' => $err,
        'SUCCESS' => $responce,
        'ENV' => $borrarEnv
    );
    echo json_encode($campos);

    mysql_query("SET foreign_key_checks = 1");
} // Fin de la opción borrar. 
else if ($_POST['opcion'] == "borrarem") {


    $borrarEnv = $_POST['borrarEnv'];
    $mot = $_POST['NCODMOTIVOBAJA'];


    mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas 

    mysql_query("UPDATE tficheros SET CODUSUARIO_BAJA='$webseal', FECHA_BAJA=now(), CODMOTIVOBAJA='$mot', FECHA_MODIFICACION=now(), CODUSUARIO_MODIFICACION='$webseal' WHERE CODENVIO='$borrarEnv'  ");
    // el usuario está dos veces el usuario se desconoce hasta tanto se tome la variable de Webseal. Terminar
    $err = mysql_error();

    if ($err == "") {
        $responce = "Envío completo dado de baja";

        $sql_num_fich = "SELECT * FROM tficheros WHERE CODENVIO='$borrarEnv' and CODUSUARIO_BAJA IS NULL AND CODFICHEROPADRE IS NULL";
        $result_num_env = mysql_query($sql_num_fich);
        $num_fich_env = mysql_num_rows($result_num_env);
        if (!$num_fich_env) {

            $sql_bajaenv = "UPDATE tenvios SET CODUSUARIO_BAJA= '$webseal', FECHA_MODIFICACION= now(), FECHA_BAJA= now(), CODMOTIVOBAJA='ENVÍO DADO DE BAJA CON TODOS SUS FICHEROS', CODUSUARIO_MODIFICACION='$webseal' WHERE CODENVIO='$borrarEnv' ";
            // El usuario de baja y modificación se desconoce hasta tanto se capte con WEBSEAL - Terminar
            mysql_query($sql_bajaenv);
            $responce = "El Envío: $borrarEnv y todos sus ficheros han sido dados de baja";
        }
    } else
        $err = "Error el dar de baja el fichero: <br />" . $err;
    $campos = array(
        'ERROR' => $err,
        'SUCCESS' => $responce,
        'ENV' => $borrarEnv
    );
    echo json_encode($campos);

    mysql_query("SET foreign_key_checks = 1");
} // Fin de la opción borrar envío. 
else if ($_POST['opcion'] == "nuevo") { // Código para crear.
// datos de envío nuevo
    $contacto = $_POST['NCONTACTO'];
    $codremedy = $_POST['NCODENVIO_REMEDY'];
    $codempresa = $_POST['NCODEMPRESA'];
    $codinterviniente = $_POST['NCODINTERVINIENTE'];
    $coddestinatario = $_POST['NCODDESTINATARIO'];
    $codfrecuencia = $_POST['NCODFRECUENCIA'];
    $codcanal = $_POST['NFILE_CODCANAL'];
    if ($codcanal == '00000')
        $err = "Se debe seleccionar un canal";
    $codautoriza = trim($_POST['NCODAUTORIZA']);
    if ($codautoriza == '0000000' || $codautoriza == "")
        $err = "Se debe introducir el usuario BBVA que solicita la cesión ";
    $maquina = $_POST['NNOMBREMAQUINA_ORIGEN'];
    $ipo = $_POST['NIP_ORIGEN'];
    $ipd = $_POST['NIP_DESTINO'];

    $chkcifrado = $_POST['NCHKCIFRADO'];
    if (!$chkcifrado)
        $chkcifrado = 'D';

    $codtipoenvio = $_POST['NCODTIPOENVIO'];
    //if ($codtipoenvio == '00000')
        //$err = "Se debe seleccionar el origen del registro";
    $motivoenvio = $_POST['NMOTIVOENVIO'];
    if (!$motivoenvio)
        $err = "El campo de Motivo de Envío es obligatorio";

    $efecha_alta = fechaAMysql2($_POST['NENVIO_FECHA_ALTA']);
    if (!$efecha_alta)
        $err = "El formato de la fecha introducida no es válido";
    $eobservaciones = $_POST['EOBSERVACIONES'];
    $observaciones = $_POST['OBSERVACIONES'];
    // datos de fichero nuevo

    $forigen = $_POST['NFICHERO_ORIGEN'];
    if (!(trim($forigen)))
        $err = "El campo Fichero de Origen es obligatorio";
    $repetido = mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM tficheros f,tenvios e where trim(f.FICHERO_ORIGEN)='" . trim($forigen) . "' AND e.CODEMPRESA='$codempresa' AND f.CODCANAL='$codcanal' "), 0, 'cuenta');
    if ($repetido > 0)
        $err = "Ya existe un fichero con el mismo nombre, en el canal y empresa seleccionados ";

    $fdestino = $_POST['NFICHERO_DESTINO'];
    $rutaorigen = $_POST['NRUTA_ORIGEN'];
    $uuaa = $_POST['NUUAA'];
    $codclasificacion = $_POST['NCODCLASIFICACION'];
    $codlopd = $_POST['NCODNIVEL_LOPD']; //repetido por ser el mismo canal
    $fcodcanal = $_POST['NFILE_CODCANAL'];
    $ffecha_alta = fechaAMysql2($_POST['NFILE_FECHA_ALTA']);
    //if (!$ffecha_alta)
    //$err = "El formato de la fecha introducida no es válido";
    if (!$err) {

        set_time_limit(0);
        mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas 
        //insertar en tenvios
        //calcular siguiente ID
        $sig_env = mysql_result((mysql_query(" SELECT MAX(CODENVIO) as max FROM tenvios ")), 0, 'max') + 1;
        $cifras = strlen($sig_env);
        $ceros = "";
        for ($i = $cifras; $i < 6; $i++) {
            $ceros.="0";
        }
        $sig_env = $ceros . $sig_env;
        $sql_newenv = "INSERT INTO tenvios ( 
                        CODENVIO, 
                        CODTIPOENVIO,
                        CODENVIO_REMEDY,
                        CODUSUARIO,
                        CODEMPRESA,
                        CODINTERVINIENTE,
                        CODDESTINATARIO,
                        CODFRECUENCIA,
                        CODCANAL,
                        NOMBREMAQUINA_ORIGEN,
                        IP_ORIGEN,
                        IP_DESTINO,
                        CHKCIFRADO,
                        MOTIVOENVIO,
                        CODMOTIVOBAJA,
                        FECHA_ALTA,
                        FECHA_MODIFICACION,
                        FECHA_BAJA,
                        CODUSUARIO_ALTA,
                        CODUSUARIO_MODIFICACION,
                        CODUSUARIO_BAJA,
                        OBSERVACIONES
                )
                values ( 
                        '$sig_env', " . /* CODENVIO autonumérico . Terminar (buscar como incrementar sin buscar maxid) */"
                        '$codtipoenvio', " . /* CODTIPOENVIO el tipo de envío de este tipo de inserción es desconocido Terminar */"
                        '$codremedy', " . /* CODENVIO_REMEDY es nulo */"
                        '$codautoriza', " . /* este es el usuario que autoriza la cesión */"
                        '$codempresa', " . /* CODEMPRESA la empresa debería insertarse manualmente ya que no viene en el log. Terminar */"
                        '$codinterviniente', " . /* CODINTERVINIENTE se desconoce debería insertarse manualmente . Terminar */"
                        '$coddestinatario', " . /* CODDESTINATARIO se desconoce, debe insertarse manualmente .Terminar */"
                        '$codfrecuencia', " . /* CODFRECUENCIA se desconoce, debe insertarse manualmente .Terminar */"
                        '$codcanal', " . /* CODCANAL */"
                        '$maquina', " . /* NOMBREMAQUINA_ORIGEN */"
                        '$ipo', " . /* IP_ORIGEN */"
                        '$ipd', " . /* IP_DESTINO */"
                        '$chkcifrado', " . /* CHKCIFRADO se desconoce insertar manualmente Terminar */"
                        '$motivoenvio', " . /* MOTIVOENVIO Podría tener que cambiarse en el inserción a algo parecido en las observaciones . Terminar */"
                        '00000', " . /* CODMOTIVOBAJA es nulo o desconocido porque no es una baja */"
                        '$efecha_alta', " . /* FECHA_ALTA puede ser la fecha que viene en el log. Terminar */"
                        '0000-00-00 00:00:00', " . /* FECHA_MODIFICACION */"
                        '0000-00-00 00:00:00', " . /* FECHA_BAJA la fecha de baja es nula */"
                        '$webseal', " . /* CODUSUARIO_ALTA el usuario se desconoce aun por WebSeal. Terminar */"
                        NULL, " . /* CODUSUARIO_MODIFICACION puede ser nulo el usuario se desconoce aun por WebSeal. Terminar */"
                        NULL, " . /* CODUSUARIO_BAJA es nulo  */"
                        '$eobservaciones'" . /* OBSERVACIONES para  envío */"
                )";
        mysql_query($sql_newenv);
        $err = mysql_error();
        //actualizar valores nulos
        if (!$codremedy)
            mysql_query("UPDATE tenvios SET CODENVIO_REMEDY=NULL WHERE CODENVIO='$sig_env'");
        if (!$maquina)
            mysql_query("UPDATE tenvios SET NOMBREMAQUINA_ORIGEN=NULL WHERE CODENVIO='$sig_env'");
        if (!$ipo)
            mysql_query("UPDATE tenvios SET IP_ORIGEN=NULL WHERE CODENVIO='$sig_env'");
        if (!$ipd)
            mysql_query("UPDATE tenvios SET IP_DESTINO=NULL WHERE CODENVIO='$sig_env'");

        if (!$observaciones)
            mysql_query("UPDATE tenvios SET OBSERVACIONES=NULL WHERE CODENVIO='$sig_env'");


        //calcular id de nuevo fichero
        $sig_fich = mysql_result((mysql_query(" SELECT MAX(CODFICHERO) as max FROM tficheros WHERE CODENVIO='$sig_env' ")), 0, 'max') + 1;
        $cifras = strlen($sig_fich);
        $ceros = "";
        for ($i = $cifras; $i < 6; $i++) {
            $ceros.="0";
        }
        $sig_fich = $ceros . $sig_fich;
        //insertar  en tficheros
        $sql_newfich = "INSERT INTO tficheros ( 
                        CODENVIO,
                        CODFICHERO,
                        CODCLASIFICACION,
                        CODNIVEL_LOPD,
                        CODCANAL,
                        FICHERO_ORIGEN,
                        FICHERO_DESTINO,
                        RUTA_ORIGEN,
                        UUAA,
                        CODMOTIVOBAJA,
                        FECHA_ALTA,
                        FECHA_MODIFICACION,
                        FECHA_BAJA,
                        CODUSUARIO_ALTA,
                        CODUSUARIO_MODIFICACION,
                        CODUSUARIO_BAJA,
                        OBSERVACIONES,
                        CODFICHEROPADRE
                )
                values (
                        '$sig_env'," . /* CODENVIO actual */"
                        '$sig_fich', " . /* CODFICHERO NUEVO respecto al mismo CODENVIO .Terminar Buscar como autoincrementar sin buscar maxid */"
                        '$codclasificacion', " . /* CODCLASIFICACION por los momentos nulo */"
                        '$codlopd', " . /* por los momentos nulo */"
                        '$codcanal', " . /* CODCANAL código de canal */"
                        '$forigen', " . /* nombre de fichero */"
                        '$fdestino', " . /* fichero destino nulo */"
                        '$rutaorigen', " . /* ruta origen nulo */"
                        '$uuaa', " . /* UUAA nula por desconocimiento Terminar */"
                        '00000', " . /* codmotivobaja nulo porque no es una baja */"
                        now(), " . /* fecha_alta */"
                        '0000-00-00 00:00:00', " . /* fecha_modificacion */"
                        '0000-00-00 00:00:00', " . /* fecha_baja nula */"
                        '$webseal', " . /* el usuario se desconoce aun por WebSeal. Terminar */"
                        NULL, " . /* el usuario_modificacion es nuelo porque es un nuevo registro */"
                        NULL, " . /* el usuario_baja es nulo */"
                        '$observaciones' , " . /* observaciones de nuevo registro */"
                        NULL " . /* codfichero padre nulo (no es una máscara) */"
                )";
        // Calcular el codclasificacion y nivel lopd del fichero, etc . Terminar
        mysql_query($sql_newfich);
        $err = mysql_error();
        //actualizar valores nulos

        if (!$uuaa)
            mysql_query("UPDATE tficheros SET UUAA=NULL WHERE CODENVIO='$sig_env' AND CODFICHERO='$sig_fich' ");
        if (!$rutaorigen)
            mysql_query("UPDATE tficheros SET RUTA_ORIGEN=NULL WHERE CODENVIO='$sig_env' AND CODFICHERO='$sig_fich' ");

        if (!$fdestino)
            mysql_query("UPDATE tficheros SET FICHERO_DESTINO=NULL WHERE CODENVIO='$sig_env' AND CODFICHERO='$sig_fich' ");
        if (!$observaciones)
            mysql_query("UPDATE tficheros SET OBSERVACIONES=NULL WHERE CODENVIO='$sig_env' AND CODFICHERO='$sig_fich' ");
        mysql_query("SET foreign_key_checks = 1"); // activa claves foráneas
        $responce = "Envío: $sig_env dado de alta correctamente.  </br>";

        if ($err)
            $err = "Ha ocurrido un error " . mysql_error();
    }

    mysql_query("SET foreign_key_checks = 1"); // Activa claves foráneas
    $campos = array(
        'ERROR' => $err,
        'SUCCESS' => $responce,
        'ENV' => $sig_env
    );
    if (!$err) {
        mysql_query("SET foreign_key_checks = 0");
        if (trim($contacto) != "") {
            $existecontacto = mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM tcontactos WHERE DESCONTACTO='$contacto'AND CODEMPRESA='$codempresa' AND CODUSUARIO_BAJA IS NULL "), 0, 'cuenta');
            if (!$existecontacto) {
                $sig_contacto = mysql_result((mysql_query(" SELECT MAX(CODCONTACTO) as max FROM tcontactos ")), 0, 'max') + 1;
                $cifras = strlen($sig_contacto);
                $ceros = "";
                for ($i = $cifras; $i < 6; $i++) {
                    $ceros.="0";
                }
                $sig_contacto = $ceros . $sig_contacto;

                mysql_query("INSERT INTO tcontactos (CODEMPRESA, CODCONTACTO, DESCONTACTO, FECHA_ALTA, CODUSUARIO_ALTA) values ('$codempresa','$sig_contacto','$contacto',now(), '$webseal')");
                // Se desconoce el usuario hasta tanto no se tome de la variable de webSeal .Terminar
                $codcontacto = $sig_contacto;
            } else {
                $codcontacto = mysql_result(mysql_query("SELECT CODCONTACTO FROM tcontactos WHERE DESCONTACTO='$contacto' AND CODEMPRESA='$codempresa' "), 0, 'CODCONTACTO');
            }
            mysql_query("DELETE FROM tcontactos_envios WHERE CODENVIO='$sig_env' ");
            mysql_query("INSERT INTO tcontactos_envios (CODENVIO,CODCONTACTO) values ('$sig_env','$codcontacto') ");
        }
        mysql_query("SET foreign_key_checks = 1");
    }
    echo json_encode($campos);
}// Fin de la opción nuevo.
else if ($_POST['opcion'] == "nuevofich") { // Código para crear.
    // datos de fichero nuevo
    $code = $_POST['envSel'];
    $codcanal = mysql_result(mysql_query("SELECT CODCANAL FROM tenvios WHERE CODENVIO='$code'"), 0, 'CODCANAL');
    $codempresa = mysql_result(mysql_query("SELECT CODEMPRESA FROM tenvios WHERE CODENVIO='$code'"), 0, 'CODEMPRESA');


    $observaciones = $_POST['OBSERVACIONES'];
    // datos de fichero nuevo

    $forigen = $_POST['NFICHERO_ORIGEN'];
    if (!(trim($forigen)))
        $err = "El campo Fichero de Origen es obligatorio";

    $repetido = mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM tficheros f,tenvios e where trim(f.FICHERO_ORIGEN)='" . trim($forigen) . "' AND e.CODEMPRESA='$codempresa' AND f.CODCANAL='$codcanal' "), 0, 'cuenta');
    if ($repetido > 0)
        $err = "Ya existe un fichero con el mismo nombre, en el canal y empresa seleccionados ";

    $fdestino = $_POST['NFICHERO_DESTINO'];
    $rutaorigen = $_POST['NRUTA_ORIGEN'];
    $uuaa = $_POST['NUUAA'];
    $codclasificacion = $_POST['NCODCLASIFICACION'];
    $codlopd = $_POST['NCODNIVEL_LOPD']; //repetido por ser el mismo canal
    $fcodcanal = $_POST['NFILE_CODCANAL'];
    $ffecha_alta = fechaAMysql2($_POST['NFILE_FECHA_ALTA']);
    //if (!$ffecha_alta)
    //   $err = "El formato de la fecha introducida no es válido";
    if (!$err) {

        set_time_limit(0);
        mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas 

        $sig_env = $_POST['envSel'];
        $codcanal = mysql_result(mysql_query("SELECT CODCANAL FROM tenvios WHERE CODENVIO='$sig_env'"), 0, 'CODCANAL');
        $err = mysql_error();
        //calcular id de nuevo fichero
        $sig_fich = mysql_result((mysql_query(" SELECT MAX(CODFICHERO) as max FROM tficheros WHERE CODENVIO='$sig_env' ")), 0, 'max') + 1;
        $cifras = strlen($sig_fich);
        $ceros = "";
        for ($i = $cifras; $i < 6; $i++) {
            $ceros.="0";
        }
        $sig_fich = $ceros . $sig_fich;
        //insertar  en tficheros
        $sql_newfich = "INSERT INTO tficheros ( 
                        CODENVIO,
                        CODFICHERO,
                        CODCLASIFICACION,
                        CODNIVEL_LOPD,
                        CODCANAL,
                        FICHERO_ORIGEN,
                        FICHERO_DESTINO,
                        RUTA_ORIGEN,
                        UUAA,
                        CODMOTIVOBAJA,
                        FECHA_ALTA,
                        FECHA_MODIFICACION,
                        FECHA_BAJA,
                        CODUSUARIO_ALTA,
                        CODUSUARIO_MODIFICACION,
                        CODUSUARIO_BAJA,
                        OBSERVACIONES,
                        CODFICHEROPADRE
                )
                values (
                        '$sig_env'," . /* CODENVIO actual */"
                        '$sig_fich', " . /* CODFICHERO NUEVO respecto al mismo CODENVIO .Terminar Buscar como autoincrementar sin buscar maxid */"
                        '$codclasificacion', " . /* CODCLASIFICACION por los momentos nulo */"
                        '$codlopd', " . /* por los momentos nulo */"
                        '$codcanal', " . /* CODCANAL código de canal */"
                        '$forigen', " . /* nombre de fichero */"
                        '$fdestino', " . /* fichero destino nulo */"
                        '$rutaorigen', " . /* ruta origen nulo */"
                        '$uuaa', " . /* UUAA nula por desconocimiento Terminar */"
                        '00000', " . /* codmotivobaja nulo porque no es una baja */"
                        now(), " . /* fecha_alta */"
                        '0000-00-00 00:00:00', " . /* fecha_modificacion */"
                        '0000-00-00 00:00:00', " . /* fecha_baja nula */"
                        '$webseal', " . /* el usuario se desconoce aun por WebSeal. Terminar */"
                        NULL, " . /* el usuario_modificacion es nuelo porque es un nuevo registro */"
                        NULL, " . /* el usuario_baja es nulo */"
                        '$observaciones' , " . /* observaciones de nuevo registro por inserción al autorizar en estados de control */"
                        NULL " . /* codfichero padre nulo (no es una máscara) */"
                )";
        // Calcular el codclasificacion y nivel lopd del fichero, etc . Terminar
        mysql_query($sql_newfich);
        $err = mysql_error();
        //actualizar valores nulos

        if (!$uuaa)
            mysql_query("UPDATE tficheros SET UUAA=NULL WHERE CODENVIO='$sig_env' AND CODFICHERO='$sig_fich' ");
        if (!$rutaorigen)
            mysql_query("UPDATE tficheros SET RUTA_ORIGEN=NULL WHERE CODENVIO='$sig_env' AND CODFICHERO='$sig_fich' ");

        if (!$fdestino)
            mysql_query("UPDATE tficheros SET FICHERO_DESTINO=NULL WHERE CODENVIO='$sig_env' AND CODFICHERO='$sig_fich' ");
        if (!$observaciones)
            mysql_query("UPDATE tficheros SET OBSERVACIONES=NULL WHERE CODENVIO='$sig_env' AND CODFICHERO='$sig_fich' ");
        mysql_query("SET foreign_key_checks = 1"); // activa claves foráneas
        $responce = "Fichero: $forigen dado de alta correctamente </br>";


        $sql_num_fich = "SELECT * FROM tficheros f,tenvios e WHERE f.CODENVIO='$sig_env' and f.CODUSUARIO_BAJA IS NULL AND f.CODFICHEROPADRE IS NULL AND e.CODENVIO=f.CODENVIO AND e.CODUSUARIO_BAJA IS NOT NULL";
        $result_num_env = mysql_query($sql_num_fich);
        $num_fich_env = mysql_num_rows($result_num_env);
        if ($num_fich_env) {

            $sql_bajaenv = "UPDATE tenvios SET CODUSUARIO_BAJA= NULL, FECHA_MODIFICACION= now(), FECHA_BAJA= '0000-00-00 00:00:00', CODUSUARIO_MODIFICACION='$webseal' WHERE CODENVIO='$sig_env' ";
            // El usuario de baja y modificación se desconoce hasta tanto se capte con WEBSEAL - Terminar
            mysql_query($sql_bajaenv);


            $env++;
        }

        if ($env)
            $responce.=" El Envío: $sig_env se encontraba de baja y ha sido dado de alta correctamente ";
        else
            $responce.=" con código de Envío $sig_env ";


        if ($err)
            $err = "Ha ocurrido un error " . mysql_error();
    }

    mysql_query("SET foreign_key_checks = 1"); // Activa claves foráneas
    $campos = array(
        'ERROR' => $err,
        'SUCCESS' => $responce,
        'ENV' => $sig_env
    );


    echo json_encode($campos);
} //fin nuevifich
if ($_GET['borrarp']) {

    $borrarFich = $_GET['borrarFich'];
    $borrarEnv = $_GET['borrarEnv'];

    mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas 
    mysql_query("DELETE FROM tficheros WHERE (CODFICHERO='$borrarFich' OR CODFICHEROPADRE='$borrarFich') AND CODENVIO='$borrarEnv'  ");
    $err = mysql_error();

    mysql_query("DELETE from tenvios  WHERE CODENVIO NOT IN (SELECT CODENVIO FROM TFICHEROS WHERE CODFICHEROPADRE IS NULL)");
    mysql_query("DELETE from tficheros  WHERE CODENVIO NOT IN (SELECT CODENVIO FROM TENVIOS)");
    $err = mysql_error();

    if ($err == "") {
        $responce = "Fichero(s) eliminados";
    } else
        $err = "Error al eliminar fichero: <br />" . $err;
    $campos = array(
        'ERROR' => $err,
        'SUCCESS' => $responce,
        'ENV' => $borrarEnv
    );
    echo json_encode($campos);

    mysql_query("SET foreign_key_checks = 1");
} // Fin de la opción borrar. 
?>
