<?php

include_once('../functions/db.php');
include_once('../functions/date.php');

$link = conectar();
mysql_set_charset('utf8');



if ($_POST['opcion'] == "editar") {
   
    $codcontacto=$_POST['ECODCONTACTO'];
$descontacto = ltrim($_POST['EDESCONTACTO']);
if (trim($descontacto) == "")
    $err = "El campo Nombre de Contacto es obligatorio";
$codempresa = $_POST['ECODEMPRESA'];

$desactual=$existecontacto=mysql_result(mysql_query("SELECT DESCONTACTO FROM tcontactos WHERE CODCONTACTO='$codcontacto' "),0,'DESCONTACTO');
if($desactual!=$descontacto){
$existecontacto=mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM tcontactos WHERE DESCONTACTO='$descontacto' AND CODEMPRESA='$codempresa' "),0,'cuenta');
if($existecontacto) $err="Ya Existe una contacto con ese nombre en la empresa";
}
$cargo = $_POST['ECARGO'];
$tlfijo = $_POST['ETELF_FIJO'];
$tlmovil = $_POST['ETELF_MOVIL'];
$fax = $_POST['EFAX'];
$email = $_POST['EEMAIL'];
$direccion = $_POST['EDIRECCION'];
$observaciones = $_POST['EOBSERVACIONES'];
$pais = $_POST['EPAIS'];
   
    $sql_ed = "
UPDATE tcontactos
SET
DESCONTACTO='$descontacto',
CODEMPRESA='$codempresa',
CARGO='$cargo',
TELF_FIJO='$tlfijo',
TELF_MOVIL='$tlmovil',
FAX='$fax',
EMAIL='$email',
DIRECCION='$direccion',
PAIS='$pais',
OBSERVACIONES='$observaciones',
FECHA_MODIFICACION=now(),
CODUSUARIO_MODIFICACION='$webseal' " . /* Se desconoce el usuario hasta tanto se sutituya la variable con webseal .Terminar */"
WHERE
 CODCONTACTO='$codcontacto' ";

    mysql_query("SET foreign_key_checks = 0");
    if ($err == "") {
        mysql_query($sql_ed);
        $err = mysql_error();
    }

    if ($err == "")
        $responce = "Contacto editado correctamente";
}else if ($_POST['opcion'] == "nuevo") {

    $codcontacto=$_POST['NCODCONTACTO'];
    $codempresa = $_POST['NCODEMPRESA'];
$descontacto = ltrim($_POST['NDESCONTACTO']);
if (trim($descontacto) == "")
    $err = "El campo Nombre de Contacto es obligatorio";
$existecontacto=mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM tcontactos WHERE DESCONTACTO='$descontacto' AND CODEMPRESA='$codempresa' "),0,'cuenta');
if($existecontacto) $err="Ya Existe una contacto con ese nombre en la empresa";



$cargo = $_POST['NCARGO'];
$tlfijo = $_POST['NTELF_FIJO'];
$tlmovil = $_POST['NTELF_MOVIL'];
$fax = $_POST['NFAX'];
$email = $_POST['NEMAIL'];
$direccion = $_POST['NDIRECCION'];
$observaciones = $_POST['NOBSERVACIONES'];
$pais = $_POST['NPAIS'];
    
    $sig_contacto = mysql_result((mysql_query(" SELECT MAX(CODCONTACTO) as max FROM tcontactos ")), 0, 'max') + 1;
    $cifras = strlen($sig_contacto);
    $ceros = "";
    for ($i = $cifras; $i < 6; $i++) {
        $ceros.="0";
    }
    $sig_contacto = $ceros . $sig_contacto;

    $sql_ins = "
INSERT INTO tcontactos
(
CODCONTACTO,
DESCONTACTO,
CODEMPRESA,
CARGO,
TELF_FIJO,
TELF_MOVIL,
FAX,
EMAIL,
DIRECCION,
PAIS,
FECHA_ALTA,
FECHA_MODIFICACION,
FECHA_BAJA,
CODUSUARIO_ALTA,
CODUSUARIO_MODIFICACION,
CODUSUARIO_BAJA,
OBSERVACIONES
)
values
(

'$sig_contacto', " . /* CODCONTACTO buscar en la  db Número de dígitos */"
'$descontacto', " . /* DESCONTACTO */"
'$codempresa', " . /* CODEMPRESA */"
'$cargo', " . /* CARGO */"
'$tlfijo', " . /* TELF_FIJO */"
'$tlmovil', " . /* TELF_MOVIL */"
'$fax', " . /* FAX */"
'$email', " . /* EMAIL */"
'$direccion', " . /* DIRECCION */"
'$pais', " . /* PAIS */"
now(), " . /* FECHA_ALTA */"
'00-00-0000 00:00:00', " . /* FECHA_MODIFICACION */"
'00-00-0000 00:00:00', " . /* FECHA_BAJA */"
'$webseal', " . /* CODUSUARIO_ALTA Se desconoce el usuario hasta tanto se cambie por la variable de webseal .Terminar */"
NULL, " . /* CODUSUARIO_MODIFICACION Se desconoce el usuario hasta tanto se cambie por la variable de webseal .Terminar */"
NULL, " . /* CODUSUARIO_BAJA Se desconoce el usuario hasta tanto se cambie por la variable de webseal .Terminar */"
'$observaciones' " . /* OBSERVACIONES Se desconoce el usuario hasta tanto se cambie por la variable de webseal .Terminar */"

)

";
    mysql_query("SET foreign_key_checks = 0");
    if ($err == "") {
        mysql_query($sql_ins);
        $err = mysql_error();
    }

    if ($err == "")
        $responce = "Contacto dado de alta correctamente";
}else if ($_GET['borrar']) {
    $borrar = $_GET['borrar'];
    mysql_query("SET foreign_key_checks = 0");
    mysql_query("UPDATE tcontactos SET CODUSUARIO_BAJA='$webseal', FECHA_BAJA=now() WHERE CODCONTACTO='$borrar' ");
    $err = mysql_error();
    if (!$err)
        $responce = "Alerta. Contacto dado de baja";
// se desconoce el usuario hasta tanto se cambie la variable con WebSeal .Terminar
}

mysql_query("SET foreign_key_checks = 1"); // Activa claves foráneas
$campos = array(
    'ERROR' => $err,
    'SUCCESS' => $responce,
    'CONT' => 1
);


echo json_encode($campos);
?>
