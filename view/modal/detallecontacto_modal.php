<script>
 $("#modalDetalleContacto").dialog({
            autoOpen: false,
            height: 520,
            width: 330,
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
                
                //$("#modalDetalleRemedy tr:odd").css("background-color", "#ddd"); // filas impares
                // $("#modalDetalleRemedy tr:even").css("background-color", "#ccc"); // filas pares
            }
        });

</script>
<div id="modalDetalleContacto" title="Detalles Contacto"  > 
    <div class="colIzquierda"  style='width: 300px; margin-left: 0px'>


        <table style="font-size: 9px; width: 300px; margin-top: 5px">
            <tr class="fila">
                <td class="celda" colspan='2'><div id="TDCODCONTACTO" class="subtitulo"></div></td>
            </tr>

            <tr  ><td align="left" ><strong>NOMBRE:</strong></td>
                <td align="right"><input id="DDESCONTACTO" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>


            <tr><td align="left"><strong>EMPRESA:</strong></td>
                <td align="right"><input id="DDESEMPRESA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>

            <tr><td align="left"><strong>CARGO:</strong></td>
                <td align="right"><input id="DCARGO" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>

            <tr><td align="left"><strong>TLF. FIJO:</strong></td>
                <td align="right"><input id="DTELF_FIJO" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>

            <tr><td align="left"><strong>TLF. MÓVIL:</strong></td>
                <td align="right"><input id="DTELF_MOVIL" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>

            <tr><td align="left"><strong>FAX:</strong></td>
                <td align="right"><input id="DFAX" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>


            <tr><td align="left"><strong>EMAIL:</strong></td>
                <td align="right"><input id="DEMAIL" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
            <tr><td align="left"><strong>DIRECCIÓN:</strong></td>
                <td align="right"><input id="DDIRECCION" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>
            <tr><td align="left"><strong>PAÍS:</strong></td>
                <td align="right"><input id="DPAIS" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>

            <tr><td align="left"><strong>FECHA ALTA:</strong></td>
                <td align="right"><input id="DFECHA_ALTA" type="text" name="CIFRADO" style="width: 180px; text-align: left "/></td></tr>               


            <tr><td align="left"><strong>COD. USR. ALTA:</strong></td>
                <td align="right"><input id="DCODUSUARIO_ALTA" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>

            <tr><td align="left" ><strong>FECHA MODIF:</strong></td>
                <td align="right"><input id="DFECHA_MODIFICACION" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>

            <tr><td align="left"><strong>COD. USR. MODIF:</strong></td>
                <td align="right"><input id="DCODUSUARIO_MODIFICACION" type="text" name="prov_tlf2" style="width: 180px; text-align: left "/></td></tr>



            <tr><td align="left"valign='top'><strong>OBSERVACIONES:</strong></td>
                <td align="right"><textarea id="DOBSERVACIONES" type="textarea" name="prov_tlf2" style="width: 180px; text-align: left; height:70px "></textarea></td></tr>


        </table>




    </div>
</div>

