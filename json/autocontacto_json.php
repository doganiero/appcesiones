<?

include("../functions/db.php");

// Nos conectamos a la base de datos.
$link = conectar();
mysql_set_charset('utf8');
set_time_limit(0);
$contactos=array();
if($_GET['CODEMPRESA']!="")
$res=mysql_query("SELECT DESCONTACTO FROM tcontactos WHERE CODUSUARIO_BAJA IS NULL AND CODEMPRESA=".$_GET['CODEMPRESA']." ORDER BY DESCONTACTO ");
else
$res=mysql_query("SELECT DESCONTACTO FROM tcontactos WHERE CODUSUARIO_BAJA IS NULL ORDER BY DESCONTACTO ");    
$i=0;
while($fila=  mysql_fetch_object($res))
{
    $contactos[$i]=$fila->DESCONTACTO;
    $i++;
}


echo json_encode($contactos);
?>
