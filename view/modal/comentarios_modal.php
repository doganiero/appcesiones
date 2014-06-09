<script>
    $("#modalComentarios").dialog({
        autoOpen: false,
        height: 300,
        width: 665,
        bgiframe: true,
        resizable: true,
        modal: false,
        overlay: {
            backgroundColor: '#000',
            opacity: 0.5
        },
        buttons: {
            'Agregar': {
                text: "Agregar",
                id: "btnAgr",
                click: function() {

                    var coment = prompt("Escriba un comentario sobre el estudio del registro:");
                    if (coment) {
                        var fila_selagr = jQuery("#bufferlist").getGridParam('selrow');
                        var camposagr = jQuery("#bufferlist").getRowData(fila_selagr);
                        $.ajax({
                            type: "POST",
                            url: 'edit/comentarios_ed.php?fichero=' + camposagr.BUFFER_FICHERO_ORIGEN + '&maquina=' + camposagr.BUFFER_NOMBREMAQUINA_DESTINO + '&ip=' + camposagr.BUFFER_IP_DESTINO + '&empresa=' + camposagr.DESEMPRESA_DESTINO,
                            async: false,
                            data: {coment: coment},
                            dataType: 'json',
                            success: function() {
                                setTimeout(function() {
                                   
                                    $("#bufferlist").trigger('reloadGrid');
                                      setTimeout(function() {
                                            jQuery('#bufferlist').jqGrid('setSelection',fila_selagr);
                                             
                                            
                                            
                                     }, 1000);
                                    
                                }, 1000);

                                 
                            },
                            error: function() {
                                alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                            }
                        });
                    }
                }},
            'Cerrar': function() {

                $(this).dialog('close');
            }
        },
        close: function() {
            $(".quitar").remove();

            //$("#modalDetalleRemedy tr:odd").css("background-color", "#ddd"); // filas impares
            // $("#modalDetalleRemedy tr:even").css("background-color", "#ccc"); // filas pares
        }
    });

</script>
<div id="modalComentarios" title="Comentarios de Fichero en Estudio"  > 
    <div class="colIzquierda"  style='width: 630px; margin-left: 0px'>


        <table id="comt" style="font-size: 9px; width: 630px; margin-top: 5px">

            <tr class="fila" >
                <td class="celda" colspan='2' ><div id="TDCOMENT" class="subtitulo"></div></td>
            </tr>
            <tr >
                <td></td>
            </tr>
            <tr  id="cabecera_coment"><td align='left' width='150' ><strong>FECHA / HORA:</strong></td><td align='left' ><strong>COMENTARIO:</strong></td></tr>







        </table>




    </div>
</div>

