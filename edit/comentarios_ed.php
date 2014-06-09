<?

include("../functions/db.php");


$fichero = $_GET['fichero'];
$empresa= $_GET['empresa'];
$maquina = $_GET['maquina'];
$coment = $_POST['coment'];
$ip = $_GET['ip'];


// Nos conectamos a la base de datos.
$link = conectar();

mysql_set_charset('utf8');
set_time_limit(0);


mysql_query(" INSERT INTO tcomentarios  (FECHA_ALTA, FICHERO_ORIGEN,EMPRESA_DESTINO,MAQUINA_DESTINO,IP_DESTINO,COMENTARIO,CODUSUARIO_ALTA) values (now(),'$fichero','$empresa','$maquina','$ip','$coment','$webseal') ");

echo json_encode(1);
?>

