<?
// Comprobación de autenticación.

include_once('../functions/db.php');
$link = conectar();
?>
<!--- Mensajes -->


<!--- Fin Mensajes -->

<!-- ventanas Modales -->

<!-- Modal  Nueva Empresa -->
<?
include_once('../view/modal/nuevaempresa_modal.php');
?>
<!-- Modal  Editar Empresa -->
<?
include_once('../view/modal/editarempresa_modal.php');
?>
<!-- Modal  Nuevo Contacto -->
<?
include_once('../view/modal/nuevocontacto_modal.php');
?>
<!-- Modal Detalle Contactos -->
<?
include_once('../view/modal/detallecontacto_modal.php');
?>
<!-- Modal Editar Contacto -->
<?
include_once('../view/modal/editarcontacto_modal.php');
?>
<!-- Fin Ventanas Modales -->

<!--<div align="center" id="titulo" class="titulo2">Gestión de Empresas y Contactos</div>-->
<div class="centradomasGrande" align="center" style="height: 600px">



    <!--- Tabla de Cargas Buffer -->
    <div class="colIzquierda" align="center" style="height: 600px; " >

        <table id="empresaslist" class="scroll"><tr><td></td></tr></table>
        <div id="pager" class="scroll" style=""></div>

        <!--- Fin Tabla de de Cargas Buffer -->

        <!-- Botones acciones listado Buffer --> 

        <div id="grupoBotonesEmpresas" class="" style='width: 395px;'>
            <table width='395' style="padding-top:10px;">
                <tr><td align="right">

                        <input id="botonActivarEmpresa" type="submit" value="Activar" class="boton ui-corner-all" 
                               style="width:110px; margin-bottom:9px;" /> 
                        <input id="botonNuevoEmpresa" type="submit" value="Nuevo" class="boton ui-corner-all" 
                               style="width:110px; margin-bottom:9px;" />  



                        <!-- Para el control de pulsación de los botones usaremos otro input oculto -->    
                        <input id="opcionA" type="hidden" name="opcionA" value="nuevo" />
                    </td>
                </tr>
            </table>

        </div>
        <!-- FIN Botones acciones listado Buffer -->
    </div>


    <!--- Tabla de Cargas Ficheros -->
    <div class="colDerecha" align="center" style="height: 600px; " >

        <table id="contactoslist" class="scroll"><tr><td></td></tr></table>
        <div id="pager2" class="scroll" style=""></div>

        <!--- Fin Tabla de de Ficheros -->

        <!-- Botones acciones listado ficheros --> 
        <div class="colDerecha" id="grupoBotonesContactos" style='width: 235px;'>
            <table style="padding-top:10px;">
                <tr><td>


                        <input id="botonDetalleContacto" type="submit" value="Detalles" class="boton ui-corner-all" 
                               style="width:110px; margin-bottom:9px;" />
                        <input id="botonNuevoContacto" type="submit" value="Nuevo" class="boton ui-corner-all" 
                               style="width:110px; margin-bottom:9px;" />


                        <!-- Para el control de pulsación de los botones usaremos otro input oculto -->    

                    </td>
                </tr>
            </table>

        </div>
        <!-- FIN Botones acciones listado ficheros -->

    </div>

</div>

<script type="text/javascript">

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
        $('#contactosForm').ajaxForm(options);
        $('#editContactosForm').ajaxForm(options);
        $('#empresasForm').ajaxForm(options);
        $('#editEmpresasForm').ajaxForm(options);


    });

    function formSuccess(data) {

        if (!data.ERROR) {
            if (data.CONT) {
                jQuery("#contactoslist").GridUnload();
                GridContactos();

            }
            if (data.EMP) {
                $("#empresaslist").trigger('reloadGrid');


            }

            $('#modalNuevoContacto').dialog('close');
            $("#modalEditarContacto").dialog('close');
            $("#modalNuevoEmpresa").dialog('close');
            $("#modalEdEmpresa").dialog('close');

            mensaje(data.SUCCESS, 'titulo');

            $('#contactosForm')[0].reset();
            $('#editContactosForm')[0].reset();
            $('#empresasForm')[0].reset();
            $('#editEmpresasForm')[0].reset();
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


    function changeSearchDefault(select) {

        select.children("option:selected").removeAttr("selected");
        var newdefault = select.children("option[value='DESEMPRESA']");
        newdefault.attr("selected", "selected");
        newdefault.trigger("change");
    }


//-----------------Tabla de Empresas-----------------------

    function GridEmpresas(selempresa, cut, baja) { // esta función crea el grid y filtra la tabla según parámetros

        if (!selempresa)
            selempresa = 0;
        if (!cut)
            cut = 0;
        if (!baja)
            baja = 0;
        var change_search_now = false;

        jQuery("#empresaslist").jqGrid({
            url: 'json/empresas_json.php?baja=' + baja,
            width: 395,
            height: 530,
            datatype: "json",
            //type: "POST",
            //data: {coincidencias: coincidencias},
            colNames: [
                'COD.',
                'CODEMPRESAPADRE',
                'DESCRIPCIÓN',
                'CHKACTIVO'
            ],
            colModel: [
                {name: 'CODEMPRESA', index: 'CODEMPRESA', align: 'center', width: 50},
                {name: 'CODEMPRESAPADRE', index: 'CODEMPRESAPADRE', align: 'center', hidden: true},
                {name: 'DESEMPRESA', index: 'DESEMPRESA', align: 'left', width: 300},
                {name: 'CHKACTIVO', index: 'CHKACTIVO', align: 'center', hidden: true}

            ],
            pager: jQuery('#pager'),
            rowNum: 50,
            loadui: "disable",
            imgpath: 'css/themes/custom-theme/images',
            sortname: 'DESEMPRESA',
            sortorder: "asc",
            loadonce: false,
            caption: "LISTADO DE EMPRESAS",
            viewrecords: true,
            recordtext: "{0}/{1} de {2}",
            emptyrecords: "Sin registros",
            ondblClickRow: function(rowid)
            {
                var fila_sel = jQuery("#empresaslist").getGridParam('selrow');
                var campos = jQuery("#empresaslist").getRowData(fila_sel);
                jQuery("#contactoslist").GridUnload();
                GridContactos(campos.CODEMPRESA);
                cut = 0;
                jQuery('#contactoslist').jqGrid('setSelection', 1);
                // $('#botonDetalleContacto').click();                 
            },
            onSelectRow: function() { // eventos al seleccionar filas y cargar ficheros

                cut = 0;
                var fila_sel = jQuery("#empresaslist").getGridParam('selrow');
                var campos = jQuery("#empresaslist").getRowData(fila_sel);


                if (campos.CHKACTIVO == "N" && !campos.CODEMPRESAPADRE) {

                    $('#botonActivarEmpresa').show();

                } else
                    $('#botonActivarEmpresa').hide();


            },
            loadComplete: function() {  // seleccionando el primer registro de la tabla
                var rowData = [];
                var scrollPos;
                var rowIds = $("#empresaslist").jqGrid('getDataIDs');
                for (var i = 1; i <= rowIds.length; i++) {//iterate over each row
                    rowData = $("#empresaslist").jqGrid('getRowData', i);
                    //set background style if ColumnValue == true
                    if (rowData['CODEMPRESAPADRE']) {

                        $("#empresaslist").setCell(i, 'CODEMPRESA', '', {'color': 'LightSlateGray'});
                        $("#empresaslist").setCell(i, 'DESEMPRESA', '', {'color': 'LightSlateGray'});

                    }
                    if (rowData['CHKACTIVO'] == "N" && !rowData['CODEMPRESAPADRE']) {

                        $("#empresaslist").setCell(i, 'CODEMPRESA', '', {'color': 'red'});
                        $("#empresaslist").setCell(i, 'DESEMPRESA', '', {'color': 'red'});

                    }
                    if (rowData['CODEMPRESA'] === selempresa) {
                        jQuery('#empresaslist').jqGrid('setSelection', i);

                    }

                } //for
                scrollPos = jQuery("#empresaslist").closest(".ui-jqgrid-bdiv").scrollTop();
                if (scrollPos > 60)
                    scrollPos = scrollPos + 200;
                if (selempresa)
                    jQuery("#empresaslist").closest(".ui-jqgrid-bdiv").scrollTop(scrollPos);


            },
        }).navGrid('#pager', {edit: false, add: false, del: false, view: false,
            beforeRefresh: function() {
                $('#botonActivarEmpresa').hide();
                jQuery("#empresaslist").GridUnload();
                GridEmpresas();

            }


        },
        {}, //  default settings for edit
                {}, //  default settings for add
                {}, // delete instead that del:false we need this
                {
                    sopt: ['cn', 'eq', 'bw', 'ew'],
                    closeAfterSearch: true,
                    //searchOnEnter: true,
                    // procedimientos para seleccionar una columnna por defecto en la búsqueda
                    onInitializeSearch: function() {
                        change_search_now = true;
                        changeSearchDefault($('.columns').children("select"));
                        $("#fbox_empresaslist_reset").click(function() {
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
                }, // search options
        {} /* view parameters*/
        ).navButtonAdd('#pager', {
            caption: "",
            title: "Eliminar Empresa",
            buttonicon: "ui-icon-trash",
            onClickButton: function() {
                var fila_sel = jQuery("#empresaslist").getGridParam('selrow');
                var campos = jQuery("#empresaslist").getRowData(fila_sel);
                var codempresa = campos.CODEMPRESA;
                if (fila_sel) {

                    if (confirm("Se dispone a dar de baja la empresa seleccionada. ¿Desea Continuar?")) {
                        $.ajax({
                            type: "POST",
                            url: 'edit/empresas_ed.php?borrar=' + codempresa,
                            dataType: 'json',
                            success: formSuccess,
                            error: function(e) {
                                alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                            }
                        });

                    }

                } else
                    alert('Seleccione la empresa a eliminar');

            },
            position: "first"
        }
        ).navButtonAdd('#pager', {
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
                    jQuery("#empresaslist").GridUnload();
                    //$('#idEnv').val('');
                    GridEmpresas(0, cut, baja);
                    //jQuery("#ficheros").GridUnload();
                    //GridFicheros(0,0,baja);

                } else {
                    jQuery("#empresaslist").GridUnload();
                    //$('#idEnv').val('');
                    GridEmpresas();
                    //jQuery("#ficheros").GridUnload();
                    //GridFicheros();
                }
            },
            position: "last"
        }
        ).navButtonAdd('#pager', {
            caption: "",
            title: "Editar Empresa",
            buttonicon: "ui-icon-pencil",
            onClickButton: function() {
                var fila_sel = jQuery("#empresaslist").getGridParam('selrow');
                var campos = jQuery("#empresaslist").getRowData(fila_sel);
                if (fila_sel) {
                    $("#EDCODEMPRESA").val(campos.CODEMPRESA);
                    $("#EDESEMPRESA").val(campos.DESEMPRESA);
                    //$("#ECODEMPRESAPADRE").val(campos.CODEMPRESAPADRE);
                    $("#ECHKACTIVO").val(campos.CHKACTIVO);
                    $("#modalEdEmpresa").dialog('open');
                    return false;
                } else
                    alert('Seleccione una Empresa para editar');

            },
            position: "first"
        });
    } //Fin GridEmpresas

//-------------------------------------------------------------------------------------------------------------------

    function GridContactos(empresa) { // esta función crea el grid y filtra la tabla según parámetros
        if (!empresa)
            empresa = 0;

        jQuery("#contactoslist").jqGrid({
            url: 'json/contactos_json.php?empresa=' + empresa,
            width: 730,
            height: 530,
            datatype: "json",
            colNames: [
                'CODCONTACTO',
                'NOMBRE',
                'CODEMPRESA',
                'CARGO',
                'TELF. FIJO',
                'TELF. MÓVIL',
                'FAX',
                'EMAIL',
                'DIRECCION',
                'PAIS',
                'FECHA_ALTA',
                'FECHA_MODIFICACION',
                'FECHA_BAJA',
                'CODUSUARIO_ALTA',
                'CODUSUARIO_MODIFICACION',
                'CODUSUARIO_BAJA',
                'OBSERVACIONES'
            ],
            colModel: [
                {name: 'CODCONTACTO', index: 'CODCONTACTO', align: 'center', hidden: true},
                {name: 'DESCONTACTO', index: 'DESCONTACTO', align: 'left', width: '230'},
                {name: 'CODEMPRESA', index: 'CODEMPRESA', align: 'center', hidden: true},
                {name: 'CARGO', index: 'CARGO', align: 'center', hidden: true},
                {name: 'TELF_FIJO', index: 'TELF_FIJO', align: 'center', width: '75'},
                {name: 'TELF_MOVIL', index: 'TELF_MOVIL', align: 'center', width: '80'},
                {name: 'FAX', index: 'FAX', align: 'center', width: '75'},
                {name: 'EMAIL', index: 'EMAIL', align: 'center', width: '140'},
                {name: 'DIRECCION', index: 'DIRECCION', align: 'center', hidden: true},
                {name: 'PAIS', index: 'PAIS', align: 'center', hidden: true},
                {name: 'FECHA_ALTA', index: 'FECHA_ALTA', align: 'center', hidden: true},
                {name: 'FECHA_MODIFICACION', index: 'FECHA_MODIFICACION', align: 'center', hidden: true},
                {name: 'FECHA_BAJA', index: 'FECHA_BAJA', align: 'center', hidden: true},
                {name: 'CODUSUARIO_ALTA', index: 'CODUSUARIO_ALTA', align: 'center', hidden: true},
                {name: 'CODUSUARIO_MODIFICACION', index: 'CODUSUARIO_MODIFICACION', align: 'center', hidden: true},
                {name: 'CODUSUARIO_BAJA', index: 'CODUSUARIO_BAJA', align: 'center', hidden: true},
                {name: 'OBSERVACIONES', index: 'OBSERVACIONES', align: 'center', hidden: true}



            ],
            pager: jQuery('#pager2'),
            rowNum: 50,
            loadui: "disable",
            imgpath: 'css/themes/custom-theme/images',
            sortname: 'DESCONTACTO',
            sortorder: "asc",
            loadonce: false,
            viewrecords: true,
            recordtext: "{0} - {1} de {2}",
            emptyrecords: "Sin registros",
            caption: "CONTACTOS",
            ondblClickRow: function(rowid)
            {
                var fila_sel = jQuery("#contactoslist").getGridParam('selrow');
                var campos = jQuery("#contactoslist").getRowData(fila_sel);
                jQuery("#empresaslist").GridUnload();
                GridEmpresas(campos.CODEMPRESA, 1);

                $('#botonDetalleContacto').click();
            },
            onSelectRow: function() { // eventos al seleccionar filas y cargar ficheros
                var fila_sel = jQuery("#contactoslist").getGridParam('selrow');
                var campos = jQuery("#contactoslist").getRowData(fila_sel);

                $.ajax({
                    type: "POST",
                    url: 'json/detallecontactos_json.php?codcontacto=' + campos.CODCONTACTO + '&codempresa=' + campos.CODEMPRESA,
                    dataType: 'json',
                    success: function(data) {


                        $('#TECODCONTACTO').text('CONTACTO: ' + data.CODCONTACTO + ''); //titulo editar
                        $('#TDCODCONTACTO').text('CONTACTO: ' + data.CODCONTACTO + ''); // titulo detalles

                        //editar
                        $('#ECODCONTACTO').val(data.CODCONTACTO);
                        $('#EDESCONTACTO').val(data.DESCONTACTO);
                        $('#ECODEMPRESA').val(data.CODEMPRESA);
                        $('#ECARGO').val(data.CARGO);
                        $('#ETELF_FIJO').val(data.TELF_FIJO);
                        $('#ETELF_MOVIL').val(data.TELF_MOVIL);
                        $('#EFAX').val(data.FAX);
                        $('#EEMAIL').val(data.EMAIL);
                        $('#EDIRECCION').val(data.DIRECCION);
                        $('#EPAIS').val(data.PAIS);
                        $('#EFECHA_ALTA').val(data.FECHA_ALTA);
                        $('#EFECHA_MODIFICACION').val(data.FECHA_MODIFICACION);
                        $('#EFECHA_BAJA').val(data.FECHA_BAJA);
                        $('#ECODUSUARIO_ALTA').val(data.CODUSUARIO_ALTA);
                        $('#ECODUSUARIO_MODIFICACION').val(data.CODUSUARIO_MODIFICACION);
                        $('#ECODUSUARIO_BAJA').val(data.CODUSUARIO_BAJA);
                        $('#EOBSERVACIONES').val(data.OBSERVACIONES);
                        //detalles
                        //$('#DCODCONTACTO').val(data.CODCONTACTO);
                        $('#DDESCONTACTO').val(data.DESCONTACTO);
                        $('#DDESEMPRESA').val(data.DESEMPRESA);
                        $('#DCARGO').val(data.CARGO);
                        $('#DTELF_FIJO').val(data.TELF_FIJO);
                        $('#DTELF_MOVIL').val(data.TELF_MOVIL);
                        $('#DFAX').val(data.FAX);
                        $('#DEMAIL').val(data.EMAIL);
                        $('#DDIRECCION').val(data.DIRECCION);
                        $('#DPAIS').val(data.PAIS);
                        $('#DFECHA_ALTA').val(data.FECHA_ALTA);
                        $('#DFECHA_MODIFICACION').val(data.FECHA_MODIFICACION);
                        $('#DFECHA_BAJA').val(data.FECHA_BAJA);
                        $('#DCODUSUARIO_ALTA').val(data.CODUSUARIO_ALTA);
                        $('#DCODUSUARIO_MODIFICACION').val(data.CODUSUARIO_MODIFICACION);
                        $('#DCODUSUARIO_BAJA').val(data.CODUSUARIO_BAJA);
                        $('#DOBSERVACIONES').val(data.OBSERVACIONES);

                        if (data.FECHA_ALTA == "00-00-0000 00:00:00")
                            $('#DFECHA_ALTA').val('');
                        if (data.FECHA_MODIFICACION == "00-00-0000 00:00:00")
                            $('#DFECHA_MODIFICACION').val('');


                    },
                    error: function(e) {
                        alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                    }
                })


            },
        }).navGrid('#pager2', {edit: false, add: false, del: false, view: false,
            beforeRefresh: function() {

                jQuery("#contactoslist").GridUnload();
                GridContactos();

            }
        },
        {}, //  default settings for edit
                {}, //  default settings for add
                {}, // delete instead that del:false we need this
                {
                    sopt: ['cn', 'eq', 'bw', 'ew'],
                    closeAfterSearch: true,
                    //searchOnEnter: true,
                    // procedimientos para seleccionar una columnna por defecto en la búsqueda

                    afterShowSearch: function() {

                        $(".data").children('input').focus();
                    }
                }, // search options
        {} /* view parameters*/
        ).navButtonAdd('#pager2', {
            caption: "",
            title: "Eliminar Contacto",
            buttonicon: "ui-icon-trash",
            onClickButton: function() {
                var fila_sel = jQuery("#contactoslist").getGridParam('selrow');
                var campos = jQuery("#contactoslist").getRowData(fila_sel);
                var codcontacto = campos.CODCONTACTO;
                if (fila_sel) {

                    if (confirm("Se dispone a dar de baja el contacto seleccionado. ¿Desea Continuar?")) {
                        $.ajax({
                            type: "POST",
                            url: 'edit/contactos_ed.php?borrar=' + codcontacto,
                            dataType: 'json',
                            success: formSuccess,
                            error: function(e) {
                                alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                            }
                        });

                    }

                } else
                    alert('Seleccione el contacto a eliminar');

            },
            position: "first"
        }
        ).navButtonAdd('#pager2', {
            caption: "",
            title: "Editar Contacto",
            buttonicon: "ui-icon-pencil",
            onClickButton: function() {
                var fila_sel = jQuery("#contactoslist").getGridParam('selrow');
                if (fila_sel) {
                    $('#modalEditarContacto').dialog('open');
                    return false;
                } else
                    alert('Seleccione un Contacto para editar');

            },
            position: "first"
        });

    } // fin funcion GridContactos
//------------------------------------------------------------------------------------------------------------------
    $(document).keypress(function(e) {
        if (e.which == 13) {
            $("#fbox_empresaslist_search").click();
            setTimeout(function() {
                $("#fbox_empresaslist_search").click();
            }, 1000);
            $("#fbox_contactoslist_search").click();
            setTimeout(function() {
                $("#fbox_contactoslist_search").click();
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
        $("input[type=text]").css({'text-transform':'uppercase'});
        GridEmpresas();
        GridContactos();



        // Manejo de los botones, y ventanas modales -------------------------------------

        $('#botonActivarEmpresa').hide();

        $('#botonNuevoEmpresa').button().click(function(evento) {
            evento.preventDefault();
            $("#modalNuevoEmpresa").dialog('open');
            return false;
        });


        $('#botonActivarEmpresa').button().click(function(evento) {
            evento.preventDefault();
            var fila_sel = jQuery("#empresaslist").getGridParam('selrow');
            var campos = jQuery("#empresaslist").getRowData(fila_sel);
            var codempresa = campos.CODEMPRESA;



            $.ajax({
                type: "POST",
                url: 'edit/empresas_ed.php?activar=' + codempresa,
                dataType: 'json',
                async: false,
                success: function(data) {
                    if (data.ERROR) {
                        $('#titulo').jAlert(data.ERROR, "fatal");
                        setTimeout(function() {
                            $('.msg-fatal').fadeOut(500, function() {
                                $('.msg-fatal').remove();
                            });
                        }, 10000);
                    }
                    $("#empresaslist").trigger('reloadGrid');
                    setTimeout(function() {
                        $('#empresaslist').jqGrid('setSelection', fila_sel);
                    }, 1000);
                },
                error: function(e) {
                    alert("Error en el servidor, por favor, intentalo de nuevo mas tarde");
                }
            });




            return false;
        });



        $('#botonDetalleContacto').button().click(function(evento) {
            evento.preventDefault();
            var fila_sel = jQuery("#contactoslist").getGridParam('selrow');
            var campos = jQuery("#contactoslist").getRowData(fila_sel);
            if (fila_sel) {
                $('#modalDetalleContacto').dialog('open');

            } else
                alert('Seleccione un Contacto para ver información adicional ');
            return false;
        });



        $('#botonNuevoContacto').button().click(function() {

            var fila_sel = jQuery("#empresaslist").getGridParam('selrow');
            var campos = jQuery("#empresaslist").getRowData(fila_sel);
            $('#NCODEMPRESA').val(campos.CODEMPRESA);

            if (fila_sel)
                $('#modalNuevoContacto').dialog('open');
            else
                alert('Seleccione una Empresa para el nuevo Contacto ');
            return false;
        });

        $("li").css({background: "#4875e8"});
        $("#util").css({background: "#77c5e1"}); //seleccionar opción actual del menú principal


    });






</script>