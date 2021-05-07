<?php
//-- colocamos el paso a la librería en el entorno
set_include_path("./includes/".PATH_SEPARATOR."./includes/PEAR/");
include_once('conexion.php');
include_once('sanitize.class.php');
require_once('HTML/Template/IT.php');

//-- creamos un objeto
$tpl = new HTML_Template_IT('./templates');
//-- levantamos el template a usar
$tpl->loadTemplatefile('adjuntar.html', true, true);
$tpl->setAll($_POST);
//-- si no hay conexion informo el error y termino
if(!is_resource($conex)){
	$tpl->setVariable('error', "<h2 style='color:#FF0000'>{$conex}</h2>");
	$tpl->show();
	die;
}	

//-- limpio la entrada
$dni = isset($_POST['dni'])?SanitizeVars::INT($_POST['dni']):FALSE;
$idMateria = isset($_POST['idMateria'])?SanitizeVars::INT($_POST['idMateria']):FALSE;
$practico = isset($_FILES['practico'])?$_FILES['practico']:FALSE;
if(isset($_POST['botonAdjuntar'])){
	//-- campos obligatorios
	if(!$dni || !$idMateria || !$practico){
		$error = "<h2 style='color:#FF0000'>Faltan campos obligatorios!</h2>";
	}
	else{
		//-- armo el SQL
		$sql = "SELECT A.idAlumno 
			FROM alumno AS A
			JOIN inscripcion AS I ON(I.idAlumno = A.idAlumno)
			WHERE (I.idMateria = '$idMateria')AND(A.dni = '$dni')";
		$resultado = @mysql_query($sql, $conex);
		if(is_resource($resultado) && mysql_num_rows($resultado) > 0){
			$alumno = mysql_fetch_array($resultado);	
			@mysql_free_result($resultado);
			
			//-- controlamos el archivo
			if(is_uploaded_file($practico['tmp_name'])){ 
				//-- carpeta a colocar el archivo
				$path = "./practicos/".$idMateria;
				if(!is_dir($path)){
					mkdir($path);
				}
				$path .= "/".$practico['name'];
				if(move_uploaded_file($practico['tmp_name'], $path)){
					$error = "<h2 style='color:#FF0000'>OK</h2>";
				}
				else{
					$error = "<h2 style='color:#FF0000'>Error en el upload de archivo!</h2>";
				}
			}
			else{
				$error = "<h2 style='color:#FF0000'>Error en la transferencia del archivo</h2>";
			}	
		}
		else{
			$error = "<h2 style='color:#FF0000'>El DNI del Alumno y/o la materia no son correctos!!</h2>";
		}
	}
}
else{
	$error = "<h2 style='color:#FF0000'>Ingrese el D.N.I. del Alumno y la Materia</h2>";
}
//-- mostramos el error
$tpl->setVariable('error', $error);

//--- materias hay que mostrarlas a todas en el listbox
$sql = "SELECT * FROM materia AS M ORDER BY nombre ASC";
$materias = @mysql_query($sql, $conex);
if(is_resource($materias) and @mysql_num_rows($materias) > 0){
	$tpl->setCurrentBlock('materias');
	while($materia = mysql_fetch_array($materias)){
		$tpl->setAll($materia,'_lista');
		if($materia['idMateria']==$idMateria){
			$tpl->setVariable('selected', "selected");		
		}
		$tpl->parseCurrentBlock();
	}
	@mysql_free_result($materias);
}
$tpl->show();
?>