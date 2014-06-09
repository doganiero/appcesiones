<?

include("../functions/db.php");

// Nos conectamos a la base de datos.
$link = conectar();
mysql_set_charset('utf8');
set_time_limit(0);
$cons_empresas = mysql_query("SELECT CODEMPRESA, DESEMPRESA FROM tempresas WHERE  CODEMPRESA<>'00000' AND CODEMPRESAPADRE IS NULL AND CHKACTIVO='S' ORDER BY DESEMPRESA ");
?>
   
              
                 <div id="refrescar"><select id="ECODEMPRESAPADRE" type="text" name="ECODEMPRESAPADRE" style="width: 180px; text-align: left ">
                            <option  value='00000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_empresas)) {

                                echo "<option id='efempresa-" . $fila->CODEMPRESA . "' value='" . $fila->CODEMPRESA . "'>" . utf8_encode($fila->DESEMPRESA) . "</option>";
                            }
                            ?>
                            </select></div>
                  
  
    
    


   
   

      
    
