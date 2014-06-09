<?

include("../functions/db.php");

$page = $_GET['page']; // Guardamos la p�gina pedida.
$limit = $_GET['rows']; // Guardamos el n�mero de filas que deseamos en nuestra tabla.
$sidx = $_GET['sidx']; // Guardamos el indice de fila (donde cliquea del usuario para ordenar).
$sord = $_GET['sord']; // Guardamos la direccion del orden (ascendente o descendente).
$searchField = $_GET['searchField'];
$searchString = $_GET['searchString'];
$operador = $_GET['searchOper'];
$codenv = $_GET['codenv'];
$mascara = $_GET['mascara'];
$codcanal = $_GET['codcanal'];
$vermask = $_GET['vermask'];
$carga = $_GET['carga'];
$verbajas = $_GET['baja'];
$coincidencias=$_GET['coincidencias'];
$desempresa=$_GET['desempresa'];
$ipdest=$_GET['ipdest'];
$ssidx=$_GET['ssidx'];

if(!$ssidx) $ssidx=" $sidx $sord ";

$link = conectar();
mysql_set_charset('utf8');

if($carga) $codcanalaux=mysql_result(mysql_query("SELECT CARGA_CODCANAL FROM v_cargas WHERE CODCARGA='$carga' "),0,'CARGA_CODCANAL');

$canal_selnom=mysql_result(mysql_query("SELECT DESCANAL FROM tcanales WHERE CODCANAL='$codcanalaux' "),0,'DESCANAL');
if (strpos($canal_selnom, "XCOM")!== false)
        $strcanal= " FILE_DESCANAL LIKE '%XCOM%' ";
    else if (strpos($canal_selnom, "FTP")!==false) {
        $strcanal= " FILE_DESCANAL LIKE '%FTP%' ";
    }
    else
        $strcanal= " FILE_CODCANAL='$codcanal' ";
    if(!$codcanalaux) $strcanal="";

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
    15 => 'FILE_FECHA_ALTA',
    16 => 'FILE_FECHA_MODIFICACION',
    17 => 'FILE_FECHA_BAJA',
    18 => 'FILE_CODUSUARIO_ALTA',
    19 => 'FILE_DESUSUARIO_ALTA',
    20 => 'FILE_CODUSUARIO_MODIFICACION',
    21 => 'FILE_DESUSUARIO_MODIFICACION',
    22 => 'FILE_CODUSUARIO_BAJA',
    23 => 'FILE_DESUSUARIO_BAJA',
    24 => 'FILE_ESTADO',
    25 => 'FILE_IP_DESTINO'
);
// Campo ID
$campoId = "CODENVIO";
// Tabla
$tabla = "v_ficheros";
// Condicion
if (!$verbajas)
    $condicion = " CODFICHEROPADRE IS NULL AND FILE_CODUSUARIO_BAJA IS NULL ";
else if ($verbajas == 2)
    $condicion = " CODFICHEROPADRE IS NULL ";
else
    $condicion = " CODFICHEROPADRE IS NULL AND FILE_CODUSUARIO_BAJA IS NOT NULL ";


if ($codenv)
    $condicion.= " AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE CODEMPRESAPADRE='$codenv' OR CODEMPRESA='$codenv') ";


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

$filters = array();
$filters = json_decode(stripslashes($_GET['filters']), true);
$cuenta = 0;
$condicionBusq = "";
foreach ($filters as $i => $rule) {
    foreach ($rule as $y => $value) {
        $pred = predicado($value['op'], $value['data']);
        if ($cuenta == 0)
            $condicionBusq.= " " . $value['field'] . $pred . " ";  // La condición serán los parámetros de búsqueda  
        else
            $condicionBusq.= " " . $filters['groupOp'] . " " . $value['field'] . $pred . " ";
        $cuenta++;
// por ej. articulos.art_nom LIKE '%Articulo%'.
    }
}
if ($filters['groupOp'])
    $condicionBusq = " ( " . $condicionBusq . " ) ";

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
    $SQL = "SELECT $stringCampos FROM $tabla WHERE $condicionBusq $andCondicion ORDER BY $ssidx LIMIT $start , $limit";
    $SQL_nolim = "SELECT $stringCampos FROM $tabla WHERE $condicionBusq $andCondicion ";
    $order = " ORDER BY $sidx $sord ";
} else {
    $SQL = "SELECT $stringCampos FROM $tabla $whereCondicion ORDER BY $ssidx LIMIT $start , $limit";
    $SQL_nolim = "SELECT $stringCampos FROM $tabla $whereCondicion ";
    $order = " ORDER BY $sidx $sord ";
}




$result = mysql_query($SQL) or die("No se pudo ejecutar la consulta. " . mysql_error());


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
    24 => 'FILE_ESTADO',
    25 => 'FILE_IP_DESTINO',
     26 => 'CODEMPRESA',
    27 => 'FILE_DESCANAL'
);


$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$responce->userdata['SQL'] = $SQL_nolim;
$responce->userdata['campos'] = $stringCampos;
$responce->userdata['order'] = $order;

$i = 0;
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $responce->rows[$i][$campoId] = $row[$campoId];
    
    $valColumnas = array();
    foreach ($campos AS $nomCampo) {
       
           if($nomCampo==='CODENVIO') $rowEnv=$row[$nomCampo]; 
           if($nomCampo==='CODEMPRESA'){ $valemp=mysql_result(mysql_query("SELECT ifnull(e.CODEMPRESA,e.CODEMPRESAPADRE) as EMPRESA FROM tficheros f,v_envios e WHERE f.CODENVIO=e.CODENVIO and f.CODENVIO='$rowEnv'"),0,'EMPRESA');
              array_push($valColumnas, $valemp);  
           }
           else array_push($valColumnas, $row[$nomCampo]);
           }
    
    array_push($valColumnas, $valemp);
    
    $responce->rows[$i]['cell'] = $valColumnas;
    $i++;
}

echo json_encode($responce);
?>

