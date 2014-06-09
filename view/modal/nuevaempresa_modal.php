<script>
    $("#modalNuevoEmpresa").dialog({
        autoOpen: false,
        height: 180,
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

                $('#empresasForm').submit();
                
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
$cons_empresas = mysql_query("SELECT CODEMPRESA, DESEMPRESA FROM tempresas WHERE  CODEMPRESA<>'00000' AND CODEMPRESAPADRE IS NULL AND CHKACTIVO='S' ORDER BY DESEMPRESA ");
?>
<div id="modalNuevoEmpresa" title="Nueva Empresa">  
    <!--- Formulario de Nuevo Remedy -->
    <form name="empresasForm" id="empresasForm" action="edit/empresas_ed.php" method="post"  > 
        <!-- Para el control de pulsación de los botones usaremos otro input oculto -->    
        <input id="opcion33" type="hidden" name="opcion" value="nuevo" /> <!-- Para nuevo remedy y fichero -->
      
                   
        <div class="colIzquierda" style='width: 190px; margin-left: 10px'>


            <table style="font-size: 9pt; width: 315px; margin-top: 10px">
               <!-- <tr class="fila">
                    <td class="celda" colspan='2'><div  class="subtitulo">DATOS DE EMPRESA</div></td>
                </tr>
-->
          
                <tr><td align="left"><strong>DESCRIPCIÓN:</strong></td>
                    <td align="right" ><input id="NDESEMPRESA" type="text" name="NDESEMPRESA" style="width: 180px; text-align: left " value="" />
                        
                    </td></tr>
                <tr><td align="left"><strong>EMPRESA PADRE:</strong></td>
                    <td><select id="CODEMPRESAPADRE" type="text" name="CODEMPRESAPADRE" style="width: 180px; text-align: left ">
                            <option  value='00000' selected="selected" >Seleccionar...</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_empresas)) {

                                echo "<option id='fempresa-" . $fila->CODEMPRESA . "' value='" . $fila->CODEMPRESA . "'>" . utf8_encode($fila->DESEMPRESA) . "</option>";
                            }
                            ?>
                        </select>
                    </td></tr>
             

            </table>    




        </div>

        
    </form>
   
</div> 