<?
// Comprobación de autenticación.

include_once('../functions/db.php');
$link = conectar();
?>
<!--- Mensajes -->


<!--- Fin Mensajes -->

<!-- ventanas Modales -->

<!-- Modal  Nueva Excepción -->
<?
//include_once('../view/modal/nuevaexcepcion_modal.php');
?>

<!-- Fin Ventanas Modales -->

<!--<div align="center" id="titulo" class="titulo2">Gestión de Empresas y Contactos</div>-->




    <!--- Tabla de Cmdb -->
    <div class="unaCol" align="center" style="height: 600px; " >

        <table id="cmdblist" class="scroll"><tr><td></td></tr></table>
        <div id="pager" class="scroll" style=""></div>

        <!--- Fin Tabla de de Cargas Buffer -->

        <!-- Botones acciones excepciones --> 

        <div id="grupoBotonesCmdb" class="colDerecha" style='width: 115px;'>
            <table style="padding-top:10px;">
                <tr><td>


                        <!--<input id="botonNuevoExcepcion" type="submit" value="Nuevo" class="boton ui-corner-all" 
                               style="width:110px; margin-bottom:9px;" />  -->


                        <!-- Para el control de pulsación de los botones usaremos otro input oculto -->    
                        <input id="opcionA" type="hidden" name="opcionA" value="nuevo" />
                    </td>
                </tr>
            </table>

        </div>
        <!-- FIN Botones acciones excepciones -->
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
   
        $('#excepcionesForm').ajaxForm(options);


    });

    function formSuccess(data) {

        if (!data.ERROR) {
          
                jQuery("#cmdblist").trigger("reloadGrid");
                                
            $("#modalNuevoExcepcion").dialog('close');

            mensaje(data.SUCCESS, 'titulo');

         
            $('#excepcionesForm')[0].reset();
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





//-----------------Tabla de Empresas-----------------------

    function GridCmdb(selempresa, cut) { // esta función crea el grid y filtra la tabla según parámetros

        if (!selempresa)
            selempresa = 0;
        if (!cut)
            cut = 0;

        jQuery("#cmdblist").jqGrid({
            url: 'json/cmdb_json.php',
            width: 1140,
            height: 530,
            datatype: "json",
            //type: "POST",
            //data: {coincidencias: coincidencias},
            colNames: [
                'NOMBRE MÁQUINA',
                'IP PRINCIPAL',
                'IP SECUNDARIO',
                'TIPO',
                'ENTORNO',
                'MODELO OS',
                'UD. ORGANIZATIVA'
            ],
            colModel: [
                {name: 'COL_A', index: 'COL_A', align: 'center', width: 80 },//, hidden: true},
                {name: 'COL_B', index: 'COL_B', align: 'center', width: 80 },//, hidden: true},
                {name: 'COL_C', index: 'COL_C', align: 'center', width: 150},//, hidden: true},
                {name: 'COL_D', index: 'COL_D', align: 'center', width: 80},//, hidden: true},
                {name: 'COL_E', index: 'COL_E', align: 'center', width: 80},//, hidden: true},
                {name: 'COL_F', index: 'COL_F', align: 'center', width: 100},
                {name: 'COL_G', index: 'COL_F', align: 'center', width: 100}

            ],
            pager: jQuery('#pager'),
            rowNum: 50,
            loadui: "disable",
            imgpath: 'css/themes/custom-theme/images',
            sortname: 'COL_A',
            sortorder: "asc",
            loadonce: false,
            caption: "DATOS DE CONSULTA DE CMDB",
            viewrecords: true,
            recordtext: "{0}/{1} de {2}",
            emptyrecords: "Sin registros",
            ondblClickRow: function(rowid)
            {
               var fila_sel = jQuery("#cmdblist").getGridParam('selrow');
                var campos = jQuery("#cmdblist").getRowData(fila_sel);
                         
            },
            onSelectRow: function() { // eventos al seleccionar filas y cargar ficheros
                
           
                
            },
            
            loadComplete: function() {  // seleccionando el primer registro de la tabla
                
            },
        }).navGrid('#pager', {edit: false, add: false, del: false, view: false,
            beforeRefresh: function() {

            }


        },
        {}, //  default settings for edit
                {}, //  default settings for add
                {}, // delete instead that del:false we need this
                {
                    multipleSearch: true,
                    sopt: ['cn', 'eq', 'bw', 'ew'],
                    closeAfterSearch: true,
                    afterShowSearch: function(){ 
                        $(".data").children('input').focus();
                    }
                }, // search options
        {} /* view parameters*/
        );
    } //Fin GridCmdb

//-------------------------------------------------------------------------------------------------------------------

     $(document).keypress(function(e) {
    if(e.which == 13) {
        
       $("#fbox_cmdblist_search").click();
        setTimeout(function() {
               $("#fbox_cmdblist_search").click();
            }, 1000);
        
    }
  });
  
    $(document).ready(function() {

       /* $("input[type=text]").keyup(function(e) {
            if (e.which >= 65) {
                $(this).val($(this).val().toUpperCase());
            }
        });*/
        $("input[type=text]").css({'text-transform':'uppercase'});
        GridCmdb();
   
        // Manejo de los botones, y ventanas modales -------------------------------------

        /*
        $('#botonNuevoExcepcion').button().click(function(evento) {
            evento.preventDefault();
            $("#modalNuevoExcepcion").dialog('open');
            return false;
        });*/
        $("li").css({background: "#4875e8"});
        $("#util").css({background: "#77c5e1"}); //seleccionar opción actual del menú principal

    });








</script>