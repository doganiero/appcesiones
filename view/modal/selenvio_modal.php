<script>
    $("#modalSelEnvio").dialog({
        autoOpen: false,
        height: 180,
        width: 380,
        bgiframe: true,
        resizable: false,
        modal: true,
        overlay: {
            backgroundColor: '#000',
            opacity: 0.5
        },
        buttons: {
            'Nuevo Envío': {
                text: "Nuevo Envío",
                id: "btnNewEnv",
                click: function() {
                    $(this).dialog('close');
                    var fila_sel = jQuery("#bufferlist").getGridParam('selrow');
                    var campos = $("#bufferlist").jqGrid('getRowData', fila_sel);
                    $('#modalremedy2').dialog('open');
                    if (campos.DESEMPRESA_DESTINO) {
                        var codempresa = $("#NCODEMPRESA option:contains('" + campos.DESEMPRESA_DESTINO + "')").val();
                        $('#NCODEMPRESA').val(codempresa);
                    }
                    if (campos.BUFFER_IP_DESTINO!="")
                        $('#NIP_DESTINO').val(campos.BUFFER_IP_DESTINO);
                    if (campos.BUFFER_NOMBREMAQUINA_DESTINO!="")
                        $('#NIP_DESTINO').val(campos.BUFFER_NOMBREMAQUINA_DESTINO);
                    var str = campos.BUFFER_DESCANAL;
                    var editran = str.indexOf("EDITRAN");
                    var xcom = str.indexOf("XCOM");
                    var gepp = str.indexOf("GEPP");
                    var ftp = str.indexOf("FTP");
                    var ftps = str.indexOf("FTPS");
                    var lftp = str.indexOf("LFTP");
                    var sftp = str.indexOf("SFTP");
                                        
                    if (gepp>=0)
                            $('#NCODCANAL').val('00003');
                    if (editran>=0)
                            $('#NCODCANAL').val('00001');
                    if (xcom>=0)
                            $('#NCODCANAL').val('00002');
                    if (ftp>=0)
                            $('#NCODCANAL').val('00004');
                    if (ftps>=0)
                            $('#NCODCANAL').val('00005');
                    if (lftp>=0)
                            $('#NCODCANAL').val('00006');
                    if (sftp>=0)
                            $('#NCODCANAL').val('00007');    
                    $('#NFICHERO_ORIGEN').val(campos.BUFFER_FICHERO_ORIGEN);
                    $('#NCODCARGA').val(campos.CODCARGA);
                     $('#opcion83').val("nuevo");
                }

            },
            'Envío Existente': {
                text: "Envío Existente",
                id: "btnExtEnv",
                click: function() {
                    $(this).dialog('close');
                    var fila_sel_m = jQuery("#bufferlist").getGridParam('selarrrow');
                    var vartmp;
                    var usrenv = prompt("Código de Envío:");
                    if (usrenv) {
                        var campos_m = new Array();
                        var rows = new Array();
                        for (var i = 0; i < fila_sel_m.length; i++) {
                            campos_m[i] = $("#bufferlist").jqGrid('getRowData', fila_sel_m[i]);
                            rows[i] = JSON.stringify($("#bufferlist").jqGrid('getRowData', fila_sel_m[i]));
                        }

                        vartmp = JSON.stringify(campos_m);
                        var postArray = {json: vartmp};
                        $.ajax({
                            type: "POST",
                            url: "edit/buffer_ed_2.php?CHKCONTROL=S&USRENV=" + usrenv,
                            dataType: "json",
                            data: postArray,
                            success: showResponse
                        });
                    }
                }

            }
        }

    });




</script>
<div id="modalSelEnvio" title="Agregar Ficheros Autorizados"> 


    <div id="refresh" class="colIzquierda" style='width: 160px; margin-left: 10px; '>



        <table style="font-size: 9pt; width: 330px; margin-top: 10px">


            <tr>
                <td > Indique si desea agregar la selección de ficheros a un nuevo envío o a uno existente </td></tr>

        </table>





    </div>



</div>