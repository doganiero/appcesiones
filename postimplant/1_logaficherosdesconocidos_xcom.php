<?php

//include_once('/usr/local/pr/aut/www/htdocs/functions/db.php');
include_once('../functions/db.php');

$link = conectar();
set_time_limit(0);
mysql_set_charset('utf8');
mysql_query("SET foreign_key_checks = 0");
$inicio = date('d-m-Y H:i:s');
echo "Actualización de Empresas en Envíos XCOM
Inicio: $inicio     
<br><br>";
$sql_ficheros = "SELECT 
                    E.CODENVIO AS CODENV, 
                    E.CODENVIO_REMEDY AS CODREM, 
                    E.CODINTERVINIENTE AS CODIN,  
                    E.CODDESTINATARIO AS CODDES,
                    E.CODFRECUENCIA AS CODFREC,
                    E.CODCANAL AS CODCANENV,
                    E.CODAUTORIZA AS CODUS,
                    E.NOMBREMAQUINA_ORIGEN AS MAQ,
                    E.IP_ORIGEN AS IPO,
                    E.IP_DESTINO AS IPDES,
                    E.CHKCIFRADO AS CHKC,
                    E.CODTIPOENVIO AS CODTENV,
                    E.ENVIO_FECHA_ALTA AS ENVFECHAA,
                    F.FICHERO_ORIGEN AS FICH,
                    F.FICHERO_DESTINO AS FICHD,
                    F.RUTA_ORIGEN AS RUT,
                    F.UUAA AS FUUAA,
                    F.CODCLASIFICACION AS CODCLAS,
                    F.CODNIVEL_LOPD AS LOPD,
                    F.CODCANAL AS FCODCAN,
                    F.FECHA_ALTA AS FFECHAA,
                    B.DESEMPRESA_DESTINO AS DESEMP,
                    B.BUFFER_NOMBREMAQUINA_DESTINO AS MAQD,
                    B.BUFFER_IP_DESTINO AS IPD
                FROM TFICHEROS F, V_ENVIOS E, V_BUFFER B
                WHERE E.CODENVIO=F.CODENVIO AND
                      F.CODCANAL='00002'AND   
                       (E.IP_DESTINO IS NULL OR E.IP_DESTINO='') AND
                        ( B.BUFFER_IP_DESTINO IS NOT NULL OR B.BUFFER_NOMBREMAQUINA_DESTINO<>'')  AND
                       B.BUFFER_FICHERO_ORIGEN=F.FICHERO_ORIGEN  AND
                       F.CODFICHEROPADRE IS NULL AND
                       F.CODUSUARIO_BAJA IS NULL
                       GROUP BY F.CODENVIO, F.FICHERO_ORIGEN, B.BUFFER_FICHERO_ORIGEN, B.BUFFER_NOMBREMAQUINA_DESTINO,BUFFER_IP_DESTINO, BUFFER_IP_DESTINO ORDER BY F.CODENVIO ";
$buffresult = mysql_query($sql_ficheros);
while ($fila = mysql_fetch_object($buffresult)) {
    // Buscar empresa
    //$sql_empresa = "SELECT * FROM TEMPRESAS WHERE DESEMPRESA='" . ltrim(rtrim($fila->DESEMP)) . "'";
    //$codempresa = mysql_result(mysql_query($sql_empresa), 0, 'CODEMPRESA');
    //if (!$codempresa)
       // $codempresa = mysql_result(mysql_query($sql_empresa), 0, 'CODEMPRESAPADRE');
    //if (!$codempresa) {
      //  if (trim($fila->DESEMP) != "") {
        //    $sig_empresa = mysql_result((mysql_query(" SELECT MAX(CODEMPRESA) as max FROM tempresas ")), 0, 'max') + 1;
          //  $cifras = strlen($sig_empresa);
           // $ceros = "";
            //for ($i = $cifras; $i < 5; $i++) {
              //  $ceros.="0";
            //}
            
           // $sig_empresa = $ceros . $sig_empresa;
           // $sql_insemp = "INSERT INTO TEMPRESAS (CODEMPRESA,DESEMPRESA,CHKACTIVO) values ('$sig_empresa','" . ltrim(rtrim($fila->DESEMP)) . "','S')";
           // mysql_query($sql_insemp);
            //$codempresa = $sig_empresa;
            //$contnewemp++;
        //}
    //}
    if (trim($fila->MAQD) || trim($fila->IPD) ) {
        if ($aux_codenvio != $fila->CODENV) {
             if ($fila->MAQD){$sql_updatem = "UPDATE TENVIOS SET IP_DESTINO='$fila->MAQD' WHERE CODENVIO='" . $fila->CODENV . "' ";
             mysql_query($sql_updatem);}else
             if ($fila->IPD){$sql_updatei = "UPDATE TENVIOS SET IP_DESTINO='$fila->IPD' WHERE CODENVIO='" . $fila->CODENV . "' ";
             mysql_query($sql_updatei);}
            if (mysql_error())
                echo mysql_error(); else {
                $contemp++;
            }
        } else {
            //-----INSERTAR NUEVO ENVÍO-----------------------
            
            // datos de envío nuevo

            $codremedy = $fila->CODREM;
            $codinterviniente = $fila->CODIN;
            $coddestinatario = $fila->CODDES;
            $codfrecuencia = $fila->CODFREC;
            $codcanal = $fila->CODCANENV;
            $codautoriza = $fila->CODUS;
            $maquina = $fila->MAQ;
            $ipo = $fila->IPO;
            $ipd = $fila->IPDES;

            $chkcifrado = $fila->CHKC;
            if (!$chkcifrado)
                $chkcifrado = 'D';

            $codtipoenvio = $fila->CODTENV;

            $motivoenvio = "ENVÍO REPLICADO DE ORIGINAL $fila->CODENV POR MIGRACIÓN DE BASE DE DATOS";

            $efecha_alta = $fila->ENVFECHAA;

            $eobservaciones = "ENVÍO REPLICADO DE ORIGINAL $fila->CODENV POR MIGRACIÓN DE BASE DE DATOS";
            $observaciones = "FICHERO REPLICADO DE ENVÍO ORIGINAL $fila->CODENV POR MIGRACIÓN DE BASE DE DATOS";
            // datos de fichero nuevo

            $forigen = $fila->FICH;



            $fdestino = $fila->FICHD;
            $rutaorigen = $fila->RUT;
            $uuaa = $fila->FUUAA;
            $codclasificacion = $fila->CODCLAS;
            $codlopd = $fila->LOPD;
            $fcodcanal = $fila->FCODCAN;
            $ffecha_alta = $fila->FFECHAA;

            //insertar en tenvios
            //calcular siguiente ID
            // Si el fichero no está para la empresa
            if(trim($fila->MAQD)=="") $fila->MAQD=0;
            if(trim($fila->IPD)=="") $fila->IPD=0;
            $repetido = mysql_result(mysql_query("SELECT COUNT(*) as cuenta FROM tficheros f,tenvios e where e.codenvio=f.codenvio AND trim(f.FICHERO_ORIGEN)='" . trim($forigen) . "' AND (e.IP_DESTINO='$fila->MAQD' OR e.IP_DESTINO='$fila->IPD')  AND f.CODCANAL='$codcanal' "), 0, 'cuenta');
            if ($repetido == 0) {



                $sig_env = mysql_result((mysql_query(" SELECT MAX(CODENVIO) as max FROM tenvios ")), 0, 'max') + 1;
                $cifras = strlen($sig_env);
                $ceros = "";
                for ($i = $cifras; $i < 6; $i++) {
                    $ceros.="0";
                }
                $sig_env = $ceros . $sig_env;
                $sql_newenv = "INSERT INTO tenvios ( 
                        CODENVIO, 
                        CODTIPOENVIO,
                        CODENVIO_REMEDY,
                        CODUSUARIO,
                        CODEMPRESA,
                        CODINTERVINIENTE,
                        CODDESTINATARIO,
                        CODFRECUENCIA,
                        CODCANAL,
                        NOMBREMAQUINA_ORIGEN,
                        IP_ORIGEN,
                        IP_DESTINO,
                        CHKCIFRADO,
                        MOTIVOENVIO,
                        CODMOTIVOBAJA,
                        FECHA_ALTA,
                        FECHA_MODIFICACION,
                        FECHA_BAJA,
                        CODUSUARIO_ALTA,
                        CODUSUARIO_MODIFICACION,
                        CODUSUARIO_BAJA,
                        OBSERVACIONES
                )
                values ( 
                        '$sig_env', " . /* CODENVIO autonumérico . Terminar (buscar como incrementar sin buscar maxid) */"
                        '$codtipoenvio', " . /* CODTIPOENVIO el tipo de envío de este tipo de inserción es desconocido Terminar */"
                        '$codremedy', " . /* CODENVIO_REMEDY es nulo */"
                        '$codautoriza', " . /* este es el usuario que autoriza la cesión */"
                        '$codempresa', " . /* CODEMPRESA la empresa debería insertarse manualmente ya que no viene en el log. Terminar */"
                        '$codinterviniente', " . /* CODINTERVINIENTE se desconoce debería insertarse manualmente . Terminar */"
                        '$coddestinatario', " . /* CODDESTINATARIO se desconoce, debe insertarse manualmente .Terminar */"
                        '$codfrecuencia', " . /* CODFRECUENCIA se desconoce, debe insertarse manualmente .Terminar */"
                        '$codcanal', " . /* CODCANAL */"
                        '$maquina', " . /* NOMBREMAQUINA_ORIGEN */"
                        '$ipo', " . /* IP_ORIGEN */"
                        '$ipd', " . /* IP_DESTINO */"
                        '$chkcifrado', " . /* CHKCIFRADO se desconoce insertar manualmente Terminar */"
                        '$motivoenvio', " . /* MOTIVOENVIO Podría tener que cambiarse en el inserción a algo parecido en las observaciones . Terminar */"
                        '00000', " . /* CODMOTIVOBAJA es nulo o desconocido porque no es una baja */"
                        '$efecha_alta', " . /* FECHA_ALTA puede ser la fecha que viene en el log. Terminar */"
                        '0000-00-00 00:00:00', " . /* FECHA_MODIFICACION */"
                        '0000-00-00 00:00:00', " . /* FECHA_BAJA la fecha de baja es nula */"
                        '$webseal', " . /* CODUSUARIO_ALTA el usuario se desconoce aun por WebSeal. Terminar */"
                        NULL, " . /* CODUSUARIO_MODIFICACION puede ser nulo el usuario se desconoce aun por WebSeal. Terminar */"
                        NULL, " . /* CODUSUARIO_BAJA es nulo  */"
                        '$eobservaciones
            '" . /* OBSERVACIONES para  envío */"
                )";
                mysql_query($sql_newenv);
                $err = mysql_error();

                //actualizar valores nulos
                if (!$codremedy)
                    mysql_query("UPDATE tenvios SET CODENVIO_REMEDY=NULL WHERE CODENVIO='$sig_env'");
                if (!$maquina)
                    mysql_query("UPDATE tenvios SET NOMBREMAQUINA_ORIGEN=NULL WHERE CODENVIO='$sig_env'");
                if (!$ipo)
                    mysql_query("UPDATE tenvios SET IP_ORIGEN=NULL WHERE CODENVIO='$sig_env'");
                if (
                        !$ipd)
                    mysql_query("UPDATE tenvios SET IP_DESTINO=NULL WHERE CODENVIO='$sig_env'");

                if (!$observaciones)
                    mysql_query("UPDATE tenvios SET OBSERVACIONES=NULL WHERE CODENVIO='$sig_env'");


                //calcular id de nuevo fichero
                $sig_fich = mysql_result((mysql_query(" SELECT MAX(CODFICHERO) as max FROM tficheros WHERE CODENVIO='$sig_env' ")), 0, 'max') + 1;
                $cifras = strlen($sig_fich);
                $ceros = "";
                for ($i = $cifras; $i < 6; $i++) {
                    $ceros.="0";
                }
                $sig_fich = $ceros . $sig_fich;
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
                        '$codclasificacion', " . /* CODCLASIFICACION por los momentos nulo */"
                        '$codlopd', " . /* por los momentos nulo */"
                        '$codcanal', " . /* CODCANAL código de canal */"
                        '$forigen', " . /* nombre de fichero */"
                        '$fdestino', " . /* fichero destino nulo */"
                        '$rutaorigen', " . /* ruta origen nulo */"
                        '$uuaa', " . /* UUAA nula por desconocimiento Terminar */"
                        '00000', " . /* codmotivobaja nulo porque no es una baja */"
                        now(), " . /* fecha_alta */"
                        '0000-00-00 00:00:00', " . /* fecha_modificacion */"
                        '0000-00-00 00:00:00', " . /* fecha_baja nula */"
                        '$webseal', " . /* el usuario se desconoce aun por WebSeal. Terminar */"
                        NULL, " . /* el usuario_modificacion es nuelo porque es un nuevo registro */"
                        NULL, " . /* el usuario_baja es nulo */"
                        '$observaciones' , " . /* observaciones de nuevo registro */"
                        NULL " . /* codfichero padre nulo (no es una máscara) */"
                )";
                // Calcular el codclasificacion y nivel lopd del fichero, etc . Terminar
                mysql_query($sql_newfich);
                $err = mysql_error();
                //actualizar valores nulos

                if (!
                        $uuaa)
                    mysql_query("UPDATE tficheros SET UUAA=NULL WHERE CODENVIO='$sig_env' AND CODFICHERO='$sig_fich' ");
                if (!$rutaorigen)
                    mysql_query("UPDATE tficheros SET RUTA_ORIGEN=NULL WHERE CODENVIO='$sig_env' AND CODFICHERO='$sig_fich' ");

                if (!$fdestino)
                    mysql_query("UPDATE tficheros SET FICHERO_DESTINO=NULL WHERE CODENVIO='$sig_env' AND CODFICHERO='$sig_fich' ");
                if (!$observaciones)
                    mysql_query("UPDATE tficheros SET OBSERVACIONES=NULL WHERE CODENVIO='$sig_env' AND CODFICHERO='$sig_fich' ");
               
                $responce = "Envío: $sig_env dado de alta correctamente.  </br>";
                 echo "</br>$responce.$err</br>";

                if ($err)
                    $err = "Ha ocurrido un error " . mysql_error();
                else
                    $contenvio++;

                //-------------------------------------------------
            }

           
        }
  }
    else
        $contnotemp++;
    $aux_codenvio = $fila->CODENV;
}

echo "ENVÍOS CON IP_DESTINO o MÄQUINA DESCONOCIDA MODIFICADOS: $contemp<br>
      NUEVOS ENVÍOS REPLICADOS: $contenvio<br> 
      ENVÍOS CON IP_DESTINO o MÄQUINA DESCONOCIDA RESTANTES: $contnotemp
   
<br><br>
        ";



$fin = date('d-m-Y H:i:s');
echo "Fin: $fin
     <br>";
mysql_query("SET foreign_key_checks = 1");
?>
