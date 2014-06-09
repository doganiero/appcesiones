<?

include("../functions/db.php");

// Nos conectamos a la base de datos.
$link = conectar();
mysql_set_charset('utf8');
set_time_limit(0);

$sig_carga=$_GET['sig_carga'];

$cuentalineas=$_GET['cuentalineas'];
$codcanal=$_GET['FCODCANAL'];

$numbuff=mysql_result(mysql_query("select CODCARGA,count(*) as cuenta from tbuffer WHERE CODCARGA='$sig_carga' group by CODCARGA"),0,'cuenta');

 if ($codcanal == "00001"){
$numtemp=mysql_result(mysql_query("select count(*) as cuenta from temp_editran where col_d<>''"),0,'cuenta');
$numtempall=mysql_result(mysql_query("select count(*) as cuenta from temp_editran "),0,'cuenta'); 

 }else
 if ($codcanal == "00002"){
$numtemp=mysql_result(mysql_query("select count(*) as cuenta from temp_xcom where col_f<>'' AND col_b<>'LR' AND col_b<>'RR' AND (col_k='C' OR col_k='T') "),0,'cuenta');
$numtempall=mysql_result(mysql_query("select count(*) as cuenta from temp_xcom  "),0,'cuenta');
 }else
 if ($codcanal == "00003"){
$numtemp=mysql_result(mysql_query("select count(*) as cuenta from temp_gepp where col_d<>'' AND col_m NOT LIKE '%get%' AND col_m NOT LIKE '%GET%' "),0,'cuenta');
$numtempall=mysql_result(mysql_query("select count(*) as cuenta from temp_gepp "),0,'cuenta');
$numbuff=mysql_result(mysql_query("select CODCARGA,count(*) as cuenta from tbuffer WHERE CODCARGA>='$sig_carga' "),0,'cuenta');

 }
 $error=mysql_error();

$porcentaje=0;
//registros de ficheros iguales  en otro canal
if($numtemp && !$numbuff){
            $porcentaje=50*$numtempall/$cuentalineas;
}else if($numbuff){
$porcentaje=51+(49*$numbuff/$numtemp);

}

//if($porcentaje==false && $numbuff>0){ $porcentaje=100; };
if(!$numtempall){ $porcentaje=100; };

//if($porcentaje>98) $porcentaje=100;

$responce->progress=round($porcentaje);
                             
echo json_encode($responce);

?>
