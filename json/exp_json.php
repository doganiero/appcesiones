<?

include("../functions/db.php");

// Nos conectamos a la base de datos.
$link = conectar();
mysql_set_charset('utf8');
set_time_limit(0);
$exsql=stripslashes($_POST['exsql']); 
$campos="'".trim(str_replace(",","','", $_POST['campos']))."'";
$order=$_POST['order'];

$nombrefich=$_POST['nombrefich'];
$ruta=  stripslashes($directorio_raiz.$nombrefich);
unlink($ruta);
$into=' INTO OUTFILE "'.$ruta.'" FIELDS TERMINATED BY ";" OPTIONALLY ENCLOSED BY """" LINES TERMINATED BY "\r\n" ';

//$exsql='SELECT '.$campos.' UNION ALL '.$exsql.' '.$order.' '.$into.' '; // CON ORDER NO FUNC
$exsql='SELECT '.$campos.' UNION ALL '.$exsql.' '.$into.' '; 
mysql_query($exsql);
$err=  mysql_error();

$response->ruta=$ruta;
$response->error=$err;
echo json_encode($response);
/*
//registros de ficheros iguales en el mismo canal 
$igual=mysql_result(mysql_query("SELECT COUNT(*) as suma FROM tcargas WHERE trim(fichero_log)=trim('".utf8_decode($filename)."') AND CODCANAL='$codcanal' "),0,'suma');
$error=mysql_error();

//registros de ficheros iguales  en otro canal
$canal=mysql_result(mysql_query("SELECT COUNT(*) as suma FROM tcargas WHERE trim(fichero_log)=trim('".utf8_decode($filename)."') AND CODCANAL<>'$codcanal' "),0,'suma');
$error=mysql_error();


$campos = array(
        'IGUAL' => $igual,
        'CANAL' => $canal
      
);
       
                                    
      

echo json_encode($campos);
 
 */
?>
