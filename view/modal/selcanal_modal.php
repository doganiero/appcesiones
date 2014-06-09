<script>
        $("#modalSelCanal").dialog({
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
                
                                
                'Aceptar': function() {
                    $(this).dialog('close');
                    $('#cargaForm').submit();    
                   
                }
                    
            },
                close: function() {
                  $('#progressbar').show();
            var progressbar = $("#progressbar"),
                    progressLabel = $(".progress-label");
            progressbar.progressbar({
                value: false,
                change: function() {
                    if(progressbar.progressbar("value")!==false)
                    progressLabel.text(progressbar.progressbar("value") + "%");
                },
                complete: function() {
                    progressLabel.text("");
                    $('#progressbar').hide();
                    $('#grupoBotonesCargas').show();
                    
                }
            });
            function progress() {
                var fila_sel = jQuery("#cargaslog").getGridParam('selrow');
            var campos = jQuery("#cargaslog").getRowData(fila_sel);
            
                var val = progressbar.progressbar("value") || 0;
               
                 
                $.getJSON("json/barraconsolida_json.php?sig_carga="+campos.CODCARGA,
                        function(data) {
                            
                            $("#progressbar").progressbar({value: data.progress});
                        })

                if (val < 100) {
                    setTimeout(progress, 100);
                }
            }
           
            
                 setTimeout(progress, 3000);
                 
                 $('#grupoBotonesCargas').hide();
            }
        });
       



</script>
<?
$cons_canales = mysql_query("SELECT DISTINCT CODCANAL, DESCANAL FROM v_envios WHERE DESCANAL NOT LIKE '%GEPP%' AND CODCANAL<>'00000' order by CODCANAL ");

//$cons_canales= mysql_query("SELECT CODCANAL, DESCANAL FROM tcanales WHERE CHKACTIVO='S' AND CODCANAL<>'00000' ");
?>
<div id="modalSelCanal" title="Seleccionar Canal de Consolidación">  
    
   

        <div class="colIzquierda" style='width: 160px; margin-left: 10px'>

   

            <table style="font-size: 9pt; width: 310px; margin-top: 10px">

              
                <tr><td align="left"><strong>CANAL:</strong></td>
                    <td><select id="SELCODCANAL"  name="SELCODCANAL" style="width: 300px; text-align: left ">
                            <option  value='' selected="selected" >Todos los Canales</option>   
                            <?
                            while ($fila = mysql_fetch_object($cons_canales)) {

                                echo "<option id='fcan-" . $fila->CODCANAL . "' value='" . $fila->CODCANAL . "'>".$fila->DESCANAL."</option>";
                            }
                            ?>
                        </select>
                    </td></tr>
                
            </table>



         

        </div>
    
</div> 
