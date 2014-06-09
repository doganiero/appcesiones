<script>
    $("#modalNuevoContacto").dialog({
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
            'Agregar': function() {

                $('#contactosForm').submit();
                
            },
            'Cancelar': function() {
                $(this).dialog('close');
            }
        },
            close: function() {
                 //$('#remedyForm').clearForm();
            }
    });




                     

</script>
<div id="modalNuevoContacto" title="Nuevo Contacto">  
    <!--- Formulario de Nuevo Remedy -->
    <form name="contactosForm" id="contactosForm" action="edit/contactos_ed.php" method="post"  > 
        <!-- Para el control de pulsación de los botones usaremos otro input oculto -->    
        <input id="opcion7" type="hidden" name="opcion" value="nuevo" /> <!-- Para nuevo remedy y fichero -->
        <input type="hidden" id="borrarFich" name="borrarFich" />
                    <input type="hidden" id="borrarEnv" name="borrarEnv" />
                    <input type="hidden" id="activarFich" name="activarFich" />
                    <input type="hidden" id="activarEnv" name="activarEnv" />
                    <input type="hidden" id="NCODEMPRESA" name="NCODEMPRESA" />
        <div class="colIzquierda" style='width: 190px; margin-left: 10px'>


            <table style="font-size: 9pt; width: 315px; margin-top: 10px">
                <tr class="fila">
                    <td class="celda" colspan='2'><div id="NCODCONTACTO" class="subtitulo">DATOS DE CONTACTO</div></td>
                </tr>

          
                <tr><td align="left"><strong>NOMBRE:</strong></td>
                    <td align="right" ><input id="NDESCONTACTO" type="text" name="NDESCONTACTO" style="width: 180px; text-align: left " value="" />
                        
                    </td></tr>
             

                <tr><td align="left"><strong>CARGO:</strong></td>
                    <td align="right"><input id="NCARGO" type="text" name="NCARGO" style="width: 180px; text-align: left "/></td></tr>
                <tr><td align="left"><strong>TLF. FIJO:</strong></td>
                    <td align="right"><input id="NTELF_FIJO" type="text" name="NTELF_FIJO" style="width: 180px; text-align: left "/></td></tr>
                <tr><td align="left"><strong>TLF. MÓVIL:</strong></td>
                    <td align="right"><input id="NTELF_MOVIL" type="text" name="NTELF_MOVIL" style="width: 180px; text-align: left "/></td></tr>

               
                        <tr><td align="left"><strong>FAX:</strong></td>
                            <td align="right"><input id="NFAX" type="text" name="NFAX" style="width: 180px; text-align: left "/></td></tr>
             <tr><td align="left"><strong>EMAIL:</strong></td>
                            <td align="right"><input id="NEMAIL" type="text" name="NEMAIL" style="width: 180px; text-align: left "/></td></tr>
             
  <tr><td align="left" valign='top'><strong>DIRECCIÓN:</strong></td>
                    <td align="right"><textarea id="NDIRECCION" type="text" name="NDIRECCION" style="width: 179px; text-align: left; height: 30px "></textarea></td></tr>
                
                <tr><td align="left"><strong>PAÍS:</strong></td>
                            <td align="right"><input id="NPAIS" type="text" name="NPAIS" style="width: 180px; text-align: left "/></td></tr>
 
                 <tr><td align="left"valign='top'><strong>OBSERVACIONES:</strong></td>
                    <td><textarea id="NOBSERVACIONES" type="textarea" name="NOBSERVACIONES" style="width: 179px; text-align: left; height:110px "></textarea></td></tr>



            </table>    




        </div>

        
    </form>
   
</div> 
