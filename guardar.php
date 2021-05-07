<?php
//-- colocamos el path de las librerías en el entorno
set_include_path("./recursos/_php/".PATH_SEPARATOR."./includes/");

//-- incluimos la libreria
include_once("class.TemplatePower.inc.php");
include_once('conexion.php');
include_once('sanitize.class.php');
include_once('arrayHash.class.php');

$botonAceptar = isset($_POST['botonAceptar'])?TRUE:FALSE;
$apellido = isset($_POST['apellido'])?SanitizeVars::SQL($_POST['apellido']):FALSE;
$nombre = isset($_POST['nombre'])?SanitizeVars::SQL($_POST['nombre']):FALSE;
$dni = isset($_POST['dni'])?SanitizeVars::INT($_POST['dni']):FALSE;
if(!$botonAceptar || !$apellido || !$nombre || !$dni){
	 header("location: nuevo.php?msg=Faltan datos Obligatorios");
} else {
		/**
		   si ya tenemos los datos obligatorios insertamos el alumno, y
		   limpiamos el resto de las variables del POST
		*/
		$edad = isset($_POST['edad'])?SanitizeVars::INT($_POST['edad']):0;
		$sexo = isset($_POST['sexo'])?SanitizeVars::STRING($_POST['sexo']):'M';
		$direccion = isset($_POST['direccion'])?SanitizeVars::SQL($_POST['direccion']):'';
		$barrio = isset($_POST['barrio'])?SanitizeVars::INT($_POST['barrio']):1;
		$observaciones = isset($_POST['observaciones'])?SanitizeVars::SQL($_POST['observaciones']):'';
		$fecha = isset($_POST['fecha'])?SanitizeVars::DATE_HISPANA($_POST['fecha']):NULL;
		//-- iniciamos la trans
		//db_start_trans($conex);

		//-- armamos el SQL
		$sql = "INSERT INTO alumno(apellido,nombre,dni,fechaNacimiento,sexo,direccion,barrio,observaciones)
				VALUES('$apellido','$nombre','$dni','$fecha','$sexo','$direccion','$barrio','$observaciones')";
		//die($sql);
		$ok = @mysqli_query($conex,$sql);
		//-- informamos del error o continuamos
		if (!$ok) {
			 $errorNro = @mysqli_errno($conex);
			 $errorMsg = "Error({$errorNro}): ".@mysqli_error($conex);
			 header("location: nuevo.php?msg=$errorMsg");
		} else {
			 $idAlumno = @mysqli_insert_id();
		}
		/**
		 si ya insertamos el alumno, ahora insetamos las inscripciones,
		 controlamos que existan materias seleccionadas.
		*/
		if(isset($_POST['materias']) and count($_POST['materias']) > 0){
			$hoy = date('Y-m-d');
			$materias = $_POST['materias'];
			foreach($materias as $idMateria){
				$sql = "INSERT INTO inscripcion(idAlumno, idMateria, fecha)
						VALUES('$idAlumno','$idMateria','$hoy')";
				$ok = @mysqli_query($conex,$sql);
				if(!$ok){
					$errorNro = @mysqli_errno($conex);
					$errorMsg = "Error({$errorNro}): ".@mysqli_error($conex);
				  header("location: nuevo.php?msg=$errorMsg");
				}
			}
		}
		/**
		 informamos y volvemos al index o a la pantalla de carga
		 */
  header("location: listado.php");
}
?>
