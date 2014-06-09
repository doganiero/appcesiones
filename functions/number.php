<?

/* -------------------------------------------------------------------- */
/* --- Función de error genérica -------------------------------------- */
/* -------------------------------------------------------------------- */

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
    $bdUser = "root";
    $bdPass = "admin";
    $bdServer = "localhost";

    if (!($link = mysql_connect($bdServer, $bdUser, $bdPass))) {
        error("Error conectando con el servidor de base de datos", mysql_error());
    }

    if (!(mysql_select_db($bdName, $link))) {
        error("Error seleccionando la base de datos", mysql_error());
    }

    return $link;
}

/* -------------------------------------------------------------------- */
/* --- Funciones de baneo de usuarios --------------------------------- */
/* -------------------------------------------------------------------- */

function ipBaneada($value) {
    $result = mysql_query("SELECT ban_attemps,(CASE WHEN ban_time IS NOT NULL
  			   AND DATE_ADD(ban_time, INTERVAL 30 MINUTE)>NOW() THEN 1 ELSE 0 END) AS Denied
			   FROM ban WHERE ban_ip = '$value'");
    $data = mysql_fetch_array($result);
    error("Error leyendo la tabla de baneos", mysql_error());

    // Comprobamos que al menos un intento de acceso se halla en la BD.
    if (!$data) {
        return 0;
    }

    if ($data["ban_attemps"] >= 3) {
        if ($data["Denied"] == 1) {
            return 1;
        } else {
            limpiarIntentos($value);
            return 0;
        }
    }
    return 0;
}

function sumarIntento($value) {
    //Incrementar el número de intentos. Establecer el último intento de entrada si es necesario.
    $result = mysql_query("SELECT * FROM ban WHERE ban_ip = '$value'");
    $data = mysql_fetch_array($result);
    error("Error leyendo la tabla de baneos", mysql_error());

    if ($data) {
        $intentos = $data["ban_attemps"] + 1;
        if ($intentos == 3) {
            $consulta = "UPDATE ban SET ban_attemps=" . $intentos . ", ban_time=NOW() WHERE ban_ip = '$value'";
            $result = mysql_query($consulta);
            error("Error actualizando la tabla de baneos", mysql_error());
        } else {
            $consulta = "UPDATE ban SET ban_attemps=" . $intentos . " WHERE ban_ip = '$value'";
            $result = mysql_query($consulta);
            error("Error actualizando la tabla de baneos", mysql_error());
        }
    } else {
        $consulta = "INSERT INTO ban (ban_ip,ban_attemps,ban_time) values ('$value', 1, NOW())";
        $result = mysql_query($consulta);
        error("Error insertando en la tabla de baneos", mysql_error());
    }
}

function limpiarIntentos($value) {
    $consulta = "DELETE FROM ban WHERE ban_ip = '$value'";
    return mysql_query($consulta);
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

// Función para redondeo de precios.
function redondear($num) {
    // Redondeamos a dos decimales y formateamos la salida (456.90, por ej).
    $redondeado = round($num, 2);
    $num = number_format($redondeado, 2, '.', '');

    return $num;
}

// Para insertar fechas en la BD, convierte de 'dd/mm/aaaa' a 'aaaa-mm-dd'.
function fechaAMysql($fecha) {
    $sec = explode("/", $fecha);
    if (count($sec) == 3) {
        $fechaConvertida = $sec[2] . "-" . $sec[1] . "-" . $sec[0];
        return $fechaConvertida;
    }
    else
        return 0;
}

	
	function generar_password () {
$i=0;
$password="";
// Aqui colocamos el largo del password
$pw_largo = 8;
// Colocamos el rango de caracteres ASCII para la creacion de el password
$desde_ascii = 50; // "2"
$hasta_ascii = 122; // "z"
// Aqui quitamos caracteres especiales
$no_usar = array (58,59,60,61,62,63,64,73,79,91,92,93,94,95,96,108,111);
while ($i < $pw_largo) {
mt_srand ((double)microtime() * 1000000);
// limites aleatorios con tabla ASCII
$numero_aleat = mt_rand ($desde_ascii, $hasta_ascii);
if (!in_array ($numero_aleat, $no_usar)) {
$password = $password . chr($numero_aleat);
$i++;
}
}
return $password;

}   
	
	
	function subir_archivo($nombre1, $archivo1, $tamanio1, $tipo1 )
	{
		
 		
		
    // $archivo1 = $_FILES[$nombre_campo]["tmp_name"];
	 //$tamanio1 = $_FILES[$nombre_campo]["size"];
	 //$tipo1    = $_FILES[$nombre_campo]["type"];
	 //$nombre1  = $_FILES[$nombre_campo]["name"];
	 	
	 	 
	 $trozos = explode(".", $nombre1); 
     $extension = end($trozos); 

	 
	//if($extension!='xls' && $extension!='xlsx' && $extension!='ppt' && $extension!='doc' ) $error="No se ha seleccionado un archivo o el formato del mismo no es vÃ¡lido. Los formatos permitidos son de extensiÃ³n .xls,. xlsx, .ppt y .doc";
	
	if(!$error)
	{

if ($nombre1 != "")
{
	if($tamanio1 < 102400000)
	{
		$ext = $nombre1;
		$ext_name = $ext;
		$ext_name = strrev($ext_name);
		$ext_name = $ext_name;
		$carac = strchr($ext_name,"\\");
		$cant1 = strlen($carac);
		$cant2 = strlen($ext_name);
		$ext_name = substr($ext_name,0,$cant2 - $cant1);
		$ext_name = substr($ext_name,0,5);
		$ext_name = strrev($ext_name);

			if( ($stream = fopen( $archivo1 , "r" )) != NULL )
		   {
			  $contenidoi = fread( $stream, 102400000 );
			  fclose( $stream );
		   }
		   $guardar01="/usr/local/pr/aut/www/tmp/".$this->generar_password(). $ext_name ."" ;

		   
			if( ($stream = fopen("" . $guardar01, "w+" )) != NULL )
		   {

			  fwrite( $stream,$contenidoi, 102400000 );
			  $carga_correcta++;
			  fclose( $stream );
		   }
	}

}
	}
	return $guardar01;
	}


?>
