<?

include_once('../functions/db.php');
$link = conectar();
mysql_set_charset('utf8');
set_time_limit(0);
$CHKCONTROL=$_GET['CHKCONTROL'];
$USRENV=$_GET['USRENV'];

$json=$_POST['json'];
$json = json_decode(stripslashes($json), true);

 $sig_env = mysql_result((mysql_query(" SELECT MAX(CODENVIO) as max FROM tenvios ")), 0, 'max');
        $cifras = strlen($sig_env);
        $ceros = "";
        for ($i = $cifras; $i < 6; $i++) {
            $ceros.="0";
        }
        $sig_env = $ceros . $sig_env;
if($USRENV)  
{
    $sig_env=$USRENV;   
    $usrdescanal=mysql_result(mysql_query("SELECT DESCANAL FROM v_envios WHERE CODENVIO=$sig_env"),0,'DESCANAL');
     if (strpos($usrdescanal, "XCOM") !== false) {
        $usrcodcanal = '00002';
    } else
    if (strpos($usrdescanal, "FTP") !== false) {
        $usrcodcanal = '00004';
    } else
    if ($usrdescanal == "EDITRAN") {
        $usrcodcanal = '00001';
    } else {
        $usrcodcanal = mysql_result(mysql_query("SELECT CODCANAL FROM tcanales WHERE DESCANAL='$usrdescanal' "), 0, 'CODCANAL');
    } 
    
    if (!$usrcodcanal) $usrcodcanal = '00000';
    $usrdesempresa=mysql_result(mysql_query("SELECT DESEMPRESA FROM v_envios WHERE CODENVIO=$sig_env"),0,'DESEMPRESA');
    $usripdestino=mysql_result(mysql_query("SELECT IP_DESTINO FROM v_envios WHERE CODENVIO=$sig_env"),0,'IP_DESTINO');
}
foreach($json as $row)
{


$CODCARGA = $row['CODCARGA'];
$CODBUFFER = $row['CODBUFFER'];
 $descanal = $row['BUFFER_DESCANAL'];
    if (strpos($descanal, "XCOM") !== false) {
        $codcanal = '00002';
    } else
    if (strpos($descanal, "FTP") !== false) {
        $codcanal = '00004';
    } else
    if ($descanal == "EDITRAN") {
        $codcanal = '00001';
    } else {
        $codcanal = mysql_result(mysql_query("SELECT CODCANAL FROM tcanales WHERE DESCANAL='$descanal' "), 0, 'CODCANAL');
    } 
    
    if (!$codcanal) $codcanal = '00000';

$mot = $_GET['mot'];
$BUFFER_FICHERO_ORIGEN = $row['BUFFER_FICHERO_ORIGEN'];
if(trim($row['DESEMPRESA_DESTINO'])=="") $row['DESEMPRESA_DESTINO']="DESCONOCIDO";

if($USRENV){
if($usrcodcanal!=$codcanal || $usrdesempresa!=$row['DESEMPRESA_DESTINO'] || ($usripdestino!=$row['BUFFER_IP_DESTINO'] && $usripdestino!=$row['BUFFER_NOMBREMAQUINA_DESTINO'])) $nocoincide=1;
}
$sql_buf = "UPDATE tbuffer SET CHKCONTROL='$CHKCONTROL', CODENVIO='$sig_env',FECHA_MODIFICACION=now(), CODUSUARIO_MODIFICACION='$webseal' WHERE  CODBUFFER='$CODBUFFER' AND CODCARGA='$CODCARGA' ";    
//$sql_buf = "UPDATE tbuffer SET CHKCONTROL='$CHKCONTROL', FECHA_MODIFICACION=now(), CODUSUARIO_MODIFICACION=NULL WHERE CODCARGA='$CODCARGA' AND CODBUFFER='$CODBUFFER'  ";

    $responce= "Estado de control editado correctamente. </br></br>";

if ($CHKCONTROL == 'S') {
     
      
        mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas para agregar valores nulos
      
       
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
                        '$sig_fich', " . /* CODFICHERO NUEVO es el único fichero que se inserta para el envío campo autonumérico respecto al mismo CODENVIO .Terminar Buscar como autoincrementar sin buscar maxid */"
                        '00000', " . /* CODCLASIFICACION por los momentos nulo */"
                        '00000', " . /* NIVEL_LOPD por los momentos nulo */"
                        '$codcanal', " . /* CODCANAL código de canal */"
                        '$BUFFER_FICHERO_ORIGEN', " . /* nombre de fichero */"
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
        if(!$nocoincide) mysql_query($sql_newfich);
        else
            $error="El envío no es compatible con los ficheros seleccionados";
        $err = mysql_error();
        mysql_query("SET foreign_key_checks = 1"); // activa claves foráneas
        $responce="Fichero: $BUFFER_FICHERO_ORIGEN dado de alta correctamente con Código de Envío: $sig_env </br>";
       $data->codenv=$sig_env;
    }





mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas para agregar valores nulos
if(!$nocoincide) mysql_query($sql_buf); // SE MODIFICA EL ESTADO DE CONTROL
$my_error = mysql_error();
mysql_query("SET foreign_key_checks = 1"); // activa claves foráneas para Agregar valores nulos

}//fin foreach
// json con código de envío 
$data->mensaje=$responce;
$data->error=trim($my_error." ".$err." ".$error);

echo json_encode($data);
?>
