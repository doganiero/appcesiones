<script>
    $("#modalSelCarga").dialog({
        autoOpen: false,
        height: 160,
        width: 400,
        bgiframe: true,
        resizable: false,
        modal: false,
        overlay: {
            backgroundColor: '#000',
            opacity: 0.5
        },
        buttons: {
            'Aceptar': {
                text: "Seleccionar",
                id: "AceptarSelCarga",
                click: function() {
                    $(this).dialog('close');
                    jQuery("#bufferlist").GridUnload();
                    jQuery("#ficheros").GridUnload();
                    var sel = $('#SELCODCARGA').val();
                    var codbuffermask = $.getUrlVar('codbuffermask');

                    GridBuffer(0, 0, 0, sel, 0, 1, codbuffermask);
                    
                    if (codbuffermask)
                        GridFicheros(0, 0, 'xxxxx');
                    else
                        GridFicheros(0, 0, sel, 0, 0);

                }
            }
        },
        open: function() {
            $('.ui-dialog-titlebar-close').remove();
            $('#refresh').load("json/loadcargas_json.php");
           
            
        },
        close: function() {

            $('#botonPruebaMascaras').toggle();
            $('#botonDefMascaras').toggle();
            $('#botonDetalleFicheros').toggle();
            $('#botonCoincidencias').toggle();
            $('#botonCompararBuffer').toggle();
            $('#botonDetalleBuffer').toggle();
            $('#botonBajaFicheros').toggle();
            $('#BFICHERO_ORIGEN').toggle();
            $('#botonBuscaFich').toggle();






        }

    });




</script>
<div id="modalSelCarga" title="Seleccionar Carga de Trabajo"> 


    <div id="refresh" class="colIzquierda" style='width: 160px; margin-left: 10px'>



        <table style="font-size: 9pt; width: 310px; margin-top: 10px">


            <tr><td align="left"><strong>CARGA:</strong></td>
                <td  ><select id="SELCODCARGA"  name="SELCODCARGA" style="width: 300px; text-align: left ">
                        <option  value='' selected="selected" >Todas las Cargas</option>   

                        <?
                        $cons_cargas = mysql_query("SELECT CODCARGA, FICHERO_LOG, DESCARGA FROM tcargas order by CODCARGA desc");
                        while ($fila = mysql_fetch_object($cons_cargas)) {

                            echo "<option id='fcarga-" . $fila->CODCARGA . "' value='" . $fila->CODCARGA . "'>" . strtoupper($fila->FICHERO_LOG) . "</option>";
                        }
                        ?>

                    </select>
                </td></tr>

        </table>





    </div>



</div>