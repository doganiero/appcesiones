<script>
    $("#modalEditarContacto").dialog({
        autoOpen: false,
        height: 525,
        width: 365,
        bgiframe: true,
        resizable: true,
        modal: true,
        overlay: {
            backgroundColor: '#000',
            opacity: 0.5
        },
        buttons: {
            'Actualizar': function() {

                $('#editContactosForm').submit();
                
            },
            'Cancelar': function() {
                $(this).dialog('close');
            }
        },
            close: function() {
                
            }
    });




                     

</script>
<div id="modalEditarContacto" title="Editar Contacto">  
    <!--- Formulario de Nuevo Remedy -->
    <form name="editContactosForm" id="editContactosForm" action="edit/contactos_ed.php" method="post"  > 
        <!-- Para el control de pulsación de los botones usaremos otro input oculto -->    
        <input id="opcion8" type="hidden" name="opcion" value="editar" /> <!-- Para nuevo remedy y fichero -->
                           <input id="ECODCONTACTO" type="hidden" name="ECODCONTACTO" value="" />
                    <input type="hidden" id="ECODEMPRESA" name="ECODEMPRESA" />
        <div class="colIzquierda" style='width: 190px; margin-left: 10px'>


            <table style="font-size: 9pt; width: 315px; margin-top: 10px">
                <tr class="fila">
                    <td class="celda" colspan='2'><div id='TECODCONTACTO' class="subtitulo">CONTACTO</div></td>
                </tr>

          
                <tr><td align="left"><strong>NOMBRE:</strong></td>
                    <td align="right" ><input id="EDESCONTACTO" type="text" name="EDESCONTACTO" style="width: 180px; text-align: left " value="" />
                        
                    </td></tr>
             

                <tr><td align="left"><strong>CARGO:</strong></td>
                    <td align="right"><input id="ECARGO" type="text" name="ECARGO" style="width: 180px; text-align: left "/></td></tr>
                <tr><td align="left"><strong>TLF. FIJO:</strong></td>
                    <td align="right"><input id="ETELF_FIJO" type="text" name="ETELF_FIJO" style="width: 180px; text-align: left "/></td></tr>
                <tr><td align="left"><strong>TLF. MÓVIL:</strong></td>
                    <td align="right"><input id="ETELF_MOVIL" type="text" name="ETELF_MOVIL" style="width: 180px; text-align: left "/></td></tr>

               
                        <tr><td align="left"><strong>FAX:</strong></td>
                            <td align="right"><input id="EFAX" type="text" name="EFAX" style="width: 180px; text-align: left "/></td></tr>
             <tr><td align="left"><strong>EMAIL:</strong></td>
                            <td align="right"><input id="EEMAIL" type="text" name="EEMAIL" style="width: 180px; text-align: left "/></td></tr>
             
  <tr><td align="left" valign='top'><strong>DIRECCIÓN:</strong></td>
                    <td align="right"><textarea id="EDIRECCION" type="text" name="EDIRECCION" style="width: 179px; text-align: left; height: 30px "></textarea></td></tr>
                
                <tr><td align="left"><strong>PAÍS:</strong></td>
                            <td align="right"><input id="EPAIS" type="text" name="EPAIS" style="width: 180px; text-align: left "/></td></tr>
 
                 <tr><td align="left"valign='top'><strong>OBSERVACIONES:</strong></td>
                    <td><textarea id="EOBSERVACIONES" type="textarea" name="EOBSERVACIONES" style="width: 179px; text-align: left; height:110px "></textarea></td></tr>



            </table>    




        </div>

        
    </form>
   
</div> 
