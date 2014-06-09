<?

include("../functions/db.php");

$page = $_GET['page']; // Guardamos la p�gina pedida.
if(!$page) $page=1;
$limit = $_GET['rows']; // Guardamos el n�mero de filas que deseamos en nuestra tabla.
if(!$limit) $limit=50;
$sidx = $_GET['sidx']; // Guardamos el indice de fila (donde cliquea del usuario para ordenar).
if(!$sidx) $sidx=" FICHERO_ORIGEN ";
$sord = $_GET['sord']; // Guardamos la direccion del orden (ascendente o descendente).
if(!$sord) $sord=" asc ";
$searchField = $_GET['searchField'];
$searchString = $_GET['searchString'];
$operador = $_GET['searchOper'];
$codenv = $_GET['codenv'];
$mascara = $_GET['mascara'];
$codcanal = $_GET['codcanal'];
$vermask = $_GET['vermask'];
$carga = $_GET['carga'];
$verbajas = $_GET['baja'];
$coincidencias = $_GET['coincidencias'];
$desempresa = $_GET['desempresa'];
$ipdest = $_GET['ipdest'];
//$json =array();
//$json='[{"CODENVIO":"020154","CODFICHERO":"000001","CODFICHEROPADRE":"","FICHERO_ORIGEN":"AC.COTRE100.DG0TC2.ACCOJ791.VALIDA","FICHERO_DESTINO":"","RUTA_ORIGEN":"","UUAA":"","CODCLASIFICACION":"00000","DESCLASIFICACION":"DESCONOCIDO","CODNIVEL_LOPD":"00000","DESNIVEL_LOPD":"DESCONOCIDO","FILE_CODCANAL":"00002","FILE_MASCARA":"","FILE_DESCANAL":"XCOM","FILE_CODMOTIVOBAJA":"00000","FILE_DESMOTIVOBAJA":"00000","FILE_FECHA_ALTA":"13-11-2013","FILE_FECHA_MODIFICACION":" ","FILE_FECHA_BAJA":"0000-00-00 00:00:00","FILE_CODUSUARIO_ALTA":"NULLUSR","FILE_DESUSUARIO_ALTA":"1","FILE_CODUSUARIO_MODIFICACION":"","FILE_DESUSUARIO_MODIFICACION":"1","FILE_CODUSUARIO_BAJA":"","FILE_DESUSUARIO_BAJA":"1","FILE_ESTADO":"ACTIVO","FILE_CODEMPRESA_DESTINO":"DESCONOCIDO","FILE_IP_DESTINO":"22.0.192.105"},{"CODENVIO":"042187","CODFICHERO":"000001","CODFICHEROPADRE":"","FICHERO_ORIGEN":"AC.COTRE100.DG0TC2.ACCOJ791.VALIDA","FICHERO_DESTINO":"","RUTA_ORIGEN":"","UUAA":"","CODCLASIFICACION":"00000","DESCLASIFICACION":"DESCONOCIDO","CODNIVEL_LOPD":"00000","DESNIVEL_LOPD":"DESCONOCIDO","FILE_CODCANAL":"00002","FILE_MASCARA":"","FILE_DESCANAL":"XCOM","FILE_CODMOTIVOBAJA":"00000","FILE_DESMOTIVOBAJA":"00000","FILE_FECHA_ALTA":"19-11-2013","FILE_FECHA_MODIFICACION":"19-11-2013","FILE_FECHA_BAJA":"0000-00-00 00:00:00","FILE_CODUSUARIO_ALTA":"A706645","FILE_DESUSUARIO_ALTA":"1","FILE_CODUSUARIO_MODIFICACION":"A706645","FILE_DESUSUARIO_MODIFICACION":"1","FILE_CODUSUARIO_BAJA":"","FILE_DESUSUARIO_BAJA":"1","FILE_ESTADO":"ACTIVO","FILE_CODEMPRESA_DESTINO":"BBVA","FILE_IP_DESTINO":""}]';
//$json='[{"CODENVIO":"020154","CODFICHERO":"000001","CODFICHEROPADRE":"","FICHERO_ORIGEN":"AC.COTRE100.DG0TC2.ACCOJ791.VALIDA","FICHERO_DESTINO":"","RUTA_ORIGEN":"","UUAA":"","CODCLASIFICACION":"00000","DESCLASIFICACION":"DESCONOCIDO","CODNIVEL_LOPD":"00000","DESNIVEL_LOPD":"DESCONOCIDO","FILE_CODCANAL":"00002","FILE_MASCARA":"","FILE_DESCANAL":"XCOM","FILE_CODMOTIVOBAJA":"00000","FILE_DESMOTIVOBAJA":"00000","FILE_FECHA_ALTA":"13-11-2013","FILE_FECHA_MODIFICACION":" ","FILE_FECHA_BAJA":"0000-00-00 00:00:00","FILE_CODUSUARIO_ALTA":"NULLUSR","FILE_DESUSUARIO_ALTA":"1","FILE_CODUSUARIO_MODIFICACION":"","FILE_DESUSUARIO_MODIFICACION":"1","FILE_CODUSUARIO_BAJA":"","FILE_DESUSUARIO_BAJA":"1","FILE_ESTADO":"ACTIVO","FILE_CODEMPRESA_DESTINO":"DESCONOCIDO","FILE_IP_DESTINO":"22.0.192.105"},{"CODENVIO":"042187","CODFICHERO":"000001","CODFICHEROPADRE":"","FICHERO_ORIGEN":"AC.COTRE100.DG0TC2.ACCOJ791.VALIDA","FICHERO_DESTINO":"","RUTA_ORIGEN":"","UUAA":"","CODCLASIFICACION":"00000","DESCLASIFICACION":"DESCONOCIDO","CODNIVEL_LOPD":"00000","DESNIVEL_LOPD":"DESCONOCIDO","FILE_CODCANAL":"00002","FILE_MASCARA":"","FILE_DESCANAL":"XCOM","FILE_CODMOTIVOBAJA":"00000","FILE_DESMOTIVOBAJA":"00000","FILE_FECHA_ALTA":"19-11-2013","FILE_FECHA_MODIFICACION":"19-11-2013","FILE_FECHA_BAJA":"0000-00-00 00:00:00","FILE_CODUSUARIO_ALTA":"A706645","FILE_DESUSUARIO_ALTA":"1","FILE_CODUSUARIO_MODIFICACION":"A706645","FILE_DESUSUARIO_MODIFICACION":"1","FILE_CODUSUARIO_BAJA":"","FILE_DESUSUARIO_BAJA":"1","FILE_ESTADO":"ACTIVO","FILE_CODEMPRESA_DESTINO":"BBVA","FILE_IP_DESTINO":""}]';
$json =$_POST['json'];

$json = json_decode(stripslashes($json), true);

$link = conectar();
mysql_set_charset('utf8');

if ($carga)
    $codcanalaux = mysql_result(mysql_query("SELECT CARGA_CODCANAL FROM v_cargas WHERE CODCARGA='$carga' "), 0, 'CARGA_CODCANAL');

$canal_selnom = mysql_result(mysql_query("SELECT DESCANAL FROM tcanales WHERE CODCANAL='$codcanalaux' "), 0, 'DESCANAL');
if (strpos($canal_selnom, "XCOM") !== false)
    $strcanal = " FILE_DESCANAL LIKE '%XCOM%' ";
else if (strpos($canal_selnom, "FTP") !== false) {
    $strcanal = " FILE_DESCANAL LIKE '%FTP%' ";
} else
    $strcanal = " FILE_CODCANAL='$codcanalaux' ";
if (!$codcanalaux)
    $strcanal = "";

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
    25 => 'FILE_CODEMPRESA_DESTINO',
    26 => 'FILE_IP_DESTINO'
    
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

if ($vermask) {
    $condicion = "";
    if (!$verbajas)
        $condicion.= "  FILE_CODUSUARIO_BAJA IS NULL ";
    else if ($verbajas == 2)
        $condicion.= "";
    else
        $condicion.= " FILE_CODUSUARIO_BAJA IS NOT NULL ";
}
if ($codenv)
    $condicion.= " AND CODENVIO='$codenv' ";



if ($carga && !$codcanal && isset($vermask)) {
    $sql_canal = "SELECT CARGA_CODCANAL, CARGA_DESCANAL FROM v_cargas WHERE CODCARGA='$carga' ";
    $canal_sel = mysql_result(mysql_query($sql_canal), 0, "CARGA_CODCANAL");
    $canal_selnom = mysql_result(mysql_query($sql_canal), 0, "CARGA_DESCANAL");
    if (strpos($canal_selnom, "XCOM") !== false)
        $condicion.= " AND FILE_CODCANAL='00002' ";
    else if (strpos($canal_selnom, "FTP") !== false) {
        $condicion.= " AND (FILE_CODCANAL='00004' OR FILE_CODCANAL='00005' OR FILE_CODCANAL='00006' OR FILE_CODCANAL='00007') ";
    } else
        $condicion.= " AND FILE_CODCANAL='$canal_sel' ";
}


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
        if ($cuenta == 0){
            if($value['field']=='FILE_CODEMPRESA_DESTINO') $pred=" IN ( SELECT CODEMPRESA FROM tempresas WHERE DESEMPRESA $pred ) ";
            $condicionBusq.= " " . $value['field'] . $pred . " ";  // La condición serán los parámetros de búsqueda  
        }else{
            if($value['field']=='FILE_CODEMPRESA_DESTINO') $pred=" IN ( SELECT CODEMPRESA FROM tempresas WHERE DESEMPRESA $pred ) ";
            $condicionBusq.= " " . $filters['groupOp'] . " " . $value['field'] . $pred . " ";
        }
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

if ($mascara) {
    $parte1 = "";
    $parte2 = "";
    $parte3 = "";
    foreach ($json as $rowm) {

        $codempresa = mysql_result(mysql_query("SELECT CODEMPRESAPADRE FROM v_envios WHERE CODENVIO='" . $rowm['CODENVIO'] . "' "), 0, 'CODEMPRESAPADRE');
        if (!$codempresa)
            $codempresa = mysql_result(mysql_query("SELECT CODEMPRESA FROM v_envios WHERE CODENVIO='" . $rowm['CODENVIO'] . "' "), 0, 'CODEMPRESA');
        if (!$codempresa)
            $codempresa = 0;
        $ipdest = mysql_result(mysql_query("SELECT IP_DESTINO FROM v_envios WHERE CODENVIO='" . $rowm['CODENVIO'] . "' "), 0, 'IP_DESTINO');
        if (!$ipdest)
            $ipdest = 0;

        

        if ($carga)
            $codcanalaux2 = mysql_result(mysql_query("SELECT CARGA_CODCANAL FROM v_cargas WHERE CODCARGA='" . $carga . "' "), 0, 'CARGA_CODCANAL');

        $canal_selnom2 = mysql_result(mysql_query("SELECT DESCANAL FROM tcanales WHERE CODCANAL='$codcanalaux2' "), 0, 'DESCANAL');
        if (strpos($canal_selnom2, "XCOM") !== false)
            $strcanal2 = " FILE_DESCANAL LIKE '%XCOM%' ";
        else if (strpos($canal_selnom2, "FTP") !== false) {
            $strcanal2 = " FILE_DESCANAL LIKE '%FTP%' ";
        } else
            $strcanal2 = " FILE_CODCANAL='$codcanalaux2' ";
        if (!$codcanalaux2)
            $strcanal2 = "";

        if (!$strcanal2)
            $strcanal2 = " 1=1 ";

        if (!$primerop1) {
            $parte1.=" $strcanal2 ";
            $primerop1++;
        } else
            $parte1.=" OR $strcanal2 ";
        
            if (!$primerop2) {
                if($ipdest!==0) $parte2 .=" ((CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa') AND IP_DESTINO='$ipdest' ) ";
                else $parte2 .=" ((CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa') AND (IP_DESTINO IS NULL OR IP_DESTINO='' )) ";
                $primerop2++;
            } else
                if($ipdest!==0) $parte2 .=" OR ((CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa') AND IP_DESTINO='$ipdest' ) ";
                else $parte2 .=" OR ((CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa') AND (IP_DESTINO IS NULL OR IP_DESTINO='' ) ) ";
        
       
       
    }
    if(!$vermask) $novermask=" AND CODFICHEROPADRE IS NULL ";
    $sql_maskm = "SELECT $stringCampos FROM  $tabla  WHERE FICHERO_ORIGEN LIKE '$mascara' AND ($parte1) AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (". $parte2 .") $novermask ) AND FILE_CODUSUARIO_BAJA IS NULL ";
    $result = mysql_query($sql_maskm);
}

if ($coincidencias) {
    $codempresa = mysql_result(mysql_query("SELECT CODEMPRESAPADRE FROM tempresas WHERE DESEMPRESA='$desempresa' "), 0, 'CODEMPRESAPADRE');
    if (!$codempresa)
        $codempresa = mysql_result(mysql_query("SELECT CODEMPRESA FROM tempresas WHERE DESEMPRESA='$desempresa' "), 0, 'CODEMPRESA');
    if (!$codempresa)
        $codempresa = 0;
    if ($strcanal) {
        if (!$vermask) {
            $result = mysql_query("SELECT COUNT(*) AS count FROM $tabla  WHERE  CODFICHEROPADRE IS NULL AND $strcanal
                    AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest')) AND FILE_CODUSUARIO_BAJA IS NULL ");
        } else {
            $result = mysql_query("SELECT COUNT(*) AS count FROM $tabla  WHERE   $strcanal
                    AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest'))  AND FILE_CODUSUARIO_BAJA IS NULL ");
        }
    } else {
        if (!$vermask) {
            $result = mysql_query("SELECT COUNT(*) AS count FROM $tabla  WHERE  CODFICHEROPADRE IS NULL 
             AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest')) AND FILE_CODUSUARIO_BAJA IS NULL ");
        } else {
            $result = mysql_query("SELECT COUNT(*) AS count FROM $tabla  WHERE  CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest')) AND FILE_CODUSUARIO_BAJA IS NULL ");
        }
    }
}




$row = mysql_fetch_array($result, MYSQL_ASSOC);

$count = $row['count'];
if(!$count) $count=  mysql_num_rows($result);

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
    $SQL_nolim = "SELECT $stringCampos FROM $tabla WHERE $condicionBusq $andCondicion ";
    $order = " ORDER BY $sidx $sord ";
} else {
    $SQL = "SELECT $stringCampos FROM $tabla $whereCondicion ORDER BY $sidx $sord LIMIT $start , $limit";
    $SQL_nolim = "SELECT $stringCampos FROM $tabla $whereCondicion ";
    $order = " ORDER BY $sidx $sord ";
}

if ($mascara) {
    $SQL = $sql_maskm . " ORDER BY $sidx $sord limit $start , $limit ";
    $SQL_nolim = $sql_maskm;
    $order = " ORDER BY FICHERO_ORIGEN  ";
    if ($_GET['_search'] == 'true' && isset($searchField)) {
        $SQL = $sql_maskm . " AND $condicionBusq $andCondicion  ORDER BY $sidx $sord limit $start , $limit ";
        $SQL_nolim = $sql_maskm . " AND $condicionBusq $andCondicion ";
        $order = " ORDER BY FICHERO_ORIGEN  ";
    }
}


if ($coincidencias) {
    if ($strcanal) {
        if (!$vermask) {
            $SQL = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE $strcanal AND CODFICHEROPADRE IS NULL AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' )) AND FILE_CODUSUARIO_BAJA IS NULL  
                    ORDER BY nivel_coincidencia LIMIT $start , $limit ";
            $SQL_nolim = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE $strcanal AND CODFICHEROPADRE IS NULL AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' )) AND FILE_CODUSUARIO_BAJA IS NULL ";
        } else {
            $SQL = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE $strcanal  AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' ))  AND FILE_CODUSUARIO_BAJA IS NULL 
                      ORDER BY nivel_coincidencia LIMIT $start , $limit ";
            $SQL_nolim = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE $strcanal  AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' )) AND FILE_CODUSUARIO_BAJA IS NULL ";
        }
        $order = " ORDER BY nivel_coincidencia ";
    } else {
        if (!$vermask) {
            $SQL = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE  CODFICHEROPADRE IS NULL AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' ))  AND FILE_CODUSUARIO_BAJA IS NULL 
                     ORDER BY nivel_coincidencia LIMIT $start , $limit ";
            $SQL_nolim = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE CODFICHEROPADRE IS NULL AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' )) AND FILE_CODUSUARIO_BAJA IS NULL ";
        } else {
            $SQL = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE   CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' ))  AND FILE_CODUSUARIO_BAJA IS NULL 
                      ORDER BY nivel_coincidencia LIMIT $start , $limit ";
            $SQL_nolim = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE  CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' )) AND FILE_CODUSUARIO_BAJA IS NULL  ";
        }
        $order = " ORDER BY nivel_coincidencia ";
    }

    if ($_GET['_search'] == 'true' && isset($searchField)) {

        if ($strcanal) {
            if (!$vermask) {
                $SQL = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE $condicionBusq $andCondicion AND $strcanal AND CODFICHEROPADRE IS NULL AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' ))  AND FILE_CODUSUARIO_BAJA IS NULL 
                      ORDER BY nivel_coincidencia LIMIT $start , $limit ";
                $SQL_nolim = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE $condicionBusq $andCondicion AND $strcanal AND CODFICHEROPADRE IS NULL AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' )) AND FILE_CODUSUARIO_BAJA IS NULL ";
            } else {
                $SQL = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE $condicionBusq $andCondicion AND $strcanal  AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' )) AND FILE_CODUSUARIO_BAJA IS NULL   
                      ORDER BY nivel_coincidencia LIMIT $start , $limit ";
                $SQL_nolim = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE $condicionBusq $andCondicion AND $strcanal  AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' )) AND FILE_CODUSUARIO_BAJA IS NULL ";
            }
            $order = " ORDER BY nivel_coincidencia ";
        } else {
            if (!$vermask) {
                $SQL = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE $condicionBusq $andCondicion AND CODFICHEROPADRE IS NULL AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' ))  AND FILE_CODUSUARIO_BAJA IS NULL 
                      ORDER BY nivel_coincidencia LIMIT $start , $limit ";
                $SQL_nolim = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE $condicionBusq $andCondicion AND CODFICHEROPADRE IS NULL AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' )) AND FILE_CODUSUARIO_BAJA IS NULL ";
            } else {
                $SQL = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE $condicionBusq $andCondicion  AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' )) AND FILE_CODUSUARIO_BAJA IS NULL  
                      ORDER BY nivel_coincidencia LIMIT $start , $limit ";
                $SQL_nolim = "SELECT $stringCampos, levenshtein(FICHERO_ORIGEN,'$coincidencias') as nivel_coincidencia FROM $tabla WHERE $condicionBusq $andCondicion  AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' OR IP_DESTINO='$ipdest' )) AND FILE_CODUSUARIO_BAJA IS NULL ";
            }
            $order = " ORDER BY nivel_coincidencia ";
        }
    }
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
    12 => 'FILE_MASCARA',
    13 => 'FILE_DESCANAL',
    14 => 'FILE_CODMOTIVOBAJA',
    15 => 'FILE_DESMOTIVOBAJA',
    16 => 'FILE_FECHA_ALTA',
    17 => 'FILE_FECHA_MODIFICACION',
    18 => 'FILE_FECHA_BAJA',
    19 => 'FILE_CODUSUARIO_ALTA',
    20 => 'FILE_DESUSUARIO_ALTA',
    21 => 'FILE_CODUSUARIO_MODIFICACION',
    22 => 'FILE_DESUSUARIO_MODIFICACION',
    23 => 'FILE_CODUSUARIO_BAJA',
    24 => 'FILE_DESUSUARIO_BAJA',
    25 => 'FILE_ESTADO',
    26 => 'FILE_CODEMPRESA_DESTINO',
    27 => 'FILE_IP_DESTINO',
     28 => 'FILE_DESCANAL'
);


$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$responce->userdata['SQL'] = $SQL_nolim;
$responce->userdata['campos'] = $stringCampos;
$responce->userdata['order'] = $order;
$responce->userdata['prueba'] = $json;
$responce->userdata['mascara'] = $mascara;

$i = 0;
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $responce->rows[$i][$campoId] = $row[$campoId];

    $valColumnas = array();
    foreach ($campos AS $nomCampo) {
        if ($nomCampo === 'FILE_MASCARA') {
            $valemp = mysql_result(mysql_query("SELECT FICHERO_ORIGEN FROM tficheros WHERE CODFICHEROPADRE='" . $row['CODFICHERO'] . "' AND CODENVIO='" . $row['CODENVIO'] . "' "), 0, 'FICHERO_ORIGEN');
            if ($valemp)
                array_push($valColumnas, $valemp);
            else
                array_push($valColumnas, "");
        }else
        if ($nomCampo === 'FILE_CODEMPRESA_DESTINO') {
            $valemp = mysql_result(mysql_query("SELECT DESEMPRESA FROM tempresas WHERE CODEMPRESA='" . $row[$nomCampo] . "' "), 0, 'DESEMPRESA');
            array_push($valColumnas, $valemp);
        } else
            array_push($valColumnas, $row[$nomCampo]);
    }

    $responce->rows[$i]['cell'] = $valColumnas;
    $i++;
}

echo json_encode($responce);

?>

