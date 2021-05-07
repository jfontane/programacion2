<?php
//-- colocamos el paso a la librería en el entorno
set_include_path("./includes/");
include_once('conexion.php');
//-- si no hay conexion informo el error y termino
if(!is_resource($conex)){
	die("<h2 style='color:#FF0000'>{$conex}</h2>");
}	
	
$sql = "SELECT * FROM alumno ORDER BY apellido ASC, nombre ASC";
$resultado = @mysql_query($sql, $conex);
if(is_resource($resultado) && mysql_num_rows($resultado) > 0){
	$datos = "";
	while($fila = mysql_fetch_array($resultado)){
		$date = explode('-',$fila['fechaNacimiento']);
		$datos .= $fila['apellido'] . chr(9);
		$datos .= $fila['nombre'] . chr(9);
		$datos .= $fila['dni'] . chr(9);
		$datos .= ($fila['sexo']=='M'?"Masculino":"Femenino") . chr(9);
		$datos .= $date[2].'-'.$date[1].'-'.$date[0] . chr(9);
		$datos .= $fila['idAlumno'];
		$datos .= chr(13) . chr(10);
	}
}
else{
	die("<h2 style='color:#FF0000'>No se encontraron resultados</h2>");
}

//-- mostramos el resultado
$filename = "datos.txt";
$handle = @fopen($filename, "wb");
if (is_resource($handle)) {
    if (fwrite($handle, $datos) === FALSE) {
		die("No puedo escribir en el archivo");
	}
	fclose($handle);
	echo "<h2 style='color:#00FF00'>Archivo Exportado Correctamente</h2>";
	echo "<a href='./ver.php' target='_blank'>[ Ver Archivo ]</a> || <a href='./descargar.php'>[ Descargar Archivo ]</a>";
	die;
}
else{
	die("el archivo no existe o no se puede leer");
}
?>