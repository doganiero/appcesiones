<?
// Comprobación de autenticación.

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

<!-- Modal Nuevo Carga -->
<?
include_once('../view/modal/cargas_modal.php');
?>
<!-- Modal Detalle carga -->
<?
include_once('../view/modal/detallecargas_modal.php');
?>
<!-- Modal Nuevo Carga -->
<?
include_once('../view/modal/infoconsolida_modal.php');
?>
<!-- Modal Seleccionar Canal -->

<?
include_once('../view/modal/selcanal_modal.php');
?>

<!-- Fin Ventanas Modales -->

<!--<div align="center" id="titulo" class="titulo2">Gestión de Logs</div>-->

<!--- Tabla de Cargas Log -->
<div class="unaCol" align="center" style="height: 600px" >

    <table id="cargaslog" class="scroll"><tr><td></td></tr></table>
    <div id="pager" class="scroll" style=""></div>

    <!--- Fin Tabla de de Cargas Log -->

    <!-- Botones acciones listado Cargas --> 

    <div id="grupoBotonesCargas" class="colDerecha" style='width: 350px;'>
        <table style="padding-top:10px;">
            <tr><td>
                    <input id="botonConsolidarCargas" type="submit" value="Consolidar" class="boton ui-corner-all" 
                           style="width:100px; margin-bottom:9px;" />             
                    <input id="botonDetalleCargas" type="submit" value="Ver Detalles" class="boton ui-corner-all" 
                           style="width:100px; margin-bottom:9px; display: none ;" />
                    <input id="botonNuevoCargas" type="submit" value="Nueva Carga" class="boton ui-corner-all" 
                           style="width:110px; margin-bottom:9px;" />



                    <input id="botonBorrarCargas" type="submit" value="Eliminar" class="boton ui-corner-all" 
                           style="width:100px; margin-bottom:9px;" />
                    <input id="botonArchivarCargas" type="submit" value="Archivar" class="boton ui-corner-all" 
                           style="width:100px; margin-bottom:9px;" />
                    <input id="sigcarga" type="hidden" name="sigcarga"/>
                    <input id="cuentalineas" type="hidden" name="cuentalineas"/>

                </td>
            </tr>
        </table>

    </div>
    <!-- FIN Botones acciones listado Cargas -->
    <div style="padding-top:13px; margin-left: 6px; margin-right: 8px">
        <div id="progressbar" ><div class="progress-label"></div></div>
    </div>

</div>


<script type="text/javascript">
    // Complemento para carga asíncrona con formularios.
    // prepara el formulario cuando el DOM está preparado.
    $(document).ready(function() {




        // Configuración de Formulario Jquery Ajax de Carga
        var options = {
            target: '#mensajeCarga', // target element(s) to be updated with server response 

            clearForm: false, // clear all form fields after successful submit 
            resetForm: false, // reset the form after successful submit 

            success: showResponse

        };
        // bind form using 'ajaxForm' 
        $('#cargaForm').ajaxForm(options);




    });
    // post-submit callback 

    function enProceso(i, scrollPos, codcarga) {

        $.getJSON("json/showprocess_json.php",
                function(data) {
                    var rowData = $("#cargaslog").getRowData(i);
                    if (data.process > 1) {
                        if (rowData['CODCARGA'] === codcarga) {
                            setTimeout(function() {
                                $("#cargaslog").setCell(i, 'CONSOLIDADO', '', {'color': 'blue'});
                                $("#cargaslog").setCell(i, 'CONSOLIDADO', '', {'font-weight': 'bold'});

                            }, 300);

                            setTimeout(function() {
                                $("#cargaslog").setCell(i, 'CONSOLIDADO', '', {'color': 'red'});
                                $("#cargaslog").setCell(i, 'CONSOLIDADO', '', {'font-weight': 'normal'});
                                scrollPos = jQuery("#cargaslog").closest(".ui-jqgrid-bdiv").scrollTop();
                                enProceso(i, scrollPos, codcarga);
                            }, 600);



                        } else {
                            $("#cargaslog").setCell(i, 'CONSOLIDADO', '', {'color': 'black'});
                            if (rowData['CHKCONSOLIDADO'] === 'N')
                                $("#cargaslog").setCell(i, 'CODCARGA', '', {'color': 'green'});

                        }

                    } else
                        $("#cargaslog").trigger('reloadGrid');
                    setTimeout(function() {
                        jQuery("#cargaslog").closest(".ui-jqgrid-bdiv").scrollTop(scrollPos);
                    }, 600);

                });
    }
    ;


    function showResponse(responseText, statusText, xhr, $form) { //respuesta de cargas
        jQuery("#cargaslog").trigger("reloadGrid");
        //mensaje(responseText, $form.attr('id'));
        var capa;
        if ($('#opcion5').val() === 'consolidar' || $('#opcion5').val() === 'consolidarm') {
            capa = 'modalInfoConsolida';

            //-------------------------------------------AJAX CONSULTA INFO CONSOLIDACIÓN
            var idCarga;
            idCarga = $('#idCarga').val();
            if (!idCarga)
                idCarga = 0;
            $.ajax({
                type: "POST",
                url: 'json/detalleconsolida_json.php?codcarga=' + idCarga + '',
                dataType: 'json',
                success: function(data) {

                    $('#ICODCARGA').val(idCarga);
                    $('#tcons').val(data.TOTAL);
                    $('#ts').val(data.S);
                    $('#tn').val(data.N);
                    $('#td').val(data.D);
                    $('#te').val(data.E);



                },
                error: function(e) {
                    alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                }
            });

            //---------------------------------------------------------------



            $('#modalInfoConsolida').dialog('open');
        }
        if ($('#opcion5').val() === 'nuevo')
            capa = 'titulo';
        if ($('#opcion5').val() === 'borrar' || $('#opcion5').val() === 'archivar')
            capa = 'titulo';

        mensaje(responseText, capa);


    }


    // Change serach default from selection option to the one with value = '3'
    function changeSearchDefault(select) {

        select.children("option:selected").removeAttr("selected");
        var newdefault = select.children("option[value='FICHERO_LOG']");
        newdefault.attr("selected", "selected");
        newdefault.trigger("change");
    }

    function GridCargas() { // esta función crea el grid y filtra la tabla según parámetros
        var selid;
        var rowData = [];
        var seleccionar;
        var change_search_now = false;
        jQuery("#cargaslog").jqGrid({
            scrollrows: true,
            url: 'json/cargas_json.php',
            width: 1140,
            height: 530,
            datatype: "json",
            colNames: [
                'CARGA',
                'DESCRIPCIÓN',
                'FICHERO LOG',
                'CARGA_CODCANAL',
                'CANAL',
                'EJERCICIO',
                'MES',
                'CONSOLIDADO',
                'CONSOLIDADO',
                'FECHA ALTA',
                'CARGA_FECHA_MODIFICACION',
                'CARGA_FECHA_BAJA',
                'CARGA_CODUSUARIO_ALTA',
                'CARGA_DESUSUARIO_ALTA',
                'CARGA_CODUSUARIO_MODIFICACION',
                'CARGA_DESUSUARIO_MODIFICACION',
                'CARGA_CODUSUARIO_BAJA',
                'CARGA_DESUSUARIO_BAJA',
                'CARGA_ESTADO',
                'OBSERVACIONES'
            ],
            colModel: [
                //, editable: true, sorttype:"text",editable:true,edittype:"select",editoptions:   
                //{ value:"1:one;2:two"},editrules:{required:true} ejemplo de campo editable. hay que modificar la propiedad editable del nav a true
                {name: 'CODCARGA', index: 'CODCARGA', align: 'center', width: 60},
                {name: 'DESCARGA', index: 'DESCARGA', align: 'left', width: 200},
                {name: 'FICHERO_LOG', index: 'FICHERO_LOG', align: 'center', width: 150},
                {name: 'CARGA_CODCANAL', index: 'CARGA_CODCANAL', align: 'center', hidden: true},
                {name: 'CARGA_DESCANAL', index: 'CARGA_DESCANAL', align: 'center', width: 100},
                {name: 'EJERCICIO', index: 'EJERCICIO', align: 'center', width: 50, hidden: true},
                {name: 'MES', index: 'MES', align: 'center', width: 50, hidden: true},
                {name: 'CHKCONSOLIDADO', index: 'CHKCONSOLIDADO', align: 'center', width: 80, hidden: true},
                {name: 'CONSOLIDADO', index: 'CONSOLIDADO', align: 'center', width: 80},
                {name: 'CARGA_FECHA_ALTA', index: 'CARGA_FECHA_ALTA', align: 'center', formatter: 'date', formatoptions: {
                        srcformat: 'Y-m-d H:i:s', newformat: 'd-m-Y H:i:s', defaultValue: null}, width: 100},
                {name: 'CARGA_FECHA_MODIFICACION', index: 'CARGA_FECHA_MODIFICACION', align: 'center', hidden: true},
                {name: 'CARGA_FECHA_BAJA', index: 'CARGA_FECHA_BAJA', align: 'center', hidden: true},
                {name: 'CARGA_CODUSUARIO_ALTA', index: 'CARGA_CODUSUARIO_ALTA', align: 'center', hidden: true},
                {name: 'CARGA_DESUSUARIO_ALTA', index: 'CARGA_DESUSUARIO_ALTA', align: 'center', hidden: true},
                {name: 'CARGA_CODUSUARIO_MODIFICACION', index: 'CARGA_CODUSUARIO_MODIFICACION', align: 'center', hidden: true},
                {name: 'CARGA_DESUSUARIO_MODIFICACION', index: 'CARGA_DESUSUARIO_MODIFICACION', align: 'center', hidden: true},
                {name: 'CARGA_CODUSUARIO_BAJA', index: 'CARGA_CODUSUARIO_BAJA', align: 'center', hidden: true},
                {name: 'CARGA_DESUSUARIO_BAJA', index: 'CARGA_DESUSUARIO_BAJA', align: 'center', hidden: true},
                {name: 'CARGA_ESTADO', index: 'CARGA_ESTADO', align: 'center', hidden: true},
                {name: 'OBSERVACIONES', index: 'OBSERVACIONES', align: 'center', hidden: true}

            ],
            pager: jQuery('#pager'),
            rowNum: 50,
            loadui: "disable",
            imgpath: 'css/themes/custom-theme/images',
            sortname: 'CODCARGA',
            sortorder: "desc",
            loadonce: false,
            viewrecords: true,
            caption: "LISTADO DE CARGAS LOG",
            ondblClickRow: function(rowid)
            {
                $('#botonDetalleCargas').click();

            },
            onSelectRow: function() { // eventos al seleccionar filas y cargar ficheros
                var fila_sel = jQuery("#cargaslog").getGridParam('selrow');
                var campos = jQuery("#cargaslog").getRowData(fila_sel);
                $('#idCarga').val(campos.CODCARGA);
                selid = jQuery("#cargaslog").getGridParam('selrow');
                $('#botonBorrarCargas').hide(0);
                $('#botonConsolidarCargas').hide(0);
                $('#botonEditarCargas').hide(0);
                $('#grupoBotonesCargas').width('460');
                $('#botonDetalleCargas').show(0);
                if (fila_sel != 1)
                    $('#botonArchivarCargas').show(0);
                else
                    $('#botonArchivarCargas').hide(0);
                $('#botonConsolidarCargas').show(0);

                if (campos.CHKCONSOLIDADO === 'S')
                {
                    if (fila_sel != 1)
                        $('#grupoBotonesCargas').width('460');
                    else
                        $('#grupoBotonesCargas').width('340');


                }

                if (campos.CHKCONSOLIDADO === 'N')
                {
                    if (fila_sel != 1)
                        $('#grupoBotonesCargas').width('560');
                    else
                        $('#grupoBotonesCargas').width('440');
                    $('#botonBorrarCargas').show('fast');

                }
                if (campos.CHKCONSOLIDADO == 'P')
                {

                    $('#grupoBotonesCargas').width('360');

                    $('#botonConsolidarCargas').hide(0);
                    $('#botonArchivarCargas').hide(0);
                    $('#botonBorrarCargas').show('fast');
                    // aquí llamar a la barra de carga



                }
                if (campos.CHKCONSOLIDADO == 'M')
                {
                    if (fila_sel != 1)
                        $('#grupoBotonesCargas').width('350');
                    else
                        $('#grupoBotonesCargas').width('230');
                    $('#botonConsolidarCargas').hide(0);

                }




            },
            loadComplete: function() {  // seleccionando el primer registro de la tabla
                $('#botonDetalleCargas').hide(0);
                $('#botonBorrarCargas').hide(0);
                $('#botonArchivarCargas').hide(0);
                $('#botonConsolidarCargas').hide(0);
                $('#botonEditarCargas').hide(0);
                $('#grupoBotonesCargas').width('130');
                var rowIds = $("#cargaslog").jqGrid('getDataIDs');
                for (var i = 1; i <= rowIds.length; i++) {//iterate over each row
                    rowData = $("#cargaslog").jqGrid('getRowData', i);
                    //set background style if ColumnValue == true

                    if (rowData['CHKCONSOLIDADO'] === 'N') {

                        $("#cargaslog").setCell(i, 'CODCARGA', '', {'color': 'green'});
                        $("#cargaslog").setCell(i, 'DESCARGA', '', {'color': 'green'});
                        $("#cargaslog").setCell(i, 'FICHERO_LOG', '', {'color': 'green'});
                        $("#cargaslog").setCell(i, 'CARGA_DESCANAL', '', {'color': 'green'});
                        $("#cargaslog").setCell(i, 'EJERCICIO', '', {'color': 'green'});
                        $("#cargaslog").setCell(i, 'MES', '', {'color': 'green'});
                        $("#cargaslog").setCell(i, 'CONSOLIDADO', '', {'color': 'green'});
                        $("#cargaslog").setCell(i, 'CARGA_FECHA_ALTA', '', {'color': 'green'});
                        $("#cargaslog").setCell(i, 'CARGA_DESUSUARIO_ALTA', '', {'color': 'green'});
                        //$("#cargaslog").jqGrid('setCell',i,'label', '',{'color':'red'});
                    }

                    if (rowData['CHKCONSOLIDADO'] === 'P') {

                        $("#cargaslog").setCell(i, 'CODCARGA', '', {'color': 'red'});
                        $("#cargaslog").setCell(i, 'DESCARGA', '', {'color': 'red'});
                        $("#cargaslog").setCell(i, 'FICHERO_LOG', '', {'color': 'red'});
                        $("#cargaslog").setCell(i, 'CARGA_DESCANAL', '', {'color': 'red'});
                        $("#cargaslog").setCell(i, 'EJERCICIO', '', {'color': 'red'});
                        $("#cargaslog").setCell(i, 'MES', '', {'color': 'red'});
                        $("#cargaslog").setCell(i, 'CONSOLIDADO', '', {'color': 'red'});
                        $("#cargaslog").setCell(i, 'CARGA_FECHA_ALTA', '', {'color': 'red'});
                        $("#cargaslog").setCell(i, 'CARGA_DESUSUARIO_ALTA', '', {'color': 'red'});

                        var scrollPos = jQuery("#cargaslog").closest(".ui-jqgrid-bdiv").scrollTop();
                        var alertar_proc_carga = rowData['CODCARGA'];
                        enProceso(i, scrollPos, alertar_proc_carga);


                    }

                } //for
                //jQuery("#cargaslog").getGridParam('selrow').focus();
                //jQuery('#cargaslog').jqGrid('setSelection', selid); //mantener selección de fila
                //jQuery('#cargaslog').jqGrid('setSelection','');

            },
            onPaging: function(pgButton) {

            }, //al cambiar página del grid volver a cargar ficheros y perder selección de un subgrid

        }).navGrid('#pager', {edit: false, add: false, del: false, view: false,
            beforeRefresh: function() {
                jQuery("#cargaslog").GridUnload();
                GridCargas();
            }

        },
        {}, //  default settings for edit
                {}, //  default settings for add
                {}, // delete instead that del:false we need this
                {
                    sopt: ['cn', 'eq', 'bw', 'ew'],
                    //multipleSearch:true,
                    closeAfterSearch: true,
                    afterShowSearch: function() {
                        $(".data").children('input').focus();
                    },
                    ////showQuery: true,
                    //multipleGroup:true

                    onInitializeSearch: function() {
                        change_search_now = true;
                        changeSearchDefault($('.columns').children("select"));
                        $("#fbox_cargaslog_reset").click(function() {
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
                    }


                }, // search options
        {} /* view parameters*/
        );
    } // fin funcion GridCargas

    $(document).keypress(function(e) {
        if (e.which == 13) {

            $("#fbox_cargaslog_search").click();
            setTimeout(function() {
                $("#fbox_cargaslog_search").click();
            }, 1000);

        }
    });

    $(document).ready(function() {




        /*
         
         $("input[type=text]").keyup(function(e) {
         if (e.which >= 65) {
         $(this).val($(this).val().toUpperCase());
         }
         });*/
        $("input[type=text]").css({'text-transform': 'uppercase'});
        GridCargas();
        // Manejo de los botones, y ventanas modales -------------------------------------

        $("#modalDetalleCargas").dialog({
            autoOpen: false,
            height: 500,
            width: 385,
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
        $('#botonDetalleCargas').button().click(function(evento) {
            evento.preventDefault();
            var idCarga = $('#idCarga').val();
            if (!idCarga)
                idCarga = 0;

            //-------------------------------------------------------------------------------------

            // AJAX CONSULTA VENTANA MODAL DETALLE Cargas

            $.ajax({
                type: "POST",
                url: 'json/detallecargas_json.php?codcarga=' + idCarga + '',
                dataType: 'json',
                success: function(data) {

                    $('#CODCARGA').text('CARGA: ' + data.CODCARGA + '');
                    $('#DESCARGA').val(data.DESCARGA);
                    $('#FICHERO_LOG').val(data.FICHERO_LOG);
                    $('#CARGA_CODCANAL').val(data.CARGA_CODCANAL);
                    $('#CARGA_DESCANAL').val(data.CARGA_DESCANAL);
                    $('#EJERCICIO').val(data.EJERCICIO);
                    $('#MES').val(data.MES);
                    //$('#CHKCONSOLIDADO').val(data.CHKCONSOLIDADO); // deshabilitado para vista de detalle
                    $('#CONSOLIDADO').val(data.CONSOLIDADO);
                    $('#CARGA_FECHA_ALTA').val(data.CARGA_FECHA_ALTA);
                    $('#CARGA_FECHA_MODIFICACION').val(data.CARGA_FECHA_MODIFICACION);
                    $('#CARGA_FECHA_BAJA').val(data.CARGA_FECHA_BAJA);
                    $('#CARGA_CODUSUARIO_ALTA').val(data.CARGA_CODUSUARIO_ALTA);
                    $('#CARGA_DESUSUARIO_ALTA').val(data.CARGA_DESUSUARIO_ALTA);
                    $('#CARGA_CODUSUARIO_MODIFICACION').val(data.CARGA_CODUSUARIO_MODIFICACION);
                    $('#CARGA_DESUSUARIO_MODIFICACION').val(data.CARGA_DESUSUARIO_MODIFICACION);
                    $('#CARGA_CODUSUARIO_BAJA').val(data.CARGA_CODUSUARIO_BAJA);
                    $('#CARGA_DESUSUARIO_BAJA').val(data.CARGA_DESUSUARIO_BAJA);
                    $('#CARGA_ESTADO').val(data.CARGA_ESTADO);
                    $('#CARGA_ESTADO').val(data.CARGA_ESTADO);
                    $('#OBSERVACIONES').val(data.OBSERVACIONES);

                },
                error: function(e) {
                    alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                }
            });
            //--------------------------------------------------------------------------------------


            $('#modalDetalleCargas').dialog('open');
            return false;
        });



        $('#botonBorrarCargas').button().click(function(evento) {
            evento.preventDefault();
            $('#opcion5').attr({value: "borrar"});

            $('#cargaForm').submit();

        });

        $('#botonArchivarCargas').button().click(function(evento) {
            evento.preventDefault();

            if (confirm('Se dispone a archivar la carga del Log seleccionado, no podrá deshacer este cambio. ¿Desea continuar?'))
            {
                $('#opcion5').attr({value: "archivar"});

                $('#cargaForm').submit();
            }

        });

        //----------------------- Nueva Carga

        $('#botonNuevoCargas').button().click(function(evento) {
            evento.preventDefault();
            var fecha = new Date();
            var mes = fecha.getMonth() + 1;
            if (mes <= 9)
                mes = '0' + mes;
            var ejercicio = fecha.getFullYear();

            //$("#fcodcanal-00001").attr({selected: 'selected'});
            $("#FDESCARGA").val("");
            $("#fcodcanal-00000").attr({selected: 'selected'});
            $(".inicial").attr({selected: 'selected'});
            $('.inicial').text('Por favor seleccione un Canal');
            $("#MES-" + mes).attr({selected: 'selected'});
            $('#EJERCICIO-' + ejercicio).attr({selected: 'selected'});
            $(".obs").val("");
            $('#opcion5').attr({value: "nuevo"});
            $('#modalCargas').dialog('open');

            return false;
        });

        $("#modalCargas").dialog({
            autoOpen: false,
            height: 320,
            width: 460,
            bgiframe: true,
            resizable: false,
            modal: true,
            overlay: {
                backgroundColor: '#000',
                opacity: 0.5
            },
            buttons: {
                'Cargar': function() {



                    var canal;
                    var igual;
                    if ($('#FDESCARGA').val() == "") {
                        //$('#DESCARGA').focus().after("<span class='error'>Ingrese una descripción para la carga</span>");
                        $('#modalCargas').jAlert('Ingrese una descripción para la carga', "fatal");
                        setTimeout(function() {
                            $('.msg-fatal').fadeOut(500, function() {
                                $('.msg-fatal').remove();
                            })
                        }, 2500);

                        return false;
                    }

                    if ($('#FFICHERO_LOG').val() == "") {
                        //$('#FDESCARGA').focus().after("<span class='error'>Ingrese una descripción para la carga</span>");
                        $('#modalCargas').jAlert('Seleccione un Fichero LOG para cargar', "fatal");
                        setTimeout(function() {
                            $('.msg-fatal').fadeOut(500, function() {
                                $('.msg-fatal').remove();
                            })
                        }, 2500);
                        return false;
                    }
                    else
                    {
                        var filename = $('#FFICHERO_LOG').val();
                        var codcanal = $('#FCODCANAL').val();

                        //Ajax de consulta de ficheros logs en la BD (error o alerta)

                        $.ajax({
                            type: "POST",
                            url: 'json/buscafich_json.php',
                            dataType: 'json',
                            async: false,
                            data: {filename: filename, codcanal: codcanal},
                            success: function(data) {
                                if (data.IGUAL > 0) { // ya existe otro fichero igual en el mismo canal
                                    $('#modalCargas').jAlert('Ya existe una carga con el mismo nombre de fichero para el canal seleccionado', "fatal");
                                    setTimeout(function() {
                                        $('.msg-fatal').fadeOut(500, function() {
                                            $('.msg-fatal').remove();
                                        })
                                    }, 2500);
                                    igual = true;
                                }
                                else if (data.CANAL > 0) {// existe un fichero igual para otro canal

                                    canal = true;
                                }

                            },
                            error: function(e) {
                                alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                            }
                        });

                    } // fin else  restricción "" de fichero LOG
                    //alert('igual:'+igual+' - canal:'+canal+'');
                    if (igual)
                        return false;
                    if (canal)
                        if (confirm('Ya existe un fichero de log con el mismo nombre. ¿Desea continuar con la carga? ')) {

                        }
                        else
                            return false;
                    $('#cargaForm').submit();
                    $(this).dialog('close');

                },
                'Cancelar': function() {
                    $(this).dialog('close');
                }
            },
            close: function() {
                $('#progressbar').show();
                var filename = $('#FFICHERO_LOG').val();
                var codcanal = $('#FCODCANAL').val();
                $.getJSON("json/sigcarga_json.php?FFICHERO_LOG=" + filename + "&FCODCANAL=" + codcanal,
                        function(data) {
                            $("#sigcarga").val(data.sig_carga);
                            $("#cuentalineas").val(data.cuentalineas);
                        });




                var progressbar = $("#progressbar"),
                        progressLabel = $(".progress-label");
                progressbar.progressbar({
                    value: false,
                    change: function() {
                        if (progressbar.progressbar("value") !== false)
                            progressLabel.text(progressbar.progressbar("value") + "%");
                    },
                    complete: function() {

                        progressLabel.text("");
                        $('#progressbar').hide();
                        $('#grupoBotonesCargas').show();

                    }
                });
                function progress() {
                    var sig_carga = $("#sigcarga").val();
                    var cuentalineas = $("#cuentalineas").val();
                    var val = progressbar.progressbar("value") || 0;


                    $.getJSON("json/barracarga_json.php?sig_carga=" + sig_carga + "&cuentalineas=" + cuentalineas + "&FCODCANAL=" + codcanal,
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



        //-----------------------------------Consolidar Carga

        $("#modalInfoConsolida").dialog({
            autoOpen: false,
            height: 290,
            width: 350,
            bgiframe: true,
            resizable: false,
            modal: true,
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
                //$('#cargaForm').clearForm();
            }
        });


        // -------------CONSOLIDACIÓN

        $('#idCanalSelect').val('');

        $('#SELCODCANAL').change(function() {
            var sel = $('#SELCODCANAL').val();

            $('#idCanalSelect').val(sel);

        });

        $('#botonConsolidarCargas').button().click(function(evento) {
            evento.preventDefault();
            $('#SELCODCANAL').val('');
            var fila_sel = jQuery("#cargaslog").getGridParam('selrow');
            var campos = jQuery("#cargaslog").getRowData(fila_sel);

            $('#opcion5').attr({value: "consolidar"});

            if (campos.CHKCONSOLIDADO == 'S')
                $('#opcion5').attr({value: "consolidarm"});

            var descanal = campos.CARGA_DESCANAL;
            if (descanal.search('GEPP-') !== -1 && campos.CHKCONSOLIDADO == 'N') {
                $('#modalSelCanal').dialog('open');
            } else {
                $('#cargaForm').submit();
                $('#progressbar').show();
                var progressbar = $("#progressbar"),
                        progressLabel = $(".progress-label");
                progressbar.progressbar({
                    value: false,
                    change: function() {
                        if (progressbar.progressbar("value") !== false)
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


                    $.getJSON("json/barraconsolida_json.php?sig_carga=" + campos.CODCARGA,
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
            if (campos.CHKCONSOLIDADO != 'N') {

                var scrollPos = jQuery("#cargaslog").closest(".ui-jqgrid-bdiv").scrollTop();
                setTimeout(function() {
                    $("#cargaslog").trigger('reloadGrid');

                }, 2000);

                setTimeout(function() {
                    jQuery("#cargaslog").closest(".ui-jqgrid-bdiv").scrollTop(scrollPos);

                }, 2500);
            }
            return false;
        });




//----------------SELECCIÓN DE FICHEROS POR CANAL
        $('#fcodcanal-00000').text('Seleccionar...');
        var codcanal = $('#FCODCANAL').val();
        if (codcanal == '00000')
        {
            $('.inicial').text('Por favor seleccione un Canal');

        }


        $('#FCODCANAL').change(function() {
            $('.agregada').remove();
            codcanal = $('#FCODCANAL').val();
            if (codcanal != '00000')
            {
                $('.inicial').text('Seleccionar...');

            } else
                $('.inicial').text('Por favor seleccione un Canal');
            $.ajax({
                type: "POST",
                url: 'json/buscalog_json.php',
                dataType: 'json',
                async: false,
                data: {codcanal: codcanal},
                success: function(data) {

                    for (var i = 2; i < data.length; i++) {//iterate over each row

                        $('#FFICHERO_LOG').append('<option class="agregada" value="' + data[i] + '" selected="selected">' + data[i] + '</option>');
                    } //for

                    $('.inicial').attr({selected: 'selected'});



                },
                error: function(e) {
                    alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                }
            });




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


        $("li").css({background: "#4875e8"});
        $("#log").css({background: "#77c5e1"}); //seleccionar opción actual del menú principal

    });





</script>