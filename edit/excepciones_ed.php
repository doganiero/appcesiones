<?php

include_once('../functions/db.php');
include_once('../functions/date.php');

    $link = conectar();
mysql_set_charset('utf8');




$desexc=ltrim($_POST['NEXCDESC']);
$codcanal=ltrim($_POST['NEXCCODCANAL']);
$mascara=ltrim(strtoupper($_POST['NEXCMASCARA']));

if(trim($desexc)=="") $err="El campo de Descripción es obligatorio";
if(trim($mascara)=="") $err="El campo Máscara es obligatorio";
if($codcanal=='00000') $err="Debe seleccionar un canal para la excepción";
$existexc=mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM texcepciones WHERE UPPER(MASCARA)='$mascara' AND CODCANAL='$codcanal'  "),0,'cuenta');
if($existexc) $err="La excepción que intenta crear ya existe";

if ($_POST['opcion'] == "nuevo") {
    
    $sig_exc = mysql_result((mysql_query(" SELECT MAX(CODEXC) as max FROM texcepciones ")), 0, 'max') + 1;
$cifras = strlen($sig_exc);
$ceros = "";
for ($i = $cifras; $i < 5; $i++) {
    $ceros.="0";
}
$sig_exc = $ceros . $sig_exc;
    
    $sql_ins = "
INSERT INTO texcepciones
(
CODEXC,
DESCEXC,
CODCANAL,
MASCARA
)
values
(

'$sig_exc', " . /* CODEXC*/"
'$desexc', " . /* DESCEXC */"
'$codcanal', " . /* CODCANAL */"
'$mascara' " . /* MASCARA */"
)

";
    mysql_query("SET foreign_key_checks = 0");
    if ($err == "") {mysql_query($sql_ins); $err = mysql_error();}
   
    if ($err == "")
       $responce = "Excepción dada de alta correctamente";
   
        
}else if($_GET['borrar']){
$borrar = $_GET['borrar'];
mysql_query("SET foreign_key_checks = 0");
mysql_query("DELETE FROM texcepciones WHERE CODEXC='$borrar' ");
$err=  mysql_error();
if(!$err) $responce="Alerta. Excepción eliminada";

}

mysql_query("SET foreign_key_checks = 1"); // Activa claves foráneas
$campos = array(
    'ERROR' => $err,
    'SUCCESS' => $responce
);

echo json_encode($campos);
?>
