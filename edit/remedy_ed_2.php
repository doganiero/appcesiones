<?

include_once('../functions/db.php');
include_once('../functions/date.php');

$link = conectar();
mysql_set_charset('utf8');


if ($_POST['opcion'] == "nuevo") { // Código para crear.
// datos de envío nuevo
    $contacto = $_POST['NCONTACTO'];
    $codremedy = $_POST['NCODENVIO_REMEDY'];
    $codempresa = $_POST['NCODEMPRESA'];
    $codinterviniente = $_POST['NCODINTERVINIENTE'];
    $coddestinatario = $_POST['NCODDESTINATARIO'];
    $codfrecuencia = $_POST['NCODFRECUENCIA'];
    $codcanal = $_POST['NCODCANAL'];
    $codcarga = $_POST['NCODCARGA'];
    /*if (strpos($descanal, "XCOM") !== false) {
        $codcanal = '00002';
    } else
    if (strpos($descanal, "FTP") !== false) {
        $codcanal = '00004';
    } else
    if ($descanal == "EDITRAN") {
        $codcanal = '00001';
    } else {
        $codcanal = mysql_result(mysql_query("SELECT CODCANAL FROM tcargas WHERE DESCANAL='$descanal' "), 0, 'CODCANAL');
    } */
     if ($codcanal == '00003') $codcanal = mysql_result(mysql_query("SELECT CODCANAL FROM tcargas WHERE CODCARGA='$codcarga' "), 0, 'CODCANAL');
    if (!$codcanal) $codcanal = '00000';
    

    $codautoriza = trim($_POST['NCODAUTORIZA']);
   // if ($codautoriza == '0000000' || $codautoriza == "")
     //   $err = "Se debe introducir el usuario BBVA que solicita la cesión ";
    $maquina = $_POST['NNOMBREMAQUINA_ORIGEN'];
    $ipo = $_POST['NIP_ORIGEN'];
    $ipd = $_POST['NIP_DESTINO'];

    $chkcifrado = $_POST['NCHKCIFRADO'];
    if (!$chkcifrado)
        $chkcifrado = 'D';

    $codtipoenvio = $_POST['NCODTIPOENVIO'];
    //if ($codtipoenvio == '00000')
      //  $err = "Se debe seleccionar el origen del registro";
    $motivoenvio = $_POST['NMOTIVOENVIO'];
    //if (!$motivoenvio)
      //  $err = "El campo de Motivo de Envío es obligatorio";

    $efecha_alta = fechaAMysql2($_POST['NENVIO_FECHA_ALTA']);
   // if (!$efecha_alta)
     //   $err = "El formato de la fecha introducida no es válido";
    $eobservaciones = $_POST['EOBSERVACIONES'];
   //$observaciones = $_POST['OBSERVACIONES'];
    // datos de fichero nuevo

   // $forigen = $_POST['NFICHERO_ORIGEN'];
    //if (!(trim($forigen)))
      //  $err = "El campo Fichero de Origen es obligatorio";
    //$repetido = mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM tficheros f,tenvios e where trim(f.FICHERO_ORIGEN)='" . trim($forigen) . "' AND e.CODEMPRESA='$codempresa' AND f.CODCANAL='$codcanal' "), 0, 'cuenta');
    //if ($repetido > 0)
      //  $err = "Ya existe un fichero con el mismo nombre, en el canal y empresa seleccionados ";

    //$fdestino = $_POST['NFICHERO_DESTINO'];
    //$rutaorigen = $_POST['NRUTA_ORIGEN'];
    //$uuaa = $_POST['NUUAA'];
    //$codclasificacion = $_POST['NCODCLASIFICACION'];
    //$codlopd = $_POST['NCODNIVEL_LOPD']; //repetido por ser el mismo canal
    //$fcodcanal = $_POST['NFILE_CODCANAL'];
    //$ffecha_alta = fechaAMysql2($_POST['NFILE_FECHA_ALTA']);
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

        if (!$eobservaciones)
            mysql_query("UPDATE tenvios SET OBSERVACIONES=NULL WHERE CODENVIO='$sig_env'");


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

?>
