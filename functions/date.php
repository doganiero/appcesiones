<?

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

function fechaAMysql2($fecha) {
    $sec = explode("-", $fecha);
    if (count($sec) == 3) {
        $fechaConvertida = $sec[2] . "-" . $sec[1] . "-" . $sec[0];
        if(checkdate($sec[1], $sec[2], $sec[0])) return 0;
        else
        return $fechaConvertida;
    }
    else
        return 0;
}

?>
