<?php
//-- colocamos el path de las librerías en el entorno
set_include_path("./recursos/_php/".PATH_SEPARATOR."./includes/");

//-- incluimos la libreria
include_once('conexion.php');
include_once('sanitize.class.php');
include_once('arrayHash.class.php');

/**
 segundo paso controlar los datos obligatorios, si faltan informar y
  volvemos a la pantalla de listado
*/
$idAlumno = isset($_GET['idAlumno'])?SanitizeVars::INT($_GET['idAlumno']):FALSE;
$hash = isset($_GET['hash'])?SanitizeVars::STRING($_GET['hash']):FALSE;
$errorMsg = "";
if(!$idAlumno || !ArrayHash::check($hash, array('idAlumno'=>$idAlumno))){
	 $errorMsg = "Faltan campos obligatorios!!";
} else {
	//-- armamos el SQL
	$errorMsg = "";
	$sql = "DELETE FROM alumno WHERE (id = '$idAlumno')";
	$ok = @mysqli_query($conex, $sql);

	if(!$ok){
		$errorNro = @mysqli_errno($conex);
		$errorMsg = "Error({$errorNro}): ".@mysqli_error($conex);
		switch($errorNro){
			case 1451:
				$errorMsg = "No puede eliminar este alumno porque tiene inscripciones!!";
				break;
		}
	} else{
		$errorMsg = "Alumno Eliminado!";
	};
}

header('location: listado.php?msg='.$errorMsg);
?>
