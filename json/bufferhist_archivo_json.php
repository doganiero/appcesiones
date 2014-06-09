<?

include("../functions/db.php");

$nomfich = $_GET['nomfich']; // Guardamos la p�gina pedida.


// Nos conectamos a la base de datos.
$link = conectar();

mysql_set_charset('utf8');

$campos = array(
    0 => 'FICHERO_LOG',
    1 => 'CHKCONTROL',
    2 => 'BUFFER_DESCANAL',
    3 => 'DESEMPRESA_DESTINO',
    4 => 'BUFFER_NOMBREMAQUINA_DESTINO',
    5 => 'BUFFER_IP_DESTINO'
);

$conslogs = mysql_query(" SELECT C.FICHERO_LOG, B.CHKCONTROL, B.BUFFER_DESCANAL, B.DESEMPRESA_DESTINO, B.BUFFER_NOMBREMAQUINA_DESTINO, B.BUFFER_IP_DESTINO  FROM V_CARGAS_ARCHIVO C, V_BUFFER_ARCHIVO B WHERE C.CODCARGA=B.CODCARGA AND B.BUFFER_FICHERO_ORIGEN='$nomfich'  ORDER BY C.CODCARGA DESC ");

$i=0;
while ($row = mysql_fetch_array($conslogs, MYSQL_ASSOC)) {
    
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

