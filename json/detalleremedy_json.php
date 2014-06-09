<?

include("../functions/db.php");

$codenv=$_GET['codenvio'];

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
    9 => 'CODINTERVINIENTE',
    10 => 'DESINTERVINIENTE',
    11 => 'CODDESTINATARIO',
    12 => 'DESDESTINATARIO',
    13 => 'CODFRECUENCIA',
    14 => 'DESFRECUENCIA',
    15 => 'CODCANAL',
    16 => 'DESCANAL',
    17 => 'NOMBREMAQUINA_ORIGEN',
    18 => 'IP_ORIGEN',
    19 => 'IP_DESTINO',
    20 => 'CHKCIFRADO',
    21 => 'MOTIVOENVIO',
    22 => 'CODMOTIVOBAJA',
    23 => 'DESMOTIVOBAJA',
    24 => "DATE_FORMAT(ENVIO_FECHA_ALTA,'%d-%m-%Y %H:%i:%s') AS ENVIO_FECHA_ALTA ",
    25 => "DATE_FORMAT(ENVIO_FECHA_MODIFICACION,'%d-%m-%Y %H:%i:%s') AS ENVIO_FECHA_MODIFICACION ",
    26 => "DATE_FORMAT(ENVIO_FECHA_BAJA,'%d-%m-%Y %H:%i:%s') AS ENVIO_FECHA_BAJA ",
    27 => 'ENVIO_CODUSUARIO_ALTA',
    28 => 'ENVIO_DESUSUARIO_ALTA',
    29 => 'ENVIO_CODUSUARIO_MODIFICACION',
    30 => 'ENVIO_DESUSUARIO_MODIFICACION',
    31 => 'ENVIO_CODUSUARIO_BAJA',
    32 => 'ENVIO_DESUSUARIO_BAJA',
    33 => 'CIFRADO'
); //nombres campos como en la db
// Campo ID
$campoId = "CODENVIO";
// Tabla
$tabla = "v_envios";
// Condicion
$condicion = "";
if($codenv)
    $condicion = " CODENVIO='$codenv' ";

// String con los campos.
$stringCampos = "";
for ($i = 0; $i < count($campos) - 1; $i++)
    $stringCampos.= $campos[$i] . ", ";

$stringCampos.= $campos[count($campos) - 1];

if ($condicion != "") { // Condiciones que empiezan por AND y WHERE, cuando hay y cuando no hay condición.
    $andCondicion = "AND " . $condicion;
    $whereCondicion = "WHERE " . $condicion;
} else {
    $andCondicion = "";
    $whereCondicion = "";
}

// Nos conectamos a la base de datos.
$link = conectar();
mysql_set_charset('utf8');
    $SQL = "SELECT $stringCampos FROM $tabla $whereCondicion  ";

$result = mysql_query($SQL) or die("No se pudo ejecutar la consulta. " . mysql_error());

$i = 0;

$campos = array(0 => 'CODENVIO',
    1 => 'CODTIPOENVIO',
    2 => 'DESTIPOENVIO',
    3 => 'CODENVIO_REMEDY',
    4 => 'CODAUTORIZA',
    5 => 'DESAUTORIZA',
    6 => 'EMAILAUTORIZA',
    7 => 'CODEMPRESA',
    8 => 'DESEMPRESA',
    9 => 'CODINTERVINIENTE',
    10 => 'DESINTERVINIENTE',
    11 => 'CODDESTINATARIO',
    12 => 'DESDESTINATARIO',
    13 => 'CODFRECUENCIA',
    14 => 'DESFRECUENCIA',
    15 => 'CODCANAL',
    16 => 'DESCANAL',
    17 => 'NOMBREMAQUINA_ORIGEN',
    18 => 'IP_ORIGEN',
    19 => 'IP_DESTINO',
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
    32 => 'ENVIO_DESUSUARIO_BAJA',
    33 => 'CIFRADO'
); //nombres campos como en la db



$responce=array();
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
       
    foreach ($campos AS $nomCampo) {
                                    
       $responce[$nomCampo] = $row[$nomCampo];

    }
   
}
$responce['OBSERVACIONES']=mysql_result(mysql_query("SELECT OBSERVACIONES FROM tenvios WHERE CODENVIO='$codenv' "),0,'OBSERVACIONES');
$responce['DESCONTACTO']=mysql_result(mysql_query("SELECT c.DESCONTACTO AS CONTACTO FROM tcontactos c, tcontactos_envios e WHERE e.CODENVIO='$codenv' AND c.CODCONTACTO=e.CODCONTACTO "),0,'CONTACTO');
echo json_encode($responce);
?>
