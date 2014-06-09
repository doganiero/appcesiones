<?

include("../functions/db.php");

// Nos conectamos a la base de datos.
$link = conectar();
mysql_set_charset('utf8');
set_time_limit(0);

$sig_carga=$_GET['sig_carga'];
$tipocons==$_GET['tipocons'];


$numbuff=mysql_result(mysql_query("select CODCARGA,count(*) as cuenta from tbuffer WHERE CODCARGA='$sig_carga' group by CODCARGA"),0,'cuenta');
$numcons=mysql_result(mysql_query("select CODCARGA,count(*) as cuenta from tbuffer WHERE CODCARGA='$sig_carga' AND CODUSUARIO_MODIFICACION IS NOT NULL group by CODCARGA"),0,'cuenta');

 $error=mysql_error();

$porcentaje=0;
//registros de ficheros iguales  en otro canal
if($numcons && $numbuff){
$porcentaje=100*$numcons/$numbuff;

}
if($numbuff===$numcons) $porcentaje=100;
//if($porcentaje==false && $numbuff>0){ $porcentaje=100; };



$responce->progress=round($porcentaje);
                              
echo json_encode($responce);

?>
