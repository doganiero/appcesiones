<?

include("../functions/db.php");

// Nos conectamos a la base de datos.
$link = conectar();
mysql_set_charset('utf8');
set_time_limit(0);

$sig_carga = mysql_result((mysql_query(" SELECT MAX(CODCARGA) as max FROM tcargas ")), 0, 'max') + 1;
$cifras = strlen($sig_carga);
$ceros = "";
for ($i = $cifras; $i < 6; $i++) {
    $ceros.="0";
}
$sig_carga = $ceros . $sig_carga;  

$codcanal=$_GET['FCODCANAL'];

$cuentalineas=0;

 if ($codcanal == "00001")
$carpetayruta = "../../tmp/logs/editran/".$_GET['FFICHERO_LOG'].""; 
 else 
    if ($codcanal == "00002")
$carpetayruta = "../../tmp/logs/xcom/".$_GET['FFICHERO_LOG'].""; 
else
    if ($codcanal == "00003")
$carpetayruta = "../../tmp/logs/gepp/".$_GET['FFICHERO_LOG'].""; 


    $file = fopen($carpetayruta, "r") or exit("Unable to open file!");  
    while (!feof($file)) {
        if(fgets($file))
            $cuentalineas++;
        }
        fclose($file);

$responce->sig_carga=$sig_carga;
$responce->cuentalineas=$cuentalineas;
                              
echo json_encode($responce);

?>
