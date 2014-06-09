<?php

include_once('../functions/db.php');
$link = conectar();
mysql_set_charset('utf8');
set_time_limit(0);
$sql_buff="SELECT  CODCARGA, CODBUFFER, FICHERO_ORIGEN, DESEMPRESA_DESTINO FROM TBUFFER WHERE CODCARGA='000001'"; //<---- falta group by fichero origen y desempresa
$buffresult=mysql_query($sql_buff);

while($fila=  mysql_fetch_object($buffresult)){

$CHKCONTROL = 'S';
$CODCARGA = $fila->CODCARGA; //<---- viene del for
$CODBUFFER = $fila->CODBUFFER; //<---- viene del for

$mot = 'PRIMERA CARGA';
$BUFFER_FICHERO_ORIGEN = $fila->FICHERO_ORIGEN; ; //<---- viene del for


// El usuario de alta y modificación se desconoce hasta tanto se capte con WEBSEAL - Terminar


$sql_envb = "SELECT b.CODBUFFER, b.CODCARGA, b.CODENVIO, b.FICHERO_ORIGEN, c.CODCANAL, b.NOMBREMAQUINA_ORIGEN, b.IP_ORIGEN, b.IP_DESTINO, b.MOTIVOLOG, b.DESEMPRESA_DESTINO, b.NOMBREMAQUINA_DESTINO FROM tbuffer b, tcargas c  WHERE b.CODCARGA='$CODCARGA' AND b.CODBUFFER='$CODBUFFER' AND b.CODCARGA=c.CODCARGA  ";
$result = mysql_query($sql_envb);
$codenv = mysql_result($result, 0, 'CODENVIO');
$codbuf = mysql_result($result, 0, 'CODBUFFER');
$codcarga=mysql_result($result, 0, 'CODCARGA');
$nom_fichero = mysql_result($result, 0, 'FICHERO_ORIGEN');
$canal = mysql_result($result, 0, 'CODCANAL');
$descanal=mysql_result(mysql_query("SELECT DESCANAL FROM tcanales WHERE CODCANAL='$canal' "), 0, 'DESCANAL');
if(strpos($descanal,"XCOM")!== false){$canal='00002';};
if(strpos($descanal,"FTP")!==false){$canal='00004';};
$nombre_maquina = mysql_result($result, 0, 'NOMBREMAQUINA_ORIGEN');
$nombre_maquinad = mysql_result($result, 0, 'NOMBREMAQUINA_DESTINO');
if (!$nombre_maquinad)
    $nombre_maquinad = NULL;
if (!$nombre_maquina)
    $nombre_maquina = NULL;
$ip_origen = mysql_result($result, 0, 'IP_ORIGEN');
if (!$ip_origen)
    $ip_origen = NULL;
$ip_destino = mysql_result($result, 0, 'IP_DESTINO');
if (!$ip_destino)
    $ip_destino = $nombre_maquinad;
$desempresa=mysql_result($result, 0, 'DESEMPRESA_DESTINO');
$desempresa=ltrim(rtrim($desempresa));
$codempresa=mysql_result(mysql_query("SELECT CODEMPRESAPADRE FROM tempresas WHERE DESEMPRESA='$desempresa' "),0,'CODEMPRESAPADRE');
if(!$codempresa) $codempresa=mysql_result(mysql_query("SELECT CODEMPRESA FROM tempresas WHERE DESEMPRESA='$desempresa' "),0,'CODEMPRESA');

if (!$codempresa) {
        if (trim($desempresa) != "") {
            
            $sig_empresa = mysql_result((mysql_query(" SELECT MAX(CODEMPRESA) as max FROM tempresas ")), 0, 'max') + 1;
            $cifras = strlen($sig_empresa);
            $ceros = "";
            for ($i = $cifras; $i < 5; $i++) {
                $ceros.="0";
            }
            $sig_empresa = $ceros . $sig_empresa;
            $sql_insemp = "INSERT INTO TEMPRESAS (CODEMPRESA,DESEMPRESA,CHKACTIVO) values ('$sig_empresa','" . $desempresa . "','S')";
            mysql_query($sql_insemp);
            
            $contnewemp++;
           
$codempresa = $sig_empresa;
        }else $codempresa=0;
    }

    if(substr($nom_fichero, -3)==="(0)") {
    $nom_fichero=  substr($nom_fichero,0,-3);
}
$motivolog = mysql_result($result, 0, 'MOTIVOLOG');
$sql_fich = "SELECT tficheros.FICHERO_ORIGEN, tficheros.CODUSUARIO_BAJA, tficheros.CODCANAL, tficheros.OBSERVACIONES, tficheros.CODENVIO as ncodenv FROM tficheros, v_envios WHERE tficheros.FICHERO_ORIGEN='$nom_fichero' AND tficheros.CODCANAL='$canal' AND tficheros.CODENVIO=v_envios.CODENVIO AND (v_envios.CODEMPRESA='$codempresa' OR v_envios.CODEMPRESAPADRE='$codempresa')  ";
$result_fich = mysql_query($sql_fich);

if(!$codempresa) $codempresa='00000';
$baja = mysql_result($result_fich, 0, 'CODUSUARIO_BAJA');
$obs = mysql_result($result_fich, 0, 'OBSERVACIONES');
$ncodenv=mysql_result($result_fich, 0, 'ncodenv');
$sql_env = "SELECT CODUSUARIO_BAJA FROM tenvios WHERE CODENVIO='$codenv' ";
$result_bajaenv = mysql_query($sql_env);
$baja_env = mysql_result($result_bajaenv, 0, 'CODUSUARIO_BAJA');
/*
if($codempresa!='00000')
$sql_buf = "UPDATE tbuffer SET CHKCONTROL='$CHKCONTROL', FECHA_MODIFICACION=now(), CODUSUARIO_MODIFICACION=NULL WHERE  trim(FICHERO_ORIGEN)=trim('$BUFFER_FICHERO_ORIGEN') AND (DESEMPRESA_DESTINO IN (SELECT DESEMPRESA FROM tempresas WHERE (CODEMPRESA='$codempresa' OR CODEMPRESAPADRE='$codempresa') ) OR NOMBREMAQUINA_DESTINO='$nombre_maquinad' OR IP_DESTINO='$ip_destino' ) ";
else if($nombre_maquinad)
    $sql_buf = "UPDATE tbuffer SET CHKCONTROL='$CHKCONTROL', FECHA_MODIFICACION=now(), CODUSUARIO_MODIFICACION=NULL WHERE  trim(FICHERO_ORIGEN)=trim('$BUFFER_FICHERO_ORIGEN') AND  NOMBREMAQUINA_DESTINO='$nombre_maquinad'  ";    
else if($ip_destino)
    $sql_buf = "UPDATE tbuffer SET CHKCONTROL='$CHKCONTROL', FECHA_MODIFICACION=now(), CODUSUARIO_MODIFICACION=NULL WHERE  trim(FICHERO_ORIGEN)=trim('$BUFFER_FICHERO_ORIGEN') AND IP_DESTINO='$ip_destino' ";    
else
$sql_buf = "UPDATE tbuffer SET CHKCONTROL='$CHKCONTROL', FECHA_MODIFICACION=now(), CODUSUARIO_MODIFICACION=NULL WHERE  trim(FICHERO_ORIGEN)=trim('$BUFFER_FICHERO_ORIGEN') AND DESEMPRESA_DESTINO='$desempresa' ";    
//$sql_buf = "UPDATE tbuffer SET CHKCONTROL='$CHKCONTROL', FECHA_MODIFICACION=now(), CODUSUARIO_MODIFICACION=NULL WHERE CODCARGA='$CODCARGA' AND CODBUFFER='$CODBUFFER'  ";
*/
    $responce= "Estado de control editado correctamente. </br></br>";


if ($CHKCONTROL == 'S') {
    if (!(mysql_num_rows($result_fich))) { 
        // crear nuevo envio y nuevo registro en tficheros (hay que agregar usuario de alta y fecha de alta por ser un registro nuevo)
        mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas para agregar valores nulos
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
                        '00002', " . /* CODTIPOENVIO el tipo de envío de este tipo de inserción es desconocido Terminar */"
                        NULL, " . /* CODENVIO_REMEDY es nulo */"
                        '0000000', " . /* este es el usuario BBVA debería insertarse manualmente Terminar */"
                        '$codempresa', " . /* CODEMPRESA la empresa debería insertarse manualmente ya que no viene en el log. Terminar */"
                        '00000', " . /* CODINTERVINIENTE se desconoce debería insertarse manualmente . Terminar */"
                        '00000', " . /* CODDESTINATARIO se desconoce, debe insertarse manualmente .Terminar */"
                        '00000', " . /* CODFRECUENCIA se desconoce, debe insertarse manualmente .Terminar */"
                        '$canal', " . /* CODCANAL */"
                        '$nombre_maquina', " . /* NOMBREMAQUINA_ORIGEN */"
                        '$ip_origen', " . /* IP_ORIGEN */"
                        '$ip_destino', " . /* IP_DESTINO */"
                        'D', " . /* CHKCIFRADO se desconoce insertar manualmente Terminar */"
                        'FICHERO AUTORIZADO - ANÁLISIS DE CONSOLIDACIÓN', " . /* MOTIVOENVIO Podría tener que cambiarse en el inserción a algo parecido en las observaciones . Terminar */"
                        '00000', " . /* CODMOTIVOBAJA es nulo o desconocido porque no es una baja */"
                        now(), " . /* FECHA_ALTA puede ser la fecha que viene en el log. Terminar */"
                        '0000-00-00 00:00:00', " . /* FECHA_MODIFICACION */"
                        '0000-00-00 00:00:00', " . /* FECHA_BAJA la fecha de baja es nula */"
                        '$webseal', " . /* CODUSUARIO_ALTA el usuario se desconoce aun por WebSeal. Terminar */"
                        NULL, " . /* CODUSUARIO_MODIFICACION puede ser nulo el usuario se desconoce aun por WebSeal. Terminar */"
                        NULL, " . /* CODUSUARIO_BAJA es nulo  */"
                        'INSERCIÓN MANUAL POR CAMBIO DE ESTADO DE CONTROL'" . /* OBSERVACIONES POR CAMBIO EN ESTADO DE CONTROL */"
                )";
        mysql_query($sql_newenv);
        $err = mysql_error();
        // insertar el nuevo código de envío en tbuffer
        $sql_codenviobuf="UPDATE tbuffer SET CODENVIO='$sig_env', MOTIVOLOG='FICHERO AUTORIZADO - ANÁLISIS DE CONSOLIDACIÓN' WHERE CODCARGA='$codcarga' AND trim(FICHERO_ORIGEN)=trim('$BUFFER_FICHERO_ORIGEN') AND DESEMPRESA_DESTINO IN (SELECT DESEMPRESA FROM tempresas WHERE (CODEMPRESA='$codempresa' OR CODEMPRESAPADRE='$codempresa')) OR NOMBREMAQUINA_DESTINO='$nombre_maquinad' OR IP_DESTINO='$ip_destino' )  "; 
        mysql_query($sql_codenviobuf);
        $err = mysql_error();
        
        //calcular id de nuevo fichero
        $sig_fich = mysql_result((mysql_query(" SELECT MAX(CODFICHERO) as max FROM tficheros ")), 0, 'max') + 1;
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
                        '000001', " . /* CODFICHERO NUEVO es el único fichero que se inserta para el envío campo autonumérico respecto al mismo CODENVIO .Terminar Buscar como autoincrementar sin buscar maxid */"
                        '00000', " . /* CODCLASIFICACION por los momentos nulo */"
                        '00000', " . /* NIVEL_LOPD por los momentos nulo */"
                        '$canal', " . /* CODCANAL código de canal */"
                        '$nom_fichero', " . /* nombre de fichero */"
                        NULL, " . /* fichero destino nulo */"
                        NULL, " . /* ruta origen nulo */"
                        UUAA, " . /* UUAA nula por desconocimiento Terminar */"
                        '00000', " . /* codmotivobaja nulo */"
                        now(), " . /* fecha_alta */"
                        '0000-00-00 00:00:00', " . /* fecha_modificacion */"
                        '0000-00-00 00:00:00', " . /* fecha_baja nula */"
                        '$webseal', " . /* el usuario se desconoce aun por WebSeal. Terminar */"
                        NULL, " . /* el usuario_modificacion se desconoce aun por WebSeal. Terminar */"
                        NULL, " . /* el usuario_baja es nulo */"
                        'INSERCIÓN MANUAL POR CAMBIO DE ESTADO DE CONTROL' , " . /* observaciones de nuevo registro por inserción al autorizar en estados de control */"
                        NULL " . /* codfichero padre nulo (no es una máscara) */"
                )";
        // Calcular el codclasificacion y nivel lopd del fichero, etc . Terminar
        mysql_query($sql_newfich);
        $err = mysql_error();
        mysql_query("SET foreign_key_checks = 1"); // activa claves foráneas
        $responce="Fichero: $nom_fichero dado de alta correctamente con Código de Envío: $sig_env </br>";
       $data->codenv=$sig_env;
    }
}

mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas para agregar valores nulos
mysql_query($sql_buf); // SE MODIFICA EL ESTADO DE CONTROL
$my_error = mysql_error();
mysql_query("SET foreign_key_checks = 1"); // activa claves foráneas para Agregar valores nulos



// json con código de envío 
$data->mensaje=$responce;

echo $data->mensaje."</br>";
}



?>
