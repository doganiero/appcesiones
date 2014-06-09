<?
// Comprobación de autenticación.

include_once('../functions/db.php');
$link = conectar();
?>
<!--- Mensajes -->


<!--- Fin Mensajes -->

<!-- ventanas Modales -->

<!-- Modal Seleccionar Carga -->
<?
include_once('../view/modal/selcarga_modal.php');
?>

<!-- Modal Detalle Buffer -->
<?
include_once('../view/modal/detallebuffer_modal.php');
?>
<!-- Modal Detalle remedy -->
<?
include_once('../view/modal/detalleremedy_modal.php');
?>

<!-- Modal Detalle Ficheros -->
<?
include_once('../view/modal/detalleficheros_modal.php');
?>
<!-- Modal Nuevo remedy -->

<?
include_once('../view/modal/remedy_modal.php');
?>
<!-- Fin Ventanas Modales -->

<!--<div align="center" id="titulo" class="titulo2">Utilidades > Máscaras</div>-->
<!--<div class="centradomasGrande" align="center" style="height: 500px">-->


<!--- Tabla de Cargas Ficheros -->
<div class="unaCol" align="center" style="height: 600px;  " >

    <table id="ficheros" class="scroll"><tr><td></td></tr></table>
    <div id="pager2" class="scroll" style=""></div>

    <!--- Fin Tabla de de Ficheros -->

    <!-- Botones acciones listado ficheros --> 
    <div class="" id="grupoBotonesFicheros" style='width: 1140px; height: 50px'>
        <table width='1145' style="padding-top:5px; ">
            <tr>
                <td align='right'>
                    <input id="BFICHERO_ORIGEN" type="text" name="BFICHERO_ORIGEN" value="Máscara / Fichero a Buscar..." style="width: 26% ; text-align: left; height: 20px; vertical-align:top; "/>


                    <input id="botonBuscaFich" type="submit" value="Buscar" class="boton ui-corner-all" 
                           style="width:115px; margin-bottom:9px;" />
                    <input id="botonPruebaMascaras" type="submit" value="Prueba Máscara" class="boton ui-corner-all" 
                           style="width:125px; margin-bottom:9px;" />
                    <input id="botonDefMascaras" type="submit" value="Alta Máscara" class="boton ui-corner-all" 
                           style="width:115px; margin-bottom:9px;" />
                    <input id="botonDetalleFicheros" type="submit" value="Ver Detalles" class="boton ui-corner-all" 
                           style="width:115px; margin-bottom:9px;" />
                    <input id="botonBajaFicheros" type="submit" value="Baja Ficheros" class="boton ui-corner-all" 
                           style="width:115px; margin-bottom:9px;" />
                    <input id="botonDetalleRemedy" type="submit" value="Ver Detalles" class="boton ui-corner-all" 
                           style="width:115px; margin-bottom:9px; display:none" />

                    <!-- Para el control de pulsación de los botones usaremos otro input oculto -->    
                    <input id="idFich" type="hidden" name="idFich" />
                </td>
            </tr>
        </table>

    </div>
    <!-- FIN Botones acciones listado ficheros -->
    <!--- Tabla de Cargas Buffer -->
    <div class="unaCol" align="center" style="height: 500px; padding-top:0px;" >

        <table id="bufferlist" class="scroll"><tr><td></td></tr></table>
        <div id="pager" class="scroll" style=""></div>

        <!--- Fin Tabla de de Cargas Buffer -->

        <!-- Botones acciones listado Buffer --> 

        <div id="grupoBotonesBuffer" class="colIzquierda" style='width: 1140px;'>
            <table width='1145' style="padding-top:10px;">
                <tr><td align='right'>
                        <input type="hidden" id="idBuff" name="idBuff" />
                        <input type="hidden" id="idCarga" name="idCarga" /> 

                        <input type="hidden" id="idEnv" name="idEnv" />
                        <input id="botonCoincidencias" type="submit" value="Coincidencias" class="boton ui-corner-all" 
                               style="width:110px; margin-bottom:9px;" />
                        <input id="botonCompararBuffer" type="submit" value="Comparar" class="boton ui-corner-all" 
                               style="width:110px; margin-bottom:9px;" />  
                        <!--
                        <input id="botonExportarBuffer" type="submit" value="Exportar" class="boton ui-corner-all" 
                               style="width:110px; margin-bottom:9px;" />
                        -->
                        <input id="botonDetalleBuffer" type="submit" value="Ver Detalles" class="boton ui-corner-all" 
                               style="width:110px; margin-bottom:9px;" />

                        <!-- Para el control de pulsación de los botones usaremos otro input oculto -->    
                        <input id="opcionA" type="hidden" name="opcionA" value="nuevo" />

                    </td>
                </tr>
            </table>

        </div>
        <!-- FIN Botones acciones listado Buffer -->
    </div>
</div>




<!--</div>-->

<script type="text/javascript">
    // Complemento para carga asíncrona con formularios.
    // prepara el formulario cuando el DOM está preparado.


    datePick = function(elem)
    {
        jQuery(elem).datepicker({dateFormat: "yy-mm-dd"});
    }

    // post-submit callback 
    function formSuccess(data) {


        if (!data.ERROR) {


            mensaje(data.SUCCESS, 'titulo');
            if (data.NMASK)
            {
                var carga = $('#SELCODCARGA').val();
                jQuery("#ficheros").GridUnload();
                GridFicheros(data.MASCARA, data.CANAL, carga, 0, 0, data.CODENV);
                jQuery("#bufferlist").GridUnload();
                GridBuffer(0, data.MASCARA, data.CANAL, carga, data.CODENV);
            }

        }
        else {

            $('#titulo').jAlert(data.ERROR, "fatal");
            setTimeout(function() {
                $('.msg-fatal').fadeOut(500, function() {
                    $('.msg-fatal').remove();
                });
            }, 10000);
        }
    }

    function bajamultiple(callback) {
        var msj = 0;
        var mot = $('#NCODMOTIVOBAJA').val();


        var fila_selb = jQuery("#ficheros").getGridParam('selarrrow');
        if (fila_selb.length > 0) {

            var camposb = new Array();
            for (var i = 0; i < fila_selb.length; i++) {
                camposb[i] = $("#ficheros").jqGrid('getRowData', fila_selb[i]);
                $('#borrarFich').val(camposb[i].CODFICHERO);
                $('#borrarEnv').val(camposb[i].CODENVIO);
                //$('#remedyForm').submit();

                $.ajax({
                    type: "POST",
                    url: 'edit/remedy_ed.php?borrar=1&borrarEnv=' + camposb[i].CODENVIO + '&borrarFich=' + camposb[i].CODFICHERO + '&mot=' + mot, //  '',
                    dataType: 'json',
                    success: function(data) {
                        if (data.SUCCESS) {
                            msj = "Selección de ficheros dados de baja correctamente";
                        }
                        if (data.ERROR) {
                            msj = "Ha ocurrido un error al dar de baja uno de los ficheros";
                        }


                    },
                    error: function() {
                        alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                    }
                });




            }

            $('.msg-info').remove();

            $('#ficheros').jAlert("Actualizando Registros...", 'info');

            setTimeout(function() {

                callback();


            }, 4000); //


        }

    }
    function reloadGridFich() {
        $('#remedyForm')[0].reset();
        jQuery("#ficheros").trigger("reloadGrid");


    }

    function changeSearchDefault2(select) {

        select.children("option:selected").removeAttr("selected");
        var newdefault = select.children("option[value='BUFFER_FICHERO_ORIGEN']");
        newdefault.attr("selected", "selected");
        newdefault.trigger("change");
    }

    function changeSearchDefault3(select) {

        select.children("option:selected").removeAttr("selected");
        var newdefault = select.children("option[value='CHKCONTROL']");
        newdefault.attr("selected", "selected");
        newdefault.trigger("change");
    }

    function GridBuffer(coincidencias, mascara, codcanal, carga, codenv, agrupa, codbuffermask) { // esta función crea el grid y filtra la tabla según parámetros
        var tit = $('#SELCODCARGA option:selected').text();
        var titcarga = carga + " - " + tit.substr(12, 100);
        var titagrupa
        if (!codbuffermask)
            codbuffermask = 0;
        if (!carga)
            titcarga = "Todas las Cargas";
        if (!coincidencias)
            coincidencias = 0;
        if (!mascara)
            mascara = 0;
        if (!codcanal)
            codcanal = 0;
        if (!carga)
            carga = 0;
        if (!codenv)
            codenv = 0;
        if (!agrupa) {
            agrupa = 0;
            titagrupa = "Agrupar Registros Duplicados ";
        } else
            titagrupa = "Ver Registros Duplicados ";
        var seleccionar;
        var scrollPos;
        var change_search_now = false;
        jQuery("#bufferlist").jqGrid({
            url: 'json/buffer_json.php?coincidencias=' + coincidencias + '&mascara=' + encodeURIComponent(mascara) + '&codcanal=' + codcanal + '&codcarga=' + carga + "&codenv=" + codenv + '&agrupa=' + agrupa + '&codbuffermask=' + codbuffermask,
            width: 1140,
            height: 257,
            datatype: "json",
            type: "POST",
            //postData: {
            //  json: rows
            //},
            colNames: [
                'CARGA',
                'CANAL',
                'CODBUFFER',
                'COD.ENVÍO',
                'CODFICHERO',
                'NOMBRE FICHERO',
                'CHKTRANSMISION_INTERNA',
                'TRANS.INTERNA',
                'CODTIPOTRANSMISION',
                'TIPO TRANS.',
                'ESTADO',
                'EMP.DESTINO',
                'DESESTADOCONTROL',
                'BUFFER_CHKCIFRADO',
                'CIFRADO',
                'CODTIPODESTINO',
                'TIPO DESTINO',
                'EMPRESA ORIGEN',
                'MAQ.ORIGEN',
                'IP ORIGEN',
                'MAQ.DESTINO',
                'IP DESTINO',
                'OBSERVACIONES',
                'FICHERO DESTINO',
                'FECHA ALTA',
                'FECHA MODIFICACIÓN',
                'BUFFER_FECHA_BAJA',
                'COD. USR. ALTA',
                'BUFFER_DESUSUARIO_ALTA',
                'COD. USR. MODIFICACIÓN',
                'BUFFER_DESUSUARIO_MODIFICACION',
                'BUFFER_CODUSUARIO_BAJA',
                'BUFFER_DESUSUARIO_BAJA',
                'BUFFER_ESTADO',
                'CODCARGA',
                'CODBUFFER',
                'BUFFER_FICHERO_ORIGEN'
            ],
            colModel: [
                //, editable: true, sorttype:"text",editable:true,edittype:"select",editoptions:   
                //{ value:"1:one;2:two"},editrules:{required:true} ejemplo de campo editable. hay que modificar la propiedad editable del nav a true
                {name: 'CODCARGA', index: 'CODCARGA', align: 'center', width: 50, hidden: true},
                {name: 'BUFFER_DESCANAL', index: 'BUFFER_DESCANAL', align: 'center', width: 50, hidden: true},
                {name: 'CODBUFFER', index: 'CODBUFFER', align: 'center', width: 50, hidden: true},
                {name: 'CODENVIO', index: 'CODENVIO', align: 'center', width: 60, stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'CODFICHERO', index: 'CODFICHERO', align: 'center', hidden: true},
                {name: 'BUFFER_FICHERO_ORIGEN', index: 'BUFFER_FICHERO_ORIGEN', align: 'left', width: 400},
                {name: 'CHKTRANSMISION_INTERNA', index: 'CHKTRANSMISION_INTERNA', align: 'center', hidden: true},
                {name: 'TRANSMISION_INTERNA', index: 'TRANSMISION_INTERNA', align: 'center', width: 180, stype: 'select', searchoptions: {searchhidden: true, value: 'DESCONOCIDO:DESCONOCIDO;TRANS. INTERNA SI:SÍ;TRANS. INTERNA NO:NO'}, hidden: true},
                {name: 'CODTIPOTRANSMISION', index: 'CODTIPOTRANSMISION', align: 'center', hidden: true},
                {name: 'DESTIPOTRANSMISION', index: 'DESTIPOTRANSMISION', align: 'center', width: 70, hidden: true},
                {name: 'CHKCONTROL', index: 'CHKCONTROL', align: 'center', width: 130, editable: true, sorttype: "text",
                    edittype: "select", editoptions: {value: "S:AUTORIZADO;N:NO AUTORIZADO;E:EN ESTUDIO;D:DESCARTADO"}, editrules: {required: true}},
                {name: 'DESEMPRESA_DESTINO', index: 'DESEMPRESA_DESTINO', align: 'center', width: 180},
                {name: 'DESESTADOCONTROL', index: 'DESESTADOCONTROL', align: 'center', hidden: true},
                {name: 'BUFFER_CHKCIFRADO', index: 'BUFFER_CHKCIFRADO', align: 'center', hidden: true},
                {name: 'BUFFER_CIFRADO', index: 'BUFFER_CIFRADO', align: 'center', hidden: true},
                {name: 'CODTIPODESTINO', index: 'CODTIPODESTINO', align: 'center', hidden: true},
                {name: 'DESTIPODESTINO', index: 'DESTIPODESTINO', align: 'center', stype: 'select', searchoptions: {searchhidden: true, value: 'DESCONOCIDO:DESCONOCIDO;SALIDA EXTERNA:SALIDA EXTERNA;A PRODUCCION:A PRODUCCION; A ENTORNO PREVIO:A ENTORNO PREVIO;A PC:A PC'}, hidden: true},
                {name: 'DESEMPRESA_ORIGEN', index: 'DESEMPRESA_ORIGEN', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'BUFFER_NOMBREMAQUINA_ORIGEN', index: 'BUFFER_NOMBREMAQUINA_ORIGEN', align: 'center', width: 80, stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'BUFFER_IP_ORIGEN', index: 'BUFFER_IP_ORIGEN', align: 'center', width: 70, stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'BUFFER_NOMBREMAQUINA_DESTINO', index: 'BUFFER_NOMBREMAQUINA_DESTINO', align: 'center', width: 180, stype: 'text', searchoptions: {searchhidden: true}},
                {name: 'BUFFER_IP_DESTINO', index: 'BUFFER_IP_DESTINO', align: 'center', width: 180, stype: 'text', searchoptions: {searchhidden: true}},
                {name: 'MOTIVOLOG', index: 'MOTIVOLOG', align: 'center', width: 310, hidden: true},
                {name: 'BUFFER_FICHERO_DESTINO', index: 'BUFFER_FICHERO_DESTINO', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'BUFFER_FECHA_ALTA', index: 'BUFFER_FECHA_ALTA', align: 'center', formatter: 'date', stype: 'text', searchoptions: {searchhidden: true, dataInit: datePick, attr: {title: 'Seleccionar Fecha'}}, hidden: true},
                {name: 'BUFFER_FECHA_MODIFICACION', index: 'BUFFER_FECHA_MODIFICACION', align: 'center', formatter: 'date', stype: 'text', searchoptions: {searchhidden: true, dataInit: datePick, attr: {title: 'Seleccionar Fecha'}}, hidden: true},
                {name: 'BUFFER_FECHA_BAJA', index: 'BUFFER_FECHA_BAJA', align: 'center', hidden: true},
                {name: 'BUFFER_CODUSUARIO_ALTA', index: 'BUFFER_CODUSUARIO_ALTA', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'BUFFER_DESUSUARIO_ALTA', index: 'BUFFER_DESUSUARIO_ALTA', align: 'center', hidden: true},
                {name: 'BUFFER_CODUSUARIO_MODIFICACION', index: 'BUFFER_CODUSUARIO_MODIFICACION', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'BUFFER_DESUSUARIO_MODIFICACION', index: 'BUFFER_DESUSUARIO_MODIFICACION', align: 'center', hidden: true},
                {name: 'BUFFER_CODUSUARIO_BAJA', index: 'BUFFER_CODUSUARIO_BAJA', align: 'center', hidden: true},
                {name: 'BUFFER_DESUSUARIO_BAJA', index: 'BUFFER_DESUSUARIO_BAJA', align: 'center', hidden: true},
                {name: 'BUFFER_ESTADO', index: 'BUFFER_ESTADO', align: 'center', hidden: true},
                {name: 'CODCARGA', index: 'CODCARGA', align: 'center', hidden: true, editable: true},
                {name: 'CODBUFFER', index: 'CODBUFFER', align: 'center', hidden: true, editable: true},
                {name: 'BUFFER_FICHERO_ORIGEN', index: 'BUFFER_FICHERO_ORIGEN', align: 'center', hidden: true, editable: true}


            ],
            pager: jQuery('#pager'),
            rowNum: 50,
            loadui: "disable",
            imgpath: 'css/themes/custom-theme/images',
            sortname: 'BUFFER_FICHERO_ORIGEN',
            sortorder: "asc",
            loadonce: false,
            viewrecords: true,
            recordtext: "{0} - {1} de {2}",
            emptyrecords: "Sin registros",
            caption: "" + tit.toUpperCase() + "",
            ondblClickRow: function(rowid)
            {
                $('#botonDetalleBuffer').click();
            },
            onSelectRow: function() { // eventos al seleccionar filas y cargar ficheros
                $('#botonDetalleBuffer').show("fast");
                $('#grupoBotonesBuffer').width('1140');
                var fila_sel = jQuery("#bufferlist").getGridParam('selrow');
                var campos = jQuery("#bufferlist").getRowData(fila_sel);
                //$('#idEnv').val(campos.CODENVIO);
                $('#idBuff').val(campos.CODBUFFER);
                $('#idCarga').val(campos.CODCARGA);
                if ($("#modalDetalleBuffer").dialog("isOpen"))
                    $("#botonDetalleBuffer").click();
                //$('#grupoBotonesRemedy').width('530');
                //$('#botonDetalleRemedy').show("fast");

            },
            loadComplete: function() {  // seleccionando el primer registro de la tabla
                $('#botonDetalleBuffer').hide("fast");
                $('#grupoBotonesBuffer').width('240');
                if (codbuffermask) {
                      $('#ficheros').jAlert("Por Favor Espere ...", "info");
                    var bval = $('#BFICHERO_ORIGEN').val();
                                     
                    //búsqueda en tbuffer
                    
                        $('#busca2').click();
                        $(".data").children('input').val(bval);
                        setTimeout(function() {
                            $('#fbox_bufferlist_search').click();
                            $('.ui-icon-closethick').click();
                        }, 1000);
                       
                        setTimeout(function() {
                          
                        codbuffermask=$.getUrlVar('codbuffermask');
                        var rowDatasel = [];
                        var rowIdssel = $("#bufferlist").jqGrid('getDataIDs');
                       //<---- aqui corrigiendo error en mascaras desde análisis de consolidación
                        for (var i = 1; i <= rowIdssel.length; i++) {//iterate over each row
                            rowDatasel = $("#bufferlist").jqGrid('getRowData', i);
                            //set background style if ColumnValue == true
                            
                            if (rowDatasel['CODBUFFER'] === codbuffermask) {
                                
                                jQuery('#bufferlist').jqGrid('setSelection', i);
                                seleccionar = i;
                            }
                        }
                        codbuffermask = 0;

                        scrollPos = jQuery("#bufferlist").closest(".ui-jqgrid-bdiv").scrollTop();
                        if (seleccionar) {
                            scrollPos = scrollPos + seleccionar * 19;
                            jQuery("#bufferlist").closest(".ui-jqgrid-bdiv").scrollTop(scrollPos);
                        }

                        setTimeout(function() {
                            $('#botonCoincidencias').click();
                        }, 1000);
                        
                  }, 2000);
                    codbuffermask = 0;
                }



                var rowData = [];
                var rowIds = $("#bufferlist").jqGrid('getDataIDs');
                for (var i = 1; i <= rowIds.length; i++) {//iterate over each row
                    rowData = $("#bufferlist").jqGrid('getRowData', i);
                    if (rowData['CHKCONTROL'] === 'S') {

                        $("#bufferlist").setCell(i, 'BUFFER_FICHERO_ORIGEN', '', {'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'DESEMPRESA_DESTINO', '', {'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'CHKCONTROL', '', {'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_NOMBREMAQUINA_ORIGEN', '', {'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_IP_ORIGEN', '', {'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_NOMBREMAQUINA_DESTINO', '', {'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_IP_DESTINO', '', {'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'MOTIVOLOG', '', {'font-weight': 'bold'});
                        //$("#remedylist").jqGrid('setCell',i,'label', '',{'color':'red'});
                    }
                    else
                    if (rowData['CHKCONTROL'] === 'N') {
                        $("#bufferlist").setCell(i, 'BUFFER_DESCANAL', '', {'color': '#A60808', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'CODCARGA', '', {'color': '#A60808', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_FICHERO_ORIGEN', '', {'color': '#A60808', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'DESEMPRESA_DESTINO', '', {'color': '#A60808', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'CHKCONTROL', '', {'color': '#A60808', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_NOMBREMAQUINA_ORIGEN', '', {'color': '#A60808', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_IP_ORIGEN', '', {'color': '#A60808', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_NOMBREMAQUINA_DESTINO', '', {'color': '#A60808', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_IP_DESTINO', '', {'color': '#A60808', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'MOTIVOLOG', '', {'color': '#A60808', 'font-weight': 'bold'});
                        //$("#remedylist").jqGrid('setCell',i,'label', '',{'color':'red'});
                    } else
                    if (rowData['CHKCONTROL'] === 'E') {
                        $("#bufferlist").setCell(i, 'BUFFER_DESCANAL', '', {'color': 'DarkBlue ', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'CODCARGA', '', {'color': 'DarkBlue ', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_FICHERO_ORIGEN', '', {'color': 'DarkBlue ', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'DESEMPRESA_DESTINO', '', {'color': 'DarkBlue ', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'CHKCONTROL', '', {'color': 'DarkBlue ', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_NOMBREMAQUINA_ORIGEN', '', {'color': 'DarkBlue ', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_IP_ORIGEN', '', {'color': 'DarkBlue ', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_NOMBREMAQUINA_DESTINO', '', {'color': 'DarkBlue ', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_IP_DESTINO', '', {'color': 'DarkBlue ', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'MOTIVOLOG', '', {'color': 'DarkBlue ', 'font-weight': 'bold'});
                        //$("#remedylist").jqGrid('setCell',i,'label', '',{'color':'red'});
                    } else
                    if (rowData['CHKCONTROL'] === 'D') {
                        $("#bufferlist").setCell(i, 'BUFFER_DESCANAL', '', {'color': 'DarkGreen', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'CODCARGA', '', {'color': 'DarkGreen', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_FICHERO_ORIGEN', '', {'color': 'DarkGreen', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'DESEMPRESA_DESTINO', '', {'color': 'DarkGreen', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'CHKCONTROL', '', {'color': 'DarkGreen', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_NOMBREMAQUINA_ORIGEN', '', {'color': 'DarkGreen', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_IP_ORIGEN', '', {'color': 'DarkGreen', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_NOMBREMAQUINA_DESTINO', '', {'color': 'DarkGreen', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'BUFFER_IP_DESTINO', '', {'color': 'DarkGreen', 'font-weight': 'bold'});
                        $("#bufferlist").setCell(i, 'MOTIVOLOG', '', {'color': 'DarkGreen', 'font-weight': 'bold'});
                        //$("#remedylist").jqGrid('setCell',i,'label', '',{'color':'red'});
                    }
                } //for


                //jQuery('#list').jqGrid('setSelection', selid); //mantener selección de fila
                //jQuery('#list').jqGrid('setSelection','');
                var carga = $('#SELCODCARGA').val();
            },
            gridComplete: function() {

                $('#pager_center').show();
                $('.msg-warning').remove();
                //var esbusqueda = 0;
                var rec = $('#bufferlist').getGridParam('records');
                //esbusqueda = $('#bufferlist').getGridParam('userData').buscando;
                //if (esbusqueda == 0) {


                if (rec == 0) {

                    setTimeout(function() {
                        $('#bufferlist').jAlert("No se encontraron registros", "warning");
                    }, 500);
                }


                //  }



            }
            , onPaging: function(pgButton) {
                //
            } //al cambiar página del grid volver a cargar ficheros y perder selección

        }).navGrid('#pager', {edit: false, add: false, del: false, view: false,
            beforeRefresh: function() {
                change_search_now = true;
                var carga = $('#SELCODCARGA').val();
                jQuery("#bufferlist").GridUnload();
                GridBuffer(0, 0, 0, carga, 0, agrupa);
            }
        },
        {}, //  default settings for edit
                {}, //  default settings for add
                {}, // delete instead that del:false we need this
                {
                    multipleSearch: true,
                    id: 'busca2',
                    width: 575,
                    sopt: ['cn', 'eq', 'bw', 'ew'],
                    closeAfterSearch: true,
                    //searchOnEnter: true,
                    // procedimientos para seleccionar una columnna por defecto en la búsqueda
                    onInitializeSearch: function() {
                        change_search_now = true;
                        changeSearchDefault2($('.columns').children("select"));
                        $("#fbox_bufferlist_reset").click(function() {
                            change_search_now = true;
                        });
                    },
                    afterRedraw: function() {

                        if (change_search_now) {
                            changeSearchDefault2($('.columns').children("select"));
                            change_search_now = false;
                        }
                        // Add "+" button click handlers to change search column default of newest search row item
                        $("input[value='+']").each(function(inputindex) {
                            $(this).click(function() {
                                $("input[value='+']").each(function(clickedindex) {
                                    if (clickedindex == inputindex) {
                                        // Change default
                                        changeSearchDefault2($(this).closest('tr').siblings(":last").children(".columns").children("select"));
                                    }
                                });
                            });
                        });
                    },
                    afterShowSearch: function() {
                        //$("#fbox_bufferlist
                        changeSearchDefault2($('.columns').children("select"));
                        $(".data").children('input').focus();
                    }
                    //------------------------------------------------------------------------- 

                }, // search options
        {} /* view parameters*/
        ).navButtonAdd('#pager', {
            caption: "",
            title: "" + titagrupa + "",
            buttonicon: " ui-icon-triangle-2-n-s",
            onClickButton: function() {

                if (agrupa === 1)
                    agrupa = 0;
                else
                    agrupa = 1;
                jQuery("#bufferlist").GridUnload();
                GridBuffer(coincidencias, mascara, codcanal, carga, codenv, agrupa)

            },
            position: "last"
        }).navSeparatorAdd('#pager', {
        }).navButtonAdd('#pager', {
            id: "sfilter",
            caption: "S",
            title: "Filtrar por Registros Autorizados ",
            buttonicon: "none",
            onClickButton: function() {

                $("#busca2").click();
                changeSearchDefault3($('.columns').children("select"));
                $(".data").children('input').val('S');
                //alert('pausa');
                setTimeout(function() {
                    $('#fbox_bufferlist_search').click();
                    $('.ui-icon-closethick').click();
                }, 1000);
            },
            position: "last"

        }).navButtonAdd('#pager', {
            caption: "E",
            title: "Filtrar por Registros en Estudio ",
            buttonicon: "none",
            onClickButton: function() {
                $("#busca2").click();
                changeSearchDefault3($('.columns').children("select"));
                $(".data").children('input').val('E');
                //alert('pausa');
                setTimeout(function() {
                    $('#fbox_bufferlist_search').click();
                    $('.ui-icon-closethick').click();
                }, 1000);
            },
            position: "last"
        }).navButtonAdd('#pager', {
            caption: "N",
            title: "Filtrar por Registros No Autorizados ",
            buttonicon: "none",
            onClickButton: function() {
                $("#busca2").click();
                changeSearchDefault3($('.columns').children("select"));
                $(".data").children('input').val('N');
                //alert('pausa');
                setTimeout(function() {
                    $('#fbox_bufferlist_search').click();
                    $('.ui-icon-closethick').click();
                }, 1000);
            },
            position: "last"
        }).navButtonAdd('#pager', {
            caption: "D",
            title: "Filtrar por Registros Descartados ",
            buttonicon: "none",
            onClickButton: function() {

                $("#busca2").click();
                changeSearchDefault3($('.columns').children("select"));
                $(".data").children('input').val('D');
                //alert('pausa');
                setTimeout(function() {
                    $('#fbox_bufferlist_search').click();
                    $('.ui-icon-closethick').click();
                }, 1000);
            },
            position: "last"
        }).navSeparatorAdd('#pager', {
        }
        );
    } // fin funcion GridBuffer

    function changeSearchDefault(select) {

        select.children("option:selected").removeAttr("selected");
        var newdefault = select.children("option[value='FICHERO_ORIGEN']");
        newdefault.attr("selected", "selected");
        newdefault.trigger("change");
    }

    function selcalldel(callback) {
        var fila_sel = jQuery("#ficheros").getGridParam('selarrrow');
        var msj = 0;
        if (confirm("Se dispone a eliminar la(s) máscara(s) de el(los) fichero(s) seleccionado(s). ¿Desea Continuar?")) {
            if (fila_sel.length > 0) {

                var campos = new Array();
                var aux = 0

                for (var i = 0; i < fila_sel.length; i++) {
                    campos[i] = $("#ficheros").jqGrid('getRowData', fila_sel[i]);
                    if (campos[i].CODFICHEROPADRE != "" || campos[i].FILE_MASCARA != "") {

                        var pdr = campos[i].CODFICHEROPADRE;
                        var forig = encodeURIComponent(campos[i].FICHERO_ORIGEN);
                        if (campos[i].FILE_MASCARA != "") {
                            pdr = campos[i].CODFICHERO;
                            forig = encodeURIComponent(campos[i].FILE_MASCARA);
                        }
                        $.ajax({
                            type: "POST",
                            url: 'edit/mascara_ed.php?borrar=1&envPadre=' + campos[i].CODENVIO + '&codfichPadre=' + pdr + '&codcanalPadre=' + campos[i].FILE_CODCANAL + '&mascara=' + forig + '', //  '',
                            dataType: 'json',
                            success: function() {

                                //

                            },
                            error: function() {
                                alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                            }
                        });
                    } //else
                    //msj = 'Ha seleccionado al menos un fichero sin máscara definida';

                }

                setTimeout(function() {
                    callback();
                }, 3000);


                //--------------

            }
            else
                msj = 'Seleccione la máscara a eliminar';
        } else
            $('.msg-info').remove();
        if (msj)
            alert(msj);


    }
    function reloadGridcalldel() {

        var ids = jQuery("#ficheros").getGridParam('selarrrow');
        var carga = $('#SELCODCARGA').val();
        if (!carga)
            carga = 0;
        var gridData;

        var vartmp = $("#ficheros").getGridParam('userData').prueba;
        var mascara = $("#ficheros").getGridParam('userData').mascara;

        if (vartmp)
        {


            vartmp = JSON.stringify(vartmp);

            var postArray = {json: vartmp};
            $.ajax({
                type: "POST",
                url: 'json/ficheros_json.php?mascara=' + encodeURIComponent(mascara) + '&codcanal=0&vermask=0&carga=' + carga + '&baja=0&codenv=0&coincidencias=0&ipdest=0&desempresa=0',
                datatype: "text",
                async: false,
                data: postArray,
                success: function(data) {
                    gridData = data;
                },
                error: function(e) {
                    alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                }
            });
            $("#ficheros").setGridParam({
                datastr: gridData,
                datatype: "jsonstring" // !!! reset datatype
            }).trigger("reloadGrid");


        } else
            jQuery("#ficheros").trigger("reloadGrid");

        setTimeout(function() {
            for (var i = 0; i < ids.length; i++) {
                jQuery('#ficheros').jqGrid('setSelection', ids[i]);
            }
        }, 1000); // seleccionar filas despues de un tiempo
    }





//-----------------Tabla de Ficheros-----------------------

    function GridFicheros(mascara, codcanal, carga, baja, vermask, codenv, desempresa, ipdest, coincidencias, sql) { // esta función crea el grid y filtra la tabla según parámetros

        if (!mascara)
            mascara = 0;
        if (!codcanal)
            codcanal = 0;
        if (!carga)
            carga = 0;
        if (!baja)
            baja = 0;
        if (!vermask)
            vermask = 0;
        if (!codenv)
            codenv = 0;
        if (!coincidencias)
            coincidencias = 0;

        if (!desempresa)
            desempresa = 0;
        if (!ipdest)
            ipdest = 0;
        var change_search_now = false;
        jQuery("#ficheros").jqGrid({
            url: 'json/ficheros_json.php?mascara=' + encodeURIComponent(mascara) + '&codcanal=' + codcanal + "&vermask=" + vermask + "&carga=" + carga + "&baja=" + baja + "&codenv=" + codenv + "&coincidencias=" + coincidencias + "&ipdest=" + ipdest + "&desempresa=" + desempresa,
            width: 1140,
            height: 140,
            datatype: "json",
            type: "POST",
            colNames: [
                'COD.ENVIO',
                'CODFICHERO',
                'CODFICHEROPADRE',
                'NOMBRE FICHERO',
                'FICHERO DESTINO',
                'RUTA ORIGEN',
                'UUAA',
                'CODCLASIFICACION',
                'CLASIFICACIÓN BBVA',
                'CODNIVEL_LOPD',
                'NIVEL LOPD',
                'FILE_CODCANAL',
                'MÁSCARA',
                'CANAL',
                'FILE_CODMOTIVOBAJA',
                'MOTIVO BAJA',
                'FECHA ALTA',
                'FECHA MODIFICACIÓN',
                'FECHA BAJA',
                'COD. USR. ALTA',
                'FILE_DESUSUARIO_ALTA',
                'COD. USR. MODIFICACIÓN',
                'FILE_DESUSUARIO_MODIFICACION',
                'COD. USR. BAJA',
                'FILE_DESUSUARIO_BAJA',
                'ESTADO',
                'EMP.DESTINO',
                'MAQ/IP. DESTINO',
                'CANAL'

            ],
            colModel: [
                {name: 'CODENVIO', index: 'CODENVIO', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'CODFICHERO', index: 'CODFICHERO', align: 'center', hidden: true},
                {name: 'CODFICHEROPADRE', index: 'CODFICHEROPADRE', align: 'center', hidden: true},
                {name: 'FICHERO_ORIGEN', index: 'FICHERO_ORIGEN', align: 'left', width: '250'},
                {name: 'FICHERO_DESTINO', index: 'FICHERO_DESTINO', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'RUTA_ORIGEN', index: 'RUTA_ORIGEN', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'UUAA', index: 'UUAA', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'CODCLASIFICACION', index: 'CODCLASIFICACION', align: 'center', hidden: true},
                {name: 'DESCLASIFICACION', index: 'DESCLASIFICACION', align: 'center', stype: 'select', searchoptions: {searchhidden: true, value: 'DESCONOCIDO:DESCONOCIDO;PUBLICA:PÚBLICA;INTERNA/DEPARTAMENTAL:INTERNA/DEPARTAMENTAL;RESTRINGIDA/CONFIDENCIAL:RESTRINGIDA/CONFIDENCIAL;SECRETA:SECRETA;ESPECIAL:ESPECIAL;CONFIDENCIAL:CONFIDENCIAL'}, hidden: true},
                {name: 'CODNIVEL_LOPD', index: 'CODNIVEL_LOPD', align: 'center', hidden: true},
                {name: 'DESNIVEL_LOPD', index: 'DESNIVEL_LOPD', align: 'center', stype: 'select', searchoptions: {searchhidden: true, value: 'DESCONOCIDO:DESCONOCIDO;ALTO:ALTO;MEDIO:MEDIO;BAJO:BAJO'}, hidden: true},
                {name: 'FILE_CODCANAL', index: 'FILE_CODCANAL', align: 'center', hidden: true},
                {name: 'FILE_MASCARA', index: 'FILE_MASCARA', align: 'left', width: '250', search: false},
                {name: 'FILE_DESCANAL', index: 'FILE_DESCANAL', align: 'center', width: '120', hidden: true},
                {name: 'FILE_CODMOTIVOBAJA', index: 'FILE_CODMOTIVOBAJA', align: 'center', hidden: true},
                {name: 'FILE_DESMOTIVOBAJA', index: 'FILE_DESMOTIVOBAJA', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'FILE_FECHA_ALTA', index: 'FILE_FECHA_ALTA', align: 'center', formatter: 'date', stype: 'text', searchoptions: {searchhidden: true, dataInit: datePick, attr: {title: 'Seleccionar Fecha'}}, hidden: true},
                {name: 'FILE_FECHA_MODIFICACION', index: 'FILE_FECHA_MODIFICACION', align: 'center', formatter: 'date', stype: 'text', searchoptions: {searchhidden: true, dataInit: datePick, attr: {title: 'Seleccionar Fecha'}}, hidden: true},
                {name: 'FILE_FECHA_BAJA', index: 'FILE_FECHA_BAJA', align: 'center', stype: 'text', searchoptions: {searchhidden: true, dataInit: datePick, attr: {title: 'Seleccionar Fecha'}}, hidden: true},
                {name: 'FILE_CODUSUARIO_ALTA', index: 'FILE_CODUSUARIO_ALTA', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'FILE_DESUSUARIO_ALTA', index: 'FILE_DESUSUARIO_ALTA', align: 'center', hidden: true},
                {name: 'FILE_CODUSUARIO_MODIFICACION', index: 'FILE_CODUSUARIO_MODIFICACION', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'FILE_DESUSUARIO_MODIFICACION', index: 'FILE_DESUSUARIO_MODIFICACION', align: 'center', hidden: true},
                {name: 'FILE_CODUSUARIO_BAJA', index: 'FILE_CODUSUARIO_BAJA', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'FILE_DESUSUARIO_BAJA', index: 'FILE_DESUSUARIO_BAJA', align: 'center', hidden: true},
                {name: 'FILE_ESTADO', index: 'FILE_ESTADO', align: 'center', stype: 'select', searchoptions: {searchhidden: true, value: 'ACTIVO:ACTIVO;BAJA:BAJA'}, hidden: true},
                {name: 'FILE_CODEMPRESA_DESTINO', index: 'FILE_CODEMPRESA_DESTINO', align: 'center', stype: 'text', searchoptions: {searchhidden: true}},
                {name: 'FILE_IP_DESTINO', index: 'FILE_IP_DESTINO', align: 'center', stype: 'text', searchoptions: {searchhidden: true}},
                {name: 'FILE_DESCANAL', index: 'FILE_DESCANAL', align: 'center', width: '120'},
            ],
            pager: jQuery('#pager2'),
            rowNum: 50,
            loadui: "disable",
            imgpath: 'css/themes/custom-theme/images',
            sortname: 'FICHERO_ORIGEN',
            sortorder: "asc",
            loadonce: false,
            caption: "Ficheros y Máscaras",
            viewrecords: true,
            multiselect: true,
            multiboxonly: true,
            recordtext: "{0} - {1} de {2}",
            emptyrecords: "Sin registros",
            beforeRequest: function()
            {
                $('#pager_center').hide();
                $('.msg-info').remove();
                if (coincidencias) {
                    $('#ficheros').jAlert("Calculando Coincidencias ...", "info");
                }
            },
            ondblClickRow: function(rowid)
            {
                $('#botonDetalleFicheros').click();
            },
            loadComplete: function() {  // seleccionando el primer registro de la tabla

                var rowData = [];
                $('#botonDetalleFicheros').hide("fast");
                $('#grupoBotonesFicheros').width('1140');
                $('#BFICHERO_ORIGEN').show();
                $('#botonBuscaFich').show();
                $('#botonBajaFicheros').show();
                var rowIds = $("#ficheros").jqGrid('getDataIDs');
                for (var i = 1; i <= rowIds.length; i++) {//iterate over each row



                    rowData = $("#ficheros").jqGrid('getRowData', i);
                    //set background style if ColumnValue == true

                    if (rowData['CODFICHEROPADRE'] != "") {

                        $("#ficheros").setCell(i, 'CODENVIO', '', {'color': 'LightSlateGray '});
                        $("#ficheros").setCell(i, 'CODENVIO', '', 'fila_mascara');
                        $("#ficheros").setCell(i, 'FICHERO_ORIGEN', '', {'color': 'LightSlateGray '});
                        $("#ficheros").setCell(i, 'FICHERO_ORIGEN', '', 'fila_mascara ');
                        $("#ficheros").setCell(i, 'FILE_DESCANAL', '', {'color': 'LightSlateGray '});
                        $("#ficheros").setCell(i, 'FILE_DESCANAL', '', 'fila_mascara');
                        $("#ficheros").setCell(i, 'FILE_MASCARA', '', {'color': 'LightSlateGray '});
                        $("#ficheros").setCell(i, 'FILE_MASCARA', '', 'fila_mascara');
                        $("#ficheros").setCell(i, 'FILE_IP_DESTINO', '', {'color': 'LightSlateGray '});
                        $("#ficheros").setCell(i, 'FILE_IP_DESTINO', '', 'fila_mascara');
                        $("#ficheros").setCell(i, 'FILE_CODEMPRESA_DESTINO', '', {'color': 'LightSlateGray '});
                        $("#ficheros").setCell(i, 'FILE_CODEMPRESA_DESTINO', '', 'fila_mascara');
                    }
                    else {
                        $("#ficheros").setCell(i, 'CODENVIO', '', 'fila_fichero');
                        $("#ficheros").setCell(i, 'FICHERO_ORIGEN', '', 'fila_fichero ');
                        $("#ficheros").setCell(i, 'FILE_DESCANAL', '', 'fila_fichero');
                        $("#ficheros").setCell(i, 'FILE_MASCARA', '', 'fila_fichero');
                        $("#ficheros").setCell(i, 'FILE_IP_DESTINO', '', 'fila_fichero');
                        $("#ficheros").setCell(i, 'FILE_CODEMPRESA_DESTINO', '', 'fila_fichero');
                    }

                    if (rowData['FILE_CODUSUARIO_BAJA'] != "") {

                        $("#ficheros").setCell(i, 'CODENVIO', '', {'color': 'red '});
                        $("#ficheros").setCell(i, 'CODENVIO', '', 'fich_baja');
                        $("#ficheros").setCell(i, 'FICHERO_ORIGEN', '', {'color': 'red '});
                        $("#ficheros").setCell(i, 'FICHERO_ORIGEN', '', 'fich_baja');
                        $("#ficheros").setCell(i, 'FILE_DESCANAL', '', {'color': 'red '});
                        $("#ficheros").setCell(i, 'FILE_DESCANAL', '', 'fich_baja');
                        $("#ficheros").setCell(i, 'FILE_MASCARA', '', {'color': 'red '});
                        $("#ficheros").setCell(i, 'FILE_MASCARA', '', 'fich_baja');
                        $("#ficheros").setCell(i, 'FILE_IP_DESTINO', '', {'color': 'red '});
                        $("#ficheros").setCell(i, 'FILE_IP_DESTINO', '', 'fich_baja');
                        $("#ficheros").setCell(i, 'FILE_CODEMPRESA_DESTINO', '', {'color': 'red '});
                        $("#ficheros").setCell(i, 'FILE_CODEMPRESA_DESTINO', '', 'fich_baja');
                    }
                    if (rowData['FILE_CODEMPRESA_DESTINO'] === 'DESCONOCIDO') {
                        rowData['FILE_CODEMPRESA_DESTINO'] = '';
                        jQuery('#ficheros').jqGrid('setRowData', i, rowData);



                    }


                } //for

                //$(".fila_mascara").toggle();
                //$(".fich_baja").toggle();
                //jQuery('#remedylist').jqGrid('setSelection', selid); //mantener selección de fila
                //jQuery('#remedylist').jqGrid('setSelection','');


                //if (rows)
                //  $("#cb_ficheros").trigger('click');




            },
            gridComplete: function() {

                $('#pager_center').show();
                $('.msg-info').remove();
                //var esbusqueda = 0;

                //esbusqueda = $('#bufferlist').getGridParam('userData').buscando;
                //if (esbusqueda == 0) {

                var recfich;
                recfich = $('#ficheros').getGridParam('records');
                if (recfich == 0) {


                    setTimeout(function() {
                        $('#ficheros').jAlert("No se encontraron registros", "info");
                    }, 1000);
                }


                //}  



            },
            onSelectRow: function() { // eventos al seleccionar filas y cargar ficheros


                $('#grupoBotonesFicheros').width('1140');
                var fila_sel = jQuery("#ficheros").getGridParam('selrow');
                var campos = jQuery("#ficheros").getRowData(fila_sel);
                var fila_selm = jQuery("#ficheros").getGridParam('selarrrow');
                //$('#BFICHERO_ORIGEN').val(campos.FICHERO_ORIGEN);
                if (campos.FILE_MASCARA != "")
                    $('#BFICHERO_ORIGEN').val(campos.FILE_MASCARA);
                if (campos.CODFICHEROPADRE == "" && fila_selm.length === 1) {
                    $('#botonDetalleFicheros').show("fast");
                    $('#botonCompararBuffer').show("fast");
                    $('#grupoBotonesFicheros').width('1140');
                }
                else {
                    $('#botonDetalleFicheros').hide("fast");
                    $('#botonCompararBuffer').hide("fast");
                    $('#grupoBotonesFicheros').width('1140');
                }
                $('#idFich').val(campos.CODFICHERO);
                $('#idEnv').val(campos.CODENVIO);
                if (campos.CODFICHEROPADRE != "") {
                    var rowData = [];
                    var rowIds = $("#ficheros").jqGrid('getDataIDs');
                    for (var i = 1; i <= rowIds.length; i++) {//iterate over each row
                        rowData = $("#ficheros").jqGrid('getRowData', i);
                        //restablecer a negro
                        if (rowData['CODFICHEROPADRE'] == "") {

                            $("#ficheros").setCell(i, 'CODENVIO', '', {'font-weight': 'normal '});
                            $("#ficheros").setCell(i, 'FICHERO_ORIGEN', '', {'font-weight': 'normal '});
                            $("#ficheros").setCell(i, 'FILE_DESCANAL', '', {'font-weight': 'normal '});
                            $("#ficheros").setCell(i, 'FILE_MASCARA', '', {'font-weight': 'normal '});
                            $("#ficheros").setCell(i, 'FILE_IP_DESTINO', '', {'font-weight': 'normal '});
                            $("#ficheros").setCell(i, 'FILE_CODEMPRESA_DESTINO', '', {'font-weight': 'normal '});
                        }

                        // cambiar color de ficheros padre  
                        if (rowData['CODFICHERO'] == campos.CODFICHEROPADRE && rowData['CODENVIO'] == campos.CODENVIO) {

                            $("#ficheros").setCell(i, 'CODENVIO', '', {'font-weight': 'Bold '});
                            $("#ficheros").setCell(i, 'FICHERO_ORIGEN', '', {'font-weight': 'Bold '});
                            $("#ficheros").setCell(i, 'FILE_DESCANAL', '', {'font-weight': 'Bold '});
                            $("#ficheros").setCell(i, 'FILE_MASCARA', '', {'font-weight': 'Bold '});
                            $("#ficheros").setCell(i, 'FILE_IP_DESTINO', '', {'font-weight': 'Bold '});
                            $("#ficheros").setCell(i, 'FILE_CODEMPRESA_DESTINO', '', {'font-weight': 'Bold '});
                        }


                    } //for

                } else {
                    var rowData = [];
                    var rowIds = $("#ficheros").jqGrid('getDataIDs');
                    for (var i = 1; i <= rowIds.length; i++) {//iterate over each row
                        rowData = $("#ficheros").jqGrid('getRowData', i);
                        //restablecer a negro
                        if (rowData['CODFICHEROPADRE'] == "") {

                            $("#ficheros").setCell(i, 'CODENVIO', '', {'font-weight': 'normal '});
                            $("#ficheros").setCell(i, 'FICHERO_ORIGEN', '', {'font-weight': 'normal '});
                            $("#ficheros").setCell(i, 'FILE_DESCANAL', '', {'font-weight': 'normal '});
                        }


                    } //for

                }
                if ($("#modalDetalleFicheros").dialog("isOpen"))
                    $("#botonDetalleFicheros").click();
                if ($("#modalDetalleRemedy").dialog("isOpen"))
                    $("#botonDetalleRemedy").click();
            }
            , onPaging: function(pgButton) {

            } //a
        }).navGrid('#pager2', {edit: false, add: false, del: false, view: false,
            beforeRefresh: function() {
                change_search_now = true;
                var carga = $('#SELCODCARGA').val();
                jQuery("#ficheros").GridUnload();
                GridFicheros(0, 0, carga, 0, vermask);
            }


        },
        {}, //  default settings for edit
                {}, //  default settings for add
                {}, // delete instead that del:false we need this
                {
                    multipleSearch: true,
                    id: 'busca',
                    width: 575,
                    sopt: ['cn', 'eq', 'bw', 'ew'],
                    closeAfterSearch: true,
                    //searchOnEnter: true,
                    // procedimientos para seleccionar una columnna por defecto en la búsqueda
                    onInitializeSearch: function() {
                        change_search_now = true;
                        changeSearchDefault($('.columns').children("select"));
                        $("#fbox_ficheros_reset").click(function() {
                            change_search_now = true;
                        });
                    },
                    afterRedraw: function() {

                        if (change_search_now) {
                            changeSearchDefault($('.columns').children("select"));
                            change_search_now = false;
                        }
                        // Add "+" button click handlers to change search column default of newest search row item
                        $("input[value='+']").each(function(inputindex) {
                            $(this).click(function() {
                                $("input[value='+']").each(function(clickedindex) {
                                    if (clickedindex == inputindex) {
                                        // Change default
                                        changeSearchDefault($(this).closest('tr').siblings(":last").children(".columns").children("select"));
                                    }
                                });
                            });
                        });
                    },
                    afterShowSearch: function() {
                        //$("#fbox_ficheros_reset").click();
                        changeSearchDefault($('.columns').children("select"));
                        $(".data").children('input').focus();
                    }
                    //------------------------------------------------------------------------- 
                }, // search options
        {} /* view parameters*/
        ).navButtonAdd('#pager2', {
            caption: "",
            title: "Eliminar Máscara",
            buttonicon: "ui-icon-trash",
            onClickButton: function() {

                $('.msg-info').remove();

                $('#ficheros').jAlert("Actualizando Registros ...", "info");
                selcalldel(reloadGridcalldel);
            },
            position: "first"
        }
        ).navButtonAdd('#pager2', {
            caption: "",
            title: "Mostrar/Ocultar Ficheros de Baja",
            buttonicon: "ui-icon-cancel",
            onClickButton: function() {

                //$(".fich_baja").toggle();
                if (baja == 0)
                    baja = 1;
                else
                    baja = 0;
                //$(".fich_baja").toggle();

                if (baja == 1) {

                    jQuery("#ficheros").GridUnload();
                    GridFicheros(0, 0, carga, baja, 1);
                } else {

                    jQuery("#ficheros").GridUnload();
                    GridFicheros(0, 0, carga, 0, 0);
                }

            },
            position: "last"
        }).navButtonAdd('#pager2', {
            caption: "",
            title: "Ver/Ocultar Máscaras",
            buttonicon: "ui-icon-link",
            onClickButton: function() {
                //if ($(".fila_mascara").is(':visible') && $(".fila_fichero").is(':visible'))
                //  $(".fila_fichero").toggle();
                //else if ($(".fila_mascara").is(':visible') && $(".fila_fichero").css('display') == 'none') {
                //  $(".fila_mascara").toggle();
                // $(".fila_fichero").toggle();
                // }
                //else if ($(".fila_fichero").is(':visible') && $(".fila_mascara").css('display') == 'none') {
                //  $(".fila_mascara").toggle();
                //}
                if (vermask == 0)
                    vermask = 1;
                else
                    vermask = 0;
                //$(".fich_baja").toggle();

                if (vermask == 1) {

                    jQuery("#ficheros").GridUnload();
                    if (!coincidencias)
                        GridFicheros(0, 0, carga, 0, vermask);
                    else
                        GridFicheros(0, 0, carga, 0, vermask, 0, desempresa, ipdest, coincidencias);
                } else {

                    jQuery("#ficheros").GridUnload();
                    if (!coincidencias)
                        GridFicheros(0, 0, carga, 0, 0);
                    else
                        GridFicheros(0, 0, carga, 0, 0, 0, desempresa, ipdest, coincidencias);
                }
            },
            position: "last"
        }
        );
    } //Fin GridFicheros


    $(document).keypress(function(e) {
        if (e.which == 13) {
            $("#fbox_ficheros_search").click();
            setTimeout(function() {
                $("#fbox_ficheros_search").click();
            }, 1000);
            $("#fbox_bufferlist_search").click();
            setTimeout(function() {
                $("#fbox_bufferlist_search").click();
            }, 1000);
            if ($("#modalSelCarga").dialog("isOpen"))
                $("#AceptarSelCarga").click();
        }
    });
    $(document).ready(function() {



       /* $("input[type=text]").keyup(function(e) {
            if (e.which >= 65) {
                
                $(this).val($(this).val().toUpperCase());
               
            }
        });*/
        $("input[type=text]").css({'text-transform':'uppercase'});
        $('#SELCODCARGA').val('');
        //GridBuffer(0, 0, 0, 0,0,1);
        //GridFicheros();
        $('#botonPruebaMascaras').toggle();
        $('#botonDefMascaras').toggle();
        $('#botonDetalleFicheros').toggle();
        $('#botonCoincidencias').toggle();
        $('#botonCompararBuffer').toggle();
        $('#botonDetalleBuffer').toggle();
        $('#BFICHERO_ORIGEN').toggle();
        $('#botonBuscaFich').toggle();
        $('#botonBajaFicheros').toggle();
        var fichmask = $.getUrlVar('fichmask');
        if (fichmask)
            $('#BFICHERO_ORIGEN').val(fichmask);
        var cargamask = $.getUrlVar('cargamask');
        if (!cargamask)
            $('#modalSelCarga').dialog('open');
        if (cargamask) {
            setTimeout(function() {
                $("#SELCODCARGA option:selected").removeAttr("selected");
                $("#SELCODCARGA option[value=" + cargamask + "]").attr("selected", "selected");
                $("#AceptarSelCarga").click();
                $('#botonPruebaMascaras').toggle();
                $('#botonDefMascaras').toggle();
                $('#botonDetalleFicheros').toggle();
                $('#botonCoincidencias').toggle();
                $('#botonCompararBuffer').toggle();
                $('#botonDetalleBuffer').toggle();
            }, 1000);
        }



        // Manejo de los botones, y ventanas modales -------------------------------------

        $("#modalDetalleBuffer").dialog({
            autoOpen: false,
            height: 660,
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
                $('#modalDetalleBuffer').dialog('option', 'position', 'center');
                $('#modalDetalleBuffer').dialog('option', 'height', 660);
            }
        });
        $('#botonDetalleBuffer').button().click(function(evento) {
            evento.preventDefault();
            var idBuf = $('#idBuff').val();
            var idCarga = $('#idCarga').val();
            if (!idBuf)
                idBuf = 0;
            if (!idCarga)
                idCarga = 0;
            //-------------------------------------------------------------------------------------

            // AJAX CONSULTA VENTANA MODAL DETALLE BUFFER



            $.ajax({
                type: "POST",
                url: 'json/detallebuffer_json.php?codcarga=' + idCarga + '&codbuf=' + idBuf,
                dataType: 'json',
                success: function(data) {

                    $('#CODBUFFER_CARGA').text('CARGA: ' + data.CODCARGA + ' - REGISTRO:' + data.CODBUFFER);
                    $('#DCODENVIO').val(data.CODENVIO);
                    $('#DCODFICHERO').val(data.CODFICHERO);
                    $('#CHKTRANSMISION_INTERNA').val(data.CHKTRANSMISION_INTERNA);
                    $('#TRANSMISION_INTERNA').val(data.TRANSMISION_INTERNA);
                    $('#CODTIPOTRANSMISION').val(data.CODTIPOTRANSMISION);
                    $('#DESTIPOTRANSMISION').val(data.DESTIPOTRANSMISION);
                    $('#CHKCONTROL').val(data.CHKCONTROL);
                    $('#DESESTADOCONTROL').val(data.DESESTADOCONTROL);
                    $('#BUFFER_CHKCIFRADO').val(data.BUFFER_CHKCIFRADO);
                    $('#BUFFER_CIFRADO').val(data.BUFFER_CIFRADO);
                    $('#CODTIPODESTINO').val(data.CODTIPODESTINO);
                    $('#DESTIPODESTINO').val(data.DESTIPODESTINO);
                    $('#MOTIVOLOG').val(data.MOTIVOLOG);
                    $('#DESEMPRESA_ORIGEN').val(data.DESEMPRESA_ORIGEN);
                    $('#DESEMPRESA_DESTINO').val(data.DESEMPRESA_DESTINO);
                    $('#BUFFER_NOMBREMAQUINA_ORIGEN').val(data.BUFFER_NOMBREMAQUINA_ORIGEN);
                    $('#BUFFER_NOMBREMAQUINA_DESTINO').val(data.BUFFER_NOMBREMAQUINA_DESTINO);
                    $('#BUFFER_IP_ORIGEN').val(data.BUFFER_IP_ORIGEN);
                    $('#BUFFER_IP_DESTINO').val(data.BUFFER_IP_DESTINO);
                    $('#BUFFER_FICHERO_ORIGEN').val(data.BUFFER_FICHERO_ORIGEN);
                    $('#BUFFER_FICHERO_DESTINO').val(data.BUFFER_FICHERO_DESTINO);
                    $('#BUFFER_FECHA_ALTA').val(data.BUFFER_FECHA_ALTA);
                    $('#BUFFER_FECHA_MODIFICACION').val(data.BUFFER_FECHA_MODIFICACION);
                    $('#BUFFER_FECHA_BAJA').val(data.BUFFER_FECHA_BAJA);
                    $('#BUFFER_CODUSUARIO_ALTA').val(data.BUFFER_CODUSUARIO_ALTA);
                    $('#BUFFER_DESUSUARIO_ALTA').val(data.BUFFER_DESUSUARIO_ALTA);
                    $('#BUFFER_CODUSUARIO_MODIFICACION').val(data.BUFFER_CODUSUARIO_MODIFICACION);
                    $('#BUFFER_DESUSUARIO_MODIFICACION').val(data.BUFFER_DESUSUARIO_MODIFICACION);
                    $('#BUFFER_CODUSUARIO_BAJA').val(data.BUFFER_CODUSUARIO_BAJA);
                    $('#BUFFER_DESUSUARIO_BAJA').val(data.BUFFER_DESUSUARIO_BAJA);
                    $('#BUFFER_ESTADO').val(data.BUFFER_ESTADO);
                    $('#JOB_USR').val(data.JOB_USR);
                    $('#USR_SUBMIT').val(data.USR_SUBMIT);
                    if ($('#BUFFER_FECHA_ALTA').val() == '0000-00-00 00:00:00')
                        $('#BUFFER_FECHA_ALTA').val('');
                },
                error: function(e) {
                    alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                }
            });
            //--------------------------------------------------------------------------------------


            $('#modalDetalleBuffer').dialog('open');
            return false;
        });
        $('#botonDefMascaras').button().click(function(evento) {
            evento.preventDefault();
            var mascara = 0;
            var msjmask = 0;
            //---Def de máscaras para multiselect

            var fila_sel_m = jQuery("#ficheros").getGridParam('selarrrow');
            var rows = new Array();
            var campos_m = new Array();
            if (fila_sel_m.length > 0) {




                for (var i = 0; i < fila_sel_m.length; i++) {

                    campos_m[i] = {};
                    campos_m[i].CODENVIO = $("#ficheros").getCell(fila_sel_m[i], 'CODENVIO');
                    campos_m[i].CODFICHERO = $("#ficheros").getCell(fila_sel_m[i], 'CODFICHERO');
                    campos_m[i].FILE_CODCANAL = $("#ficheros").getCell(fila_sel_m[i], 'FILE_CODCANAL');
                    campos_m[i].FILE_DESCANAL = $("#ficheros").getCell(fila_sel_m[i], 'FILE_DESCANAL');
                    campos_m[i].FILE_CODUSUARIO_BAJA = $("#ficheros").getCell(fila_sel_m[i], 'FILE_CODUSUARIO_BAJA');
                    campos_m[i].FILE_MASCARA = $("#ficheros").getCell(fila_sel_m[i], 'FILE_MASCARA');
                    campos_m[i].CODFICHEROPADRE = $("#ficheros").getCell(fila_sel_m[i], 'CODFICHEROPADRE');
                    rows[i] = JSON.stringify($("#ficheros").jqGrid('getRowData', fila_sel_m[i]));
                    //-------modificar    
                    if (campos_m[i].CODFICHEROPADRE == "") {
                        if (!(campos_m[i].FILE_CODUSUARIO_BAJA == "")) {

                            msjmask = 'Ha seleccionado un Fichero que está de baja, no se pueden definir máscaras de ficheros inactivos';
                        }
                        if (campos_m[i].FILE_MASCARA != "") {
                            msjmask = 'Ha seleccionado un Fichero que ya tiene una máscara definida, seleccione un fichero o grupo de ficheros padre, para los cuales no se han definido máscaras';
                        }


                    }
                    else  // si la selección es una fila que define una máscara y no un fichero padre
                        msjmask = 'Ha seleccionado una máscara, por favor seleccione el(los) fichero(s) padre para definir una nueva máscara';
                } // fin for multiselect


                if (!msjmask) {

                    $('.msg-info').remove();

                    $('#ficheros').jAlert("Actualizando Registros ...", "info");
                    mascara = $('#BFICHERO_ORIGEN').val();
                    var carga = $('#SELCODCARGA').val();
                    var vartmp;
                    vartmp = JSON.stringify(campos_m);
                    var postArray = {json: vartmp};
                    $.ajax({
                        type: "POST",
                        url: 'edit/mascara_ed.php?mascara=' + encodeURIComponent(mascara), //  '',
                        dataType: "json",
                        data: postArray,
                        success: function(data) {

                            if (!data.ERROR) {


                                mensaje(data.SUCCESS, 'titulo');
                                if (data.NMASK)
                                {
                                    //-------------Filtrado de grids según máscara


                                    var carga = $('#SELCODCARGA').val();
                                    var gridData;
                                    var gridDataB;
                                    var vartmp;
                                    vartmp = JSON.stringify(campos_m);
                                    var postArray = {json: vartmp};
                                    var sqlcomp = $("#ficheros").getGridParam('userData').SQL;
                                    var sqlorder = $("#ficheros").getGridParam('userData').order;
                                    var pageact = $("#ficheros").getGridParam('page');
                                    var recordsact = $("#ficheros").getGridParam('records');
                                    var ndact = $("#ficheros").jqGrid('getGridParam', 'postData').nd;
                                    var sidxact = $("#ficheros").jqGrid('getGridParam', 'postData').sidx;
                                    var sordact = $("#ficheros").jqGrid('getGridParam', 'postData').sord;
                                    var postD = {sqldel: sqlcomp, sqlorder: sqlorder};

                                    reloadGridcalldel();


                                    $.ajax({
                                        type: "POST",
                                        url: 'json/buffer_json.php?coincidencias=0&mascara=' + encodeURIComponent(mascara) + '&codcanal=0&codcarga=' + carga + '&codenv=0&agrupa=1&codbuffermask=0',
                                        datatype: "text",
                                        async: false,
                                        data: postArray,
                                        success: function(data) {
                                            gridDataB = data;
                                        },
                                        error: function(e) {
                                            alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                                        }
                                    });
                                    $("#bufferlist").setGridParam({
                                        datastr: gridDataB,
                                        datatype: "jsonstring" // !!! reset datatype
                                    }).trigger("reloadGrid");
                                    //-----------------Fin filtrado grids según def máscaras------------------------------
                                }

                            }
                            else {

                                $('#titulo').jAlert(data.ERROR, "fatal");
                                setTimeout(function() {
                                    $('.msg-fatal').fadeOut(500, function() {
                                        $('.msg-fatal').remove();
                                    });
                                }, 10000);
                            }





                        },
                        error: function(e) {
                            alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                        }
                    });
                } else
                    alert(msjmask);
            } else
                alert('Por favor seleccione el(los) fichero(s) padre para la máscara');
            //-----------fin def de máscaras para multiselect

        });
        $('#botonPruebaMascaras').button().click(function(evento) {
            evento.preventDefault();
            var mascara = 0;
            var msjmask = 0;
            //---Prueba de máscaras para multiselect

            var fila_sel_m = jQuery("#ficheros").getGridParam('selarrrow');
            var rows = new Array();
            if (fila_sel_m.length > 0) {



                var campos_m = new Array();
                for (var i = 0; i < fila_sel_m.length; i++) {
                    campos_m[i] = {};
                    campos_m[i].CODENVIO = $("#ficheros").getCell(fila_sel_m[i], 'CODENVIO');
                    rows[i] = JSON.stringify($("#ficheros").jqGrid('getRowData', fila_sel_m[i]));
                } // fin for multiselect

                if (!msjmask) {
                    var gridData;
                    var gridDataB;
                    mascara = $('#BFICHERO_ORIGEN').val();
                    var carga = $('#SELCODCARGA').val();
                    var vartmp;
                    vartmp = JSON.stringify(campos_m);
                    var postArray = {json: vartmp};
                    $.ajax({
                        type: "POST",
                        url: 'json/ficheros_json.php?mascara=' + encodeURIComponent(mascara) + '&codcanal=0&vermask=0&carga=' + carga + '&baja=0&codenv=0&coincidencias=0&ipdest=0&desempresa=0',
                        datatype: "text",
                        async: false,
                        data: postArray,
                        success: function(data) {
                            gridData = data;
                        },
                        error: function(e) {
                            alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                        }
                    });
                    $("#ficheros").setGridParam({
                        datastr: gridData,
                        datatype: "jsonstring" // !!! reset datatype
                    }).trigger("reloadGrid");

                    $.ajax({
                        type: "POST",
                        url: 'json/buffer_json.php?coincidencias=0&mascara=' + encodeURIComponent(mascara) + '&codcanal=0&codcarga=' + carga + '&codenv=0&agrupa=1&codbuffermask=0',
                        datatype: "text",
                        async: false,
                        data: postArray,
                        success: function(data) {
                            gridDataB = data;
                        },
                        error: function(e) {
                            alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                        }
                    });
                    $("#bufferlist").setGridParam({
                        datastr: gridDataB,
                        datatype: "jsonstring" // !!! reset datatype
                    }).trigger("reloadGrid");
                    $("#cb_ficheros").trigger('click');
                } else
                    alert(msjmask);
            }
            //-----------fin prueba de máscaras para multiselect

            else
                alert('Seleccione al menos un Fichero Padre o una máscara para ejecutar la prueba');
        });
        $('#botonCoincidencias').button().click(function(evento) {
            evento.preventDefault();
            var codbuffermask = $.getUrlVar('codbuffermask');
            var carga = $('#SELCODCARGA').val();
            var fila_sel = jQuery("#bufferlist").getGridParam('selrow');
            var campos = jQuery("#bufferlist").getRowData(fila_sel);
            if (fila_sel) {
                //if(carga=prompt('Introducir el Código de carga de Log para el cual se desean obtener coincidencias\n con el nombre de fichero: ' +campos.FICHERO_ORIGEN)){    
                jQuery("#ficheros").GridUnload();
                var ipdest = campos.BUFFER_NOMBREMAQUINA_DESTINO;
                if (!ipdest)
                    ipdest = campos.BUFFER_IP_DESTINO;
                GridFicheros(0, 0, carga, 0, 0, 0, campos.DESEMPRESA_DESTINO, ipdest, campos.BUFFER_FICHERO_ORIGEN);
                //}
            } else
            if (!codbuffermask)
                alert('Seleccione el fichero para el cual desea obtener coincidencias\n de posición de caracteres ');
        });
        $('#botonBuscaFich').button().click(function(evento) {
            evento.preventDefault();
            var bval = $('#BFICHERO_ORIGEN').val();
            //búsqueda en ficheros
            $('#busca').click();
            $(".data").children('input').val(bval);
            setTimeout(function() {
                $('#fbox_ficheros_search').click();
                $('.ui-icon-closethick').click();
            }, 1000);
            //----
            //búsqueda en tbuffer
            setTimeout(function() {
                $('#busca2').click();
                $(".data").children('input').val(bval);
                setTimeout(function() {
                    $('#fbox_bufferlist_search').click();
                    $('.ui-icon-closethick').click();
                }, 1000);
            }, 1000);
            //------


        });
        $('#botonCompararBuffer').button().click(function(evento) {
            evento.preventDefault();
            var fila_sel = jQuery("#ficheros").getGridParam('selrow');
            var fila_sel2 = jQuery("#bufferlist").getGridParam('selrow');
            if (fila_sel && fila_sel2) {

                $('#botonDetalleBuffer').click();
                $('#botonDetalleFicheros').click();
                $('#modalDetalleBuffer').dialog('option', 'height', 650);
                $('#modalDetalleFicheros').dialog('option', 'height', 650);
                $('#modalDetalleRemedy').dialog('option', 'height', 650);
                $('#modalDetalleRemedy').dialog('option', 'height', 650);
                $('#modalDetalleRemedy').dialog('option', 'width', 340);
                $('#modalDetalleRemedy').dialog('option', 'position', [348, 29]);
                $('#modalDetalleFicheros').dialog('option', 'position', [685, 29]);
                $('#modalDetalleBuffer').dialog('option', 'position', [1032, 29]);
            } else
                alert('Seleccione un fichero y un registro de log para comparar');
            return false;
        });
        $('#botonBajaFicheros').button().click(function(evento) {
            evento.preventDefault();
            var fila_selb = jQuery("#ficheros").getGridParam('selarrrow');
            if (fila_selb.length > 0) {

               // if (confirm('Se dispone a dar de baja la selección de ficheros. ¿Desea Continuar? ')) {

                    $('#opcion4').val("borrar");
                    var mot = prompt('Introducir Motivo de Baja:');
                    if (mot == '')
                        mot = 'FIN AUTORIZACION DE LA CESION';
                    $('#NCODMOTIVOBAJA').val(mot);

                    bajamultiple(reloadGridFich);

                    //---- fin multi

               // }
            } else
                alert("Seleccione al menos un fichero a dar de baja");
            return false;
        });
        $("#modalDetalleRemedy").dialog({
            autoOpen: false,
            height: 650,
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
            }
        });
        $('#botonDetalleRemedy').button().click(function(evento) {
            evento.preventDefault();
            var codenv = $('#idEnv').val();
            //-------------------------------------------------------------------------------------

            // AJAX CONSULTA VENTANA MODAL DETALLE REMEDY
            if (!codenv)
                codenv = 0;
            $.ajax({
                type: "POST",
                url: 'json/detalleremedy_json.php?codenvio=' + codenv + '',
                dataType: 'json',
                success: function(data) {

                    $('#CODENVIO').text('ENVÍO: ' + data.CODENVIO + '');
                    $('#CODTIPOENVIO').val(data.CODTIPOENVIO);
                    $('#DESTIPOENVIO').val(data.DESTIPOENVIO);
                    $('#CODENVIO_REMEDY').val(data.CODENVIO_REMEDY);
                    $('#CODAUTORIZA').val(data.CODAUTORIZA);
                    $('#DESAUTORIZA').val(data.DESAUTORIZA);
                    $('#EMAILAUTORIZA').val(data.EMAILAUTORIZA);
                    $('#CODEMPRESA').val(data.CODEMPRESA);
                    $('#DESEMPRESA').val(data.DESEMPRESA);
                    $('#CODINTERVINIENTE').val(data.CODINTERVINIENTE);
                    $('#DESINTERVINIENTE').val(data.DESINTERVINIENTE);
                    $('#CODDESTINATARIO').val(data.CODDESTINATARIO);
                    $('#DESDESTINATARIO').val(data.DESDESTINATARIO);
                    $('#CODFRECUENCIA').val(data.CODFRECUENCIA);
                    $('#DESFRECUENCIA').val(data.DESFRECUENCIA);
                    $('#CODCANAL').val(data.CODCANAL);
                    $('#DESCANAL').val(data.DESCANAL);
                    $('#NOMBREMAQUINA_ORIGEN').val(data.NOMBREMAQUINA_ORIGEN);
                    $('#IP_ORIGEN').val(data.IP_ORIGEN);
                    $('#IP_DESTINO').val(data.IP_DESTINO);
                    $('#CHKCIFRADO').val(data.CHKCIFRADO);
                    $('#CIFRADO').val(data.CIFRADO);
                    $('#MOTIVOENVIO').val(data.MOTIVOENVIO);
                    $('#CODMOTIVOBAJA').val(data.CODMOTIVOBAJA);
                    $('#DESMOTIVOBAJA').val(data.DESMOTIVOBAJA);
                    //var fecha=data.ENVIO_FECHA_ALTA;
                    //var fecha_sub=fecha.substring(0,10);
                    //var parsedDate = $.datepicker.parseDate('yy-mm-dd', fecha_sub);
                    //$('#ENVIO_FECHA_ALTA').val($.datepicker.formatDate('dd-mm-yy', parsedDate));
                    $('#ENVIO_FECHA_ALTA').val(data.ENVIO_FECHA_ALTA);
                    //fecha=data.ENVIO_FECHA_MODIFICACION;
                    //fecha_sub=fecha.substring(0,10);
                    //parsedDate = $.datepicker.parseDate('yy-mm-dd', fecha_sub);
                    //$('#ENVIO_FECHA_MODIFICACION').val($.datepicker.formatDate('dd-mm-yy', parsedDate));
                    $('#ENVIO_FECHA_MODIFICACION').val(data.ENVIO_FECHA_MODIFICACION);
                    //fecha=data.ENVIO_FECHA_BAJA;
                    //fecha_sub=fecha.substring(0,10);
                    //parsedDate = $.datepicker.parseDate('yy-mm-dd', fecha_sub);
                    //$('#ENVIO_FECHA_BAJA').val($.datepicker.formatDate('dd-mm-yy', parsedDate));    
                    $('#ENVIO_FECHA_BAJA').val(data.ENVIO_FECHA_BAJA);
                    $('#ENVIO_CODUSUARIO_ALTA').val(data.ENVIO_CODUSUARIO_ALTA);
                    $('#ENVIO_DESUSUARIO_ALTA').val(data.ENVIO_DESUSUARIO_ALTA);
                    $('#ENVIO_CODUSUARIO_MODIFICACION').val(data.ENVIO_CODUSUARIO_MODIFICACION);
                    $('#ENVIO_DESUSUARIO_MODIFICACION').val(data.ENVIO_DESUSUARIO_MODIFICACION);
                    $('#ENVIO_CODUSUARIO_BAJA').val(data.ENVIO_CODUSUARIO_BAJA);
                    $('#ENVIO_DESUSUARIO_BAJA').val(data.ENVIO_DESUSUARIO_BAJA);
                    $('#DEOBSERVACIONES').val(data.OBSERVACIONES);
                    $('#DESCONTACTO').val(data.DESCONTACTO);
                    if ($('#DESCONTACTO').val() == 'false')
                        $('#DESCONTACTO').val('');
                    if ($('#CODAUTORIZA').val() == '0000000')
                        $('#CODAUTORIZA').val('');
                    if ($('#ENVIO_FECHA_BAJA').val() == '00-00-0000 00:00:00')
                        $('#ENVIO_FECHA_BAJA').val('');
                    if ($('#ENVIO_FECHA_ALTA').val() == '00-00-0000 00:00:00')
                        $('#ENVIO_FECHA_ALTA').val('');
                    if ($('#ENVIO_FECHA_MODIFICACION').val() == '00-00-0000 00:00:00')
                        $('#ENVIO_FECHA_MODIFICACION').val('');
                    if (!data.ENVIO_CODUSUARIO_BAJA) {
                        $('#ENVIO_FECHA_BAJA').hide();
                        $('#CODMOTIVOBAJA').hide();
                        $('#DESMOTIVOBAJA').hide();
                        $('#ENVIO_CODUSUARIO_BAJA').hide();
                        $('#ENVIO_DESUSUARIO_BAJA').hide();
                        $('.baja').hide();
                    } else
                    {
                        $('#ENVIO_FECHA_BAJA').show();
                        //$('#CODMOTIVOBAJA').show();
                        $('#DESMOTIVOBAJA').show();
                        $('#ENVIO_CODUSUARIO_BAJA').show();
                        $('#ENVIO_DESUSUARIO_BAJA').show();
                        $('.baja').show();
                    }

                    if ($('#DESMOTIVOBAJA').val() == '00000')
                        $('#DESMOTIVOBAJA').val('');
                    if ($('#DESMOTIVOBAJA').val() == '00002')
                        $('#DESMOTIVOBAJA').val('FIN AUTORIZACIÓN DE LA CESION');
                    if ($('#DESMOTIVOBAJA').val() == '00001')
                        $('#DESMOTIVOBAJA').val('BAJA EN LA MIGRACIÓN');
                },
                error: function(e) {
                    alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                }
            });
            //--------------------------------------------------------------------------------------

        });
        $("#modalDetalleFicheros").dialog({
            autoOpen: false,
            height: 650,
            width: 340,
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
        $('#botonDetalleFicheros').button().click(function(evento) {
            evento.preventDefault();
            var idFich = $('#idFich').val();
            var codenv = $('#idEnv').val();
            //$("#clonarEnvio tr:odd").css("background-color", "#ddd"); // filas impares
            // $("#clonarEnvio tr:even").css("background-color", "#ccc"); // filas pares





            //-------------------------------------------------------------------------------------

            // AJAX CONSULTA VENTANA MODAL DETALLE FICHEROS
            if (!codenv)
                codenv = 0;
            if (!idFich)
                idFich = 0;
            $.ajax({
                type: "POST",
                url: 'json/detalleficheros_json.php?codfich=' + idFich + '&codenv=' + codenv,
                dataType: 'json',
                success: function(data) {

                    $('#CODFICHERO').text('FICHERO: ' + data.CODFICHERO + '');
                    //$('#CODENVIO').val(data.CODENVIO);
                    //$('#CODFICHEROPADRE').val(data.CODFICHEROPADRE);
                    $('#FICHERO_ORIGEN').val(data.FICHERO_ORIGEN);
                    $('#FICHERO_DESTINO').val(data.FICHERO_DESTINO);
                    $('#RUTA_ORIGEN').val(data.RUTA_ORIGEN);
                    $('#UUAA').val(data.UUAA);
                    //$('#CODCLASIFICACION').val(data.CODCLASIFICACION);
                    $('#DESCLASIFICACION').val(data.DESCLASIFICACION);
                    //$('#CODNIVEL_LOPD').val(data.CODNIVEL_LOPD);
                    $('#DESNIVEL_LOPD').val(data.DESNIVEL_LOPD);
                    $('#FILE_CODCANAL').val(data.FILE_CODCANAL);
                    $('#FILE_DESCANAL').val(data.FILE_DESCANAL);
                    $('#FILE_CODMOTIVOBAJA').val(data.FILE_CODMOTIVOBAJA);
                    $('#FILE_DESMOTIVOBAJA').val(data.FILE_DESMOTIVOBAJA);
                    //var fecha=data.FILE_FECHA_ALTA;
                    //var fecha_sub=fecha.substring(0,10);
                    //var parsedDate = $.datepicker.parseDate('yy-mm-dd', fecha_sub);
                    //$('#FILE_FECHA_ALTA').val($.datepicker.formatDate('dd-mm-yy', parsedDate));
                    $('#FILE_FECHA_ALTA').val(data.FILE_FECHA_ALTA);
                    //fecha=data.FILE_FECHA_MODIFICACION;
                    //fecha_sub=fecha.substring(0,10);
                    //parsedDate = $.datepicker.parseDate('yy-mm-dd', fecha_sub);
                    //$('#FILE_FECHA_MODIFICACION').val($.datepicker.formatDate('dd-mm-yy', parsedDate));
                    $('#FILE_FECHA_MODIFICACION').val(data.FILE_FECHA_MODIFICACION);
                    //fecha=data.FILE_FECHA_BAJA;
                    //fecha_sub=fecha.substring(0,10);
                    //parsedDate = $.datepicker.parseDate('yy-mm-dd', fecha_sub);
                    //$('#FILE_FECHA_BAJA').val($.datepicker.formatDate('dd-mm-yy', parsedDate));
                    $('#FILE_FECHA_BAJA').val(data.FILE_FECHA_BAJA);
                    $('#FILE_CODUSUARIO_ALTA').val(data.FILE_CODUSUARIO_ALTA);
                    $('#FILE_DESUSUARIO_ALTA').val(data.FILE_DESUSUARIO_ALTA);
                    $('#FILE_CODUSUARIO_MODIFICACION').val(data.FILE_CODUSUARIO_MODIFICACION);
                    $('#FILE_DESUSUARIO_MODIFICACION').val(data.FILE_DESUSUARIO_MODIFICACION);
                    $('#FILE_CODUSUARIO_BAJA').val(data.FILE_CODUSUARIO_BAJA);
                    $('#FILE_DESUSUARIO_BAJA').val(data.FILE_DESUSUARIO_BAJA);
                    $('#FILE_ESTADO').val(data.FILE_ESTADO);
                    $('#DFOBSERVACIONES').val(data.OBSERVACIONES);
                    if ($('#FILE_FECHA_ALTA').val() == '00-00-0000 00:00:00')
                        $('#FILE_FECHA_ALTA').val('');
                    if ($('#FILE_FECHA_MODIFICACION').val() == '00-00-0000 00:00:00')
                        $('#FILE_FECHA_MODIFICACION').val('');
                    if ($('#FILE_FECHA_BAJA').val() == '00-00-0000 00:00:00')
                        $('#FILE_FECHA_BAJA').val('');
                    if ($('#FILE_DESMOTIVOBAJA').val() == '00000')
                        $('#FILE_DESMOTIVOBAJA').val('');
                    if ($('#FILE_DESMOTIVOBAJA').val() == '00002')
                        $('#FILE_DESMOTIVOBAJA').val('FIN AUTORIZACIÓN DE LA CESION');
                    if ($('#FILE_DESMOTIVOBAJA').val() == '00001')
                        $('#FILE_DESMOTIVOBAJA').val('BAJA EN LA MIGRACIÓN');
                    if (!data.FILE_CODUSUARIO_BAJA) {
                        //$('#FILE_FECHA_BAJA').hide();
                        //$('#FILE_CODMOTIVOBAJA').hide();
                        //$('#FILE_DESMOTIVOBAJA').hide();
                        //$('#FILE_CODUSUARIO_BAJA').hide();
                        //$('#FILE_DESUSUARIO_BAJA').hide();
                        //$('.bajaf').hide();
                    }



                },
                error: function(e) {
                    alert("Error en el servidor, por favor, intentalo de nuevo más tarde");
                }
            });
            //--------------------------------------------------------------------------------------
            if (!($("#modalDetalleRemedy").dialog("isOpen") || $("#modalDetalleFicheros").dialog("isOpen"))) {


                $('#botonDetalleRemedy').click();
                $('#modalDetalleRemedy').dialog('option', 'height', 650);
                $('#modalDetalleRemedy').dialog('option', 'width', 340);
                $('#modalDetalleRemedy').dialog('option', 'position', [498, 29]);
                $('#modalDetalleFicheros').dialog('option', 'position', [835, 29]);
                $('#modalDetalleFicheros').dialog('open');
                $('#modalDetalleRemedy').dialog('open');
            }




        });
        $("li").css({background: "#4875e8"});
        $("#util").css({background: "#77c5e1"}); //seleccionar opción actual del menú principal









    });






</script>