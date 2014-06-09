<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <title>BBVA Aplicación de Cesiones</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link type="text/css" href="css/customStyle.css" rel="stylesheet" />
        <link type="text/css" href="css/theme/jquery-ui-1.10.2.custom.min.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="css/jalert.css" media="screen" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/ui.jqgrid.css" />
        <link rel="stylesheet" type="text/css" href="css/superfish.css" media="screen" />

        <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="js/jquery-migrate-1.1.1.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.10.2.custom.min.js"></script>
        <script type="text/javascript" src="js/jquery.jqGrid.min.js"></script>
        <script type="text/javascript" src="js/i18n/grid.locale-es.js"></script>
        <script type="text/javascript" src="js/hoverIntent.js"></script>
        <script type="text/javascript" src="js/superfish.js"></script>
        <script type="text/javascript" src="js/jquery.form.js"></script>
        <script type="text/javascript" src="js/jquery.jalert.js"></script>
        <script type="text/javascript" src="js/jquery.jalert.packed.js"></script>
        <!--jquery validate
        
      <script type="text/javascript" src="js/jquery.validate.js"></script>-->
        
        
        <script type="text/javascript">
            
            function mensaje(mensaje, capa) {
                // Funcion de alerta de mensajes en formularios.
                capa = '#' + capa;

                if (mensaje.search('correctamente') !== -1) {
                    $(capa).jAlert(mensaje, "success");
                    setTimeout(function() {
                        $('.msg-success').fadeOut(500, function() {
                            $('.msg-success').remove();
                        })
                    }, 15000);
                }
                else if ((mensaje.search('seleccione') !== -1) || (mensaje.search('cancelado') !== -1) || (mensaje.search('baja') !== -1) || (mensaje.search('Alerta') !== -1)) {
                    $(capa).jAlert(mensaje, "warning");
                    setTimeout(function() {
                        $('.msg-warning').fadeOut(500, function() {
                            $('.msg-warning').remove();
                        })
                    }, 30000);
                } else {
                    $(capa).jAlert(mensaje, "fatal");
                }

                // Para mensajes tipo INFO se usaría $(capa).jAlert(mensaje, "info");
            }
            
        </script>

    </head>

    <body>
         
        <div id="contenedor">
         <div class="msj" id="titulo" ></div>
              <div  style="margin: auto; background:  #fff ">
                    <img src="img/cabecera.jpg" width="1152"  alt="BBVA" />
                    
                </div>
            <div id="cabecera">
                 
                <div style="margin: auto; width: 1155px;">
                    <? include ("header.php"); ?>
                    
                </div>
                 
            </div>
            <div id="cargando" align="left" style="float: left; display: none; position: absolute; margin: 2px 0 0 10px">
                <img src="img/cargador.gif" width="35" height="35" alt="cargador" />
            </div>
            <div id="contenido">
                <? include ("view/inicio.php"); ?>
                <div id="mensaje"><!-- Para el uso de jquery form --></div>
            </div> 
            <div id="pie">
                <? include ("footer.php"); ?>
            </div>
        </div>
        <?
        //phpinfo();
        ?>
    </body>

</html>
