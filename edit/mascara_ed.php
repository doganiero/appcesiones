<?php

include_once('../functions/db.php');
include_once('../functions/date.php');

$link = conectar();
mysql_set_charset('utf8');
    set_time_limit(0);
 mysql_query("SET foreign_key_checks = 0");
$mascara= $_GET['mascara'];
$envPadre=$_GET['envPadre'];
$codfichPadre=$_GET['codfichPadre'];
$codcanalPadre=$_GET['codcanalPadre'];

$rescanal=mysql_query(" SELECT DESCANAL FROM tcanales WHERE CODCANAL='$codcanalPadre' ");
$canal_selnom=mysql_result($rescanal,0,"DESCANAL");
if (strpos($canal_selnom, "XCOM")!== false){
        $strcanal= " CODCANAL LIKE '%XCOM%' ";
        $xcom=1;
}
    else if (strpos($canal_selnom, "FTP")!==false) {
        $strcanal= "  CODCANAL LIKE '%FTP%' ";
        $ftp=1;
    }
    else
        $strcanal= " CODCANAL='$codcanalPadre' ";

$borrar=$_GET['borrar'];
if ($mascara && !$borrar) { // Código para crear.  
 
 $json=$_POST['json'];  
$json = json_decode(stripslashes($json), true);
foreach($json as $filajax)
{
$row=array();
$row= $filajax;
    
    $envPadre=$row['CODENVIO'];
    $codfichPadre=$row['CODFICHERO'];
    $codcanalPadre=$row['FILE_CODCANAL'];
    $canal_selnom=$row['FILE_DESCANAL'];
    
if (strpos($canal_selnom, "XCOM")!== false){
        $strcanal= " CODCANAL='00002' ";
        $codcanalPadre='00002';
        $xcom=1;
}
    else if (strpos($canal_selnom, "FTP")!==false) {
        $strcanal= "  CODCANAL='00004' ";
        $codcanalPadre='00004';
        $ftp=1;
    }
    else
        $strcanal= " CODCANAL='$codcanalPadre' ";
    
    
            
    $codempresa=mysql_result(mysql_query("SELECT CODEMPRESAPADRE FROM v_envios WHERE CODENVIO='$envPadre' "),0,'CODEMPRESAPADRE');
    if(!$codempresa) $codempresa=mysql_result(mysql_query("SELECT CODEMPRESA FROM v_envios WHERE CODENVIO='$envPadre' "),0,'CODEMPRESA');
    if(!$codempresa) $codempresa=0;
    $ipdest=mysql_result(mysql_query("SELECT IP_DESTINO FROM v_envios WHERE CODENVIO='$envPadre' "),0,'IP_DESTINO');
    if(!$ipdest) $ipdest=0;
    if(!$yaexiste=mysql_num_rows(mysql_query("SELECT FICHERO_ORIGEN FROM tficheros WHERE FICHERO_ORIGEN='$mascara' AND $strcanal' 
                                              AND CODENVIO IN (SELECT CODENVIO FROM v_envios WHERE (CODEMPRESAPADRE='$codempresa' OR CODEMPRESA='$codempresa' ) OR IP_DESTINO=$ipdest)  "))){
            if($incluye=mysql_num_rows(mysql_query("SELECT FICHERO_ORIGEN FROM tficheros WHERE CODFICHERO='$codfichPadre' AND CODENVIO='$envPadre' AND FICHERO_ORIGEN LIKE '$mascara' "))){

        $sig_env=$envPadre;
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
                        'MÁSCARA' , " . /* observaciones de nuevo registro */"
                        '$codfichPadre' " . /* codfichero es una máscara) */"
                )";
        
        mysql_query($sql_newfich);
        $err = mysql_error();
        

            
        $responce = "Máscara dada de alta correctamente. </br>Es necesario ejecutar el proceso de consolidación en las cargas a las que se quiera aplicar. ";
        $nmask=1;
        if($err) $err= "Ha ocurrido un error ".mysql_error ();
            }
        else $err="Ha seleccionado un fichero padre que no coincide con la máscara introducida";
    }
    else $err="La máscara introducida ya existe";
    
}
    
  }
  else  if($borrar && $mascara) // BORRAR MÁSCARA
  {

mysql_query("DELETE FROM tficheros WHERE CODFICHEROPADRE='$codfichPadre' AND CODENVIO='$envPadre' AND FICHERO_ORIGEN='$mascara' ");
      $err=  mysql_error();
      if($err=="") $responce="Alerta. Máscara Eliminada";
  }
    
    mysql_query("SET foreign_key_checks = 1"); // Activa claves foráneas
$campos = array(
        'ERROR' => $err,
        'SUCCESS' => $responce,
         'CANAL' => $codcanalPadre,
         'MASCARA' => $mascara,
         'CODENV'=> $envPadre,
         'NMASK' => $nmask
      
);
       
                                    
  echo json_encode($campos);    



    
?>
