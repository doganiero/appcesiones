<?
$ruta=$_GET['ruta'];
$nombrefich=$_GET['nombrefich'];
header('Content-Type: application/csv');
header("Content-Disposition: attachment; filename=$nombrefich.csv");
header('Pragma: no-cache');
readfile($ruta);

?>