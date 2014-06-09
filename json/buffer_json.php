<?

include("../functions/db.php");

$page = $_GET['page']; // Guardamos la p�gina pedida.
if(!$page) $page=1;
$limit = $_GET['rows']; // Guardamos el n�mero de filas que deseamos en nuestra tabla.
if(!$limit) $limit=50; 
$sidx = $_GET['sidx']; // Guardamos el indice de fila (donde cliquea del usuario para ordenar).
if(!$sidx) $sidx='BUFFER_FICHERO_ORIGEN'; 
$sord = $_GET['sord']; // Guardamos la direccion del orden (ascendente o descendente).
if(!$sord) $sord='asc';
$searchField = $_GET['searchField'];
$searchString = $_GET['searchString'];
$operador = $_GET['searchOper'];
$chkcontrol = $_GET['chkcontrol'];
$coincidencias = $_GET['coincidencias'];
$mascara = $_GET['mascara'];
$codcanal = $_GET['codcanal'];
$codcarga = $_GET['codcarga'];
$codenv = $_GET['codenv'];
$agrupa = $_GET['agrupa'];
$codbuffermask = $_GET['codbuffermask'];
$json = $_POST['json'];
$json = json_decode(stripslashes($json), true);

if ($agrupa)
    $agrupa = " GROUP BY BUFFER_DESCANAL, BUFFER_FICHERO_ORIGEN, DESEMPRESA_DESTINO,BUFFER_NOMBREMAQUINA_DESTINO, BUFFER_IP_DESTINO ";
else
    $agrupa = "";


// Nos conectamos a la base de datos.
$link = conectar();

mysql_set_charset('utf8');

$rescanal = mysql_query(" SELECT DESCANAL FROM tcanales WHERE CODCANAL='$codcanal' ");
$canal_selnom = mysql_result($rescanal, 0, "DESCANAL");
if (strpos($canal_selnom, "XCOM") !== false)
    $strcanal = " v_cargas.CARGA_DESCANAL LIKE '%XCOM%' ";
else if (strpos($canal_selnom, "FTP") !== false) {
    $strcanal = "  v_cargas.CARGA_DESCANAL LIKE '%FTP%' ";
} else
    $strcanal = " v_cargas.CARGA_CODCANAL='$codcanal' ";

$strcanal = stripslashes($strcanal);

//echo $filters['groupOp'];
//echo $filters['rules'][0]['field'];
//echo $filters['rules'][0]['op'];
//echo $filters['rules'][1]['field'];
//echo $filters['rules'][1]['op'];

settype($searchString, "string");    // Forzamos el tipo a string.
// Array con los campos.
$campos = array(
    0 => 'V_BUFFER.CODCARGA',
    1 => 'V_BUFFER.BUFFER_DESCANAL',
    2 => 'CODBUFFER',
    3 => 'V_BUFFER.CODENVIO',
    4 => 'CODFICHERO',
    5 => 'BUFFER_FICHERO_ORIGEN',
    6 => 'CHKTRANSMISION_INTERNA',
    7 => 'TRANSMISION_INTERNA',
    8 => 'CODTIPOTRANSMISION',
    9 => 'DESTIPOTRANSMISION',
    10 => 'CHKCONTROL',
    11 => 'DESEMPRESA_DESTINO',
    12 => 'DESESTADOCONTROL',
    13 => 'BUFFER_CHKCIFRADO',
    14 => 'BUFFER_CIFRADO',
    15 => 'CODTIPODESTINO',
    16 => 'DESTIPODESTINO',
    17 => 'DESEMPRESA_ORIGEN',
    18 => 'BUFFER_NOMBREMAQUINA_ORIGEN',
    19 => 'BUFFER_IP_ORIGEN',
    20 => 'BUFFER_NOMBREMAQUINA_DESTINO',
    21 => 'BUFFER_IP_DESTINO',
    22 => 'MOTIVOLOG',
    23 => 'BUFFER_FICHERO_DESTINO',
    24 => 'BUFFER_FECHA_ALTA',
    25 => 'BUFFER_FECHA_MODIFICACION',
    26 => 'BUFFER_FECHA_BAJA',
    27 => 'BUFFER_CODUSUARIO_ALTA',
    28 => 'BUFFER_DESUSUARIO_ALTA',
    29 => 'BUFFER_CODUSUARIO_MODIFICACION',
    30 => 'BUFFER_DESUSUARIO_MODIFICACION',
    31 => 'BUFFER_CODUSUARIO_BAJA',
    32 => 'BUFFER_DESUSUARIO_BAJA',
    33 => 'BUFFER_ESTADO',
    34 => 'V_BUFFER.CODCARGA',
    35 => 'CODBUFFER',
    36 => 'BUFFER_FICHERO_ORIGEN'
);
// Campo ID
$campoId = "CODBUFFER";
// Tabla
$tabla = "V_BUFFER ";
// Condicion



$condicion = "";

//$condicion = " CHKCONTROL<>'S' "; // solo registros N E y D
if ($codcarga)
    $condicion = " CODCARGA='$codcarga' ";

if ($chkcontrol)
//$condicion = " CHKCONTROL='$chkcontrol' AND CHKCONTROL<>'S'"; // solo registros N E y D
    $condicion = " CHKCONTROL='$chkcontrol' ";

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



if ($_GET['_search'] == 'true' && isset($searchField)) {
    $result = mysql_query("SELECT BUFFER_FICHERO_ORIGEN AS count FROM $tabla WHERE 1=1 AND $condicionBusq $andCondicion $agrupa ");
} else {
    $result = mysql_query("SELECT BUFFER_FICHERO_ORIGEN AS count FROM $tabla $whereCondicion $agrupa ");
}

if ($mascara) {
    $codempresa = mysql_result(mysql_query("SELECT CODEMPRESAPADRE FROM v_envios WHERE CODENVIO='$codenv' "), 0, 'CODEMPRESAPADRE');
    if (!$codempresa)
        $codempresa = mysql_result(mysql_query("SELECT CODEMPRESA FROM v_envios WHERE CODENVIO='$codenv' "), 0, 'CODEMPRESA');
    if (!$codempresa)
        $codempresa = 0;
    $ipdest = mysql_result(mysql_query("SELECT IP_DESTINO FROM v_envios WHERE CODENVIO='$codenv' "), 0, 'IP_DESTINO');
    if (!$ipdest)
        $ipdest = 0;
    if ($codcarga)
        $result = mysql_query("SELECT BUFFER_FICHERO_ORIGEN AS count FROM $tabla, v_cargas WHERE BUFFER_FICHERO_ORIGEN LIKE '$mascara' AND v_cargas.CODCARGA=V_BUFFER.CODCARGA AND $strcanal AND v_cargas.CODCARGA='$codcarga' 
                                AND (V_BUFFER.DESEMPRESA_DESTINO IN (SELECT DESEMPRESA FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' )) OR V_BUFFER.BUFFER_IP_DESTINO='$ipdest' OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='$ipdest') ");
    else
        $result = mysql_query("SELECT BUFFER_FICHERO_ORIGEN AS count FROM $tabla, v_cargas WHERE BUFFER_FICHERO_ORIGEN LIKE '$mascara' AND v_cargas.CODCARGA=V_BUFFER.CODCARGA AND $strcanal  
                                AND (V_BUFFER.DESEMPRESA_DESTINO IN (SELECT DESEMPRESA FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' )) OR V_BUFFER.BUFFER_IP_DESTINO='$ipdest' OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='$ipdest') ");
}

$row = mysql_fetch_array($result, MYSQL_ASSOC);

$count = mysql_num_rows($result);
if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 0;
}

if ($codbuffermask) {
    $sql_pos = "SELECT * FROM (SELECT @rownum:=@rownum + 1 as row_number, 
       t.*
FROM ( ";
    $sql_pos.="select CODBUFFER from v_buffer WHERE CODCARGA='$codcarga' GROUP BY BUFFER_DESCANAL, BUFFER_FICHERO_ORIGEN, DESEMPRESA_DESTINO,BUFFER_NOMBREMAQUINA_DESTINO, BUFFER_IP_DESTINO  ORDER BY $sidx $sord ";

    $sql_pos.=") t,
(SELECT @rownum := 0) r ORDER BY $sidx $sord) s WHERE s.CODBUFFER='$codbuffermask' ";
    $posicion = mysql_result(mysql_query($sql_pos), 0, 'row_number');

    if (!$page || $page == 1)
        $page = ceil($posicion / $limit);
}

if ($page > $total_pages)
    $page = $total_pages;
$start = $limit * $page - $limit; // No poner $limit*($page - 1) 
if ($start < 0)
    $start = 0;   // LIMIT nunca puede ser negativo, como m�nimo es 0.

if ($_GET['_search'] == 'true' && isset($searchField)) {
    $SQL = "SELECT $stringCampos FROM $tabla WHERE $condicionBusq $andCondicion $agrupa ORDER BY $sidx $sord LIMIT $start , $limit";
    $SQL_nolim = "SELECT $stringCampos FROM $tabla WHERE $condicionBusq $andCondicion $agrupa ";
    $order = " ORDER BY $sidx $sord ";
} else {
    $SQL = "SELECT $stringCampos FROM $tabla $whereCondicion $agrupa ORDER BY $sidx $sord LIMIT $start , $limit";
    $SQL_nolim = "SELECT $stringCampos FROM $tabla $whereCondicion $agrupa ";
    $order = " ORDER BY $sidx $sord ";
}



if ($mascara) {
    $parte1 = "";
    $parte2 = "";
    $parte3 = "";
    $primerop2=0;
    foreach ($json as $rowm) {
       
        //------------------------------  SELECT V_BUFFER.CODCARGA, V_BUFFER.BUFFER_DESCANAL, CODBUFFER, V_BUFFER.CODENVIO, CODFICHERO, BUFFER_FICHERO_ORIGEN, CHKTRANSMISION_INTERNA, TRANSMISION_INTERNA, CODTIPOTRANSMISION, DESTIPOTRANSMISION, CHKCONTROL, DESEMPRESA_DESTINO, DESESTADOCONTROL, BUFFER_CHKCIFRADO, BUFFER_CIFRADO, CODTIPODESTINO, DESTIPODESTINO, DESEMPRESA_ORIGEN, BUFFER_NOMBREMAQUINA_ORIGEN, BUFFER_IP_ORIGEN, BUFFER_NOMBREMAQUINA_DESTINO, BUFFER_IP_DESTINO, MOTIVOLOG, BUFFER_FICHERO_DESTINO, BUFFER_FECHA_ALTA, BUFFER_FECHA_MODIFICACION, BUFFER_FECHA_BAJA, BUFFER_CODUSUARIO_ALTA, BUFFER_DESUSUARIO_ALTA, BUFFER_CODUSUARIO_MODIFICACION, BUFFER_DESUSUARIO_MODIFICACION, BUFFER_CODUSUARIO_BAJA, BUFFER_DESUSUARIO_BAJA, BUFFER_ESTADO, V_BUFFER.CODCARGA, CODBUFFER, BUFFER_FICHERO_ORIGEN FROM V_BUFFER , v_cargas WHERE BUFFER_FICHERO_ORIGEN LIKE 'A%' AND v_cargas.CODCARGA=V_BUFFER.CODCARGA AND (  BUFFER_DESCANAL LIKE '%XCOM%'   OR  BUFFER_DESCANAL LIKE '%XCOM%'  ) AND v_cargas.CODCARGA='000254'  
              //   AND( (V_BUFFER.DESEMPRESA_DESTINO IN (SELECT DESEMPRESA FROM tempresas WHERE CODEMPRESA='000000' OR CODEMPRESAPADRE='000000') AND (V_BUFFER.BUFFER_IP_DESTINO='' OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='') )
                 
                // 		OR (V_BUFFER.DESEMPRESA_DESTINO IN (SELECT DESEMPRESA FROM tempresas WHERE CODEMPRESA='000000' OR CODEMPRESAPADRE='000000') AND (V_BUFFER.BUFFER_IP_DESTINO='' OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='') )
                 //)

         $codempresa = mysql_result(mysql_query("SELECT CODEMPRESAPADRE FROM v_envios WHERE CODENVIO='" . $rowm['CODENVIO'] . "' "), 0, 'CODEMPRESAPADRE');
        if (!$codempresa)
            $codempresa = mysql_result(mysql_query("SELECT CODEMPRESA FROM v_envios WHERE CODENVIO='" . $rowm['CODENVIO'] . "' "), 0, 'CODEMPRESA');
        if (!$codempresa)
            $codempresa = 0;
        $ipdest = mysql_result(mysql_query("SELECT IP_DESTINO FROM v_envios WHERE CODENVIO='" . $rowm['CODENVIO'] . "' "), 0, 'IP_DESTINO');
        
        if (!$ipdest) 
            $ipdest = 0;
        
        if ($codcarga) {
            
            $codcanalaux2 = mysql_result(mysql_query("SELECT CARGA_CODCANAL FROM v_cargas WHERE CODCARGA='" . $codcarga . "' "), 0, 'CARGA_CODCANAL');

        $canal_selnom2 = mysql_result(mysql_query("SELECT DESCANAL FROM tcanales WHERE CODCANAL='$codcanalaux2' "), 0, 'DESCANAL');
        if (strpos($canal_selnom2, "XCOM") !== false)
            $strcanal2 = " BUFFER_DESCANAL LIKE '%XCOM%' ";
        else if (strpos($canal_selnom2, "FTP") !== false) {
            $strcanal2 = " BUFFER_DESCANAL LIKE '%FTP%' ";
        } 
        if (!$codcanalaux2)
            $strcanal2 = "";

        if (!$strcanal2)
            $strcanal2 = " 1=1 ";

        if (!$primerop1) {
            $parte1.=" $strcanal2 ";
            $primerop1++;
        } else
            $parte1.=" OR $strcanal2 ";
            
            if($codempresa!=="00000" && $codempresa!=0)$sqlsiemp="V_BUFFER.DESEMPRESA_DESTINO IN (SELECT DESEMPRESA FROM tempresas WHERE CODEMPRESA='$codempresa' OR CODEMPRESAPADRE='$codempresa') AND ";
            if (!$primerop2) {
                if($ipdest!==0) $parte2.="($sqlsiemp(V_BUFFER.BUFFER_IP_DESTINO='$ipdest' OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='$ipdest') ) ";
                else $parte2.="($sqlsiemp ((V_BUFFER.BUFFER_IP_DESTINO IS NULL OR V_BUFFER.BUFFER_IP_DESTINO='') AND  (V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO IS NULL OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='')) ) ";
                $primerop2++;
            } else
                if($ipdest!==0) $parte2.=" OR ($sqlsiemp (V_BUFFER.BUFFER_IP_DESTINO='$ipdest' OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='$ipdest') )  ";
                else $parte2.=" OR ($sqlsiemp ((V_BUFFER.BUFFER_IP_DESTINO IS NULL OR V_BUFFER.BUFFER_IP_DESTINO='') AND  (V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO IS NULL OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='')) ) ";
           $sqlsiemp="";     
            
            $SQL = "SELECT $stringCampos FROM $tabla, v_cargas WHERE BUFFER_FICHERO_ORIGEN LIKE '$mascara' AND v_cargas.CODCARGA=V_BUFFER.CODCARGA AND ($parte1) AND v_cargas.CODCARGA='$codcarga'  
                 AND ($parte2) $agrupa ORDER BY $sidx $sord  LIMIT $start , $limit";
            $SQL_nolim = "SELECT $stringCampos FROM $tabla, v_cargas WHERE BUFFER_FICHERO_ORIGEN LIKE '$mascara' AND v_cargas.CODCARGA=V_BUFFER.CODCARGA AND ($parte1) AND v_cargas.CODCARGA='$codcarga'  
                            AND ($parte2) $agrupa ";
            $order = " ORDER BY $sidx $sord ";
        } else {
            
            if($codempresa!=="00000" && $codempresa!=0)$sqlsiemp="V_BUFFER.DESEMPRESA_DESTINO IN (SELECT DESEMPRESA FROM tempresas WHERE CODEMPRESA='$codempresa' OR CODEMPRESAPADRE='$codempresa') AND ";
            if (!$primerop2) {
                if($ipdest!==0) $parte2.="($sqlsiemp(V_BUFFER.BUFFER_IP_DESTINO='$ipdest' OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='$ipdest') ) ";
                else $parte2.="($sqlsiemp ((V_BUFFER.BUFFER_IP_DESTINO IS NULL OR V_BUFFER.BUFFER_IP_DESTINO='') AND  (V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO IS NULL OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='')) ) ";
                $primerop2++;
            } else
                if($ipdest!==0) $parte2.=" OR ($sqlsiemp (V_BUFFER.BUFFER_IP_DESTINO='$ipdest' OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='$ipdest') )  ";
                else $parte2.=" OR ($sqlsiemp ((V_BUFFER.BUFFER_IP_DESTINO IS NULL OR V_BUFFER.BUFFER_IP_DESTINO='') AND  (V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO IS NULL OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='')) ) ";
           $sqlsiemp=""; 
            
            $SQL = "SELECT $stringCampos FROM $tabla, v_cargas WHERE BUFFER_FICHERO_ORIGEN LIKE '$mascara' AND v_cargas.CODCARGA=V_BUFFER.CODCARGA  
                AND ($parte2) $agrupa ORDER BY $sidx $sord  LIMIT $start , $limit";
            $SQL_nolim = "SELECT $stringCampos FROM $tabla, v_cargas WHERE BUFFER_FICHERO_ORIGEN LIKE '$mascara' AND v_cargas.CODCARGA=V_BUFFER.CODCARGA   
                       AND ($parte2) $agrupa ";
            $order = " ORDER BY $sidx $sord ";
        }

        if ($_GET['_search'] == 'true' && isset($searchField)) {
            if ($codcarga) {
                
                 $codcanalaux2 = mysql_result(mysql_query("SELECT CARGA_CODCANAL FROM v_cargas WHERE CODCARGA='" . $carga . "' "), 0, 'CARGA_CODCANAL');

        $canal_selnom2 = mysql_result(mysql_query("SELECT DESCANAL FROM tcanales WHERE CODCANAL='$codcanalaux2' "), 0, 'DESCANAL');
        if (strpos($canal_selnom2, "XCOM") !== false)
            $strcanal2 = " BUFFER_DESCANAL LIKE '%XCOM%' ";
        else if (strpos($canal_selnom2, "FTP") !== false) {
            $strcanal2 = " BUFFER_DESCANAL LIKE '%FTP%' ";
        } 
        if (!$codcanalaux2)
            $strcanal2 = "";

        if (!$strcanal2)
            $strcanal2 = " 1=1 ";

        if (!$primerop1) {
            $parte1.=" $strcanal2 ";
            $primerop1++;
        } else
            $parte1.=" OR $strcanal2 ";
        
        
          if($codempresa!=="00000" && $codempresa!=0 )$sqlsiemp="V_BUFFER.DESEMPRESA_DESTINO IN (SELECT DESEMPRESA FROM tempresas WHERE CODEMPRESA='$codempresa' OR CODEMPRESAPADRE='$codempresa') AND ";
            if (!$primerop2) {
                if($ipdest!==0) $parte2.="($sqlsiemp(V_BUFFER.BUFFER_IP_DESTINO='$ipdest' OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='$ipdest') ) ";
                else $parte2.="($sqlsiemp ((V_BUFFER.BUFFER_IP_DESTINO IS NULL OR V_BUFFER.BUFFER_IP_DESTINO='') AND  (V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO IS NULL OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='')) ) ";
                $primerop2++;
            } else
                if($ipdest!==0) $parte2.=" OR ($sqlsiemp (V_BUFFER.BUFFER_IP_DESTINO='$ipdest' OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='$ipdest') )  ";
                else $parte2.=" OR ($sqlsiemp ((V_BUFFER.BUFFER_IP_DESTINO IS NULL OR V_BUFFER.BUFFER_IP_DESTINO='') AND  (V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO IS NULL OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='')) ) ";
           $sqlsiemp=""; 
                
                
                $SQL = "SELECT $stringCampos FROM $tabla, v_cargas WHERE $condicionBusq $andCondicion and BUFFER_FICHERO_ORIGEN LIKE '$mascara' AND v_cargas.CODCARGA=V_BUFFER.CODCARGA AND ($parte1) AND v_cargas.CODCARGA='$codcarga'  
                 AND ($parte2)   
                    $agrupa ORDER BY $sidx $sord  LIMIT $start , $limit";
                $SQL_nolim = "SELECT $stringCampos FROM $tabla, v_cargas WHERE $condicionBusq $andCondicion and BUFFER_FICHERO_ORIGEN LIKE '$mascara' AND v_cargas.CODCARGA=V_BUFFER.CODCARGA AND ($parte1) AND v_cargas.CODCARGA='$codcarga'  
                            AND ($parte2) $agrupa ";
                $order = " ORDER BY $sidx $sord ";
            } else {
                
                if($codempresa!=="00000" && $codempresa!=0)$sqlsiemp="V_BUFFER.DESEMPRESA_DESTINO IN (SELECT DESEMPRESA FROM tempresas WHERE CODEMPRESA='$codempresa' OR CODEMPRESAPADRE='$codempresa') AND ";
            if (!$primerop2) {
                if($ipdest!==0) $parte2.="($sqlsiemp(V_BUFFER.BUFFER_IP_DESTINO='$ipdest' OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='$ipdest') ) ";
                else $parte2.="($sqlsiemp ((V_BUFFER.BUFFER_IP_DESTINO IS NULL OR V_BUFFER.BUFFER_IP_DESTINO='') AND  (V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO IS NULL OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='')) ) ";
                $primerop2++;
            } else
                if($ipdest!==0) $parte2.=" OR ($sqlsiemp (V_BUFFER.BUFFER_IP_DESTINO='$ipdest' OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='$ipdest') )  ";
                else $parte2.=" OR ($sqlsiemp ((V_BUFFER.BUFFER_IP_DESTINO IS NULL OR V_BUFFER.BUFFER_IP_DESTINO='') AND  (V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO IS NULL OR V_BUFFER.BUFFER_NOMBREMAQUINA_DESTINO='')) ) ";
           $sqlsiemp=""; 
                
                $SQL = "SELECT $stringCampos FROM $tabla, v_cargas WHERE $condicionBusq $andCondicion AND BUFFER_FICHERO_ORIGEN LIKE '$mascara' AND v_cargas.CODCARGA=V_BUFFER.CODCARGA   
                AND ($parte2) $agrupa ORDER BY $sidx $sord  LIMIT $start , $limit";
                $SQL_nolim = "SELECT $stringCampos FROM $tabla, v_cargas WHERE $condicionBusq $andCondicion AND BUFFER_FICHERO_ORIGEN LIKE '$mascara' AND v_cargas.CODCARGA=V_BUFFER.CODCARGA   
                       AND ($parte2) $agrupa ";
                $order = " ORDER BY $sidx $sord ";
            }
        }
    }
    //--------------------------------------------------------------------
}


$result = mysql_query($SQL) or die("No se pudo ejecutar la consulta. " . mysql_error());



$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$responce->userdata['SQL'] = $SQL_nolim;
$responce->userdata['campos'] = $stringCampos;
$responce->userdata['order'] = $order;
if ($_GET['_search'] == 'true' && isset($searchField))
    $responce->userdata['buscando'] = "1";
else
    $responce->userdata['buscando'] = "0";
$sql_s=$SQL_nolim." HAVING (CHKCONTROL='S') ";
$s=number_format(mysql_num_rows(mysql_query($sql_s)), 0, ',', '.');
$sql_e=$SQL_nolim." HAVING (CHKCONTROL='E') ";
$e=number_format(mysql_num_rows(mysql_query($sql_e)), 0, ',', '.');
$sql_n=$SQL_nolim." HAVING (CHKCONTROL='N') ";
$n=number_format(mysql_num_rows(mysql_query($sql_n)), 0, ',', '.');
$sql_d=$SQL_nolim." HAVING (CHKCONTROL='D') ";
$d=number_format(mysql_num_rows(mysql_query($sql_d)), 0, ',', '.');

 $responce->userdata['s'] = $s;
 $responce->userdata['e'] = $e;
 $responce->userdata['n'] = $n;
 $responce->userdata['d'] = $d;
$campos = array(
    0 => 'CODCARGA',
    1 => 'BUFFER_DESCANAL',
    2 => 'CODBUFFER',
    3 => 'CODENVIO',
    4 => 'CODFICHERO',
    5 => 'BUFFER_FICHERO_ORIGEN',
    6 => 'CHKTRANSMISION_INTERNA',
    7 => 'TRANSMISION_INTERNA',
    8 => 'CODTIPOTRANSMISION',
    9 => 'DESTIPOTRANSMISION',
    10 => 'CHKCONTROL',
    11 => 'DESEMPRESA_DESTINO',
    12 => 'DESESTADOCONTROL',
    13 => 'BUFFER_CHKCIFRADO',
    14 => 'BUFFER_CIFRADO',
    15 => 'CODTIPODESTINO',
    16 => 'DESTIPODESTINO',
    17 => 'DESEMPRESA_ORIGEN',
    18 => 'BUFFER_NOMBREMAQUINA_ORIGEN',
    19 => 'BUFFER_IP_ORIGEN',
    20 => 'BUFFER_NOMBREMAQUINA_DESTINO',
    21 => 'BUFFER_IP_DESTINO',
    22 => 'MOTIVOLOG',
    23 => 'BUFFER_FICHERO_DESTINO',
    24 => 'BUFFER_FECHA_ALTA',
    25 => 'BUFFER_FECHA_MODIFICACION',
    26 => 'BUFFER_FECHA_BAJA',
    27 => 'BUFFER_CODUSUARIO_ALTA',
    28 => 'BUFFER_DESUSUARIO_ALTA',
    29 => 'BUFFER_CODUSUARIO_MODIFICACION',
    30 => 'BUFFER_DESUSUARIO_MODIFICACION',
    31 => 'BUFFER_CODUSUARIO_BAJA',
    32 => 'BUFFER_DESUSUARIO_BAJA',
    33 => 'BUFFER_ESTADO',
    34 => 'CODCARGA',
    35 => 'CODBUFFER',
    36 => 'BUFFER_FICHERO_ORIGEN'
);


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
        } else
            array_push($valColumnas, $row[$nomCampo]);
    }

    $responce->rows[$i]['cell'] = $valColumnas;

    $i++;
}

echo json_encode($responce);
?>

