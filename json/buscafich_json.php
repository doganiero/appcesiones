<?

include("../functions/db.php");

// Nos conectamos a la base de datos.
$link = conectar();
mysql_set_charset('utf8');
set_time_limit(0);
$filename=$_POST['filename']; 
$codcanal=$_POST['codcanal']; 

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
?>
