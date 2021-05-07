<?php
//-- colocamos el path de las librerías en el entorno
set_include_path("./recursos/_php/".PATH_SEPARATOR."./includes/");

//-- incluimos la libreria
include_once("class.TemplatePower.inc.php");
include_once('conexion.php');
include_once('sanitize.class.php');
include_once('arrayHash.class.php');

//-- levantamos el template a usar
$tpl = new TemplatePower('templates/nuevo.html');
$tpl-> prepare();

if ( isset($_REQUEST['msg']) && $_REQUEST['msg']) {
  $tpl->newBlock("mensaje");
  $tpl->assign("mensaje_tipo", $_REQUEST['msg']);
};
//-- enviamos el template al navegador
echo $tpl->getOutputContent();




?>
