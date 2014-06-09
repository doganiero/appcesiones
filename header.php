<script type="text/javascript">

    $.extend({
        getUrlVars: function() {
            var vars = [], hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for (var i = 0; i < hashes.length; i++)
            {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        },
        getUrlVar: function(name) {
            return $.getUrlVars()[name];
        }
    });



    $(document).ready(function() {

        // Para saber en qué sección nos encontramos actualmente.
        var actual = "inicio";
        // Animación del cargador.
        $('#cargando').ajaxStart(function() {
            $(this).show();
            $("body").append('<div id="bloquea" class="" style="height: 100%; width: 100%; position: fixed; left: 0px; top: 0px; z-index: 949; opacity: 0.3;"></div>');

        }).ajaxStop(function() {
            $(this).hide();
            $("#bloquea").remove();
        });

        // Diseño del menú principal.
        $('ul.sf-menu').superfish();

        // Gestión de cambios de sección.
        $('#menu li').click(function(evt) {
            $('.msg-info').remove();
            $('.msg-warning').remove();
            $('.msg-fatal').remove();
            var $html_detalleRemedy;
            var idBoton = $(this).attr("id");

            if (idBoton != "salir")
            {
                if (idBoton == "ayuda")
                    window.open('ayuda.php');
                else if (idBoton != "")
                {
                    evt.preventDefault();
                    if ($(this).children().is('a:only-child'))
                    {
                        //if (idBoton != actual)
                        //{
                        //$('#contenedor').nextAll().not('.ui-datepicker').remove();
                        //actual = idBoton;

                        // Acoplar extensión
                        $(".ui-dialog-content").dialog("close"); // cerrar cualquier ventana abierta
                        $(".ui-dialog-content").dialog("destroy").remove();



                        var id = 'view/' + idBoton + '.php';
                        // Cerrar la capa activa cuando se hace click en cualquier enlace.
                        $('div#pie').fadeOut('fast', function() {
                            $('div#contenido').fadeOut('fast', function() {
                                $('div#contenido').load(id, function() {
                                    $('div#contenido').fadeIn('normal');
                                    $('div#pie').fadeIn('fast');

                                });
                            });
                        });
                        //}
                    }

                }
            } else
                window.onbeforeunload = null;

        });

        var newmask = $.getUrlVar('newmask');

        if (newmask)
            $('#logficheros').click();



    });
</script>
<!-- Esta es la cabecera estándar del programa -->
<ul id="menu" class="sf-menu">
    <!--<li class="current"><a href="#">Inicio</a></li>-->

    <!--<li id="alta" ><a  href="#">Envíos</a></li>  -->

    <li id="log" ><a  href="#">Gestión de Logs</a></li>  

    <li id="control" ><a  href="#">Análisis de Consolidación</a></li>


    <li id="cesiones" ><a  href="#">Cesiones&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a> 

        <ul>
            <li id="remedyemp" ><a href="#">Por Empresa</a></li>
            <li id="remedy" ><a href="#">Por Envío</a></li>

        </ul>

    </li>


    <li id="util" ><a  href="#">Utilidades&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>

        <ul>
            <li id="logficheros" ><a href="#">Máscaras</a></li>
            <li id="empresas" ><a href="#">Empresas</a></li>
            <li id="excepciones" ><a href="#">Excepciones</a></li>
            <li id="cmdb" ><a href="#">Consulta CMDB</a></li>
            <li id="control_archivo" ><a href="#">Consulta Archivo</a></li>

        </ul>

    </li>






    <li id="salir"><a href="https://de-portalintranet.es.igrupobbva/pkmslogout">Salir</a></li>

</ul>


