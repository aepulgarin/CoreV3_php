<?php
session_start();
include_once("core/config.php");
include_once(CORE."mainModel.php");
include_once(CORE."xajaxController.php");
include_once(CORE."mainController.php");
include_once("core/mainTemplate.php");	      

$tipo=$_REQUEST['tipo'];
$modulo=$_REQUEST['modulo'];
$metodo=$_REQUEST['metodo'];
$token=$_REQUEST['token'];
$parametros=$_REQUEST['parametros'];

include_once(MODULE_PATH."1/programa/modelo.php");
include_once(MODULE_PATH."1/login/modelo.php");

$fileController=STATIC_PATH."widgets/".$modulo."/controlador.php";
$fileModel=STATIC_PATH."widgets/".$modulo."/modelo.php";

$clase="servicio";

if(file_exists($fileController)){
	include_once($fileController);
}
if(file_exists($fileModel)){
	include_once($fileModel);
}
$rest= new $clase();
$rest->{$metodo}($parametros);
