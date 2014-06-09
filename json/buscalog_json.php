<?

include("../functions/db.php");

// Nos conectamos a la base de datos.
$link = conectar();
mysql_set_charset('utf8');
set_time_limit(0);
$codcanal=$_POST['codcanal']; 

//linux -----------------------
/*
if ($codcanal == "00001")
     $directorio="../../tmp/logs/editran";
      else if ($codcanal == "00002")
          $directorio="../../tmp/logs/xcom";
           else if ($codcanal == "00003")
               $directorio="../../tmp/logs/gepp";

//-------------------------------------------        
*/

//Mac -----------------------
/*
if ($codcanal == "00001")
     $directorio="/Applications/MAMP/tmp/logs/editran";
      else if ($codcanal == "00002")
          $directorio="/Applications/MAMP/tmp/logs/xcom";
           else if ($codcanal == "00003")
               $directorio="/Applications/MAMP/tmp/logs/gepp";
 //----------------------------------------------          
*/

//Windows y linux----------------------- 

if ($codcanal == "00001")
     $directorio="/Applications/MAMP/tmp/logs/editran";
      else if ($codcanal == "00002")
          $directorio="../../tmp/logs/xcom";
           else if ($codcanal == "00003")
               $directorio="../../tmp/logs/gepp";

//-------------------------------------------        


//scandir   
$ficheros=scandir($directorio);
//echo $directorio;
echo json_encode($ficheros);
?>
