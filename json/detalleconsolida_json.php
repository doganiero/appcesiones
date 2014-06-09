<?


include("../functions/db.php");

// Nos conectamos a la base de datos.
$link = conectar();
mysql_set_charset('utf8');
set_time_limit(0);
$codcarga=$_GET['codcarga'];    

//Total registros Consolidados: 
$tcons=mysql_result(mysql_query("SELECT COUNT(*) as suma FROM TBUFFER B, TCARGAS C WHERE C.CODCARGA=B.CODCARGA AND C.CHKCONSOLIDADO='S' AND C.CODCARGA='$codcarga' "),0,'suma');
$error=mysql_error();

//Total registros Controlados (S): 
$ts=mysql_result(mysql_query("SELECT COUNT(*) as suma FROM TBUFFER WHERE CHKCONTROL='S' AND CODCARGA='$codcarga' "),0,'suma');
$error=mysql_error();
//Total registros No Controlados (N):
$tn=mysql_result(mysql_query("SELECT COUNT(*) as suma FROM TBUFFER WHERE CHKCONTROL='N' AND CODCARGA='$codcarga' "),0,'suma');
$error=mysql_error();
//Total registros Descartados (D): 
$td=mysql_result(mysql_query("SELECT COUNT(*) as suma FROM TBUFFER WHERE CHKCONTROL='D' AND CODCARGA='$codcarga' "),0,'suma');
$error=mysql_error();
//Total registros en estudio (E): 
$te=mysql_result(mysql_query("SELECT COUNT(*) as suma FROM TBUFFER WHERE CHKCONTROL='E' AND CODCARGA='$codcarga' "),0,'suma');
$error=mysql_error();

// aquí hay que estudiar si se quiere ver el total de registros para una carga o de todo TBUFFER

$campos = array(
        'TOTAL' => $tcons,
        'S' => $ts,
        'N' => $tn,
        'D' => $td,
        'E' => $te
);
       
                                    
      

echo json_encode($campos);
?>
