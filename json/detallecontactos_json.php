<?

include("../functions/db.php");

$codcontacto=$_GET['codcontacto'];
$codempresa=$_GET['codempresa'];

// Array con los campos.
$campos = array(0 => 'CODCONTACTO',
    1 => 'DESCONTACTO',
    2 => 'CODEMPRESA',
    3 => 'CARGO',
    4 => 'TELF_FIJO',
    5 => 'TELF_MOVIL',
    6 => 'FAX',
    7 => 'EMAIL',
    8 => 'DIRECCION',
    9 => 'PAIS',
    10 => "DATE_FORMAT(FECHA_ALTA,'%d-%m-%Y %H:%i:%s') AS FECHA_ALTA ",
    11 => "DATE_FORMAT(FECHA_MODIFICACION,'%d-%m-%Y %H:%i:%s') AS FECHA_MODIFICACION ",
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
$condicion = "";
if($codcontacto)
    $condicion = " CODCONTACTO='$codcontacto' ";

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
    10 => 'FECHA_ALTA',
    11 => 'FECHA_MODIFICACION',
    12 => 'FECHA_BAJA',
    13 => 'CODUSUARIO_ALTA',
    14 => 'CODUSUARIO_MODIFICACION',
    15 => 'CODUSUARIO_BAJA',
    16 => 'OBSERVACIONES'
); //nombres campos como en la db



$responce=array();
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
       
    foreach ($campos AS $nomCampo) {
                                    
       $responce[$nomCampo] = $row[$nomCampo];

    }
   
}
$responce['DESEMPRESA']=mysql_result(mysql_query("SELECT DESEMPRESA FROM tempresas WHERE CODEMPRESA='$codempresa' "),0,'DESEMPRESA');
echo json_encode($responce);
?>
