<?php
include_once('./includes/conexion.php');
include_once('./includes/sanitize.class.php');
include_once('./includes/arrayHash.class.php');
/**
 primero controlamos que exista una conexion, sino informamos del error y
 volvemos a la pantalla de carga
 */
if(!is_resource($conex)){
	include_once('./editar.php');
	die("<h2 style='color:#FF0000'>{$conex}</h2>");
}
/**
 segundo paso controlar los datos obligatorios, si faltan informar y 
  volvemos a la pantalla de carga
*/
$idAlumno = isset($_POST['idAlumno'])?SanitizeVars::INT($_POST['idAlumno']):FALSE;
$hash = isset($_POST['hash'])?SanitizeVars::STRING($_POST['hash']):FALSE;
if(!$idAlumno || !ArrayHash::check($hash, array('idAlumno'=>$idAlumno))){
	include_once('./editar.php');
	die("<h2 style='color:#FF0000'>Faltan parametros obligatorios!!</h2>");
}

$botonAceptar = isset($_POST['botonAceptar'])?TRUE:FALSE;
$apellido = isset($_POST['apellido'])?SanitizeVars::SQL($_POST['apellido']):FALSE;
$nombre = isset($_POST['nombre'])?SanitizeVars::SQL($_POST['nombre']):FALSE;
$dni = isset($_POST['dni'])?SanitizeVars::INT($_POST['dni']):FALSE;
if(!$botonAceptar || !$apellido || !$nombre || !$dni){
	include_once('./editar.php');
	die("<h2 style='color:#FF0000'>Complete todos los campos obligatorios!!</h2>");
}
/**
 si ya tenemos los datos obligatorios actualizamos el alumno, y
 limpiamos el resto de las variables del POST
*/
$edad = isset($_POST['edad'])?SanitizeVars::INT($_POST['edad']):0;
$sexo = isset($_POST['sexo'])?SanitizeVars::STRING($_POST['sexo']):'M';
$direccion = isset($_POST['direccion'])?SanitizeVars::SQL($_POST['direccion']):'';
$barrio = isset($_POST['barrio'])?SanitizeVars::INT($_POST['barrio']):1;
$observaciones = isset($_POST['observaciones'])?SanitizeVars::SQL($_POST['observaciones']):'';
$fecha = isset($_POST['fecha'])?SanitizeVars::DATE_HISPANA($_POST['fecha']):NULL;
//-- iniciamos la trans
db_start_trans($conex);

//-- armamos el SQL
$sql = "UPDATE alumno 
		SET apellido = '$apellido',
			nombre = '$nombre',
			dni = '$dni',
			fechaNacimiento = '$fecha',
			sexo = '$sexo',
			direccion = '$direccion',
			barrio = '$barrio',
			observaciones = '$observaciones'
		WHERE (idAlumno = '$idAlumno')";
$ok = @mysql_query($sql, $conex);
//-- informamos del error o continuamos
if(!$ok){
	$errorNro = @mysql_errno($conex);
	$errorMsg = "Error({$errorNro}): ".@mysql_error($conex);
	db_rollback($conex);
	include_once('./editar.php');
	die("<h2 style='color:#FF0000'>{$errorMsg}</h2>");
}
/**
 si ya insertamos el alumno, ahora insetamos las inscripciones,
 pero primero debemos borrarlas a todas para luego insertar
*/
//-- armamos el SQL
$sql = "DELETE FROM inscripcion WHERE (idAlumno = '$idAlumno')";
$ok = @mysql_query($sql, $conex);
//-- informamos del error o continuamos
if(!$ok){
	$errorNro = @mysql_errno($conex);
	$errorMsg = "Error({$errorNro}): ".@mysql_error($conex);
	db_rollback($conex);
	include_once('./editar.php');
	die("<h2 style='color:#FF0000'>{$errorMsg}</h2>");
}
//-- actualizamos las inscripciones
$todoOk = true;
if(isset($_POST['materias']) and count($_POST['materias']) > 0){
	$hoy = date('Y-m-d');
	$materias = $_POST['materias'];
	foreach($materias as $idMateria){
		$sql = "INSERT INTO inscripcion(idAlumno, idMateria, fecha)
				VALUES('$idAlumno','$idMateria','$hoy')";
		$ok = @mysql_query($sql, $conex);
		if(!$ok){
			$errorNro = @mysql_errno($conex);
			$errorMsg = "Error({$errorNro}): ".@mysql_error($conex);
			$todoOk = false;
			break;
		}
	}	
}
/**
 informamos y volvemos al index o a la pantalla de listado
 */
if($todoOk){
	db_commit($conex);
	echo("<h2 style='color:#00FF00'>Alumno Actualizado Correctamente</h2>");
	echo "<a href='./index.html'>[ Volver al Menú ]</a> || <a href='./listado.php'>[ Volver al Listado ]</a>";
}
else{
	db_rollback($conex);
	include_once('./editar.php');
	die("<h2 style='color:#FF0000'>{$errorMsg}</h2>");
}
?>