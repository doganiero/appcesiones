<script>
    $("#modalficheros").dialog({
        autoOpen: false,
        height: 430,
        width: 370,
        bgiframe: true,
        resizable: false,
        modal: true,
        overlay: {
            backgroundColor: '#000',
            opacity: 0.5
        },
        buttons: {
            'Agregar': function() {

                $('#remedyFormfich').submit();
                
            },
            'Cancelar': function() {
                $(this).dialog('close');
            }
        },
        open: function()
        {
            
           $("#FNFILE_FECHA_ALTA").datepicker({dateFormat: "dd-mm-yy"});
           
          
            
        },
            close: function() {
                 //$('#remedyFormfich').clearForm();
            }
    
    });





</script>
<?
$cons_clasificaciones = mysql_query("SELECT CODCLASIFICACION, DESCLASIFICACION FROM tclasificaciones WHERE CHKACTIVO='S' AND CODCLASIFICACION<>'00000' ");
$cons_niveleslopd= mysql_query("SELECT CODNIVEL_LOPD, DESNIVEL_LOPD FROM tniveles_lopd WHERE CHKACTIVO='S' AND CODNIVEL_LOPD<>'00000' ");
//$cons_canales= mysql_query("SELECT CODCANAL, DESCANAL FROM tcanales WHERE CHKACTIVO='S' AND CODCANAL<>'00000' ");
?>
<div id="modalficheros" title="Nuevo Fichero">  
    <!--- Formulario de Nuevo Remedy -->
    <form name="remedyFormfich" id="remedyFormfich" action="edit/remedy_ed.php" method="post"  > 
        <!-- Para el control de pulsación de los botones usaremos otro input oculto -->    
        <input id="opcion2" type="hidden" name="opcion" value="nuevofich" /> <!-- Para nuevo remedy y fichero -->
        <input id="envSel" type="hidden" name="envSel" value="" />

        <div class="colIzquierda" style='width: 190px; margin-left: 10px'>



            <table style="font-size: 9pt; width: 330px; margin-top: 10px">
                <tr class="fila">
                    <td class="celda" colspan='2'><div id="NCODFICHERO" class="subtitulo">DATOS FICHERO</div></td>
                </tr>
                <!--<tr><td align="left"><strong>CODENVIO:</strong></td>
                    <td><input id="CODENVIO" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
    
                <tr><td align="left"><strong>CODFICHEROPADRE:</strong></td>
                    <td><input id="CODFICHEROPADRE" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>-->
                <tr><td align="left"><strong>FICHERO ORIGEN:</strong></td>
                    <td><input id="NFICHERO_ORIGEN" type="text" name="NFICHERO_ORIGEN" style="width: 180px; text-align: left "/></td></tr>


                <tr><td align="left"><strong>FICHERO DESTINO:</strong></td>
                    <td><input id="NFICHERO_DESTINO" type="text" name="NFICHERO_DESTINO" style="width: 180px; text-align: left "/></td></tr>

                <tr><td align="left"><strong>RUTA ORIGEN:</strong></td>
                    <td><input id="NRUTA_ORIGEN" type="text" name="NRUTA_ORIGEN" style="width: 180px; text-align: left "/></td></tr>

                <tr><td align="left"><strong>UUAA:</strong></td>
                    <td><input id="NUUAA" type="text" name="NUUAA" style="width: 180px; text-align: left "/></td></tr>

                <tr><td align="left"><strong>CLASIFICACIÓN:</strong></td>
                    <td><select id="NCODCLASIFICACION" type="text" name="NCODCLASIFICACION" style="width: 180px; text-align: left ">
                    <option  value='00000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_clasificaciones)) {

                                echo "<option id='fclasificacion-" . $fila->CODCLASIFICACION . "' value='" . $fila->CODCLASIFICACION . "'>" . utf8_decode($fila->DESCLASIFICACION) . "</option>";
                            }
                            ?>
                        </select>
                    </td></tr>
                <!--
                            <tr><td align="left"><strong>CLASIFICACIÓN:</strong></td>
                                <td><input id="NDESCLASIFICACION" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->
                <tr><td align="left"><strong>NIVEL LOPD:</strong></td>
                    <td><select id="NCODNIVEL_LOPD" type="text" name="NCODNIVEL_LOPD" style="width: 180px; text-align: left ">
                    <option  value='00000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_niveleslopd)) {

                                echo "<option id='fnivellopd-" . $fila->CODNIVEL_LOPD . "' value='" . $fila->CODNIVEL_LOPD . "'>" . utf8_decode($fila->DESNIVEL_LOPD) . "</option>";
                            }
                            ?>
                        </select>
                    </td></tr>
                <!--          
                           <tr><td align="left"><strong>NIVEL LOPD:</strong></td>
                               <td><input id="NDESNIVEL_LOPD" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->            
                <!--
                <tr><td align="left"><strong>CANAL:</strong></td>
                    <td><select id="NFILE_CODCANAL" type="text" name="NFILE_CODCANAL" style="width: 180px; text-align: left ">
                    <option  value='00000' selected="selected" >Seleccionar...</option>   
                            <?
                            /*
                            while ($fila = mysql_fetch_object($cons_canales)) {

                                echo "<option id='fcanal-" . $fila->CODCANAL . "' value='" . $fila->CODCANAL . "'>" . utf8_decode($fila->DESCANAL) . "</option>";
                            }
                            
                             */
                            ?>
                        </select>
                    </td></tr>
                 --> 
                <!--
                <tr><td align="left"><strong>CANAL:</strong></td>
                                <td><input id="NFILE_DESCANAL" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->        
                        <!--<tr><td align="left"><strong>FILE_CODMOTIVOBAJA:</strong></td>
                            <td><input id="FILE_CODMOTIVOBAJA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>-->

<!--
                <tr><td align="left"><strong>FECHA ALTA:</strong></td>
                    <td><input id="FNFILE_FECHA_ALTA" type="text" name="NFILE_FECHA_ALTA" style="width: 180px; text-align: left " value="<?= date('d-m-Y') ?>" /></td></tr>
-->
                <tr><td align="left"valign='top'><strong>OBSERVACIONES:</strong></td>
                    <td><textarea id="OBSERVACIONES" type="textarea" name="OBSERVACIONES" style="width: 180px; text-align: left; height:110px ">INSERCIÓN MANUAL</textarea></td></tr>


                <!--         
               <tr><td align="left"><strong>FECHA BAJA:</strong></td>
                    <td><input id="FILE_FECHA_BAJA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
    
                <tr><td align="left"><strong>COD USR. ALTA:</strong></td>
                    <td><input id="NFILE_CODUSUARIO_ALTA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
    
                 <tr><td align="left"><strong>USUARIO ALTA:</strong></td>
                    <td><input id="NFILE_DESUSUARIO_ALTA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                 
                 <tr><td align="left"><strong>FECHA MODIFICACIÓN:</strong></td>
                    <td><input id="NFILE_FECHA_MODIFICACION" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                 
                <tr><td align="left"><strong>COD USR. MODIFICACIÓN:</strong></td>
                    <td><input id="NFILE_CODUSUARIO_MODIFICACION" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
    
                <tr><td align="left"><strong>USUARIO MODIFICACIÓN:</strong></td>
                    <td><input id="NFILE_DESUSUARIO_MODIFICACION" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                
                 <tr><td align="left"><strong>MOTIVO BAJA:</strong></td>
                    <td><input id="NFILE_DESMOTIVOBAJA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                
                <tr><td align="left"><strong>COD USR. BAJA:</strong></td>
                    <td><input id="NFILE_CODUSUARIO_BAJA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
    
                <tr><td align="left"><strong>USUARIO BAJA:</strong></td>
                    <td><input id="NFILE_DESUSUARIO_BAJA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
    
                <tr><td align="left"><strong>ESTADO:</strong></td>
                    <td><input id="NFILE_ESTADO" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>       
    
                -->
            </table>



            <div id="mensajeRemedyFich" class="mensaje"></div> 

        </div>
    </form>
    <!--- Fin Formulario de Nuevo Remedy -->
</div> <!--- Fin modalremedy  -->
