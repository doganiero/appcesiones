<?

/* -------------------------------------------------------------------- */
/* --- Función de error genérica -------------------------------------- */
/* -------------------------------------------------------------------- */
$directorio_raiz="/Applications/MAMP/tmp/"; //Mac
//$directorio_raiz= "/wamp/www/tmp/" ; //Local Windows
//$directorio_raiz="/usr/local/pr/aut/www/tmp/"; //server linux

$webseal=$_SERVER['HTTP_IV_USER']; 
if(!$webseal || $webseal=="") $webseal="NULLUSR";

function error($title, $body) {
    if ($body != "") {
        echo "<h1>" . $title . "</h1><br/>- Detalles del error: " . $body . "<br/><br/>
		      Ayúdenos a corregir los errores poniendose en contacto con el
		      administrador del sistema.<br/> Gracias";
        exit();
    }
}

/* -------------------------------------------------------------------- */
/* --- Función de conexión con la base de datos ----------------------- */
/* -------------------------------------------------------------------- */

function conectar() {
    $bdName = "db_cesiones";
    $bdUser = "root"; // local - mac
    $bdPass = "admin"; // local - mac
    //$bdUser = "autorizaciones"; //server
    //$bdPass = "useraut2013"; //server
    $bdServer = "localhost";

    if (!($link = mysql_connect($bdServer, $bdUser, $bdPass))) {
        error("Error conectando con el servidor de base de datos", mysql_error());
    }   

    if (!(mysql_select_db($bdName, $link))) {
        error("Error seleccionando la base de datos", mysql_error());
    }
        
    return $link;
}

// Función de asistencia en búsquedas, devuelve un predicado dados un operador y una cadena a buscar.
function predicado($oper, $cadena) {
    switch ($oper) {
        case 'cn': $pred = " LIKE '%" . $cadena . "%' ";
            break;
        case 'lt': $pred = " < '" . $cadena . "'";
            break;
        case 'le': $pred = " <= '" . $cadena . "'";
            break;
        case 'gt': $pred = " > '" . $cadena . "'";
            break;
        case 'ge': $pred = " >= '" . $cadena . "'";
            break;
        case 'bw': $pred = " LIKE '" . $cadena . "%'";
            break;
        case 'ew': $pred = " LIKE '%" . $cadena . "'";
            break;
        default: $pred = " LIKE '" . $cadena . "' ";
    }

    return $pred;
}

?>
