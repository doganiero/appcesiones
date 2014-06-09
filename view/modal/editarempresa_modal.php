<script>
    $("#modalEdEmpresa").dialog({
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
            'Enviar': function() {

                $('#editEmpresasForm').submit();

            },
            'Cancelar': function() {
                $(this).dialog('close');
            }
        },
        open: function() {
            var fila_sel = jQuery("#empresaslist").getGridParam('selrow');
            var campos = jQuery("#empresaslist").getRowData(fila_sel);
            $('#refrescar').load("json/loadempadres_json.php");
            setTimeout(function() {
                $("#ECODEMPRESAPADRE").val(campos.CODEMPRESAPADRE);
            }, 500);

        }
    });






</script>
<?
$cons_empresas = mysql_query("SELECT CODEMPRESA, DESEMPRESA FROM tempresas WHERE  CODEMPRESA<>'00000' AND CODEMPRESAPADRE IS NULL AND CHKACTIVO='S' ORDER BY DESEMPRESA ");
?>
<div id="modalEdEmpresa" title="Editar Empresa">  
    <!--- Formulario de Nuevo Remedy -->
    <form name="editEmpresasForm" id="editEmpresasForm" action="edit/empresas_ed.php" method="post"  > 
        <!-- Para el control de pulsación de los botones usaremos otro input oculto -->    
        <input id="opcion33" type="hidden" name="opcion" value="editar" /> <!-- Para nuevo remedy y fichero -->
        <input id="EDCODEMPRESA" type="hidden" name="EDCODEMPRESA" value="" />
        <input id="ECHKACTIVO" type="hidden" name="ECHKACTIVO" value="" />
        <div class="colIzquierda" style='width: 190px; margin-left: 10px'>


            <table style="font-size: 9pt; width: 315px; margin-top: 10px">
               <!-- <tr class="fila">
                    <td class="celda" colspan='2'><div  class="subtitulo">DATOS DE EMPRESA</div></td>
                </tr>
                -->

                <tr><td align="left"><strong>DESCRIPCIÓN:</strong></td>
                    <td align="right" ><input id="EDESEMPRESA" type="text" name="EDESEMPRESA" style="width: 180px; text-align: left " value="" />

                    </td></tr>
                <tr><td align="left"><strong>EMPRESA PADRE:</strong></td>
                    <td><div id="refrescar"><select id="ECODEMPRESAPADRE" type="text" name="ECODEMPRESAPADRE" style="width: 180px; text-align: left ">
                                <option  value='00000' selected="selected" >Seleccionar...</option>   
                                <?
                                while ($fila = mysql_fetch_object($cons_empresas)) {

                                    echo "<option id='efempresa-" . $fila->CODEMPRESA . "' value='" . $fila->CODEMPRESA . "'>" . utf8_encode($fila->DESEMPRESA) . "</option>";
                                }
                                ?>
                            </select></div>
                    </td></tr>


            </table>    




        </div>


    </form>

</div> 