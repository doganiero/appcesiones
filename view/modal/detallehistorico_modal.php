<script>
 $("#modalDetalleHistorico").dialog({
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
<div id="modalDetalleHistorico" title="Histórico de Cargas de Fichero"  > 
    <div class="colIzquierda"  style='width: 630px; margin-left: 0px'>


        <table id="hist" style="font-size: 9px; width: 630px; margin-top: 5px">
           
            <tr class="fila" >
                <td class="celda" colspan='5'  ><div id="TDFICHERO" class="subtitulo"></div></td>
            </tr>
            <tr >
                <td></td>
            </tr>
             <tr><td align='center' ><strong>FICHERO LOG</strong></td><td align='center' ><strong>CANAL</strong></td><td align='center' ><strong>EMP.DESTINO</strong></td><td align='center' ><strong>MAQ. DESTINO</strong></td><td align='center' ><strong>IP DESTINO</strong></td></tr>

            


            


        </table>




    </div>
</div>

