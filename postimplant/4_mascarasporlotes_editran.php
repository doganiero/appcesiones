<?php

//include_once('/usr/local/pr/aut/www/htdocs/functions/db.php');
include_once('../functions/db.php');

$link = conectar();
set_time_limit(0);
mysql_set_charset('utf8');
mysql_query("SET foreign_key_checks = 0");
$inicio = date('d-m-Y H:i:s');
echo "Actualización de Empresas en Envíos EDITRAN
Inicio: $inicio     
<br><br>";


$sql_ficheros = "SELECT * FROM TFICHEROS F, V_ENVIOS E WHERE F.CODCANAL='00001' AND F.CODENVIO=E.CODENVIO AND F.CODUSUARIO_BAJA IS NULL AND F.CODFICHEROPADRE IS NULL GROUP BY F.FICHERO_ORIGEN, E.DESEMPRESA, E.IP_DESTINO ";
$result = mysql_query($sql_ficheros);
while ($fila = mysql_fetch_object($result)) {

    $sig_env = $fila->CODENVIO;
//calcular id de nuevo fichero
    $sig_fich = mysql_result((mysql_query(" SELECT MAX(CODFICHERO) as max FROM tficheros WHERE CODENVIO='$sig_env' ")), 0, 'max') + 1;
    $cifras = strlen($sig_fich);
    $ceros = "";
    for ($i = $cifras; $i < 6; $i++) {
        $ceros.="0";
    }
    $sig_fich = $ceros . $sig_fich;

    $codcanalPadre = $fila->CODCANAL;
    $mascara = $fila->FICHERO_ORIGEN . "%";
    $codfichPadre = $fila->CODFICHERO;
    //insertar  en tficheros
    $sql_newfich = "INSERT INTO tficheros ( 
                        CODENVIO,
                        CODFICHERO,
                        CODCLASIFICACION,
                        CODNIVEL_LOPD,
                        CODCANAL,
                        FICHERO_ORIGEN,
                        FICHERO_DESTINO,
                        RUTA_ORIGEN,
                        UUAA,
                        CODMOTIVOBAJA,
                        FECHA_ALTA,
                        FECHA_MODIFICACION,
                        FECHA_BAJA,
                        CODUSUARIO_ALTA,
                        CODUSUARIO_MODIFICACION,
                        CODUSUARIO_BAJA,
                        OBSERVACIONES,
                        CODFICHEROPADRE
                )
                values (
                        '$sig_env'," . /* CODENVIO actual */"
                        '$sig_fich', " . /* CODFICHERO NUEVO respecto al mismo CODENVIO .Terminar Buscar como autoincrementar sin buscar maxid */"
                        '00000', " . /* CODCLASIFICACION por los momentos nulo */"
                        '00000', " . /* por los momentos nulo */"
                        '$codcanalPadre', " . /* CODCANAL código de canal */"
                        '$mascara', " . /* nombre de fichero */"
                        NULL, " . /* fichero destino nulo */"
                        NULL, " . /* ruta origen nulo */"
                        NULL, " . /* UUAA nula por desconocimiento Terminar */"
                        '00000', " . /* codmotivobaja nulo porque no es una baja */"
                        now(), " . /* fecha_alta */"
                        '0000-00-00 00:00:00', " . /* fecha_modificacion */"
                        '0000-00-00 00:00:00', " . /* fecha_baja nula */"
                        '$webseal', " . /* el usuario se desconoce aun por WebSeal. Terminar */"
                        NULL, " . /* el usuario_modificacion es nuelo porque es un nuevo registro */"
                        NULL, " . /* el usuario_baja es nulo */"
                        'MÁSCARA CASO ESPECIAL' , " . /* observaciones de nuevo registro */"
                        '$codfichPadre' " . /* codfichero es una máscara) */"
                )";


    mysql_query($sql_newfich);
    if (mysql_error())
        echo mysql_error() . "</br>";
    else
        $cont_mask++;
}

$fin = date('d-m-Y H:i:s');
echo "Fin: $fin
     <br>Contador de Nuevas Máscaras: $cont_mask     <br>";
mysql_query("SET foreign_key_checks = 1");
?>
