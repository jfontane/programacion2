<?php
//-- mostramos el resultado
$filename = "./datos.txt";
$handle = @fopen($filename, "rb");
if (is_resource($handle)) {
    while ($buffer = fgets($handle, 4096)) {
		$aux = nl2br($buffer);
		$aux = str_replace(chr(9), ' | ', $aux);
		echo $aux;
    }
	fclose($handle);
	die();
}
else{
	die("el archivo no existe o no se puede leer");
}
?>