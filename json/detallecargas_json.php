<?

include("../functions/db.php");

$page = $_GET['page']; // Guardamos la p�gina pedida.
$limit = 1; // Guardamos el n�mero de filas que deseamos en nuestra tabla.
$sidx = $_GET['sidx']; // Guardamos el indice de fila (donde cliquea del usuario para ordenar).
$sord = $_GET['sord']; // Guardamos la direccion del orden (ascendente o descendente).
$searchField = $_GET['searchField'];
$searchString = $_GET['searchString'];
$operador = $_GET['searchOper'];
$codcarga=$_GET['codcarga'];

settype($searchString, "string");    // Forzamos el tipo a string.
// Array con los campos.
$campos = array(
     0 => 'v.CODCARGA',
    1 => 'v.DESCARGA',  
    2 => 'v.FICHERO_LOG',  
    3 => 'v.CARGA_CODCANAL',  
    4 => 'v.CARGA_DESCANAL',  
    5 => 'v.EJERCICIO',  
    6 => 'v.MES',  
    7 => 'v.CHKCONSOLIDADO',  
    8 => 'v.CONSOLIDADO',  
    9 => "DATE_FORMAT(v.CARGA_FECHA_ALTA,'%d-%m-%Y %H:%i:%s') AS CARGA_FECHA_ALTA", 
    10 => 'v.CARGA_FECHA_MODIFICACION',  
    11 => 'v.CARGA_FECHA_BAJA',  
    12 => 'v.CARGA_CODUSUARIO_ALTA',  
    13 => 'v.CARGA_DESUSUARIO_ALTA',  
    14 => 'v.CARGA_CODUSUARIO_MODIFICACION',  
    15 => 'v.CARGA_DESUSUARIO_MODIFICACION',  
    16 => 'v.CARGA_CODUSUARIO_BAJA',  
    17 => 'v.CARGA_DESUSUARIO_BAJA',  
    18 => 'v.CARGA_ESTADO',
     19 => 't.OBSERVACIONES'
);
// Campo ID
$campoId = "v.CODCARGA";
// Tabla
$tabla = " V_CARGAS v, TCARGAS t ";
// Condicion
$condicion = "";
if($codcarga)
    $condicion = " v.CODCARGA='$codcarga' AND v.CODCARGA=t.CODCARGA ";

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

$campos = array(
     0 => 'CODCARGA',
    1 => 'DESCARGA',  
    2 => 'FICHERO_LOG',  
    3 => 'CARGA_CODCANAL',  
    4 => 'CARGA_DESCANAL',  
    5 => 'EJERCICIO',  
    6 => 'MES',  
    7 => 'CHKCONSOLIDADO',  
    8 => 'CONSOLIDADO',  
    9 => 'CARGA_FECHA_ALTA',  
    10 => 'CARGA_FECHA_MODIFICACION',  
    11 => 'CARGA_FECHA_BAJA',  
    12 => 'CARGA_CODUSUARIO_ALTA',  
    13 => 'CARGA_DESUSUARIO_ALTA',  
    14 => 'CARGA_CODUSUARIO_MODIFICACION',  
    15 => 'CARGA_DESUSUARIO_MODIFICACION',  
    16 => 'CARGA_CODUSUARIO_BAJA',  
    17 => 'CARGA_DESUSUARIO_BAJA',  
    18 => 'CARGA_ESTADO',
     19 => 'OBSERVACIONES'
);
$i = 0;
$responce=array();
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
       
    foreach ($campos AS $nomCampo) {
                                    
       $responce[$nomCampo] = $row[$nomCampo];

    }
   
}

echo json_encode($responce);
?>
