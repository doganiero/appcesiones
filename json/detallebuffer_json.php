<?
include("../functions/db.php");

$page = $_GET['page']; // Guardamos la p�gina pedida.
$limit = 1; // Guardamos el n�mero de filas que deseamos en nuestra tabla.
$sidx = $_GET['sidx']; // Guardamos el indice de fila (donde cliquea del usuario para ordenar).
$sord = $_GET['sord']; // Guardamos la direccion del orden (ascendente o descendente).
$searchField = $_GET['searchField'];
$searchString = $_GET['searchString'];
$operador = $_GET['searchOper'];
$codcarga = $_GET['codcarga'];
$codbuf = $_GET['codbuf'];

settype($searchString, "string");    // Forzamos el tipo a string.
// Array con los campos.
$campos = array(
    0 => 'CODCARGA',
    1 => 'CODBUFFER',
    2 => 'CODENVIO',
    3 => 'CODFICHERO',
    4 => 'CHKTRANSMISION_INTERNA',
    5 => 'TRANSMISION_INTERNA',
    6 => 'CODTIPOTRANSMISION',
    7 => 'DESTIPOTRANSMISION',
    8 => 'CHKCONTROL',
    9 => 'DESESTADOCONTROL',
    10 => 'BUFFER_CHKCIFRADO',
    11 => 'BUFFER_CIFRADO',
    12 => 'CODTIPODESTINO',
    13 => 'DESTIPODESTINO',
    14 => 'MOTIVOLOG',
    15 => 'DESEMPRESA_ORIGEN',
    16 => 'DESEMPRESA_DESTINO',
    17 => 'BUFFER_NOMBREMAQUINA_ORIGEN',
    18 => 'BUFFER_NOMBREMAQUINA_DESTINO',
    19 => 'BUFFER_IP_ORIGEN',
    20 => 'BUFFER_IP_DESTINO',
    21 => 'BUFFER_FICHERO_ORIGEN',
    22 => 'BUFFER_FICHERO_DESTINO',
    23 =>"DATE_FORMAT(BUFFER_FECHA_ALTA,'%d-%m-%Y %H:%i:%s') AS BUFFER_FECHA_ALTA", 
    24 =>"DATE_FORMAT(BUFFER_FECHA_MODIFICACION,'%d-%m-%Y %H:%i:%s') AS BUFFER_FECHA_MODIFICACION", 
    25 =>"DATE_FORMAT(BUFFER_FECHA_BAJA,'%d-%m-%Y %H:%i:%s') AS BUFFER_FECHA_BAJA",  
    26 => 'BUFFER_CODUSUARIO_ALTA',
    27 => 'BUFFER_DESUSUARIO_ALTA',
    28 => 'BUFFER_CODUSUARIO_MODIFICACION',
    29 => 'BUFFER_DESUSUARIO_MODIFICACION',
    30 => 'BUFFER_CODUSUARIO_BAJA',
    31 => 'BUFFER_DESUSUARIO_BAJA',
    32 => 'BUFFER_ESTADO',
    33 => 'JOB_USR',
    34 => 'USR_SUBMIT'
);


// Campo ID
$campoId = "CODBUFFER";
// Tabla
$tabla = "V_BUFFER";
// Condicion
$condicion = "";
if (($codcarga ) && ($codbuf ))
    $condicion = " CODBUFFER='$codbuf' AND CODCARGA='$codcarga' ";

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
    1 => 'CODBUFFER',
    2 => 'CODENVIO',
    3 => 'CODFICHERO',
    4 => 'CHKTRANSMISION_INTERNA',
    5 => 'TRANSMISION_INTERNA',
    6 => 'CODTIPOTRANSMISION',
    7 => 'DESTIPOTRANSMISION',
    8 => 'CHKCONTROL',
    9 => 'DESESTADOCONTROL',
    10 => 'BUFFER_CHKCIFRADO',
    11 => 'BUFFER_CIFRADO',
    12 => 'CODTIPODESTINO',
    13 => 'DESTIPODESTINO',
    14 => 'MOTIVOLOG',
    15 => 'DESEMPRESA_ORIGEN',
    16 => 'DESEMPRESA_DESTINO',
    17 => 'BUFFER_NOMBREMAQUINA_ORIGEN',
    18 => 'BUFFER_NOMBREMAQUINA_DESTINO',
    19 => 'BUFFER_IP_ORIGEN',
    20 => 'BUFFER_IP_DESTINO',
    21 => 'BUFFER_FICHERO_ORIGEN',
    22 => 'BUFFER_FICHERO_DESTINO',
    23 => 'BUFFER_FECHA_ALTA',
    24 => 'BUFFER_FECHA_MODIFICACION',
    25 => 'BUFFER_FECHA_BAJA',
    26 => 'BUFFER_CODUSUARIO_ALTA',
    27 => 'BUFFER_DESUSUARIO_ALTA',
    28 => 'BUFFER_CODUSUARIO_MODIFICACION',
    29 => 'BUFFER_DESUSUARIO_MODIFICACION',
    30 => 'BUFFER_CODUSUARIO_BAJA',
    31 => 'BUFFER_DESUSUARIO_BAJA',
    32 => 'BUFFER_ESTADO',
    33 => 'JOB_USR',
    34 => 'USR_SUBMIT'
);

$i = 0;
$responce = array();
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

    foreach ($campos AS $nomCampo) {

        $responce[$nomCampo] = $row[$nomCampo];
    }
}

echo json_encode($responce);
?>
