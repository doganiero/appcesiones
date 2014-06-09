<?

/* -------------------------------------------------------------------- */
/* --- Funciones de baneo de usuarios --------------------------------- */
/* -------------------------------------------------------------------- */
function ipBaneada($value) {
    require_once 'functions/db.php';
    $ln = conectar();
    
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
    require_once 'functions/db.php';
    $ln = conectar();
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
    require_once 'functions/db.php';
    $ln = conectar();
    $consulta = "DELETE FROM ban WHERE ban_ip = '$value'";
    return mysql_query($consulta);
}
?>
