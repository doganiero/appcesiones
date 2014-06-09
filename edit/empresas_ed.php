<?php

include_once('../functions/db.php');
include_once('../functions/date.php');

$link = conectar();
mysql_set_charset('utf8');




$codpadre = ltrim($_POST['CODEMPRESAPADRE']);

$desempresa = ltrim($_POST['NDESEMPRESA']);
if (trim($desempresa) == "")
    $err = "El campo Descripción de Empresa es obligatorio";
$existeempresa = mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM tempresas WHERE DESEMPRESA='$desempresa' "), 0, 'cuenta');
if ($existeempresa)
    $err = "Ya Existe una Empresa con esa descripción";

if ($_POST['opcion'] == "nuevo") {

    $sig_empresa = mysql_result((mysql_query(" SELECT MAX(CODEMPRESA) as max FROM tempresas ")), 0, 'max') + 1;
    $cifras = strlen($sig_empresa);
    $ceros = "";
    for ($i = $cifras; $i < 5; $i++) {
        $ceros.="0";
    }
    $sig_empresa = $ceros . $sig_empresa;

    $sql_ins = "
INSERT INTO tempresas
(
CODEMPRESA,
DESEMPRESA,
CHKACTIVO,
CODEMPRESAPADRE
)
values
(

'$sig_empresa', " . /* CODEMPRESA */"
'$desempresa', " . /* DESEMPRESA */"
'S', " . /* CHKACTIVO */"
'$codpadre' " . /* CHKACTIVO */"
)

";
    mysql_query("SET foreign_key_checks = 0");
    if ($err == "") {
        mysql_query($sql_ins);
        $err = mysql_error();
    }

    if ($err == "")
        if ($codpadre == '00000')
            mysql_query("UPDATE tempresas SET CODEMPRESAPADRE=NULL, CHKACTIVO='S' WHERE CODEMPRESA='$sig_empresa'");
    $responce = "Empresa dada de alta correctamente";
}if ($_POST['opcion'] == "editar") {
  $err=0;  
$codemp=$_POST['EDCODEMPRESA'];    
$codpadre = ltrim($_POST['ECODEMPRESAPADRE']);
$desempresa = ltrim($_POST['EDESEMPRESA']);
$chkactivo = $_POST['ECHKACTIVO'];

if (ltrim($desempresa) == "")
    $err = "El campo Descripción de Empresa es obligatorio";
$existeempresa = mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM tempresas WHERE DESEMPRESA='$desempresa' AND CODEMPRESA<>'$codemp' "), 0, 'cuenta');
if ($existeempresa)
    $err = "Ya Existe una Empresa con esa descripción";


    $sql_ins = "
UPDATE tempresas

SET 
DESEMPRESA='$desempresa',
CODEMPRESAPADRE='$codpadre'
WHERE CODEMPRESA='$codemp'

";
    mysql_query("SET foreign_key_checks = 0");
    if ($err == "") {
        mysql_query($sql_ins);
        $err = mysql_error();
    }

    if ($err == "")
        if ($codpadre == '00000')
            mysql_query("UPDATE tempresas SET CODEMPRESAPADRE=NULL, CHKACTIVO='$chkactivo' WHERE CODEMPRESA='$codemp'");
    $responce = "Empresa editada correctamente";
}else if ($_GET['borrar']) {
    $borrar = $_GET['borrar'];
    mysql_query("SET foreign_key_checks = 0");
    mysql_query("UPDATE tempresas SET CHKACTIVO='N', CODEMPRESAPADRE = NULL  WHERE CODEMPRESA='$borrar' OR CODEMPRESAPADRE='$borrar' ");
    $err = mysql_error();
    if (!$err)
        $responce = "Alerta. Empresa dada de baja";
// se desconoce el usuario hasta tanto se cambie la variable con WebSeal .Terminar
}else if ($_GET['activar']) {
    $activar = $_GET['activar'];
    mysql_query("SET foreign_key_checks = 0");
    mysql_query("UPDATE tempresas SET CHKACTIVO='S'  WHERE CODEMPRESA='$activar' ");
    $err = mysql_error();
    
}

mysql_query("SET foreign_key_checks = 1"); // Activa claves foráneas
$campos = array(
    'ERROR' => $err,
    'SUCCESS' => $responce,
    'EMP' => 1
);


echo json_encode($campos);
?>
