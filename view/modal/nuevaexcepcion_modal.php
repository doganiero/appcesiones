<script>
    $("#modalNuevoExcepcion").dialog({
        autoOpen: false,
        height: 200,
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

                $('#excepcionesForm').submit();
                
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
<?
$cons_canales= mysql_query("SELECT CODCANAL, DESCANAL FROM tcanales WHERE CHKACTIVO='S' AND CODCANAL<>'00000' AND DESCANAL NOT LIKE '%GEPP%' ");
?>
<div id="modalNuevoExcepcion" title="Nueva Excepción">  
    <!--- Formulario de Nuevo Remedy -->
    <form name="excepcionesForm" id="excepcionesForm" action="edit/excepciones_ed.php" method="post"  > 
        <!-- Para el control de pulsación de los botones usaremos otro input oculto -->    
        <input id="opcion33" type="hidden" name="opcion" value="nuevo" /> <!-- Para nuevo remedy y fichero -->
      
                   
        <div class="colIzquierda" style='width: 190px; margin-left: 10px'>


            <table style="font-size: 9pt; width: 315px; margin-top: 10px">

          
                <tr><td align="left"><strong>MÁSCARA:</strong></td>
                    <td align="right" ><input id="NEXCMASCARA" type="text" name="NEXCMASCARA" style="width: 180px; text-align: left " value="" />
                       
                        <tr><td align="left"><strong>DESCRIPCIÓN:</strong></td>
                    <td align="right" ><input id="NEXCDESC" type="text" name="NEXCDESC" style="width: 180px; text-align: left " value="" />
    
                        
                    </td></tr>
               <tr><td align="left"><strong>CANAL:</strong></td>
                    <td align='right'><select id="NEXCCODCANAL" type="text" name="NEXCCODCANAL" style="width: 185px; text-align: left ">
                    <option  value='00000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_canales)) {

                                echo "<option id='fcanal-" . $fila->CODCANAL . "' value='" . $fila->CODCANAL . "'>" . utf8_decode($fila->DESCANAL) . "</option>";
                            }
                            ?>
                        </select>
                    </td></tr>
             

            </table>    




        </div>

        
    </form>
   
</div> 