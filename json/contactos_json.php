<?

include("../functions/db.php");


$page = $_GET['page']; // Guardamos la p�gina pedida.
$limit = $_GET['rows']; // Guardamos el n�mero de filas que deseamos en nuestra tabla.
$sidx = $_GET['sidx']; // Guardamos el indice de fila (donde cliquea del usuario para ordenar).
$sord = $_GET['sord']; // Guardamos la direccion del orden (ascendente o descendente).
$searchField = $_GET['searchField'];
$searchString = $_GET['searchString'];
$operador = $_GET['searchOper'];
settype($searchString, "string");    // Forzamos el tipo a string.
$emp=$_GET['empresa'];

// Array con los campos.
$campos = array(
     
    0 => 'CODCONTACTO',
    1 => 'DESCONTACTO',
    2 => 'CODEMPRESA',
    3 => 'CARGO',
    4 => 'TELF_FIJO',
    5 => 'TELF_MOVIL',
    6 => 'FAX',
    7 => 'EMAIL',
    8 => 'DIRECCION',
    9 => 'PAIS',
    10 => "DATE_FORMAT(FECHA_ALTA,'%d-%m-%Y %H:%i:%s') AS FECHA_ALTA",
    11 => "DATE_FORMAT(FECHA_MODIFICACION,'%d-%m-%Y %H:%i:%s') AS FECHA_MODIFICACION",
    12 => "DATE_FORMAT(FECHA_BAJA,'%d-%m-%Y %H:%i:%s') AS FECHA_BAJA ",
    13 => 'CODUSUARIO_ALTA',
    14 => 'CODUSUARIO_MODIFICACION',
    15 => 'CODUSUARIO_BAJA',
    16 => 'OBSERVACIONES'
); //nombres campos como en la db
// Campo ID
$campoId = "CODCONTACTO";
// Tabla
$tabla = "tcontactos";
// Condicion
if($emp)
    $condicion = " CODEMPRESA='$emp' AND CODUSUARIO_BAJA IS NULL ";
    else
$condicion = " CODUSUARIO_BAJA IS NULL ";
    
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

$campos = array(
    0 => 'CODCONTACTO',
    1 => 'DESCONTACTO',
    2 => 'CODEMPRESA',
    3 => 'CARGO',
    4 => 'TELF_FIJO',
    5 => 'TELF_MOVIL',
    6 => 'FAX',
    7 => 'EMAIL',
    8 => 'DIRECCION',
    9 => 'PAIS',
    10 => "FECHA_ALTA",
    11 => "FECHA_MODIFICACION",
    12 => "FECHA_BAJA",
    13 => 'CODUSUARIO_ALTA',
    14 => 'CODUSUARIO_MODIFICACION',
    15 => 'CODUSUARIO_BAJA',
    16 => 'OBSERVACIONES'
); //nombres campos como en la db


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
        else
            array_push($valColumnas, $row[$nomCampo]);
       
    }
    
    $responce->rows[$i]['cell'] = $valColumnas;
    
    $i++;
}



echo json_encode($responce);



?>
