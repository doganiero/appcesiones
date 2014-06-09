<script>
    $("#modalEditarFicheros").dialog({
        autoOpen: false,
        height: 460,
        width: 370,
        bgiframe: true,
        resizable: true,
        modal: true,
        overlay: {
            backgroundColor: '#000',
            opacity: 0.5
        },
        buttons: {
            'Actualizar': function() {

                $('#editFormfich').submit();

            },
            'Cancelar': function() {
                $(this).dialog('close');
            }
        },
        open: function()
        {
              
           //-------------------------------------------------------------------------------------

            // AJAX CONSULTA VENTANA MODAL DETALLE FICHEROS

            var idFich=$('#idFich').val();
            var codenv=$('#idEnv').val();
            
            if(!$('#edfichSel').val())$('#edfichSel').val(idFich);
            else idFich=$('#edfichSel').val();
            if(!$('#edenvSel').val())$('#edenvSel').val(codenv);
            else codenv=$('#edenvSel').val();
            if(!codenv) codenv=0;
                if(!idFich) idFich=0;
            
              $('#modalEditarFicheros').dialog('option', 'title', 'Editar Fichero - Envío: ' + codenv + '');
            $.ajax({
                type: "POST",
                url: 'json/detalleficheros_json.php?codfich=' + idFich + '&codenv=' + codenv,
                dataType: 'json',
                success: function(data) {

                    $('#ECODFICHERO').text("FICHERO: " + idFich + "");
                    $('#modalEditarFicheros').attr({title:"Editar Fichero. ENVÍO: " +codenv+ ""});
                    $('#EDCODENVIO').val(data.CODENVIO);
                    //$('#CODFICHEROPADRE').val(data.CODFICHEROPADRE);
                    $('#EFICHERO_ORIGEN').val(data.FICHERO_ORIGEN);
                    $('#EFICHERO_DESTINO').val(data.FICHERO_DESTINO);
                    $('#ERUTA_ORIGEN').val(data.RUTA_ORIGEN);
                    $('#EUUAA').val(data.UUAA);
                    $('#ECODCLASIFICACION').val(data.CODCLASIFICACION);
                    //$('#EDESCLASIFICACION').val(data.DESCLASIFICACION);//opciones select
                    $('#ECODNIVEL_LOPD').val(data.CODNIVEL_LOPD);
                    $('#EDOBSERVACIONES').val(data.OBSERVACIONES);
                    //$('#DESNIVEL_LOPD').val(data.DESNIVEL_LOPD);
                    //$('#FILE_CODCANAL').val(data.FILE_CODCANAL);
                    //$('#FILE_DESCANAL').val(data.FILE_DESCANAL);
                    //$('#FILE_CODMOTIVOBAJA').val(data.FILE_CODMOTIVOBAJA);
                    //$('#FILE_DESMOTIVOBAJA').val(data.FILE_DESMOTIVOBAJA);
                    //var fecha=data.FILE_FECHA_ALTA;
                    //var fecha_sub=fecha.substring(0,10);
                    //var parsedDate = $.datepicker.parseDate('yy-mm-dd', fecha_sub);
                   //$('#FILE_FECHA_ALTA').val($.datepicker.formatDate('dd-mm-yy', parsedDate));
                    //$('#FILE_FECHA_ALTA').val(data.FILE_FECHA_ALTA);
                    //fecha=data.FILE_FECHA_MODIFICACION;
                    //fecha_sub=fecha.substring(0,10);
                    //parsedDate = $.datepicker.parseDate('yy-mm-dd', fecha_sub);
                   //$('#FILE_FECHA_MODIFICACION').val($.datepicker.formatDate('dd-mm-yy', parsedDate));
                    //$('#FILE_FECHA_MODIFICACION').val(data.FILE_FECHA_MODIFICACION);
                    //fecha=data.FILE_FECHA_BAJA;
                    //fecha_sub=fecha.substring(0,10);
                    //parsedDate = $.datepicker.parseDate('yy-mm-dd', fecha_sub);
                   //$('#FILE_FECHA_BAJA').val($.datepicker.formatDate('dd-mm-yy', parsedDate));
                    //$('#FILE_FECHA_BAJA').val(data.FILE_FECHA_BAJA);
                    //$('#FILE_CODUSUARIO_ALTA').val(data.FILE_CODUSUARIO_ALTA);
                    //$('#FILE_DESUSUARIO_ALTA').val(data.FILE_DESUSUARIO_ALTA);
                    //$('#FILE_CODUSUARIO_MODIFICACION').val(data.FILE_CODUSUARIO_MODIFICACION);
                    //$('#FILE_DESUSUARIO_MODIFICACION').val(data.FILE_DESUSUARIO_MODIFICACION);
                    //$('#FILE_CODUSUARIO_BAJA').val(data.FILE_CODUSUARIO_BAJA);
                    //$('#FILE_DESUSUARIO_BAJA').val(data.FILE_DESUSUARIO_BAJA);
                    //$('#FILE_ESTADO').val(data.FILE_ESTADO);
                    
                  
                  

                },
                error: function(e) {
                    alert("Error en el servidor, por favor, intentalo de nuevo más tarde");
                }
                
            });

            //--------------------------------------------------------------------------------------



        },
        close: function() {
            if($('#edfichSel').val())$('#edfichSel').val('');
                if($('#edenvSel').val()) $('#edenvSel').val('');
        }

    });





</script>
<?
$cons_clasificaciones = mysql_query("SELECT CODCLASIFICACION, DESCLASIFICACION FROM tclasificaciones WHERE CHKACTIVO='S' AND CODCLASIFICACION<>'00000' ");
$cons_niveleslopd = mysql_query("SELECT CODNIVEL_LOPD, DESNIVEL_LOPD FROM tniveles_lopd WHERE CHKACTIVO='S' AND CODNIVEL_LOPD<>'00000' ");
//$cons_canales= mysql_query("SELECT CODCANAL, DESCANAL FROM tcanales WHERE CHKACTIVO='S' AND CODCANAL<>'00000' ");
?>
<div id="modalEditarFicheros" title="Editar Fichero">  
    <!--- Formulario de Nuevo Remedy -->
    <form name="editFormfich" id="editFormfich" action="edit/remedy_ed.php" method="post"  > 
        <!-- Para el control de pulsación de los botones usaremos otro input oculto -->    
        <input id="opcion3" type="hidden" name="opcion" value="editfich" /> 
        <input id="edfichSel" type="hidden" name="edfichSel" value="" />
        <input id="edenvSel" type="hidden" name="edenvSel" value="" />

        <div class="colIzquierda" style='width: 190px; margin-left: 10px'>



            <table style="font-size: 9pt; width: 330px; margin-top: 10px">

                <tr class="fila">
                    <td class="celda" colspan='2'><div id="ECODFICHERO" class="subtitulo"></div></td>
                </tr>
               <tr><td align="left"><strong>CODENVIO:</strong></td>
                    <td><input id="EDCODENVIO" type="text" name="EDCODENVIO" style="width: 180px; text-align: left "/></td></tr>
    
                 <!--<tr><td align="left"><strong>CODFICHEROPADRE:</strong></td>
                    <td><input id="CODFICHEROPADRE" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>-->
                <tr><td align="left"><strong>FICHERO ORIGEN:</strong></td>
                    <td><input id="EFICHERO_ORIGEN" type="text" name="EFICHERO_ORIGEN" style="width: 180px; text-align: left "/></td></tr>


                <tr><td align="left"><strong>FICHERO DESTINO:</strong></td>
                    <td><input id="EFICHERO_DESTINO" type="text" name="EFICHERO_DESTINO" style="width: 180px; text-align: left "/></td></tr>

                <tr><td align="left"><strong>RUTA ORIGEN:</strong></td>
                    <td><input id="ERUTA_ORIGEN" type="text" name="ERUTA_ORIGEN" style="width: 180px; text-align: left "/></td></tr>

                <tr><td align="left"><strong>UUAA:</strong></td>
                    <td><input id="EUUAA" type="text" name="EUUAA" style="width: 180px; text-align: left "/></td></tr>

                <tr><td align="left"><strong>CLASIFICACIÓN:</strong></td>
                    <td><select id="ECODCLASIFICACION" type="text" name="ECODCLASIFICACION" style="width: 180px; text-align: left ">
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
                    <td><select id="ECODNIVEL_LOPD" type="text" name="ECODNIVEL_LOPD" style="width: 180px; text-align: left ">
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
                    <td><textarea id="EDOBSERVACIONES" type="textarea" name="EDOBSERVACIONES" style="width: 180px; text-align: left; height:110px "></textarea></td></tr>


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



         

        </div>
    </form>
    <!--- Fin Formulario de Nuevo Remedy -->
</div> <!--- Fin modalremedy  -->
