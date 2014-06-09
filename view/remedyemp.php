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

<!-- Modal Detalle Ficheros -->
<?
include_once('../view/modal/detalleficheros_modal.php');
?>

<!-- Modal Nuevo remedy -->

<?
include_once('../view/modal/remedy_modal.php');
?>


<!-- Fin Ventanas Modales -->

<!--<div align="center" id="titulo" class="titulo2">Ficheros Autorizados por Empresa</div>-->

<div class="centradomasGrande" align="center" style="height: 600px">
    <!--- Tabla de Cargas Remedy -->
    <div class="colIzquierda" align="center" style="height: 600px" >

        <table id="remedylist" class="scroll"><tr><td></td></tr></table>
        <div id="pager" class="scroll" style=""></div>

        <!--- Fin Tabla de de Cargas Remedy -->

        <!-- Botones acciones listado Remedy --> 
        <!--
                <div id="grupoBotonesRemedy" class="colDerecha" style='width: 350px;'>
                    <table style="padding-top:0px;">
                        <tr><td>
        
        <!--<input id="botonNuevoRemedy" type="submit" value="Nuevo" class="boton ui-corner-all" 
        style="width:100px; margin-bottom:9px;" />
        <input id="botonBorrarRemedy" type="submit" value="Dar de Baja" class="boton ui-corner-all" 
        style="width:100px; margin-bottom:9px;" />
        <input id="botonImportarRemedy" type="submit" value="Importar" class="boton ui-corner-all" 
        style="width:100px; margin-bottom:9px;" />
        <input id="botonExportarRemedy" type="submit" value="Exportar" class="boton ui-corner-all" 
        style="width:100px; margin-bottom:9px;" />
        
                            </td>
                        </tr>
                    </table>
        
                </div>
        <!-- FIN Botones acciones listado Remedy -->
    </div>

    <!--- Tabla de Cargas Ficheros -->
    <div class="colDerecha" align="center" style="height: 600px;" >

        <table id="ficheros" class="scroll"><tr><td></td></tr></table>
        <div id="pager2" class="scroll" style=""></div>

        <!--- Fin Tabla de de Ficheros -->

        <!-- Botones acciones listado ficheros -->
        <div class="unaCol" id="grupoBotonesFicheros" style='width: 750px;'>
            <table width='435' style="padding-top:10px;">
                <tr ><td align='right' >
                        <input type="hidden" id="idFich" name="idFich" />
                        <input type="hidden" id="idEnv" name="idEnv" />
                        <input id="botonDetalleFicheros" type="submit" value="Ver Detalles" class="boton ui-corner-all" 
                               style="width:110px; margin-bottom:9px; display: none ;" />
                        <input id="botonDetalleRemedy" type="submit" value="Detalles Envío" class="boton ui-corner-all" 
                               style="width:110px; margin-bottom:9px; display: none ;" />
                        <input id="botonBorrarFichero" type="submit" value="Baja Fichero" class="boton ui-corner-all" 

                               style="width:100px; margin-bottom:9px;" />





 <!--<input id="botonExportarRemedy" type="submit" value="Exportar" class="boton ui-corner-all" 
        style="width:100px; margin-bottom:9px;" />-->



                    </td>
                </tr>
            </table>

        </div>
        <!-- FIN Botones acciones listado ficheros -->

    </div>

    <a id="openfich" href="exporta.php" style='display: none' >Descarga</a>

</div>


<script type="text/javascript">
    // Complemento para carga asíncrona con formularios.
    // prepara el formulario cuando el DOM está preparado.
    $(document).ready(function() {

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
        $('#remedyFormfich').ajaxForm(options);
        $('#editFormfich').ajaxForm(options);
        $('#editFormremedy').ajaxForm(options);




    });

    datePick = function(elem)
    {
        jQuery(elem).datepicker({dateFormat: "yy-mm-dd"});
    }
    function formSuccess(data) {
        var cut;
        var idEnv = $("#idEnv").val();
        if (!data.ERROR) {

            if (data.ENV) {
                idEnv = data.ENV;

            }
            jQuery("#remedylist").GridUnload();
            GridRemedy(idEnv);
            jQuery("#ficheros").GridUnload();
            GridFicheros(idEnv, 0);
            $('#modalficheros').dialog('close');
            $('#modalremedy').dialog('close');
            $('#modalEditarFicheros').dialog('close');
            $('#modalEditarRemedy').dialog('close');
            mensaje(data.SUCCESS, 'titulo');

            $('#remedyForm')[0].reset();
            $('#remedyFormfich')[0].reset();
            $('#editFormfich')[0].reset();
            $('#editFormremedy')[0].reset();

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
                //$('#borrarFich').val(camposb[i].CODFICHERO);
                //$('#borrarEnv').val(camposb[i].CODENVIO);
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
            var scrollPos = jQuery("#ficheros").closest(".ui-jqgrid-bdiv").scrollTop();
            setTimeout(function() {

                callback();
                setTimeout(function() {
                   
                    jQuery("#ficheros").closest(".ui-jqgrid-bdiv").scrollTop(scrollPos);
                }, 500);


            }, 4000); //

             jQuery('#ficheros').jqGrid('setSelection', fila_selb[0]);

        }
    }




    function reloadGridFich() {
        $('#remedyForm')[0].reset();
        jQuery("#ficheros").trigger("reloadGrid");

    }


    function GridRemedy(selenv, sql, baja) { // esta función crea el grid y filtra la tabla según parámetros
        var selid;
        var rowData = [];
        var seleccionar;
        if (!selenv)
            selenv = 0;
        if (!sql)
            sql = 0;
        if (!baja)
            baja = 0;
        var scrollPos;



        jQuery("#remedylist").jqGrid({
            scrollrows: true,
            url: 'json/remedyemp_json.php?codenvio=' + selenv + '&SQL=' + sql + '&baja=' + baja,
            width: 380,
            height: 530,
            datatype: "json",
            colNames: [
                'CODEMPRESA',
                'EMPRESA DESTINO',
                'CODEMPRESAPADRE',
                'CHKACTIVO',
                'SQL'

            ],
            colModel: [
                //, editable: true, sorttype:"text",editable:true,edittype:"select",editoptions:   
                //{ value:"1:one;2:two"},editrules:{required:true} ejemplo de campo editable. hay que modificar la propiedad editable del nav a true

                {name: 'CODEMPRESA', index: 'CODENVIO', align: 'center', width: '70', hidden: true},
                {name: 'DESEMPRESA', index: 'DESEMPRESA', align: 'left'},
                {name: 'CODEMPRESAPADRE', index: 'CODEMPRESAPADRE', align: 'left', hidden: true},
                 {name: 'CHKACTIVO', index: 'CHKACTIVO', align: 'center', hidden: true},
                {name: 'SQL', index: 'SQL', align: 'center', hidden: true}


            ],
            pager: jQuery('#pager'),
            rowNum: 50,
            loadui: "disable",
            imgpath: 'css/themes/custom-theme/images',
            sortname: 'DESEMPRESA',
            sortorder: "asc",
            loadonce: false,
            viewrecords: true,
            recordtext: "{0}/{1} de {2}",
            emptyrecords: "Sin registros",
            caption: "LISTADO DE EMPRESAS",
            ondblClickRow: function(rowid)
            {
                var fila_sel = jQuery("#remedylist").getGridParam('selrow');
                var campos = jQuery("#remedylist").getRowData(fila_sel);

                if (!campos.CODEMPRESAPADRE) {

                    jQuery("#ficheros").GridUnload();
                    GridFicheros(campos.CODEMPRESA, 1);
                } else
                    alert('Debe seleccionar una Empresa Padre para ver sus ficheros');


                //comentado abrir detalles en doble cick
                //setTimeout(function() {
                //   $('#botonDetalleFicheros').click();
                //}, 500);

                selenv = 0;
                /* $('#idFich').val('');
                 $('#idEnv').val('');
                 jQuery('#ficheros').resetSelection();
                 jQuery('#remedylist').resetSelection();*/

            },
            onSelectRow: function() { // eventos al seleccionar filas y cargar ficheros
                var fila_sel = jQuery("#remedylist").getGridParam('selrow');
                var campos = jQuery("#remedylist").getRowData(fila_sel);
                $('#idEnv').val(campos.CODENVIO);
                $('#envSel').val(campos.CODENVIO);

                selid = jQuery("#remedylist").getGridParam('selrow');
                //$('#grupoBotonesRemedy').width('540');
                //$('#grupoBotonesFicheros').width('440');
                //$('#botonDetalleRemedy').show("fast");





            },
            loadComplete: function() {  // seleccionando el primer registro de la tabla

                var rowIds = $("#remedylist").jqGrid('getDataIDs');
                for (var i = 1; i <= rowIds.length; i++) {//iterate over each row
                    rowData = $("#remedylist").jqGrid('getRowData', i);
                    //set background style if ColumnValue == true

                    if (rowData['CODEMPRESAPADRE']) {

                        $("#remedylist").setCell(i, 'DESEMPRESA', '', {'color': 'LightSlateGray'});

                    }
                    if (rowData['CHKACTIVO'] == "N" && !rowData['CODEMPRESAPADRE']) {

                        $("#remedylist").setCell(i, 'CODEMPRESA', '', {'color': 'red'});
                        $("#remedylist").setCell(i, 'DESEMPRESA', '', {'color': 'red'});

                    }
                    if (rowData['CODEMPRESA'] === selenv) {

                        jQuery('#remedylist').jqGrid('setSelection', i);

                        seleccionar = i;
                    }

                } //for
                scrollPos = jQuery("#remedylist").closest(".ui-jqgrid-bdiv").scrollTop();
                if (scrollPos > 60)
                    scrollPos = scrollPos + 200;
                if (seleccionar)
                    jQuery("#remedylist").closest(".ui-jqgrid-bdiv").scrollTop(scrollPos);
                //jQuery("#remedylist").closest(".ui-jqgrid-bdiv").scrollTop(scrollPos);

                //$(".env_baja").toggle();
            }//,
            //onPaging: function(pgButton) {
            //  jQuery("#ficheros").GridUnload();
            // GridFicheros(0,0,baja);
            //}, //al cambiar página del grid volver a cargar ficheros y perder selección

        }).navGrid('#pager', {edit: false, add: false, del: false, view: false,
            beforeRefresh: function() {
                jQuery("#remedylist").GridUnload();
                $('#idEnv').val('');
                GridRemedy();

                if (!selenv) {

                    $('#grupoBotonesFicheros').width('120');
                    $('#botonDetalleRemedy').hide("fast");
                }

            }
        },
        {}, //  default settings for edit
                {
                }, //  default settings for add
                {}, // delete instead that del:false we need this
                {
                    multipleSearch: true,
                    width: 575,
                    sopt: ['cn', 'eq', 'bw', 'ew'],
                    closeAfterSearch: true,
                    //searchOnEnter: true,
                    // procedimientos para seleccionar una columnna por defecto en la búsqueda

                    afterShowSearch: function() {

                        $(".data").children('input').focus();
                    }
                }, // search options
        {} /* view parameters*/
        )
                //.navButtonAdd('#pager', {
                //caption: "",
                //title: "Configurar Columnas",
                //buttonicon: "ui-icon-gear",
                //onClickButton: function() {
                //  alert("Con este botón se configuran las columnas que se quieran ver u ocultar");
                //},
                //position: "last"
                //})
                .navButtonAdd('#pager', {
                    caption: "",
                    title: "Mostrar/Ocultar Empresas Hijas",
                    buttonicon: "ui-icon-cancel",
                    onClickButton: function() {
                        if (baja == 0)
                            baja = 1;
                        else
                            baja = 0;
                        //$(".env_baja").toggle();
                        if (baja == 1) {
                            jQuery("#remedylist").GridUnload();
                            $('#idEnv').val('');
                            GridRemedy(0, 0, baja);
                            //jQuery("#ficheros").GridUnload();
                            //GridFicheros(0,0,baja);

                        } else {
                            jQuery("#remedylist").GridUnload();
                            $('#idEnv').val('');
                            GridRemedy();
                            //jQuery("#ficheros").GridUnload();
                            //GridFicheros();
                        }
                    },
                    position: "last"
                })/*.navButtonAdd('#pager', {
                 caption: "",
                 title: "Exportar",
                 buttonicon: "ui-icon-extlink",
                 onClickButton: function() {
                 var exsql = $('#remedylist').getGridParam('userData').SQL;
                 var campos = $('#remedylist').getGridParam('userData').campos;
                 var order = $('#remedylist').getGridParam('userData').order;
                 $.ajax({
                 type: "POST",
                 url: 'json/exp_json.php',
                 dataType: 'json',
                 data: {exsql: exsql, campos: campos, order: order, nombrefich: 'envios.csv'},
                 success: function(data) {
                 if (data.error != "")
                 alert(data.error);
                 else {
                 $("#openfich").attr('href', 'exporta.php?ruta=' + data.ruta + '&nombrefich=envios');
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
                 }) */
                ;


    } // fin funcion GridRemedy

// Change serach default from selection option to the one with value = '3'
    function changeSearchDefault(select) {

        select.children("option:selected").removeAttr("selected");
        var newdefault = select.children("option[value='FICHERO_ORIGEN']");
        newdefault.attr("selected", "selected");
        newdefault.trigger("change");
    }

//-----------------Tabla de Ficheros-----------------------

    function GridFicheros(codenv, cut, baja) { // esta función crea el grid y filtra la tabla según parámetros
        var selid;
        var rowData = [];
        if (!codenv)
            codenv = 0;
        if (!baja)
            baja = 0;

        var change_search_now = false;

        jQuery("#ficheros").jqGrid({
            url: 'json/ficherosemp_json.php?codenv=' + codenv + '&baja=' + baja,
            width: 750,
            height: 530,
            datatype: "json",
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
                'MAQ./IP DESTINO',
                'CODEMPRESA',
                'CANAL'

            ],
            colModel: [
                {name: 'CODENVIO', index: 'CODENVIO', align: 'center', width: '70'},
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
                {name: 'FILE_IP_DESTINO', index: 'FILE_IP_DESTINO', align: 'center', stype: 'text', searchoptions: {searchhidden: true}},
                {name: 'CODEMPRESA', index: 'CODEMPRESA', align: 'center', stype: 'text', searchoptions: {searchhidden: true}, hidden: true},
                {name: 'FILE_DESCANAL', index: 'FILE_DESCANAL', align: 'center', width: '120'}

            ],
            pager: jQuery('#pager2'),
            rowNum: 50,
            loadui: "disable",
            imgpath: 'css/themes/custom-theme/images',
            sortname: 'FILE_CODCANAL,FICHERO_ORIGEN',
            sortorder: "asc",
            loadonce: false,
            multiselect: true,
            multiboxonly: true,
            viewrecords: true,
            recordtext: "{0} - {1} de {2}",
            emptyrecords: "Sin registros",
            caption: "LISTADO DE FICHEROS",
            beforeRequest: function()
            {

                $('.msg-info').remove();

            },
            ondblClickRow: function(rowid)
            {

                var fila_sel = jQuery("#ficheros").getGridParam('selrow');
                var campos = jQuery("#ficheros").getRowData(fila_sel);

                $('#idFich').val(campos.CODFICHERO);
                $('#idEnv').val(campos.CODENVIO);


                var codemp = campos.CODEMPRESA;

                rowData = $("#remedylist").jqGrid('getRowData', 1);
                var sql = rowData['SQL'];

                setTimeout(function() {
                    jQuery("#remedylist").GridUnload();

                    GridRemedy(codemp, sql, baja);
                }, 100);


                setTimeout(function() {

                    $('#botonDetalleFicheros').click();
                }, 500);




                /*
                 $('#idFich').val('');
                 $('#idEnv').val('');
                 jQuery('#ficheros').resetSelection();
                 jQuery('#remedylist').resetSelection();*/

            },
            onSelectRow: function() { // eventos al seleccionar filas y cargar ficheros
                var fila_sel = jQuery("#ficheros").getGridParam('selrow');
                var campos = jQuery("#ficheros").getRowData(fila_sel);
                var baja = campos.FILE_CODUSUARIO_BAJA;

                var fila_sel_m = jQuery("#ficheros").getGridParam('selarrrow');
                if (fila_sel_m.length > 1) {
                    $('#botonDetalleFicheros').hide("fast");
                } else if (fila_sel_m.length === 1)
                    $('#botonDetalleFicheros').show("fast");
                    $('#botonBorrarFichero').show("fast");

                if (fila_sel_m.length === 0) {
                    $('#botonDetalleFicheros').hide("fast");
                    $('#botonBorrarFichero').hide("fast");
                }

                $('#idFich').val(campos.CODFICHERO);
                $('#idEnv').val(campos.CODENVIO);
                $('#borrarFich').val(campos.CODFICHERO);
                $('#borrarEnv').val(campos.CODENVIO);
                $('#activarFich').val(campos.CODFICHERO);
                $('#activarEnv').val(campos.CODENVIO);
                $('#fichSel').val(campos.CODFICHERO);

                selid = jQuery("#ficheros").getGridParam('selrow');

                //codenv = campos.CODENVIO;

                $('#grupoBotonesFicheros').width('120');
        
                $('#botonDetalleRemedy').hide("fast");


                //$('#botonDetalleRemedy').show("fast");
                if ($("#modalDetalleFicheros").dialog("isOpen"))
                    $("#botonDetalleFicheros").click();
                setTimeout(function() {
                    if ($("#modalDetalleRemedy").dialog("isOpen"))
                        $("#botonDetalleRemedy").click();
                }, 500);

            },
             onSelectAll: function() {
                 var fila_sel_m = jQuery("#ficheros").getGridParam('selarrrow');
                if (fila_sel_m.length > 1) {
                    $('#botonDetalleFicheros').hide("fast");
                } else if (fila_sel_m.length === 1)
                    $('#botonDetalleFicheros').show("fast");
                    $('#botonBorrarFichero').show("fast");

                if (fila_sel_m.length === 0) {
                    $('#botonDetalleFicheros').hide("fast");
                    $('#botonBorrarFichero').hide("fast");
                }

             },
            loadComplete: function() {  // seleccionando el primer registro de la tabla
                $('#botonNuevoFichero').show("fast");
                $('#botonDetalleFicheros').hide("fast");
                $('#botonDetalleRemedy').hide("fast");
                $('#botonBorrarFichero').hide("fast");
                $('#botonActivarFichero').hide("fast");
                $('#grupoBotonesFicheros').width('220');
                if (codenv) {
                    $('#grupoBotonesFicheros').width('120');
                    $('#botonDetalleRemedy').show("fast");


                }
                $('#botonImportarRemedy').show("fast");

                //$('#botonExportarRemedy').show("fast");

                var rowIds = $("#ficheros").jqGrid('getDataIDs');
                for (var i = 1; i <= rowIds.length; i++) {//iterate over each row
                    rowData = $("#ficheros").jqGrid('getRowData', i);
                    //set background style if ColumnValue == true

                    if (rowData['FILE_ESTADO'] === 'BAJA') {

                        $("#ficheros").setCell(i, 'CODENVIO', '', {'color': 'red'});
                        $("#ficheros").setCell(i, 'CODENVIO', '', 'fich_baja');

                        $("#ficheros").setCell(i, 'FICHERO_ORIGEN', '', {'color': 'red'});
                        $("#ficheros").setCell(i, 'FICHERO_ORIGEN', '', 'fich_baja');
                        $("#ficheros").setCell(i, 'DESCLASIFICACION', '', {'color': 'red'});
                        //$("#ficheros").setCell(i, 'DESCLASIFICACION', '', 'fich_baja');
                        $("#ficheros").setCell(i, 'DESNIVEL_LOPD', '', {'color': 'red'});
                        //$("#ficheros").setCell(i, 'DESNIVEL_LOPD', '', 'fich_baja');
                        $("#ficheros").setCell(i, 'FILE_DESCANAL', '', {'color': 'red'});
                        $("#ficheros").setCell(i, 'FILE_DESCANAL', '', 'fich_baja');

                    }

                } //for

                // $(".fich_baja").toggle();
                //jQuery('#remedylist').jqGrid('setSelection', selid); //mantener selección de fila

            }
        }).navGrid('#pager2', {edit: false, add: false, del: false, view: false,
            beforeRefresh: function() {
                jQuery("#ficheros").GridUnload();

                GridFicheros();



            }

        },
        {}, //  default settings for edit
                {}, //  default settings for add
                {}, // delete instead that del:false we need this
                {
                    multipleSearch: true,
                    width: 575,
                    sopt: ['cn', 'eq', 'bw', 'ew'],
                    closeAfterSearch: true,
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

                        $(".data").children('input').focus();
                    }
                }, // search options
        {} /* view parameters*/
        )
                // .navButtonAdd('#pager2', {
                //caption: "",
                //title: "Configurar Columnas",
                //buttonicon: "ui-icon-gear",
                //onClickButton: function() {
                //  alert("Con este botón se configuran las columnas que se quieran ver u ocultar");
                //},
                //position: "last"
                //})
                /*  .navButtonAdd('#pager2', {
                 caption: "",
                 title: "Mostrar/Ocultar Ficheros de Baja",
                 buttonicon: "ui-icon-cancel",
                 onClickButton: function() {
                 if(baja==0)baja=1;
                 else baja=0;
                 
                 //$(".fich_baja").toggle();
                 if(baja==1){
                 //jQuery("#remedylist").GridUnload();
                 //$('#idEnv').val('');
                 //GridRemedy(0,0,2);
                 jQuery("#ficheros").GridUnload();
                 GridFicheros(0,0,baja);
                 
                 }else{
                 //jQuery("#remedylist").GridUnload();
                 //$('#idEnv').val('');
                 //GridRemedy();
                 jQuery("#ficheros").GridUnload();
                 GridFicheros();
                 
                 }
                 
                 },
                 position: "last"
                 })*/
                .navButtonAdd('#pager2', {
                    caption: "",
                    title: "Exportar",
                    buttonicon: "ui-icon-extlink",
                    onClickButton: function() {
                        var exsql = $('#ficheros').getGridParam('userData').SQL;
                        var campos = $('#ficheros').getGridParam('userData').campos;
                        var order = $('#ficheros').getGridParam('userData').order;
                        var namefile = prompt("Por favor, proporcione un nombre de fichero sin extensión, para exportar los datos: ", "listado_ficheros");
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
                });
    } //Fin GridFicheros

    $(document).keypress(function(e) {
        if (e.which == 13) {
            $("#fbox_remedylist_search").click();
            setTimeout(function() {
                $("#fbox_remedylist_search").click();
            }, 1000);
            $("#fbox_ficheros_search").click();
            setTimeout(function() {
                $("#fbox_ficheros_search").click();
            }, 1000);



        }
    });

    $(document).ready(function() {

        /*$("input[type=text]").keyup(function(e) {
            if (e.which >= 65) {
                $(this).val($(this).val().toUpperCase());
            }
        });*/
        $("input[type=text]").css({'text-transform':'uppercase'});
        
        $("#openfich").click(function() {
            location.href = this.href; // ir al link
        });



        $('#botonImportarRemedy').hide("fast");
        //$('#botonExportarRemedy').hide("fast");
        $('#botonBorrarFichero').hide("fast");
        $('#botonActivarFichero').hide("fast");
        $('#botonNuevoFichero').hide("fast");
        GridRemedy();
        GridFicheros();


        // Manejo de los botones, y ventanas modales -------------------------------------

        $("#modalDetalleRemedy").dialog({
            autoOpen: false,
            height: 680,
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
            }
        });


        $('#botonDetalleRemedy').button().click(function(evento) {
            evento.preventDefault();

            var fila_sel = jQuery("#ficheros").getGridParam('selrow');
            var campos = jQuery("#ficheros").getRowData(fila_sel);

            var codenv = campos.CODENVIO;

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
            height: 680,
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
            var fila_sel = jQuery("#ficheros").getGridParam('selrow');
            var campos = jQuery("#ficheros").getRowData(fila_sel);

            var idFich = campos.CODFICHERO;
            var codenv = campos.CODENVIO;



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
                setTimeout(function() {
                    $('#botonDetalleRemedy').click();
                }, 500);

                $('#modalDetalleRemedy').dialog('option', 'height', 680);
                $('#modalDetalleRemedy').dialog('option', 'width', 340);
                $('#modalDetalleFicheros').dialog('option', 'height', 680);
                $('#modalDetalleFicheros').dialog('option', 'width', 340);
                $('#modalDetalleRemedy').dialog('option', 'position', [498, 29]);
                $('#modalDetalleFicheros').dialog('option', 'position', [835, 29]);
                $('#modalDetalleRemedy').dialog('open');


            }

            $('#modalDetalleFicheros').dialog('open');


        });

        $('#botonBorrarFichero').button().click(function(evento) {

            evento.preventDefault();
            //if (confirm('Se dispone a dar de baja la selección de ficheros. ¿Desea Continuar? ')) {

            $('#opcion4').val("borrar");
            var mot = prompt('Introducir Motivo de Baja:');
            if (mot == '')
                mot = 'FIN AUTORIZACION DE LA CESION';
            $('#NCODMOTIVOBAJA').val(mot);

            bajamultiple(reloadGridFich);

            //---- fin multi

            //}

        });



        //  $("#innerRemedy tr:odd").css("background-color", "#ddd"); // filas impares
        //$("#innerRemedy tr:even").css("background-color", "#ccc"); // filas pares

        //   $("#modalDetalleFicheros tr:odd").css("background-color", "#ddd"); // filas impares
        // $("#modalDetalleFicheros tr:even").css("background-color", "#ccc"); // filas pares
        //$("#remedylist").bind("ondblClickRow", function() {
        //  return false;
        //});
        //$("#ficheros").bind("ondblClickRow", function() {
        //return false;
        //});
        $("#NCONTACTO").autocomplete({
            source: 'json/autocontacto_json.php'
        });
        $('#NCODEMPRESA').change(function() {

            var sel = $('#NCODEMPRESA').val();
            if (sel == '00000')
                sel == '';
            $("#NCONTACTO").autocomplete({
                source: 'json/autocontacto_json.php?CODEMPRESA=' + sel
            });

        });


        $("#ERCONTACTO").autocomplete({
            source: 'json/autocontacto_json.php'
        });
        $('#ERCODEMPRESA').change(function() {

            var sel = $('#ERCODEMPRESA').val();
            if (sel == '00000')
                sel == '';
            $("#ERCONTACTO").autocomplete({
                source: 'json/autocontacto_json.php?CODEMPRESA=' + sel
            });

        });


        $('.noAnalizar').hide();

        $("li").css({background: "#4875e8"});
        $("#cesiones").css({background: "#77c5e1"}); //seleccionar opción actual del menú principal

    });







</script>