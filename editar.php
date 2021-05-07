<?php
set_include_path("./includes/".PATH_SEPARATOR."./includes/PEAR/");
include_once('conexion.php');
include_once('sanitize.class.php');
include_once('arrayHash.class.php');
//primero controlamos que exista una conexion, sino informamos del error
if(!is_resource($conex)){
	$errorExterno = "<h2 style='color:#FF0000'>{$conex}</h2>";
	include_once('./listado.php');
	die;
}
//segundo paso controlar los datos obligatorios, si faltan informar
$idAlumno = isset($_REQUEST['idAlumno'])?SanitizeVars::INT($_REQUEST['idAlumno']):FALSE;
$hash = isset($_REQUEST['hash'])?SanitizeVars::STRING($_REQUEST['hash']):FALSE;
if(!$idAlumno || !ArrayHash::check($hash, array('idAlumno'=>$idAlumno))){
	$errorExterno = "<h2 style='color:#FF0000'>Faltan campos obligatorios!!</h2>";
	include_once('./listado.php');
	die;
}
//-- armamos el SQL
$sql = "SELECT * FROM alumno WHERE (idAlumno = '$idAlumno')";
$resultado = @mysql_query($sql, $conex);
if(!is_resource($resultado) || @mysql_num_rows($resultado) <= 0){
	$errorExterno = "<h2 style='color:#FF0000'>Alumno no encontrado!!</h2>";
	include_once('./listado.php');
	die;
}
$alumno = mysql_fetch_array($resultado);
@mysql_free_result($resultado);
//si todos los datos ok, hacemos la plantilla
require_once('HTML/Template/IT.php');
//-- creamos un objeto
$tpl = new HTML_Template_IT('./templates');
//-- levantamos el template a usar
$tpl->loadTemplatefile('editar.html', true, true);
//seteo todos los parametros
$tpl->setAll($_REQUEST);
//seteo todos los datos del alumno
$tpl->setAll($alumno);
//ahora los casos particulares
$date = explode('-',$alumno['fechaNacimiento']);
$tpl->setVariable('fechaNacimiento', $date[2].'-'.$date[1].'-'.$date[0]);
$tpl->setVariable('sexo_'.$alumno['sexo'], "checked");
//--- materias en las que se inscribe
$sql = "SELECT M.*, I.id
		FROM materia AS M
		LEFT JOIN inscripcion AS I ON(M.idMateria = I.idMateria AND idAlumno = '$idAlumno')";
$materias = @mysql_query($sql, $conex);
if(!is_resource($materias) || @mysql_num_rows($materias) <= 0){
	$tpl->setVariable('sin_materias', "<h2 style='color:#FF0000'>No hay materias para inscribirse!!</h2>");
}
else{
	$tpl->setCurrentBlock('materias');
	while($materia = mysql_fetch_array($materias)){
		$tpl->setAll($materia, '_m');
		if($materia['id']){
			$tpl->setVariable('checked_m', "checked");
		}
		$tpl->parseCurrentBlock();
	}
	@mysql_free_result($materias);
}
$tpl->show();
?>
