<?php
$filename = "datos.txt";
if (file_exists($filename) and is_readable($filename)) {
    $contenido = file_get_contents($filename);
    header('Content-Type: application/x-download');
    header('Content-Length: '.strlen($contenido));
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    ini_set('zlib.output_compression','0');

    echo $contenido;
}
else{
    echo "Error. El archivo no existe o no se puede leer";
}
?>