<?
include_once('../functions/db.php');
$link = conectar();
?>
<!--- Mensajes -->


<!--- Fin Mensajes -->

<!-- ventanas Modales -->

<!-- Modal Detalle remedy -->
<?
include_once('../view/modal/detalleremedy_modal.php');
?>
<!-- Modal Seleccionar Carga -->

<?
include_once('../view/modal/selcarga_archivo_modal.php');
?>

<!-- Modal Seleccionar Envío -->

<?
include_once('../view/modal/selenvio_modal.php');
?>

<!-- Modal Detalle Buffer -->
<?
include_once('../view/modal/detallebuffer_modal.php');
?>
<!-- Modal HISToRICO Buffer -->
<?
include_once('../view/modal/detallehistorico_modal.php');
?>
<!-- Modal Comentarios Buffer -->
<?
include_once('../view/modal/comentarios_modal.php');
?>
<!-- Modal Editar Fichero -->
<?
//include_once('../view/modal/ficheros_modal.php');
?>
<!-- Modal Editar Remedy -->
<?
include_once('../view/modal/remedy_modal_2.php');
?>

<!-- Fin Ventanas Modales -->

<!--<div align="center" id="titulo" class="titulo2">Análisis de Consolidación</div>-->

<!--- Tabla de Cargas Buffer -->
<div class="unaCol" align="center" style="height: 600px" >

    <table id="bufferlist" class="scroll"><tr><td></td></tr></table>
    <div id="pager" class="scroll" style=""></div>

    <!--- Fin Tabla de de Cargas Buffer -->

    <!-- Botones acciones listado Buffer -->

    <div id="grupoBotonesBuffer" class="" style='width: 1140px; '>
        <table width='1145' style="padding-top:10px;">
            <tr><td align='right'>
                    <input type="hidden" id="idBuff" name="idBuff" />
                    <input type="hidden" id="idCarga" name="idCarga" />
                    <!--
                    <input id="botonEstadoBuffer" type="submit" value="Editar" class="boton ui-corner-all"
                           style="width:115px; margin-bottom:9px;" />
                    <input id="botonExportarBuffer" type="submit" value="Exportar" class="boton ui-corner-all"
                           style="width:115px; margin-bottom:9px;" />
                    -->
                   <input id="botonDetalleHistorico" type="submit" value="Histórico" class="boton ui-corner-all"
                           style="width:115px; margin-bottom:9px;" />
                    <input id="botonComentarios" type="submit" value="Comentarios" class="boton ui-corner-all"
                           style="width:115px; margin-bottom:9px;" />
                    <input id="botonDetalleBuffer" type="submit" value="Ver Detalles" class="boton ui-corner-all"
                           style="width:115px; margin-bottom:9px;" />
                    <!--<input id="botonMascaras" type="submit" value="Máscaras" class="boton ui-corner-all"
                           style="width:115px; margin-bottom:9px;" />-->
                    <!--<input id="botonS" type="submit" value="S" class="boton ui-corner-all"
                           style="width:115px; margin-bottom:9px;" />-->
                    <!--<input id="botonE" type="submit" value="E" class="boton ui-corner-all"
                           style="width:115px; margin-bottom:9px;" />-->
                    <!--<input id="botonN" type="submit" value="N" class="boton ui-corner-all"
                           style="width:115px; margin-bottom:9px;" />-->
                    <!--<input id="botonD" type="submit" value="D" class="boton ui-corner-all"
                           style="width:115px; margin-bottom:9px;" />-->



                    <!-- Para el control de pulsación de los botones usaremos otro input oculto -->
                    <input id="opcionA" type="hidden" name="opcionA" value="nuevo" />
                </td>
            </tr>
        </table>

    </div>
    <!-- FIN Botones acciones listado Buffer -->
    <a id="openfich" href="exporta.php" style='display: none' >Descarga</a>
</div>


<script type="text/javascript">

    $(document).ready(function() {

        $("#control").attr("class", "sfHover");
        // Configuración de Formulario Jquery Ajax de Carga
        var options = {
            //target: '#mensajeRemedy', // target element(s) to be updated with server response
            dataType: 'json',
            clearForm: false, // clear all form fields after successful submit
            resetForm: false, // reset the form after successful submit

            success: formSuccess

        };
        // bind form using 'ajaxForm'


        $('#remedyForm').ajaxForm(options);
    });
    function formSuccess(data) {


        if (!data.ERROR) {

            $('#modalremedy').dialog('close');
            //----Esto va en la ventana de datos adicionales de nuevo envío no de edición (llamada a buffer_ed)

            var fila_sel = jQuery("#bufferlist").getGridParam('selarrrow');
            if (fila_sel.length) {
                var campos_m = new Array();
                var rows = new Array();
                for (var i = 0; i < fila_sel.length; i++) {
                    campos_m[i] = $("#bufferlist").jqGrid('getRowData', fila_sel[i]);
                    rows[i] = JSON.stringify($("#bufferlist").jqGrid('getRowData', fila_sel[i]));
                }

                var vartmp = JSON.stringify(campos_m);
                var postArray = {json: vartmp};
                $.ajax({
                    type: "POST",
                    url: "edit/buffer_ed_2.php?CHKCONTROL=S",
                    dataType: "json",
                    data: postArray,
                    success: showResponse
                });
            }
            //------- hasta aquí ventana de datos adicionales de nuevo envío

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



    datePick = function(elem)
    {
        jQuery(elem).datepicker({dateFormat: "yy-mm-dd"});
    }

    // post-submit callback
    function showResponse(data) { //respuesta de envios
        $('#cData').click();
        $('#idFich').val('000001');
        $('#edfichSel').val('000001');
        $('#idEnv').val(data.codenv);
        $('#edenvSel').val(data.codenv);
        $('#edremEnv').val(data.codenv);
        //titulo de ventanas
        //alto height de ventanas
        if (data.error != "") {

            mensaje(data.error, 'titulo');
        } else {
            //mensaje(data.mensaje, 'titulo'); // este mensaje solo deberá salir si no aparece la ventana de actualización de envío o después que se actualice el envío
            //jQuery("#bufferlist").GridUnload();
            //GridBuffer();
            $('.msg-fatal').remove();
            jQuery("#bufferlist").trigger("reloadGrid");
        }
    }


    // Change serach default from selection option to the one with value = '3'
    function changeSearchDefault(select) {

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


    function GridBuffer(var1, var2, var3, carga, var4, agrupa) { // esta función crea el grid y filtra la tabla según parámetros

        var tit = $('#SELCODCARGA_ARCHIVO option:selected').text();
        var titagrupa
        if (!carga)
            carga = 0;
        if (!agrupa) {
            agrupa = 0;
            titagrupa = "Agrupar Registros Duplicados ";
        } else
            titagrupa = "Ver Registros Duplicados ";
        var selid;
        var codcarga;
        var scrollPos;
        var change_search_now = false;
        jQuery("#bufferlist").jqGrid({
            url: 'json/buffer_archivo_json.php?codcarga=' + carga + '&agrupa=' + agrupa,
            width: 1140,
            height: 530,
            datatype: "json",
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
                'BUFFER_FICHERO_ORIGEN',
                'JOB USR',
                'USR ENVIO'
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
                {name: 'BUFFER_IP_DESTINO', index: 'BUFFER_IP_DESTINO', align: 'center', width: 160, stype: 'text', searchoptions: {searchhidden: true}},
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
                {name: 'BUFFER_FICHERO_ORIGEN', index: 'BUFFER_FICHERO_ORIGEN', align: 'center', hidden: true, editable: true},
                {name: 'JOB_USR', index: 'JOB_USR', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'USR_SUBMIT', index: 'USR_SUBMIT', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true}

            ],
            pager: jQuery('#pager'),
            rowNum: 50,
            loadui: "disable",
            imgpath: 'css/themes/custom-theme/images',
            sortname: 'CODCARGA desc, BUFFER_FICHERO_ORIGEN',
            sortorder: "asc",
            loadonce: false,
            viewrecords: true,
            multiselect: true,
            multiboxonly: true,
            multiselectWidth: 40,
            caption: "" + tit.toUpperCase() + "" + '<span  class="ui-paging-info" style="margin-left:270px; color:white; font-size: 15px; white-space: nowrap;"><strong>CARGAS ARCHIVADAS</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong style="color: black; " >S: </strong><span id="contregs" style="color: black; "></span><strong style="color: DarkBlue; ">  &nbsp;&nbsp;E: </strong><span id="contrege" style="color: DarkBlue; "></span><strong style="color: #A60808; ">  &nbsp;&nbsp;N: </strong><span id="contregn" style="color: #A60808; "></span><strong style="color: DarkGreen; ">  &nbsp;&nbsp;D: </strong><span id="contregd" style="color: DarkGreen; " ></span></span>',
            shrinkToFit: false,
            beforeRequest: function()
            {
                $("#contregs").empty();
                $("#contrege").empty();
                $("#contregn").empty();
                $("#contregd").empty();
            },
            ondblClickRow: function(rowid)
            {
                $('#botonDetalleBuffer').click();
            },
            onSelectRow: function() { // eventos al seleccionar filas y cargar ficheros
                $(".quitar").remove();
                var fila_sel = jQuery("#bufferlist").getGridParam('selrow');
                var campos = jQuery("#bufferlist").getRowData(fila_sel);
                var ocultaS = 0;
                var ocultaE = 0;
                var ocultaN = 0;
                var ocultaD = 0;
                var selS = 0;
                var ocultos = 0;
                if (campos.CHKCONTROL == 'S') {
                    $('#botonS').hide("fast");
                    $('#botonE').hide("fast");
                    $('#botonN').hide("fast");
                    $('#botonD').hide("fast");
                    ocultaS++;
                    ocultaE++;
                    ocultaN++;
                    ocultaD++;
                    selS++;
                }
                if (campos.CHKCONTROL == 'E') {

                    $('#botonE').hide("fast");
                    $('#botonComentarios').show("fast");
                    ocultaE++;
                }
                if (campos.CHKCONTROL == 'N') {

                    $('#botonN').hide("fast");
                    ocultaN++;
                }
                if (campos.CHKCONTROL == 'D') {

                    $('#botonD').hide("fast");
                    ocultaD++;
                }

                $('#idBuff').val(campos.CODBUFFER);
                $('#idCarga').val(campos.CODCARGA);
                scrollPos = jQuery("#bufferlist").closest(".ui-jqgrid-bdiv").scrollTop();
                var fila_sel_m = jQuery("#bufferlist").getGridParam('selarrrow');
                var campos_m = new Array();
                if (fila_sel_m.length) {
                    if (fila_sel_m.length == 1) {
                        $('#botonDetalleBuffer').show("fast");
                        $('#botonDetalleHistorico').show("fast");
                        $('#botonMascaras').show("fast");
                        var fila_sele = jQuery("#bufferlist").getGridParam('selrow');
                        var campose = jQuery("#bufferlist").getRowData(fila_sele);
                        if (campose.CHKCONTROL == 'E') {
                            $('#botonComentarios').show("fast");
                            if ($("#modalComentarios").dialog("isOpen"))
                                $("#botonComentarios").click();
                        } else {
                            $('#botonComentarios').hide("fast");
                            $("#modalComentarios").dialog("close");
                        }
                    }
                    else {
                        $('#modalDetalleHistorico').dialog('close');
                        $('#modalComentarios').dialog('close');
                        $('#botonDetalleBuffer').hide("fast");
                        $('#botonDetalleHistorico').hide("fast");
                        $('#botonComentarios').hide("fast");
                        $("#modalComentarios").dialog("close");
                        $('#botonMascaras').hide("fast");
                        for (var i = 0; i < fila_sel_m.length; i++) {

                            campos_m[i] = $("#bufferlist").jqGrid('getRowData', fila_sel_m[i]);
                            if (campos_m[i].CHKCONTROL == 'S') {
                                $('#botonS').hide("fast");
                                $('#botonE').hide("fast");
                                $('#botonN').hide("fast");
                                $('#botonD').hide("fast");
                                ocultaS++;
                                ocultaE++;
                                ocultaN++;
                                ocultaD++;
                                selS++;
                            }
                            if (campos_m[i].CHKCONTROL == 'E') {

                                $('#botonE').hide("fast");
                                ocultaE++;
                            }
                            if (campos_m[i].CHKCONTROL == 'N') {

                                $('#botonN').hide("fast");
                                ocultaN++;
                            }
                            if (campos_m[i].CHKCONTROL == 'D') {

                                $('#botonD').hide("fast");
                                ocultaD++;
                            }
                            for (var y = i - 1; y >= 0; y--) {

                                if (campos_m[i].BUFFER_IP_DESTINO != campos_m[y].BUFFER_IP_DESTINO || campos_m[i].BUFFER_NOMBREMAQUINA_DESTINO != campos_m[y].BUFFER_NOMBREMAQUINA_DESTINO || campos_m[i].DESEMPRESA_DESTINO != campos_m[y].DESEMPRESA_DESTINO)
                                {
                                    $('#botonS').hide("fast");
                                    ocultaS++;
                                }
                            }
                        }

                    }

                    if (!ocultaS)
                        $('#botonS').show("fast");
                    if (!ocultaE)
                        $('#botonE').show("fast");
                    else
                        ocultos++;
                    if (!ocultaN)
                        $('#botonN').show("fast");
                    else
                        ocultos++;
                    if (!ocultaD)
                        $('#botonD').show("fast");
                    else
                        ocultos++;
                    if (ocultos >= 2 && !selS) {
                        $('#botonE').show("fast");
                        $('#botonN').show("fast");
                        $('#botonD').show("fast");
                    }
                    ;
                } else
                {
                    $('#botonDetalleBuffer').hide("fast");
                    $('#botonDetalleHistorico').hide("fast");
                    $('#botonComentarios').hide("fast");
                    $("#modalComentarios").dialog("close");
                    $('#botonMascaras').hide("fast");
                    $('#botonS').hide("fast");
                    $('#botonE').hide("fast");
                    $('#botonN').hide("fast");
                    $('#botonD').hide("fast");
                }

                if ($("#modalDetalleBuffer").dialog("isOpen"))
                    $("#botonDetalleBuffer").click();
                if ($("#modalDetalleHistorico").dialog("isOpen"))
                    $("#botonDetalleHistorico").click();
                //$('#grupoBotonesRemedy').width('530');
                //$('#botonDetalleRemedy').show("fast");
                //jQuery("#ficheros").GridUnload();
                //GridFicheros(campos.CODENVIO);
            },
            onSelectAll: function() { // eventos al seleccionar filas y cargar ficheros

                var fila_sel = jQuery("#bufferlist").getGridParam('selrow');
                var campos = jQuery("#bufferlist").getRowData(fila_sel);
                var ocultaS = 0;
                var ocultaE = 0;
                var ocultaN = 0;
                var ocultaD = 0;
                var selS = 0;
                var ocultos = 0;
                if (campos.CHKCONTROL == 'S') {
                    $('#botonS').hide("fast");
                    $('#botonE').hide("fast");
                    $('#botonN').hide("fast");
                    $('#botonD').hide("fast");
                    ocultaS++;
                    ocultaE++;
                    ocultaN++;
                    ocultaD++;
                }

                var fila_sel_m = jQuery("#bufferlist").getGridParam('selarrrow');
                var campos_m = new Array();
                if (fila_sel_m.length) {
                    if (fila_sel_m.length == 1) {
                        $('#botonDetalleBuffer').show("fast");
                        $('#botonDetalleHistorico').show("fast");
                        $('#botonComentarios').show("fast");
                        $('#botonMascaras').show("fast");
                    }
                    else {
                        $('#botonDetalleBuffer').hide("fast");
                        $('#botonDetalleHistorico').hide("fast");
                        $('#botonComentarios').hide("fast");
                        $("#modalComentarios").dialog("close");
                        $('#botonMascaras').hide("fast");
                        for (var i = 0; i < fila_sel_m.length; i++) {

                            campos_m[i] = $("#bufferlist").jqGrid('getRowData', fila_sel_m[i]);
                            if (campos_m[i].CHKCONTROL == 'S') {
                                $('#botonS').hide("fast");
                                $('#botonE').hide("fast");
                                $('#botonN').hide("fast");
                                $('#botonD').hide("fast");
                                ocultaS++;
                                ocultaE++;
                                ocultaN++;
                                ocultaD++;
                                selS++;
                            }
                            if (campos_m[i].CHKCONTROL == 'E') {

                                $('#botonE').hide("fast");
                                ocultaE++;
                            }
                            if (campos_m[i].CHKCONTROL == 'N') {

                                $('#botonN').hide("fast");
                                ocultaN++;
                            }
                            if (campos_m[i].CHKCONTROL == 'D') {

                                $('#botonD').hide("fast");
                                ocultaD++;
                            }
                            for (var y = i - 1; y >= 0; y--) {

                                if (campos_m[i].BUFFER_IP_DESTINO != campos_m[y].BUFFER_IP_DESTINO || campos_m[i].BUFFER_NOMBREMAQUINA_DESTINO != campos_m[y].BUFFER_NOMBREMAQUINA_DESTINO || campos_m[i].DESEMPRESA_DESTINO != campos_m[y].DESEMPRESA_DESTINO)
                                {
                                    $('#botonS').hide("fast");
                                    ocultaS++;
                                }
                            }
                        }

                    }

                    if (!ocultaS)
                        $('#botonS').show("fast");
                    if (!ocultaE)
                        $('#botonE').show("fast");
                    else
                        ocultos++;
                    if (!ocultaN)
                        $('#botonN').show("fast");
                    else
                        ocultos++;
                    if (!ocultaD)
                        $('#botonD').show("fast");
                    else
                        ocultos++;
                    if (ocultos >= 2 && !selS) {
                        $('#botonE').show("fast");
                        $('#botonN').show("fast");
                        $('#botonD').show("fast");
                    }
                    ;
                } else
                {
                    $('#botonDetalleBuffer').hide("fast");
                    $('#botonDetalleHistorico').hide("fast");
                    $('#botonMascaras').hide("fast");
                    $('#botonS').hide("fast");
                    $('#botonE').hide("fast");
                    $('#botonN').hide("fast");
                    $('#botonD').hide("fast");
                }

                if ($("#modalDetalleBuffer").dialog("isOpen"))
                    $("#botonDetalleBuffer").click();
                if ($("#modalDetalleHistorico").dialog("isOpen"))
                    $("#modalDetalleHistorico").dialog("close");
                if ($("#modalComentarios").dialog("isOpen"))
                    $("#modalComentarios").dialog("close");
            },
            loadComplete: function() {  // seleccionando el primer registro de la tabla
                var conts = 0;
                var conte = 0;
                var contn = 0;
                var contd = 0;
                $('#botonDetalleBuffer').hide("fast");
                $('#botonDetalleHistorico').hide("fast");
                $('#botonMascaras').hide("fast");
                $('#botonS').hide("fast");
                $('#botonE').hide("fast");
                $('#botonN').hide("fast");
                $('#botonD').hide("fast");
                $('#bufferlist').setGridHeight(530);
                //jQuery('#list').jqGrid('setSelection', selid); //mantener selección de fila
                //jQuery('#list').jqGrid('setSelection','');
                if (scrollPos)
                    jQuery("#bufferlist").closest(".ui-jqgrid-bdiv").scrollTop(scrollPos);
                var rowData = [];
                var rowIds = $("#bufferlist").jqGrid('getDataIDs');
                for (var i = 1; i <= rowIds.length; i++) {//iterate over each row
                    rowData = $("#bufferlist").jqGrid('getRowData', i);
                    //set background style if ColumnValue == true

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
                        $.ajax({
                            type: "POST",
                            url: 'json/comentarios_json.php?fichero=' + rowData['BUFFER_FICHERO_ORIGEN'] + '&maquina=' + rowData['BUFFER_NOMBREMAQUINA_DESTINO'] + '&ip=' + rowData['BUFFER_IP_DESTINO'] + '&empresa=' + rowData['DESEMPRESA_DESTINO'],
                            dataType: 'json',
                            async: false,
                            success: function(data) {

                                if (data) {

                                    $("#bufferlist_cb").css("width", "40px");
                                    $("#bufferlist tbody tr").children().first("td").css("width", "40px");
                                    $("#jqg_bufferlist_" + i).parent().append("<span class='ui-icon ui-icon-comment' style='display: inline-block;'></span>");
                                }
                            },
                            error: function(e) {
                                alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                            }

                        })

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

                var conts = $('#bufferlist').getGridParam('userData').s;
                var conte = $('#bufferlist').getGridParam('userData').e;
                var contn = $('#bufferlist').getGridParam('userData').n;
                var contd = $('#bufferlist').getGridParam('userData').d;
                $("#contregs").append(conts);
                $("#contrege").append(conte);
                $("#contregn").append(contn);
                $("#contregd").append(contd);
            },
            onPaging: function(pgButton) {
                //jQuery("#ficheros").GridUnload();
                //GridFicheros();
            }, //al cambiar página del grid volver a cargar ficheros y perder selección
            editurl: 'edit/buffer_ed.php',
        }).navGrid('#pager', {edit: false, add: false, del: false, view: false,
            beforeRefresh: function() {

                change_search_now = true;
            }

        },
        {
            /*  beforeCheckValues: function(postdata, formid, opertype) {
             
             var mot;
             if (postdata.CHKCONTROL == 'N' || postdata.CHKCONTROL == 'D') {
             mot = prompt('Introducir un Motivo:');
             if (mot == "")
             if (postdata.CHKCONTROL == 'N')
             mot = 'FIN AUTORIZACION DE LA CESION';
             if (mot == "")
             if (postdata.CHKCONTROL == 'D')
             mot = 'REGISTRO DESCARTADO SIN AUTORIZACIÓN';
             //$('.DataTD').add('<input id="mot" type="hidden" name="mot" value="'+mot+'" />');
             postdata.mot = mot;
             }
             return postdata;
             },
             checkOnSubmit: false,
             height: 100,
             //closeAfterEdit: true,
             ajaxEditOptions: {dataType: 'json', // target element(s) to be updated with server response
             
             success: showResponse
             
             }
             
             */
        }, //  default settings for edit
                {}, //  default settings for add
                {}, // delete instead that del:false we need this
                {
                    multipleSearch: true,
                    id: "busca",
                    width: 530,
                    sopt: ['cn', 'eq', 'bw', 'ew'],
                    closeAfterSearch: true,
                    // procedimientos para seleccionar una columnna por defecto en la búsqueda
                    onInitializeSearch: function() {
                        change_search_now = true;
                        changeSearchDefault($('.columns').children("select"));
                        $("#fbox_bufferlist_reset").click(function() {
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
                        //$("#fbox_bufferlist_reset").click();
                        $(".data").children('input').focus();
                    }
                    //-------------------------------------------------------------------------
                }, // search options
        {} /* view parameters*/
        // ).navButtonAdd('#pager', {
        //     caption: "",
        //     title: "Configurar Columnas",
        //     buttonicon: "ui-icon-gear",
        //     onClickButton: function() {
        //         alert("Con este botón se configuran las columnas que se quieran ver u ocultar");
        //     },
        //     position: "last"
        // }
        ).navButtonAdd('#pager', {
            caption: "",
            title: "" + titagrupa + "",
            buttonicon: "ui-icon-triangle-2-n-s",
            onClickButton: function() {

                if (agrupa === 1)
                    agrupa = 0;
                else
                    agrupa = 1;
                jQuery("#bufferlist").GridUnload();
                GridBuffer(0, 0, 0, carga, 0, agrupa)

            },
            position: "last"
        }
        ).navButtonAdd('#pager', {
            caption: "",
            title: "Exportar",
            buttonicon: "ui-icon-extlink",
            onClickButton: function() {
                var exsql = $('#bufferlist').getGridParam('userData').SQL;
                var campos = $('#bufferlist').getGridParam('userData').campos;
                var order = $('#bufferlist').getGridParam('userData').order;
                var namefile = prompt("Por favor, proporcione un nombre de fichero sin extensión, para exportar los datos: ", "log");
                $.ajax({
                    type: "POST",
                    url: 'json/exp_json.php',
                    dataType: 'json',
                    data: {exsql: exsql, campos: campos, order: order, nombrefich: '' + namefile + '.csv'},
                    success: function(data) {
                        if (data.error != "")
                            alert(data.error);
                        else {
                            $("#openfich").attr('href', 'exporta.php?ruta=' + data.ruta + '&nombrefich=' + namefile + '');
                            if (namefile)
                                $("#openfich").click();
                        }
                        ;
                    },
                    error: function(e) {
                        alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                    }
                });
            },
            position: "last"
        }).navSeparatorAdd('#pager', {
        }).navButtonAdd('#pager', {
            id: "sfilter",
            caption: "S",
            title: "Filtrar por Registros Autorizados ",
            buttonicon: "none",
            onClickButton: function() {

                $("#busca").click();
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
                $("#busca").click();
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
                $("#busca").click();
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

                $("#busca").click();
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
        });
    } // fin funcion GridBuffer


    $(document).keypress(function(e) {
        if (e.which == 13) {

            $("#fbox_bufferlist_search").click();
            setTimeout(function() {
                $("#fbox_bufferlist_search").click();
            }, 1000);
            if ($("#modalSelCarga").dialog("isOpen"))
                $("#AceptarSelCarga").click();
        }
    });
    $(document).ready(function() {



        $('#botonS').css({background: '#8D8A8A'});
        $('#botonE').css({background: 'DarkBlue'});
        $('#botonN').css({background: '#A60808'});
        $('#botonD').css({background: 'DarkGreen'});
       /* $("input[type=text]").keyup(function(e) {
            if (e.which >= 65) {
                $(this).val($(this).val().toUpperCase());
            }
        });*/
        $("input[type=text]").css({'text-transform':'uppercase'});
        $('#SELCODCARGA_ARCHIVO').val('');
        $("#openfich").click(function() {
            location.href = this.href; // ir al link
        });
        $('#botonMascaras').hide("fast");
        $('#botonDetalleBuffer').hide("fast");
        $('#botonDetalleHistorico').hide("fast");
        $('#botonComentarios').hide("fast");
        $("#modalComentarios").dialog("close");
        $('#botonS').hide("fast");
        $('#botonE').hide("fast");
        $('#botonN').hide("fast");
        $('#botonD').hide("fast");
        //GridBuffer(0, 0, 0, 0,0,1);

        $('#modalSelCarga_archivo').dialog('open');
        // Manejo de los botones, y ventanas modales -------------------------------------

        $("#modalDetalleBuffer").dialog({
            autoOpen: false,
            height: 660,
            width: 320,
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
        $('#botonMascaras').button().click(function(evento) {
            // aquí seleccionar nombre de fichero y abrir ventana en nueva pestaña de máscaras
            var fila_sel = jQuery("#bufferlist").getGridParam('selrow');
            var campos = jQuery("#bufferlist").getRowData(fila_sel);
            window.open('index.php?newmask=1&fichmask=' + campos.BUFFER_FICHERO_ORIGEN + '&cargamask=' + campos.CODCARGA + '&codbuffermask=' + campos.CODBUFFER, '_blank');
        });
        $('#botonS').button().click(function(evento) {
            var fila_sel_m = jQuery("#bufferlist").getGridParam('selarrrow');
            var vartmp;
            if (fila_sel_m.length > 1) {
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
            } else {

           
                //aquí abrir ventana prompt de jquery dialog (modal)
                
               $("#modalSelEnvio").dialog('open');
            
                //fin ventana modal
            } // fin de un solo registro seleccionado
        });
        $('#botonE').button().click(function(evento) {
            var fila_sel = jQuery("#bufferlist").getGridParam('selarrrow');
            if (fila_sel.length) {
                var campos_m = new Array();
                var rows = new Array();
                for (var i = 0; i < fila_sel.length; i++) {
                    campos_m[i] = $("#bufferlist").jqGrid('getRowData', fila_sel[i]);
                    rows[i] = JSON.stringify($("#bufferlist").jqGrid('getRowData', fila_sel[i]));
                }
                ;
                var vartmp = JSON.stringify(campos_m);
                var postArray = {json: vartmp};
                $.ajax({
                    type: "POST",
                    url: "edit/buffer_ed.php?CHKCONTROL=E",
                    dataType: "json",
                    data: postArray,
                    success: showResponse
                });
            }
        });
        $('#botonN').button().click(function(evento) {
            var fila_sel = jQuery("#bufferlist").getGridParam('selarrrow');
            if (fila_sel.length) {
                var campos_m = new Array();
                var rows = new Array();
                for (var i = 0; i < fila_sel.length; i++) {
                    campos_m[i] = $("#bufferlist").jqGrid('getRowData', fila_sel[i]);
                    rows[i] = JSON.stringify($("#bufferlist").jqGrid('getRowData', fila_sel[i]));
                }
                ;
                var vartmp = JSON.stringify(campos_m);
                var postArray = {json: vartmp};
                var mot = prompt('Introducir el Motivo de cambio de Estado:');
                if (mot == "")
                    mot = 'FIN AUTORIZACION DE LA CESION';
                $.ajax({
                    type: "POST",
                    url: "edit/buffer_ed.php?CHKCONTROL=N&mot=" + mot,
                    dataType: "json",
                    data: postArray,
                    success: showResponse
                });
            }
        });
        $('#botonD').button().click(function(evento) {
            var fila_sel = jQuery("#bufferlist").getGridParam('selarrrow');
            if (fila_sel.length) {
                var campos_m = new Array();
                var rows = new Array();
                for (var i = 0; i < fila_sel.length; i++) {
                    campos_m[i] = $("#bufferlist").jqGrid('getRowData', fila_sel[i]);
                    rows[i] = JSON.stringify($("#bufferlist").jqGrid('getRowData', fila_sel[i]));
                }
                ;
                var vartmp = JSON.stringify(campos_m);
                var postArray = {json: vartmp};
                var mot = prompt('Introducir el Motivo de cambio de Estado:');
                if (mot == "")
                    mot = 'REGISTRO DESCARTADO SIN AUTORIZACIÓN';
                $.ajax({
                    type: "POST",
                    url: "edit/buffer_ed.php?CHKCONTROL=D&mot=" + mot,
                    dataType: "json",
                    data: postArray,
                    success: showResponse
                });
            }
        });
        $('#botonDetalleHistorico').button().click(function(evento) {
            evento.preventDefault();
            var fila_sel = jQuery("#bufferlist").getGridParam('selrow');
            var campos = jQuery("#bufferlist").getRowData(fila_sel);
            $('#TDFICHERO').text(campos.BUFFER_FICHERO_ORIGEN);
            $.ajax({
                type: "POST",
                url: 'json/bufferhist_archivo_json.php?nomfich=' + campos.BUFFER_FICHERO_ORIGEN,
                dataType: 'json',
                success: function(data) {
                    var index;
                    var color = " style='color: black;' ";
                    for (index = 0; index < data.rows.length; ++index) {
                        if (data.rows[index][1] === "S")
                            color = " style='color: black;' ";
                        if (data.rows[index][1] === "E")
                            color = " style='color: DarkBlue;' ";
                        if (data.rows[index][1] === "N")
                            color = " style='color: #A60808;' ";
                        if (data.rows[index][1] === "D")
                            color = " style='color: DarkGreen;' ";
                        $("#hist").append("<tr class='quitar' ><td align='center' ><div " + color + ">" + data.rows[index][0] + "</div></td><td align='center' ><div " + color + ">" + data.rows[index][2] + "</div></td><td align='center' ><div " + color + ">" + data.rows[index][3] + "</div></td><td align='center' ><div " + color + ">" + data.rows[index][4] + "</div></td><td align='center' ><div " + color + ">" + data.rows[index][5] + "</div></td></tr>");
                    }




                },
                error: function(e) {
                    alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                }

            });
            $('#modalDetalleHistorico').dialog('open');
        });
        //-----------boton comentarios

        $('#botonComentarios').button().click(function(evento) {
            evento.preventDefault();
            var fila_sel = jQuery("#bufferlist").getGridParam('selrow');
            var campos = jQuery("#bufferlist").getRowData(fila_sel);
            $('#TDCOMENT').text(campos.BUFFER_FICHERO_ORIGEN);
            $.ajax({
                type: "POST",
                url: 'json/comentarios_json.php?fichero=' + campos.BUFFER_FICHERO_ORIGEN + '&maquina=' + campos.BUFFER_NOMBREMAQUINA_DESTINO + '&ip=' + campos.BUFFER_IP_DESTINO + '&empresa=' + campos.DESEMPRESA_DESTINO,
                dataType: 'json',
                success: function(data) {
                    var index;
                    if (!data) {
                        $("#cabecera_coment").hide();
                        $("#comt").append("<tr class='quitar' ><td colspan='2' align='center' ><div >No se han agregado comentarios para este registro</div></td></tr>");
                        if ($('#modalComentarios').dialog('isOpen')) {
                            $('#modalComentarios').dialog('open');
                        } else {
                            $('#modalComentarios').dialog('open');
                            $("#btnAgr").click();
                        }
                    } else {
                        $("#cabecera_coment").show();
                        for (index = 0; index < data.rows.length; ++index) {

                            $("#comt").append("<tr class='quitar' ><td align='left' width='150' ><div >" + data.rows[index][0] + "</div></td><td align='left' ><div>&#8226;&nbsp;" + data.rows[index][1] + "</div></td></tr>");
                        }
                        $('#modalComentarios').dialog('open');
                    }
                },
                error: function(e) {
                    alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                }

            });


        });
        //----fin boton comentarios
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
                url: 'json/detallebuffer_archivo_json.php?codcarga=' + idCarga + '&codbuf=' + idBuf,
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
                    $.ajax({
                        type: "POST",
                        url: 'json/detalleremedy_json.php?codenvio=' + data.CODENVIO + '',
                        dataType: 'json',
                        success: function(data2) {

                            $('#CODENVIO').text('ENVÍO: ' + data.CODENVIO + '');
                            $('#CODTIPOENVIO').val(data2.CODTIPOENVIO);
                            $('#DESTIPOENVIO').val(data2.DESTIPOENVIO);
                            $('#CODENVIO_REMEDY').val(data2.CODENVIO_REMEDY);
                            $('#CODAUTORIZA').val(data2.CODAUTORIZA);
                            $('#DESAUTORIZA').val(data2.DESAUTORIZA);
                            $('#EMAILAUTORIZA').val(data2.EMAILAUTORIZA);
                            $('#CODEMPRESA').val(data2.CODEMPRESA);
                            $('#DESEMPRESA').val(data2.DESEMPRESA);
                            $('#CODINTERVINIENTE').val(data2.CODINTERVINIENTE);
                            $('#DESINTERVINIENTE').val(data2.DESINTERVINIENTE);
                            $('#CODDESTINATARIO').val(data2.CODDESTINATARIO);
                            $('#DESDESTINATARIO').val(data2.DESDESTINATARIO);
                            $('#CODFRECUENCIA').val(data2.CODFRECUENCIA);
                            $('#DESFRECUENCIA').val(data2.DESFRECUENCIA);
                            $('#CODCANAL').val(data2.CODCANAL);
                            $('#DESCANAL').val(data2.DESCANAL);
                            $('#NOMBREMAQUINA_ORIGEN').val(data2.NOMBREMAQUINA_ORIGEN);
                            $('#IP_ORIGEN').val(data2.IP_ORIGEN);
                            $('#IP_DESTINO').val(data2.IP_DESTINO);
                            $('#CHKCIFRADO').val(data2.CHKCIFRADO);
                            $('#CIFRADO').val(data2.CIFRADO);
                            $('#MOTIVOENVIO').val(data2.MOTIVOENVIO);
                            $('#CODMOTIVOBAJA').val(data2.CODMOTIVOBAJA);
                            $('#DESMOTIVOBAJA').val(data2.DESMOTIVOBAJA);
                            //var fecha=data.ENVIO_FECHA_ALTA;
                            //var fecha_sub=fecha.substring(0,10);
                            //var parsedDate = $.datepicker.parseDate('yy-mm-dd', fecha_sub);
                            //$('#ENVIO_FECHA_ALTA').val($.datepicker.formatDate('dd-mm-yy', parsedDate));
                            $('#ENVIO_FECHA_ALTA').val(data2.ENVIO_FECHA_ALTA);
                            //fecha=data.ENVIO_FECHA_MODIFICACION;
                            //fecha_sub=fecha.substring(0,10);
                            //parsedDate = $.datepicker.parseDate('yy-mm-dd', fecha_sub);
                            //$('#ENVIO_FECHA_MODIFICACION').val($.datepicker.formatDate('dd-mm-yy', parsedDate));
                            $('#ENVIO_FECHA_MODIFICACION').val(data2.ENVIO_FECHA_MODIFICACION);
                            //fecha=data.ENVIO_FECHA_BAJA;
                            //fecha_sub=fecha.substring(0,10);
                            //parsedDate = $.datepicker.parseDate('yy-mm-dd', fecha_sub);
                            //$('#ENVIO_FECHA_BAJA').val($.datepicker.formatDate('dd-mm-yy', parsedDate));
                            $('#ENVIO_FECHA_BAJA').val(data2.ENVIO_FECHA_BAJA);
                            $('#ENVIO_CODUSUARIO_ALTA').val(data2.ENVIO_CODUSUARIO_ALTA);
                            $('#ENVIO_DESUSUARIO_ALTA').val(data2.ENVIO_DESUSUARIO_ALTA);
                            $('#ENVIO_CODUSUARIO_MODIFICACION').val(data2.ENVIO_CODUSUARIO_MODIFICACION);
                            $('#ENVIO_DESUSUARIO_MODIFICACION').val(data2.ENVIO_DESUSUARIO_MODIFICACION);
                            $('#ENVIO_CODUSUARIO_BAJA').val(data2.ENVIO_CODUSUARIO_BAJA);
                            $('#ENVIO_DESUSUARIO_BAJA').val(data2.ENVIO_DESUSUARIO_BAJA);
                            $('#DEOBSERVACIONES').val(data2.OBSERVACIONES);
                            $('#DESCONTACTO').val(data2.DESCONTACTO);
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
                            if (!data2.ENVIO_CODUSUARIO_BAJA) {
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
                },
                error: function(e) {
                    alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                }
            });
            //--------------------------------------------------------------------------------------
            var fila_sel = jQuery("#bufferlist").getGridParam('selrow');
            var campos = jQuery("#bufferlist").getRowData(fila_sel);
            if (campos.CODENVIO)
            {
                $('#modalDetalleRemedy').dialog('option', 'height', 670);
                $('#modalDetalleBuffer').dialog('option', 'height', 670);
                $('#modalDetalleRemedy').dialog('option', 'width', 340);
                $('#modalDetalleRemedy').dialog('option', 'position', [498, 29]);
                $('#modalDetalleBuffer').dialog('option', 'position', [835, 29]);
                $('#modalDetalleRemedy').dialog('open');
            } else
                $('#modalDetalleRemedy').dialog('close');
            $('#modalDetalleBuffer').dialog('open');
            return false;
        });
        $("#modalDetalleRemedy").dialog({
            autoOpen: false,
            height: 650,
            width: 420,
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
        /*
         $('#botonDetalleFicheros').button().click(function(evento) {
         evento.preventDefault();
         
         $('#modalDetalleBuffer').dialog('open');
         return false;
         });
         
         
         $('#botonExportarBuffer').button().click(function(evento) {
         evento.preventDefault();
         
         $('#modalDetalleBuffer').dialog('open');
         return false;
         });
         
         $('#botonEstadoBuffer').button().click(function(evento) {
         evento.preventDefault();
         
         $('#modalDetalleBuffer').dialog('open');
         return false;
         });
         
         */



        $("li").css({background: "#4875e8"});
        $("#util").css({background: "#77c5e1"}); //seleccionar opción actual del menú principal

    });






</script>