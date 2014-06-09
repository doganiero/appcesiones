<?

include("../functions/db.php");


$fichero = $_GET['fichero'];
$empresa= $_GET['empresa'];
$maquina = $_GET['maquina'];
$ip = $_GET['ip'];


// Nos conectamos a la base de datos.
$link = conectar();

mysql_set_charset('utf8');

$campos = array(
    0 => 'FECHA_ALTA',
    1 => 'COMENTARIO'
   
);

$conscom = mysql_query(" SELECT DATE_FORMAT(FECHA_ALTA,'%d-%m-%Y %H:%i:%s') AS FECHA_ALTA, COMENTARIO  FROM tcomentarios WHERE FICHERO_ORIGEN='$fichero' AND EMPRESA_DESTINO='$empresa' AND MAQUINA_DESTINO='$maquina' AND IP_DESTINO='$ip' ORDER BY FECHA_ALTA DESC ");

$i=0;
while ($row = mysql_fetch_array($conscom, MYSQL_ASSOC)) {
    
    $valColumnas = array();
     foreach ($campos AS $nomCampo) {
         if($row[$nomCampo]==null || $row[$nomCampo]=="") $row[$nomCampo]='-';
        
     array_push($valColumnas, $row[$nomCampo]);
     }
     $responce->rows[$i] = $valColumnas;
     $i++;
}

echo json_encode($responce);
?>

