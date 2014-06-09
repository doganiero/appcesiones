<?

include("../functions/db.php");

$page = $_GET['page']; // Guardamos la p�gina pedida.
$limit = $_GET['rows']; // Guardamos el n�mero de filas que deseamos en nuestra tabla.
$sidx = $_GET['sidx']; // Guardamos el indice de fila (donde cliquea del usuario para ordenar).
$sord = $_GET['sord']; // Guardamos la direccion del orden (ascendente o descendente).
$searchField = $_GET['searchField'];
$searchString = $_GET['searchString'];
$operador = $_GET['searchOper'];
$baja = $_GET['baja'];
settype($searchString, "string");    // Forzamos el tipo a string.
// Array con los campos.
$campos = array(
    0 => 'COL_A',
    1 => 'COL_B',
    2 => 'COL_C',
    3 => 'COL_D',
    4 => 'COL_E',
    5 => 'COL_F',
    6 => 'COL_G'
); //nombres campos como en la db
// Campo ID
$campoId = "COL_A";
// Tabla
$tabla = "temp_cmdb";

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

$filters=array();
$filters= json_decode(stripslashes($_GET['filters']), true);
$cuenta=0;
$condicionBusq ="";
foreach($filters as $i=>$rule){
    foreach($rule as $y=>$value){
$pred = predicado($value['op'], $value['data']);
if($cuenta==0)$condicionBusq.= " ".$value['field']. $pred." ";  // La condición serán los parámetros de búsqueda  
else $condicionBusq.= " ".$filters['groupOp']." ".$value['field'] . $pred." "; 
$cuenta++;
// por ej. articulos.art_nom LIKE '%Articulo%'.
}}
if($filters['groupOp'])$condicionBusq=" ( ".$condicionBusq." ) ";

if (!$sidx)
    $sidx = 1;

// Nos conectamos a la base de datos.
$link = conectar();

mysql_set_charset('utf8');

if ($_GET['_search'] == 'true' && isset($searchField)) {
    $result = mysql_query("SELECT COUNT(*) AS count FROM $tabla WHERE $condicionBusq $andCondicion");
} else {
    $result = mysql_query("SELECT COUNT(*) AS count FROM $tabla $whereCondicion");
}

$row = mysql_fetch_array($result, MYSQL_ASSOC);

$count = $row['count'];
if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 0;
}
if ($page > $total_pages)
    $page = $total_pages;
$start = $limit * $page - $limit; // No poner $limit*($page - 1) 
if ($start < 0)
    $start = 0;   // LIMIT nunca puede ser negativo, como m�nimo es 0.

if ($_GET['_search'] == 'true' && isset($searchField)) {
    $SQL = "SELECT $stringCampos FROM $tabla WHERE $condicionBusq $andCondicion ORDER BY $sidx $sord LIMIT $start , $limit";
} else {
    $SQL = "SELECT $stringCampos FROM $tabla $whereCondicion ORDER BY $sidx $sord LIMIT $start , $limit";
}

$result = mysql_query($SQL) or die("No se pudo ejecutar la consulta. " . mysql_error());

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {


    $responce->rows[$i][$campoId] = $row[$campoId];

    $valColumnas = array();
    foreach ($campos AS $nomCampo) {
        if ($nomCampo == "u_type") {
            if ($row[$nomCampo] == '0')
                array_push($valColumnas, "Administrador");
            else
                array_push($valColumnas, "Usuario");
        }
        else {

            array_push($valColumnas, $row[$nomCampo]);
        }
    }
    if (!(trim($row['COL_A']) == "" && trim($row['COL_B']) == "" && trim($row['COL_C']) == "" && trim($row['COL_D']) == "" && trim($row['COL_E']) == "" && trim($row['COL_F']) == "" && trim($row['COL_G']) == "" )) {
        $responce->rows[$i]['cell'] = $valColumnas;

        $i++;
    }
}

if (!($_GET['_search'] == 'true' && isset($searchField)))
    array_shift($responce->rows); // quitar la primera fila si no es una búsqueda

echo json_encode($responce);
?>
