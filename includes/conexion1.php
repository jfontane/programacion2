<?php
include_once(dirname(__FILE__).'/config.php');

/////////////////////////////////////////////
// Funcion conectar -> conecta con la base de datos
function conectar(){
	$handle = @mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	if(!$handle){
		$errorNro = @mysqli_errno($handle);
		$errorMsg = "No existe conexion con la Base de Datos.".@mysqli_error($handle);
		switch($errorNro){
			case 2005:
				$errorMsg = "No existe conexion con la Base de Datos, no se puede encontrar el Servidor MySQL";
				break;
			case 1045:
				$errorMsg = "No existe conexion con la Base de Datos, el usuario es incorrecto";
				break;
		}
		return $errorMsg;
	}
	if(!@mysqli_select_db($handle, DB_NAME)){
		$errorNro = @mysqli_errno($handle);
		$errorMsg = "No existe conexion con la Base de Datos.".@mysqli_error($handle);
		switch($errorNro){
			case 1044:
				$errorMsg = "No existe conexion con la Base de Datos, usuario sin permisos";
				break;
		}
		return $errorMsg;
	}
	@mysqli_query($handle, "SET NAMES 'utf8'");
	return $handle;
}
//------------------

//------------------
function db_start_trans($handle) {
	$ok = mysqli_query($handle, "SET AUTOCOMMIT=0");
	$ok2 = mysqli_query($handle, "BEGIN");
	if(!$ok || !$ok2){
		return false;
	}
}
function db_commit($handle) {
	$ok = mysqli_query($handle, "COMMIT");
	$ok2 = mysqli_query($handle, "SET AUTOCOMMIT=1");
	if(!$ok || !$ok2){
		return false;
	}
}
function db_rollback($handle) {
	$ok = mysqli_query($handle, "ROLLBACK");
	$ok2 = mysqli_query($handle, "SET AUTOCOMMIT=1");
	if(!$ok || !$ok2){
		return false;
	}
}
//-------------------

$conex = conectar();
?>
