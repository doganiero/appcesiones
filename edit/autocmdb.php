<?

//include_once('/usr/local/pr/aut/www/htdocs/functions/db.php');
//include_once('../functions/db.php');
//$link = conectar();
//mysql_set_charset('utf8');
echo "</br>Inicio de Proceso de AutoCarga CMDB: ".date('d-m-Y H:i:s')."</br></br>";

//linux -----------------------
/*

  $directorio_cmdb="../../tmp/cmdb";

  
  //-------------------------------------------
 */
/*
//Mac -----------------------

$directorio_cmdb = "/Applications/MAMP/tmp/cmdb";


//----------------------------------------------          
 */

  //Windows y linux-----------------------


  $directorio_cmdb=$directorio_raiz."cmdb";

 
  //-------------------------------------------


$descarga = "CARGA CMDB AUTOMÁTICA";
$ejercicio = date('Y');
$mes = date('m');
$observaciones = "CARGA CMDB EJECUTADA AUTOMÁTICAMENTE";

//scandir   


set_time_limit(0);
mysql_query("SET foreign_key_checks = 0"); // Desactiva claves foráneas 
//$ruta_fichero=  str_replace('\','"/", $ruta_fichero);
mysql_query("TRUNCATE TABLE temp_cmdb");
  

    $ruta_fichero_cmdb = $directorio_cmdb . "/aut_cmdb.CSV";
   

        // CARGANDO EL CMDB en tabla temp_cmdb BY TREMENDINI
       
        $filecmdb = fopen($ruta_fichero_cmdb, "r") or exit("Unable to open file!");
        //Output a line of the file until the end is reached
        while (!feof($filecmdb)) {
            $linea = fgets($filecmdb);
                    list($cola,$colb,$colc,$cold,$cole,$colf,$colg)=explode(";",$linea);

            mysql_query("INSERT INTO TEMP_CMDB (COL_A,COL_B,COL_C,COL_D,COL_E,COL_F,COL_G)
                                values ('" . str_replace('"','',$cola) . "','" . str_replace('"','',$colb) . "','" . str_replace('"','',$colc) . "','" . str_replace('"','',$cold) . "','" . str_replace('"','',$cole) . "','" . str_replace('"','',$colf) . "','" . str_replace('"','',$colg) . "' )");
            $error_l = mysql_error();
            $error_lf = mysql_num_rows(mysql_query("SHOW WARNINGS")) + $error_lf;
        }
        fclose($filecmdb);

     
        //////////////////////////////////////////////////////////////////////////////

        //-------fin CARGA CMDB-------------------------------------------------
        

        if ($error_l == "") {
            if (!$error_lf)
                echo "Fichero CMDB - $fichero_cmdb cargado correctamente.<br /> ";
            else
                echo "Fichero CMDB - $fichero_cmdb cargado correctamente.<br /> ";
             
             //unlink($ruta_fichero_cmdb); <---- aquí revisar si se tiene que botrrar el fichero o no
        }
        else {

            echo "Error en la carga <br />" . $error_l;

        }
     $error_l=0;
     $error_lf=0;

mysql_query("SET foreign_key_checks = 1"); // Activa claves foráneas 
echo "</br></br>Fin de Proceso de AutoCarga CMDB: ".date('d-m-Y H:i:s')."</br></br>";
?>
