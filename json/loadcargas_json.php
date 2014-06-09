<?

include("../functions/db.php");

// Nos conectamos a la base de datos.
$link = conectar();
mysql_set_charset('utf8');
set_time_limit(0);

?>
    
    
      <div id="refresh" class="colIzquierda" style='width: 160px; margin-left: 10px'>

   

            <table style="font-size: 9pt; width: 310px; margin-top: 10px">

              
                <tr><td align="left"><strong>CARGA:</strong></td>
                    <td  ><select id="SELCODCARGA"  name="SELCODCARGA" style="width: 300px; text-align: left ">
                          
                           
                            <?
                            $cons_cargas = mysql_query("SELECT CODCARGA, FICHERO_LOG, CARGA_FECHA_ALTA, CARGA_DESCANAL FROM v_cargas order by CARGA_FECHA_ALTA desc ");
                            //$total_cargas=mysql_num_rows($cons_cargas);
                            while ($fila = mysql_fetch_object($cons_cargas)) {
                                 if(strpos($fila->CARGA_DESCANAL,"GEPP")!== false){
                                     $muestracanal=  str_replace("GEPP","", $fila->CARGA_DESCANAL);
                                 }else $muestracanal="";
                                $primera++;
                                if($primera==1) $seleccionar=" selected='selected' ";
                                list($Y,$m,$d)=explode("-",substr($fila->CARGA_FECHA_ALTA,0,10));   
                                echo "<option $seleccionar id='fcarga-" . $fila->CODCARGA . "' value='" . $fila->CODCARGA . "'>".strtoupper($fila->FICHERO_LOG)."".$muestracanal."</option>";
                                $seleccionar="";
                                
                            }
                            ?>
                           <option  value='' >Todas las Cargas</option> 
                        </select>
                    </td></tr>
                
            </table>



         

        </div>
    
    


   
   

      
    
