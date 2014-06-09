<?
$cons_canales=mysql_query("SELECT CODCANAL, DESCANAL FROM tcanales WHERE DESCANAL NOT LIKE '%GEPP-%' AND (CODCANAL='00001' OR CODCANAL='00002' OR CODCANAL='00003' OR CODCANAL='00000')order by CODCANAL ");
$ejercicio=array();
$y=0; $i=0;
for($i=date('Y')-3;$i<=date('Y')+5;$i++){
    $ejercicio[$y]=$i;
    $y++;
}

?>
<div id="modalCargas" title="Nueva Carga Log"  > 
    <form name="cargaForm" id="cargaForm" action="edit/cargas_ed.php" method="post"  > 
        <input type="hidden" id="idCarga" name="idCarga" />
              <input id="opcion5" type="hidden" name="opcion" value="nuevo" />
              <input id="idCanalSelect" type="hidden" name="idCanalSelect" value="" />
        <div class="colIzquierda" style='width: 160px; margin-left: 10px'>

            <table style="font-size: 9pt; width: 160px; margin-top: 20px">
                <!--<tr class="fila">
                    <td class="celda" colspan='2'><div id="CODCARGA" class="subtitulo">CODCARGA: </div></td>
                </tr>-->
                <tr><td align="left"><strong>DESCRIPCIÓN:</strong></td>
                    <td><input id="FDESCARGA" type="text" name="FDESCARGA" class="required" style="width: 280px; text-align: left "/></td></tr>
              
                <tr><td align="left"><strong>CANAL:</strong></td>
                    <td><select id="FCODCANAL" name="FCODCANAL" style="width: 280px; text-align: left ">
                    <?while($fila=mysql_fetch_object($cons_canales)){
                   
                    echo "<option id='fcodcanal-".$fila->CODCANAL."' value='".$fila->CODCANAL."'>".$fila->DESCANAL."</option>";
                    
                    }
                    ?></select>
                    </td></tr>
                  <tr><td align="left"><strong>FICHERO LOG:</strong></td>
                    <td><select id="FFICHERO_LOG"  name="FFICHERO_LOG" style="width: 280px; text-align: left ">
                            <option  class="inicial" value='' selected="selected" >Seleccionar...</option>
                        </select></td></tr>
                    
                <tr><td align="left"><strong>EJERCICIO:</strong></td>
                    <td><select id="EJERCICIO" name="EJERCICIO" style="width: 280px; text-align: left ">
                        <?
                        foreach($ejercicio as $anio){
                            if($anio==date('Y'))$selected='selected';
                          echo "<option id='EJERCICIO-".$anio."' value='".$anio."' $selected >".$anio."</option>";
                          $selected='';
                        }
                        ?>
                        </select>
                    </td></tr>

                <tr><td align="left"><strong>MES:</strong></td>
                    <td><select id="MES"  name="MES" style="width: 280px; text-align: left ">
                    <option value='01' id="MES-01" <?if(date('m')=='01') echo ' selected '; ?> >ENERO</option>
                    <option value='02' id="MES-02" <?if(date('m')=='02') echo ' selected '; ?> >FEBRERO</option>
                    <option value='03' id="MES-03"  <?if(date('m')=='03') echo ' selected '; ?> >MARZO</option>
                    <option value='04' id="MES-04"  <?if(date('m')=='04') echo ' selected '; ?> >ABRIL</option>
                    <option value='05' id="MES-05" <?if(date('m')=='05') echo ' selected '; ?> >MAYO</option>
                    <option value='06' id="MES-06" <?if(date('m')=='06') echo ' selected '; ?> >JUNIO</option>
                    <option value='07' id="MES-07" <?if(date('m')=='07') echo ' selected '; ?> >JULIO</option>
                    <option value='08' id="MES-08" <?if(date('m')=='08') echo ' selected '; ?> >AGOSTO</option>
                    <option value='09' id="MES-09" <?if(date('m')=='09') echo ' selected '; ?> >SEPTIEMBRE</option>
                    <option value='10' id="MES-10" <?if(date('m')=='10') echo ' selected '; ?> >OCTUBRE</option>
                    <option value='11' id="MES-11" <?if(date('m')=='11') echo ' selected '; ?> >NOVIEMBRE</option>
                    <option value='12' id="MES-12" <?if(date('m')=='12') echo ' selected '; ?> >DICIEMBRE</option>
                        </select>
                    </td></tr>
                <tr><td align="left"valign='top'><strong>OBSERVACIONES:</strong></td>
                    <td><textarea id="OBSERVACIONES" class="obs" type="textarea" name="OBSERVACIONES" style="width: 280px; text-align: left; height:60px "/></textarea></td></tr>
                      <!-- Campos deshabilitados en insert (automáticos) o no se utilizan
                                <tr><td align="left"><strong>CHKCONSOLIDADO:</strong></td>
                    <td><input id="CHKCONSOLIDADO" type="text" name="prov_tlf2" style="width: 150px; text-align: left "/></td></tr>
                    
                              
                <tr><td align="left"><strong>FECHA_ALTA:</strong></td>
                    <td><input id="FECHA_ALTA" type="text" name="prov_tlf2" style="width: 150px; text-align: left "/></td></tr>
                <tr><td align="left"><strong>CARGA_FECHA_MODIFICACION:</strong></td>
                    <td><input id="CARGA_FECHA_MODIFICACION" type="text" name="prov_tlf2" style="width: 150px; text-align: left "/></td></tr>

                <tr><td align="left"><strong>CARGA_FECHA_BAJA:</strong></td>
                    <td><input id="CARGA_FECHA_BAJA" type="text" name="prov_tlf2" style="width: 150px; text-align: left "/></td></tr>

                <tr><td align="left"><strong>CARGA_CODUSUARIO_ALTA:</strong></td>
                    <td><input id="CARGA_CODUSUARIO_ALTA" type="text" name="prov_tlf2" style="width: 150px; text-align: left "/></td></tr>
                <tr><td align="left"><strong>CARGA_DESUSUARIO_ALTA:</strong></td>
                    <td><input id="CARGA_DESUSUARIO_ALTA" type="text" name="prov_tlf2" style="width: 150px; text-align: left "/></td></tr>
                <tr><td align="left"><strong>CARGA_CODUSUARIO_MODIFICACION:</strong></td>
                    <td><input id="CARGA_CODUSUARIO_MODIFICACION" type="text" name="prov_tlf2" style="width: 150px; text-align: left "/></td></tr>

-->
            </table>




        </div>
<!-- Campos deshabilitados en insert (automáticos) o no se utilizan
        <div class="colIzquierda" style='width: 160px; margin-left: 280px'>

            <table style="font-size: 9pt; width: 160px; margin-top: 20px">
                <tr class="fila">
                    <td class="celda" colspan='2'><div  class="subtitulo">DESCRIPCIONES</div></td>
                </tr>
<!-- Campos deshabilitados en insert (automáticos) o no se utilizan
                <tr><td align="left"><strong>CARGA_DESUSUARIO_MODIFICACION:</strong></td>
                    <td><input id="CARGA_DESUSUARIO_MODIFICACION" type="text" name="prov_tlf2" style="width: 150px; text-align: left "/></td></tr>

                <tr><td align="left"><strong>CARGA_CODUSUARIO_BAJA:</strong></td>
                    <td><input id="CARGA_CODUSUARIO_BAJA" type="text" name="prov_tlf2" style="width: 150px; text-align: left "/></td></tr>

                <tr><td align="left"><strong>CARGA_DESUSUARIO_BAJA:</strong></td>
                    <td><input id="CARGA_DESUSUARIO_BAJA" type="text" name="prov_tlf2" style="width: 150px; text-align: left "/></td></tr>

                <tr><td align="left"><strong>CARGA_ESTADO:</strong></td>
                    <td><input id="CARGA_ESTADO" type="text" name="prov_tlf2" style="width: 150px; text-align: left "/></td></tr>

            </table>



            
        </div>
-->
    </form>
    <!--- Fin Formulario de Nuevo Carga -->
    <input type="hidden" id="mensajeCarga" name="mensajeCarga" />
</div> <!--- Fin modalCargas  -->