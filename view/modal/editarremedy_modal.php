<script>
    $("#modalEditarRemedy").dialog({
        autoOpen: false,
        height: 510,
        width: 380,
        bgiframe: true,
        resizable: true,
        modal: true,
        overlay: {
            backgroundColor: '#000',
            opacity: 0.5
        },
        buttons: {
            'Actualizar': function() {

                $('#editFormremedy').submit();
                
            },
            'Cancelar': function() {
                $(this).dialog('close');
            }
        },
        open: function()
        {
             
             var codenv=$('#idEnv').val();
             if(!$('#edremEnv').val()){
                 $('#edremEnv').val(codenv);
             }else codenv= $('#edremEnv').val();
         
             
         
        $('#modalEditarRemedy').dialog('option', 'title', 'Editar Envío');
            // AJAX CONSULTA VENTANA MODAL DETALLE REMEDY
            
            /* $("input[type='radio']").click(function()
            {
                var previousValue = $(this).attr('previousValue');
                var name = $(this).attr('name');

                if (previousValue == 'checked')
                {
                    $(this).removeAttr('checked');
                    $(this).attr('previousValue', false);
                }
                else
                {
                    $("input[name=" + name + "]:radio").attr('previousValue', false);
                    $(this).attr('previousValue', 'checked');
                }
                
            });*/
            
            if(!codenv) codenv=0;
            
            $.ajax({
                type: "POST",
                url: 'json/detalleremedy_json.php?codenvio=' + codenv + '',
                dataType: 'json',
                success: function(data) {

                    $('#ERCODENVIO').text('ENVÍO: ' + data.CODENVIO + '');
                    $('#ERCODTIPOENVIO').val(data.CODTIPOENVIO);
                    $('#ERCODENVIO_REMEDY').val(data.CODENVIO_REMEDY);
                    $('#ERCODAUTORIZA').val(data.CODAUTORIZA);
                    $('#ERFILE_CODCANAL').val(data.CODCANAL);
        
                    $('#ERCODEMPRESA').val(data.CODEMPRESA);
                  
                    $('#ERCODINTERVINIENTE').val(data.CODINTERVINIENTE);
                    $('#ERCODDESTINATARIO').val(data.CODDESTINATARIO);
                    $('#ERCODFRECUENCIA').val(data.CODFRECUENCIA);
                    $('#ERCODCANAL').val(data.CODCANAL);
                    $('#ERNOMBREMAQUINA_ORIGEN').val(data.NOMBREMAQUINA_ORIGEN);
                    $('#ERIP_ORIGEN').val(data.IP_ORIGEN);
                    $('#ERIP_DESTINO').val(data.IP_DESTINO);
                    
                      $('#ERCONTACTO').val(data.DESCONTACTO);
                    
                     if ($('#ERCONTACTO').val() == 'false')
                        $('#ERCONTACTO').val('');
                    
                    if(data.CHKCIFRADO==='S'){ 
                       
                        $('#ERCHKCIFRADO_S').attr({checked: true})
                        
                  
                    };
                    if(data.CHKCIFRADO==='N') {
                       
                        $('#ERCHKCIFRADO_N').attr({checked: true })
                        
              
                        
                    };
                    $('#ERMOTIVOENVIO').val(data.MOTIVOENVIO);
                    $('#ERCODMOTIVOBAJA').val(data.CODMOTIVOBAJA);
                    $('#EROBSERVACIONES').val(data.OBSERVACIONES);
                    
                    if($('#ERCODAUTORIZA').val()=='0000000') $('#ERCODAUTORIZA').val(''); 
                    
                },
                error: function(e) {
                    alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                }
            });
             
                     
           
            
        },
            close: function() {
                if($('#edremEnv').val())  $('#edremEnv').val('')            
            }
    });




                     

</script>
<?
$cons_empresas = mysql_query("SELECT CODEMPRESA, DESEMPRESA FROM tempresas WHERE  CODEMPRESA<>'00000' AND NOT (CODEMPRESAPADRE IS NULL AND CHKACTIVO='N') ORDER BY DESEMPRESA ");
$cons_autoriza = mysql_query("SELECT CODUSUARIO, DESUSUARIO FROM tusuarios_bbva WHERE CHKACTIVO='S' AND CODUSUARIO<>'00000' ORDER BY DESUSUARIO asc ");
$cons_intervinientes = mysql_query("SELECT CODINTERVINIENTE, DESINTERVINIENTE FROM tintervinientes WHERE CHKACTIVO='S' AND CODINTERVINIENTE<>'00000' ");
$cons_destinatarios = mysql_query("SELECT CODDESTINATARIO, DESDESTINATARIO FROM tdestinatarios WHERE CHKACTIVO='S' AND CODDESTINATARIO<>'00000' ");
$cons_frecuencias = mysql_query("SELECT CODFRECUENCIA, DESFRECUENCIA FROM tfrecuencias WHERE CHKACTIVO='S' AND CODFRECUENCIA<>'00000' ");
$cons_tiposenvios = mysql_query("SELECT CODTIPOENVIO, DESTIPOENVIO FROM ttiposenvios WHERE CHKACTIVO='S' AND CODTIPOENVIO<>'00000' ");
$cons_canales= mysql_query("SELECT CODCANAL, DESCANAL FROM tcanales WHERE CHKACTIVO='S' AND CODCANAL<>'00000' AND DESCANAL NOT LIKE '%GEPP%' ");
?>
<div id="modalEditarRemedy" title="">  
    <!--- Formulario de Nuevo Remedy -->
    <form name="editFormremedy" id="editFormremedy" action="edit/remedy_ed.php" method="post"  > 
        <!-- Para el control de pulsación de los botones usaremos otro input oculto -->    
        <input id="opcion" type="hidden" name="opcion" value="edremfich" /> <!-- Para nuevo remedy y fichero -->
                          
                    <input type="hidden" id="edremEnv" name="edremEnv" />
        <div class="colIzquierda" style='width: 190px; margin-left: 10px'>


            <table style="font-size: 9pt; width: 340px; margin-top: 10px">
                <tr class="fila">
                    <td class="celda" colspan='2'><div id="ERCODENVIO" class="subtitulo"></div></td>
                </tr>
<!--
                <tr><td align="left"><strong>FECHA ALTA:</strong></td>
                    <td><input id="NENVIO_FECHA_ALTA" type="text" name="NENVIO_FECHA_ALTA" style="width: 180px; text-align: left " value="<?= date('d-m-Y') ?>" /></td></tr>
-->

                <tr><td align="left"><strong>COD. REMEDY:</strong></td>
                    <td><input id="ERCODENVIO_REMEDY" type="text" name="ERCODENVIO_REMEDY" style="width: 180px; text-align: left "/></td></tr>
                
                <tr><td align="left"><strong>SOLICITANTE BBVA:</strong></td>
                    <td><input id="ERCODAUTORIZA" type="text" name="ERCODAUTORIZA" style="width: 180px; text-align: left "/></td></tr>
                        <!--
                        <select id="ERCODAUTORIZA" type="text" name="ERCODAUTORIZA" style="width: 180px; text-align: left ">
                    <option  value='0000000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_autoriza)) {

                                echo "<option id='fautoriza-" . $fila->CODUSUARIO . "' value='" . $fila->CODUSUARIO . "'> (".$fila->CODUSUARIO.") - " . utf8_decode($fila->DESUSUARIO) . "</option>";
                            }
                            ?>
                        </select>
                        -->
                    </td></tr>
                <tr  ><td align="left"><strong>CANAL:</strong></td>
                    <td><select  id="ERFILE_CODCANAL" type="text" name="ERFILE_CODCANAL" style="width: 180px; text-align: left ">
                    <option  value='00000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_canales)) {

                                echo "<option id='fcanal-" . $fila->CODCANAL . "' value='" . $fila->CODCANAL . "'>" . utf8_decode($fila->DESCANAL) . "</option>";
                            }
                            ?>
                        </select>
                    </td></tr>
                
                <!--
   <tr><td align="left"><strong>AUTORIZA:</strong></td>
       <td><input id="NDESAUTORIZA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->
                <!--
            <tr><td align="left"><strong>EMAIL AUTORIZA:</strong></td>
                    <td><input id="NEMAILAUTORIZA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
               
                -->

                <tr><td align="left"><strong>EMPRESA:</strong></td>
                    <td><select id="ERCODEMPRESA" type="text" name="ERCODEMPRESA" style="width: 180px; text-align: left ">
                            <option  value='00000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_empresas)) {

                                echo "<option id='fempresa-" . $fila->CODEMPRESA . "' value='" . $fila->CODEMPRESA . "'>" . utf8_encode($fila->DESEMPRESA) . "</option>";
                            }
                            ?>
                        </select>
                    </td></tr>
                
                    <tr><td align="left"><strong>CONTACTO:</strong></td>
                    <td align="right" ><input id="ERCONTACTO" type="text" name="ERCONTACTO" style="width: 180px; text-align: left " value="" />


<!--<tr><td align="left"><strong>EMPRESA:</strong></td>
<td><input id="NDESEMPRESA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>-->


                <tr><td align="left"><strong>INTERVINIENTE:</strong></td>
                    <td><select id="ERCODINTERVINIENTE" type="text" name="ERCODINTERVINIENTE" style="width: 180px; text-align: left " >
                            <option  value='00000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_intervinientes)) {

                                echo "<option id='finterviniente-" . $fila->CODINTERVINIENTE . "' value='" . $fila->CODINTERVINIENTE . "'>" . utf8_decode($fila->DESINTERVINIENTE) . "</option>";
                            }
                            ?>
                        </select>
                    </td></tr>
                <!--<tr><td align="left"><strong>INTERVINIENTE:</strong></td>
                    <td><input id="NDESINTERVINIENTE" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->
                <tr><td align="left"><strong>DESTINATARIO:</strong></td>
                    <td><select id="ERCODDESTINATARIO" type="text" name="ERCODDESTINATARIO" style="width: 180px; text-align: left " >
                            <option  value='00000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_destinatarios)) {

                                echo "<option id='fdestinatario-" . $fila->CODDESTINATARIO . "' value='" . $fila->CODDESTINATARIO . "'>" . utf8_decode($fila->DESDESTINATARIO) . "</option>";
                            }
                            ?>
                        </select>
                    </td></tr>


            <!--<tr><td align="left"><strong>DESTINATARIO:</strong></td>
                <td><input id="NDESDESTINATARIO" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>-->

                <tr><td align="left"><strong>FRECUENCIA:</strong></td>
                    <td><select id="ERCODFRECUENCIA" type="text" name="ERCODFRECUENCIA" style="width: 180px; text-align: left ">
                            <option  value='00000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_frecuencias)) {

                                echo "<option id='ffrecuencia-" . $fila->CODFRECUENCIA . "' value='" . $fila->CODFRECUENCIA . "'>" . utf8_decode($fila->DESFRECUENCIA) . "</option>";
                            }
                            ?>
                        </select>
                    </td></tr>

                <!--
                <tr><td align="left"><strong>FRECUENCIA:</strong></td>
                    <td><input id="NDESFRECUENCIA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->
                <!--
                  <tr><td align="left"><strong>CANAL:</strong></td>
                    <td><input id="NCODCANAL" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->
                <!--
               <tr><td align="left"><strong>CANAL:</strong></td>
                   <td><input id="NDESCANAL" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
   
                -->

                <tr><td align="left"><strong>MÁQUINA ORIGEN:</strong></td>
                    <td><input id="ERNOMBREMAQUINA_ORIGEN" type="text" name="ERNOMBREMAQUINA_ORIGEN" style="width: 180px; text-align: left "/></td></tr>
                <tr><td align="left"><strong>IP ORIGEN:</strong></td>
                    <td><input id="ERIP_ORIGEN" type="text" name="ERIP_ORIGEN" style="width: 180px; text-align: left "/></td></tr>
                <tr><td align="left"><strong>IP DESTINO:</strong></td>
                    <td><input id="ERIP_DESTINO" type="text" name="ERIP_DESTINO" style="width: 180px; text-align: left "/></td></tr>

                <tr><td align="left"><strong>CIFRADO:</strong></td>
                    <td align='left' ><input id="ERCHKCIFRADO_S" type="radio" name="ERCHKCIFRADO" value='S' style="width: 15px; text-align: left; margin-left: 15px  "/>SÍ
                        <input id="ERCHKCIFRADO_N" type="radio" name="ERCHKCIFRADO" value='N' style="width: 15px; text-align: left; margin-left: 15px  "/>NO</td>

                </tr>

                <!--          
              <tr><td align="left"><strong>CIFRADO:</strong></td>
                              <td><input id="NCIFRADO" type="text" name="CIFRADO" style="width: 180px; text-align: left "/></td></tr>               
               
                -->
                <tr class="noAnalizar" ><td align="left" id="octit" ><strong>ORIGEN REGISTRO:</strong></td>
                    <td><select id="ERCODTIPOENVIO" type="text" name="ERCODTIPOENVIO" style="width: 180px; text-align: left ">
                            <option  value='00000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_tiposenvios)) {

                                echo "<option id='ftipoenvio-" . $fila->CODTIPOENVIO . "' value='" . $fila->CODTIPOENVIO . "'>" . utf8_decode($fila->DESTIPOENVIO) . "</option>";
                            }
                            ?>
                        </select>

                    </td></tr>
                <!--
                 <tr><td align="left"><strong>TIPO ENVÍO:</strong></td>
                               <td><input id="NDESTIPOENVIO" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->

                <tr><td align="left" valign='top'><strong>MOTIVO ENVÍO:</strong></td>
                    <td><textarea id="ERMOTIVOENVIO" type="text" name="ERMOTIVOENVIO" style="width: 180px; text-align: left; height: 80px "></textarea></td></tr>

    <!--
                 <tr><td align="left"valign='top'><strong>OBSERVACIONES:</strong></td>
                    <td><textarea id="EROBSERVACIONES" type="textarea" name="EROBSERVACIONES" style="width: 180px; text-align: left; height:110px ">INSERCIÓN MANUAL</textarea></td></tr>


   -->
                <!--
                        <tr><td align="left"><strong>USUARIO ALTA::</strong></td>
                            <td><input id="NENVIO_CODUSUARIO_ALTA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->
                <!--
                            <tr><td align="left"><strong>USUARIO ALTA:</strong></td>
                                <td><input id="NENVIO_DESUSUARIO_ALTA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                
                -->     
                <!--
                           <tr><td align="left"><strong>FECHA MODIFICACIÓN:</strong></td>
                               <td><input id="NENVIO_FECHA_MODIFICACION" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->      
                <!-- 
                  <tr><td align="left"><strong>USUARIO MODIFICACIÓN::</strong></td>
                     <td><input id="NENVIO_CODUSUARIO_MODIFICACION" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->
                <!--
                           <tr><td align="left"><strong>USUARIO MODIFICACIÓN:</strong></td>
                               <td><input id="NENVIO_DESUSUARIO_MODIFICACION" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->
                <!--       
                       <tr><td align="left"><strong>FECHA BAJA:</strong></td>
                           <td><input id="NENVIO_FECHA_BAJA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->     
                <!--
                       <tr><td align="left"><strong>MOTIVO BAJA:</strong></td>
                           <td><input id="NCODMOTIVOBAJA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->
                <!-- 
                  <tr><td align="left"><strong>MOTIVO BAJA:</strong></td>
                     <td><input id="NDESMOTIVOBAJA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
     
                -->
                <!--
                            <tr><td align="left"><strong>USUARIO BAJA:</strong></td>
                                <td><input id="NENVIO_CODUSUARIO_BAJA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->           
                <!--
                <tr><td align="left"><strong>USUARIO BAJA:</strong></td>
                    <td><input id="NENVIO_DESUSUARIO_BAJA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                
                -->  

            </table>    





        

        </div>
    </form>
    <!--- Fin Formulario de Nuevo Remedy -->
</div> <!--- Fin modalremedy  -->
