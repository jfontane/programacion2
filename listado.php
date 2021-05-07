<?php
//-- colocamos el path de las librerías en el entorno
set_include_path("./recursos/_php/".PATH_SEPARATOR."./includes/");

//-- incluimos la libreria
include_once("class.TemplatePower.inc.php");
include_once('conexion.php');
include_once('sanitize.class.php');
include_once('arrayHash.class.php');

//-- levantamos el template a usar
$tpl = new TemplatePower('templates/listado.html');
$tpl-> prepare();

if( isset($_POST['botonBuscar']) && $_POST['botonBuscar'] ){
		//-- limpio la entrada
		$apellido = isset($_POST['apellido'])?SanitizeVars::SQL($_POST['apellido']):FALSE;
		$nombre = isset($_POST['nombre'])?SanitizeVars::SQL($_POST['nombre']):FALSE;
		$dni = isset($_POST['dni'])?SanitizeVars::SQL($_POST['dni']):FALSE;
		//-- armo el filtro de búsqueda
		$where = array();

		if($apellido){
			$where[] = "(apellido LIKE '%{$apellido}%')";
			$tpl->assign("apellido_b", $apellido);
		}
		if($nombre){
			$where[] = "(nombre LIKE '%{$nombre}%')";
			$tpl->assign("nombre_b", $nombre);
		}
		if($dni){
			$where[] = "(dni = '$dni')";
			$tpl->assign("dni_b", $dni);
    }
	  $where = implode('AND', $where);
	  $sql = "SELECT * FROM alumno ".(!empty($where)?"WHERE {$where}":"")." ORDER BY apellido ASC, nombre ASC";
} else {
		$sql = "SELECT * FROM alumno ORDER BY apellido ASC, nombre ASC";
}

//-- EJECUTAMOS LA CONSULTA Y MOSTRAMOS EL RESULTADO
$resultado = @mysqli_query($conex,$sql);
if ($resultado) {
			$mostrarTabla = ($resultado && mysqli_num_rows($resultado) > 0)?true:false;
			if($mostrarTabla){
					while($fila = mysqli_fetch_array($resultado)){
						$tpl->newBlock("listado");
						$date = explode('-',$fila['fechaNacimiento']);
						$fechaNacimiento = $date[2].'-'.$date[1].'-'.$date[0];
						$tpl->assign("apellido", $fila['apellido']);
						$tpl->assign("nombre", $fila['nombre']);
						$tpl->assign('dni', $fila['dni']);
						$tpl->assign('sexo', ($fila['sexo']=='M'?"Masculino":"Femenino"));
						$tpl->assign('fechaNacimiento', $fechaNacimiento);
						$tpl->assign('idAlumno', $fila['id']);
						$tpl->assign('hash', ArrayHash::encode(array('idAlumno'=>$fila['id'])));
					}
			} else {
				  $tpl->newBlock("no_resultado");
			}
} else {
      $errorNo = mysqli_errno($conex);
			$error = mysqli_error($conex);
			$error_completo = $errorNo.': '.$error;
			$tpl->newBlock("mensaje");
			$tpl->assign("mensaje_tipo", $error_completo);

}
//-- enviamos el template al navegador
echo $tpl->getOutputContent();
?>
