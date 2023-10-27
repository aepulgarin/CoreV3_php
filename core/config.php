<?php
//definicion de rutas y constantes
//header("Access-Control-Allow-Origin: *");
define('ROOT_PATH',	"./");
define('LIBS_PATH',	ROOT_PATH.'libs/');
define('MODULE_PATH',	ROOT_PATH.'public/');
define('STATIC_PATH',	ROOT_PATH.'static/');
define('COMPONENT_PATH',	STATIC_PATH.'componentes/');
define('CORE',ROOT_PATH.'core/');
define('TEMPLATE_PATH',	CORE.'template/');
define ('BASE_URL_PATH', 'http://'.dirname($_SERVER['HTTP_HOST'].''.$_SERVER['SCRIPT_NAME']).'/');

define('IMG_USURIOS_PATH',STATIC_PATH.'images_usuarios/');

date_default_timezone_set('America/Bogota');
define('LOCALE','es_CO');




//require CONF_PATH;

$file=CORE."/config/".$_SERVER['SERVER_NAME'].".ini";

if(!isset($_SESSION['config'])){
	
	if(file_exists($file)){
		$_SESSION['config']=parse_ini_file($file,true);
	}
}
