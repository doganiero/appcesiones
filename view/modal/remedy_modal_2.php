<script>
    $("#modalremedy2").dialog({
        autoOpen: false,
        height: 510,
        width: 405,
        bgiframe: true,
        resizable: false,
        modal: true,
        overlay: {
            backgroundColor: '#000',
            opacity: 0.5
        },
        buttons: {
            'Agregar': function() {

                $('#remedyForm2').submit();
                
            },
            'Cancelar': function() {
                $(this).dialog('close');
            }
        },
        open: function()
        {
            
            //$("#ftipoenvio-00002").attr({selected: 'selected'}); preselección de tipo de envío (origen de registro)
             
            $("#NENVIO_FECHA_ALTA").datepicker({dateFormat: "dd-mm-yy"});
            $("#NENVIO_FECHA_ALTA").attr("tabindex", "1");
            $("#NCODENVIO_REMEDY").focus();
           $("#NFILE_FECHA_ALTA").datepicker({dateFormat: "dd-mm-yy"});
          
           
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
            
        },
            close: function() {
                 $('#remedyForm2')[0].reset();
                 //$('#remedyForm')[0].reset();
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
$cons_clasificaciones = mysql_query("SELECT CODCLASIFICACION, DESCLASIFICACION FROM tclasificaciones WHERE CHKACTIVO='S' AND CODCLASIFICACION<>'00000' ");
$cons_niveleslopd= mysql_query("SELECT CODNIVEL_LOPD, DESNIVEL_LOPD FROM tniveles_lopd WHERE CHKACTIVO='S' AND CODNIVEL_LOPD<>'00000' ");
$cons_canales= mysql_query("SELECT CODCANAL, DESCANAL FROM tcanales WHERE CHKACTIVO='S' AND CODCANAL<>'00000' AND DESCANAL NOT LIKE '%GEPP%' ");
?>
<div id="modalremedy2" title="Nuevo Envío">  
    <!--- Formulario de Nuevo Remedy -->
    <form name="remedyForm2" id="remedyForm2" action="edit/remedy_ed_2.php" method="post"  > 
        <!-- Para el control de pulsación de los botones usaremos otro input oculto -->    
        <input id="opcion83" type="hidden" name="opcion" value="nuevo" /> <!-- Para nuevo remedy y fichero -->
        
                   
                   
                   
        <div class="colIzquierda" style='width: 190px; margin-left: 10px'>


            <table style="font-size: 9pt; width: 360px; margin-top: 10px">
                <tr class="fila">
                    <td class="celda" colspan='2'><div id="NCODENVIO" class="subtitulo">DATOS DE ENVÍO</div></td>
                </tr>

               
                <!--
                <tr><td align="left"><strong>COD ENVÍO REMEDY:</strong></td>
                    <td><input id="NCODENVIO_REMEDY" type="text" name="NCODENVIO_REMEDY" style="width: 180px; text-align: left "/></td></tr>
                -->
                <tr><td align="left"><strong>SOLICITANTE BBVA:</strong></td>
                    <td align="right" ><input id="NCODAUTORIZA" type="text" name="NCODAUTORIZA" style="width: 180px; text-align: left " value="" />
                        <!--
                        <select id="NCODAUTORIZA" type="text" name="NCODAUTORIZA" style="width: 180px; text-align: left ">
                    <option  value='0000000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_autoriza)) {

                                echo "<option id='fautoriza-" . $fila->CODUSUARIO . "' value='" . $fila->CODUSUARIO . "'> (".$fila->CODUSUARIO.") - " . utf8_decode($fila->DESUSUARIO) . "</option>";
                            }
                            ?>
                        </select>
                        -->
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
                    <td align="right"><select id="NCODEMPRESA" type="text" name="NCODEMPRESA" style="width: 184px; text-align: left ">
                            <option  value='00000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_empresas)) {

                                echo "<option id='fempresa-" . $fila->CODEMPRESA . "' value='" . $fila->CODEMPRESA . "'>" . utf8_encode($fila->DESEMPRESA) . "</option>";
                            }
                            ?>
                        </select>
                    </td></tr>
                
                  <tr><td align="left"><strong>CONTACTO:</strong></td>
                    <td align="right" ><input id="NCONTACTO" type="text" name="NCONTACTO" style="width: 180px; text-align: left " value="" />

<!--<tr><td align="left"><strong>EMPRESA:</strong></td>
<td><input id="NDESEMPRESA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>-->


                <tr><td align="left"><strong>INTERVINIENTE:</strong></td>
                    <td align="right"><select id="NCODINTERVINIENTE" type="text" name="NCODINTERVINIENTE" style="width: 184px; text-align: left " >
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
                    <td align="right"><select id="NCODDESTINATARIO" type="text" name="NCODDESTINATARIO" style="width: 184px; text-align: left " >
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
                    <td align="right"><select id="NCODFRECUENCIA" type="text" name="NCODFRECUENCIA" style="width: 184px; text-align: left ">
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
               
                  <input id="NCODCANAL" type="hidden" name="NCODCANAL" style="width: 180px; text-align: left;  "/>
                   <input id="NCODCARGA" type="hidden" name="NCODCARGA" style="width: 180px; text-align: left;  "/>
                <!--
               <tr><td align="left"><strong>CANAL:</strong></td>
                   <td><input id="NDESCANAL" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
   
                -->

                <tr><td align="left"><strong>MÁQUINA ORIGEN:</strong></td>
                    <td align="right"><input id="NNOMBREMAQUINA_ORIGEN" type="text" name="NNOMBREMAQUINA_ORIGEN" style="width: 180px; text-align: left "/></td></tr>
                <tr><td align="left"><strong>IP ORIGEN:</strong></td>
                    <td align="right"><input id="NIP_ORIGEN" type="text" name="NIP_ORIGEN" style="width: 180px; text-align: left "/></td></tr>
                <tr><td align="left"><strong>IP DESTINO:</strong></td>
                    <td align="right"><input id="NIP_DESTINO" type="text" name="NIP_DESTINO" style="width: 180px; text-align: left "/></td></tr>

                <tr><td align="left"><strong>CIFRADO:</strong></td>
                    <td align='left' ><input id="NCHKCIFRADO_S" type="radio" name="NCHKCIFRADO" value='S' style="width: 15px; text-align: left; margin-left: 15px  "/>SÍ
                        <input id="NCHKCIFRADO_N" type="radio" name="NCHKCIFRADO" value='N' style="width: 15px; text-align: left; margin-left: 15px  "/>NO</td>

                </tr>
                 <input id="NCODTIPOENVIO" type="hidden" name="NCODTIPOENVIO" value='00002' />
                <!--          
              <tr><td align="left"><strong>CIFRADO:</strong></td>
                              <td><input id="NCIFRADO" type="text" name="CIFRADO" style="width: 180px; text-align: left "/></td></tr>               
               
                -->
                
                   <!--
                <tr><td align="left"><strong>ORIGEN REGISTRO:</strong></td>
                 
                    <td>
                         
                        
                        <select id="NCODTIPOENVIO" type="text" name="NCODTIPOENVIO" style="width: 180px; text-align: left ">
                            <option  value='00000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_tiposenvios)) {

                                echo "<option id='ftipoenvio-" . $fila->CODTIPOENVIO . "' value='" . $fila->CODTIPOENVIO . "'>" . utf8_decode($fila->DESTIPOENVIO) . "</option>";
                            }
                            ?>
                        </select>
                           
                    </td></tr>
                     -->
                <!--
                 <tr><td align="left"><strong>TIPO ENVÍO:</strong></td>
                               <td><input id="NDESTIPOENVIO" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
                -->

                 <tr><td align="left"><strong>FECHA ALTA:</strong></td>
                    <td align="right"><input id="NENVIO_FECHA_ALTA" type="text" name="NENVIO_FECHA_ALTA" style="width: 180px; text-align: left " value="<?= date('d-m-Y') ?>" /></td></tr>

                
                <tr><td align="left" valign='top'><strong>MOTIVO ENVÍO:</strong></td>
                    <td align="right"><textarea id="NMOTIVOENVIO" type="text" name="NMOTIVOENVIO" style="width: 184px; text-align: left; height: 60px "></textarea></td></tr>
                
                
                
                <!--
                
                 <tr><td align="left"valign='top'><strong>OBSERVACIONES:</strong></td>
                    <td><textarea id="EOBSERVACIONES" type="textarea" name="EOBSERVACIONES" style="width: 180px; text-align: left; height:110px ">INSERCIÓN MANUAL</textarea></td></tr>

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
               
                       <tr style="display: none"><td align="left"><strong>MOTIVO BAJA:</strong></td>
                           <td><input id="NCODMOTIVOBAJA" type="text" name="NCODMOTIVOBAJA" style="width: 180px; text-align: left "/></td></tr>
                
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
