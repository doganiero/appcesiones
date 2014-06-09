<?

include("../functions/db.php");

// Nos conectamos a la base de datos.
$link = conectar();
mysql_set_charset('utf8');
set_time_limit(0);

$sig_carga=$_GET['sig_carga'];

$cuentalineas=$_GET['cuentalineas'];
$codcanal=$_GET['FCODCANAL'];

 $sql_showprocesslist = "SHOW STATUS LIKE '%THREADS_RUNNING%' ";
                 $result_showprocesslist = (mysql_query ($sql_showprocesslist));
                 $row = mysql_fetch_array($result_showprocesslist);
                            $responce->process=$row["Value"];
                         
                      
               
                                              
echo json_encode($responce);

?>
