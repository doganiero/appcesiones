<?


include("../functions/db.php");

$page = $_GET['page']; // Guardamos la p�gina pedida.
$limit = 1; // Guardamos el n�mero de filas que deseamos en nuestra tabla.
$sidx = $_GET['sidx']; // Guardamos el indice de fila (donde cliquea del usuario para ordenar).
$sord = $_GET['sord']; // Guardamos la direccion del orden (ascendente o descendente).
$searchField = $_GET['searchField'];
$searchString = $_GET['searchString'];
$operador = $_GET['searchOper'];
$codfich=$_GET['codfich'];
$codenv=$_GET['codenv'];

settype($searchString, "string");    // Forzamos el tipo a string.
// Array con los campos.
$campos = array(
    0 => 'CODENVIO',
    1 => 'CODFICHERO',
    2 => 'CODFICHEROPADRE',
    3 => 'FICHERO_ORIGEN',
    4 => 'FICHERO_DESTINO',
    5 => 'RUTA_ORIGEN',
    6 => 'UUAA',
    7 => 'CODCLASIFICACION',
    8 => 'DESCLASIFICACION',
    9 => 'CODNIVEL_LOPD',
    10 => 'DESNIVEL_LOPD',
    11 => 'FILE_CODCANAL',
    12 => 'FILE_DESCANAL',
    13 => 'FILE_CODMOTIVOBAJA',
    14 => 'FILE_DESMOTIVOBAJA',
    15 => "DATE_FORMAT(FILE_FECHA_ALTA,'%d-%m-%Y %H:%i:%s') AS FILE_FECHA_ALTA",
    16 => "DATE_FORMAT(FILE_FECHA_MODIFICACION,'%d-%m-%Y %H:%i:%s') AS FILE_FECHA_MODIFICACION",
    17 => "DATE_FORMAT(FILE_FECHA_BAJA,'%d-%m-%Y %H:%i:%s') AS FILE_FECHA_BAJA",
    18 => 'FILE_CODUSUARIO_ALTA',
    19 => 'FILE_DESUSUARIO_ALTA',
    20 => 'FILE_CODUSUARIO_MODIFICACION',
    21 => 'FILE_DESUSUARIO_MODIFICACION',
    22 => 'FILE_CODUSUARIO_BAJA',
    23 => 'FILE_DESUSUARIO_BAJA',
    24 => 'FILE_ESTADO'
    
);
// Campo ID
$campoId = "CODFICHERO";
// Tabla
$tabla = "v_ficheros";
// Condicion
$condicion = "";
if(($codfich )&& ($codenv))
    $condicion = " CODFICHERO='$codfich' AND CODENVIO='$codenv' ";

// String con los campos.
$stringCampos = "";
for ($i = 0; $i < count($campos) - 1; $i++)
    $stringCampos.= $campos[$i] . ", ";

$stringCampos.= $campos[count($campos) - 1];
$pred = predicado($operador, $searchString); // y el predicado según tipo de búsqueda.

if ($condicion != "") { // Condiciones que empiezan por AND y WHERE, cuando hay y cuando no hay condición.
    $andCondicion = "AND " . $condicion;
    $whereCondicion = "WHERE " . $condicion;
} else {
    $andCondicion = "";
    $whereCondicion = "";
}

$condicionBusq = $searchField . $pred;  // La condición serán los parámetros de búsqueda  
// por ej. articulos.art_nom LIKE '%Articulo%'.

if (!$sidx)
    $sidx = 1;

// Nos conectamos a la base de datos.
$link = conectar();
mysql_set_charset('utf8');
    $SQL = "SELECT $stringCampos FROM $tabla $whereCondicion  ";

$result = mysql_query($SQL) or die("No se pudo ejecutar la consulta. " . mysql_error());

$i = 0;

$campos = array(
    0 => 'CODENVIO',
    1 => 'CODFICHERO',
    2 => 'CODFICHEROPADRE',
    3 => 'FICHERO_ORIGEN',
    4 => 'FICHERO_DESTINO',
    5 => 'RUTA_ORIGEN',
    6 => 'UUAA',
    7 => 'CODCLASIFICACION',
    8 => 'DESCLASIFICACION',
    9 => 'CODNIVEL_LOPD',
    10 => 'DESNIVEL_LOPD',
    11 => 'FILE_CODCANAL',
    12 => 'FILE_DESCANAL',
    13 => 'FILE_CODMOTIVOBAJA',
    14 => 'FILE_DESMOTIVOBAJA',
    15 => 'FILE_FECHA_ALTA',
    16 => 'FILE_FECHA_MODIFICACION',
    17 => 'FILE_FECHA_BAJA',
    18 => 'FILE_CODUSUARIO_ALTA',
    19 => 'FILE_DESUSUARIO_ALTA',
    20 => 'FILE_CODUSUARIO_MODIFICACION',
    21 => 'FILE_DESUSUARIO_MODIFICACION',
    22 => 'FILE_CODUSUARIO_BAJA',
    23 => 'FILE_DESUSUARIO_BAJA',
    24 => 'FILE_ESTADO' );


$responce=array();
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
       
    foreach ($campos AS $nomCampo) {
                                    
       $responce[$nomCampo] = $row[$nomCampo];

    }
   
}
$responce['OBSERVACIONES']=mysql_result(mysql_query("SELECT OBSERVACIONES FROM tficheros WHERE CODENVIO='$codenv' AND CODFICHERO='$codfich' "),0,'OBSERVACIONES');

echo json_encode($responce);
?>
