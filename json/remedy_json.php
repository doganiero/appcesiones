<?


include("../functions/db.php");

$page = $_GET['page']; // Guardamos la p�gina pedida.
$limit = $_GET['rows']; // Guardamos el n�mero de filas que deseamos en nuestra tabla.
$sidx = $_GET['sidx']; // Guardamos el indice de fila (donde cliquea del usuario para ordenar).
$sord = $_GET['sord']; // Guardamos la direccion del orden (ascendente o descendente).
$searchField = $_GET['searchField'];
$searchString = $_GET['searchString'];
$operador = $_GET['searchOper'];
$codenvio=$_GET['codenvio'];
$sqlaux=  stripslashes($_GET['SQL']);
$verbajas=$_GET['baja'];
if ($_GET['_search'] == 'true' && isset($searchField)) $sqlaux=0;
settype($searchString, "string");    // Forzamos el tipo a string.
// Array con los campos.
$campos = array(0 => 'CODENVIO',
    1 => 'CODTIPOENVIO',
    2 => 'DESTIPOENVIO',
    3 => 'CODENVIO_REMEDY',
    4 => 'CODAUTORIZA',
    5 => 'DESAUTORIZA',
    6 => 'EMAILAUTORIZA',
    7 => 'CODEMPRESA',
    8 => 'DESEMPRESA',
    9 => 'IP_DESTINO',
    10 => 'DESINTERVINIENTE',
    11 => 'CODDESTINATARIO',
    12 => 'DESDESTINATARIO',
    13 => 'CODFRECUENCIA',
    14 => 'DESFRECUENCIA',
    15 => 'CODCANAL',
    16 => 'DESCANAL',
    17 => 'NOMBREMAQUINA_ORIGEN',
    18 => 'IP_ORIGEN',
    19 => 'CODINTERVINIENTE',
    20 => 'CHKCIFRADO',
    21 => 'MOTIVOENVIO',
    22 => 'CODMOTIVOBAJA',
    23 => 'DESMOTIVOBAJA',
    24 => 'ENVIO_FECHA_ALTA',
    25 => 'ENVIO_FECHA_MODIFICACION',
    26 => 'ENVIO_FECHA_BAJA',
    27 => 'ENVIO_CODUSUARIO_ALTA',
    28 => 'ENVIO_DESUSUARIO_ALTA',
    29 => 'ENVIO_CODUSUARIO_MODIFICACION',
    30 => 'ENVIO_DESUSUARIO_MODIFICACION',
    31 => 'ENVIO_CODUSUARIO_BAJA',
    32 => 'ENVIO_DESUSUARIO_BAJA'
   
   
  
); //nombres campos como en la db
// Campo ID
$campoId = "CODENVIO";
// Tabla
$tabla = "v_envios";
// Condicion
if(!$verbajas)
$condicion = " ENVIO_CODUSUARIO_BAJA IS NULL "; 
else if($verbajas==2)
    $condicion = ""; 
else
    $condicion = " ENVIO_CODUSUARIO_BAJA IS NOT NULL ";  
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
    if($sqlaux) 
    $result = mysql_query($sqlaux);
    else
    $result = mysql_query("SELECT COUNT(*) AS count FROM $tabla WHERE $condicionBusq $andCondicion");
} else {
    if($sqlaux) 
    $result = mysql_query($sqlaux);
    else
    $result = mysql_query("SELECT COUNT(*) AS count FROM $tabla $whereCondicion");
}

$row = mysql_fetch_array($result, MYSQL_ASSOC);
if($sqlaux) 
    $count = mysql_numrows($result);
else
$count = $row['count']; 
if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 0;
}
if($codenvio){
    $sql_pos="SELECT * FROM (SELECT @rownum:=@rownum + 1 as row_number, 
       t.*
FROM ( ";
   if(!$sqlaux)  $sql_pos.="select CODENVIO from v_envios ORDER BY $sidx $sord ";
   if($sqlaux)  $sql_pos.=$sqlaux;
$sql_pos.=") t,
(SELECT @rownum := 0) r ORDER BY $sidx $sord) s WHERE s.CODENVIO='$codenvio' ";
 $posicion=mysql_result(mysql_query($sql_pos),0,'row_number');
 
 $page=ceil($posicion/$limit);  
}
if ($page > $total_pages)
    $page = $total_pages;
$start = $limit * $page - $limit; // No poner $limit*($page - 1) 

if ($start < 0)
    $start = 0;   // LIMIT nunca puede ser negativo, como m�nimo es 0.

if ($_GET['_search'] == 'true' && isset($searchField)) {
    $SQL = "SELECT $stringCampos FROM $tabla WHERE $condicionBusq $andCondicion ORDER BY $sidx $sord LIMIT $start , $limit";
    $SQL_nolim="SELECT $stringCampos FROM $tabla WHERE $condicionBusq $andCondicion ORDER BY $sidx $sord ";
    $SQL_nolim2="SELECT $stringCampos FROM $tabla WHERE $condicionBusq $andCondicion ";
    $order=" ORDER BY $sidx $sord ";
    
} else {
    $SQL = "SELECT $stringCampos FROM $tabla $whereCondicion ORDER BY $sidx $sord LIMIT $start , $limit";
    $SQL_nolim="SELECT $stringCampos FROM $tabla $whereCondicion ORDER BY $sidx $sord ";
    $SQL_nolim2="SELECT $stringCampos FROM $tabla $whereCondicion ";
    $order=" ORDER BY $sidx $sord ";
}
    if($sqlaux) 
    {
        $SQL = $sqlaux." LIMIT $start , $limit";
        $SQL_nolim=$sqlaux;
        $SQL_nolim2=$sqlaux;
    }
$result = mysql_query($SQL) or die("No se pudo ejecutar la consulta. " . mysql_error());

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$responce->userdata['SQL'] = $SQL_nolim2;
$responce->userdata['campos'] = $stringCampos;
$responce->userdata['order'] = $order;
$i = 0;

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
   
    $responce->rows[$i][$campoId] = $row[$campoId];
     array_push($campos , 'OBSERVACIONES');
     $row['OBSERVACIONES']=mysql_result(mysql_query("SELECT OBSERVACIONES FROM tenvios WHERE CODENVIO='".$row['CODENVIO']."'"),0,'OBSERVACIONES');
     array_push($campos , 'SQL');
     $row['SQL']=$SQL_nolim;
     $valColumnas = array();
    foreach ($campos AS $nomCampo) {
           
            array_push($valColumnas, $row[$nomCampo]);
           
    }
    
    $responce->rows[$i]['cell'] = $valColumnas;
   
    $i++;
}

echo json_encode($responce);
?>