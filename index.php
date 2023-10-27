<?php
session_start();

$plantilla='';
require_once __DIR__ . '/vendor/autoload.php';
include_once ("core/config.php");
use Core\mainModel;
use Core\mainController;
use Core\mainBusiness;
use Core\mainTemplate;
use App\Models\ProgramaModel;

//sanitizar parametros GET
$Core = new  mainController();
$_GET = $Core->sanitize($_GET??[]);

$modulo=strtolower($_GET['modulo']??''); 
$nameModel =ucfirst($modulo);
$usuario =$_SESSION['usuario']??'';

if($modulo!=''){	
	$Programa= new ProgramaModel();
	$data=$Programa->readOneByColumn('programa',strtoUpper($modulo));
	if($data->autenticado=='S' && $usuario==''){
		$modulo=''; //forza autenticacion si no esta logeado
	}

	#Valida permiso al modulo
	if(!$Programa->getPermiso($data->id,'') && $data->programa!='INICIO' && $data->autenticado=='S'){
		echo "<script>alert('$modulo: Modulo no existe. ".$Programa->mensaje."');document.location='index.php?modulo=inicio'</script>";
		die();
	}
	#Log de acceso
	//$mPrograma->core_log_programa($modulo,$parametros);

	$controlador = ROOT_PATH."app/Controllers/{$nameModel}Controller.php";
	if(file_exists($controlador)){include_once($controlador);}
	
	###---VISTA
	$template = new mainTemplate();
	$template->modulo = $nameModel;
	$template->template = $plantilla;
	$template->cargarTemplate();

	if (isset($_GET['redirect'])) {
		$redirect = $_GET['redirect'];
	}else {
		$redirect = '';
	}

	#Redireccion CORE
	if($redirect!=''){
		$url=explode("/",substr(base64_decode($_GET['redirect']),1));
		if($url[1]=='index.php' && count($url)==2){
			//do nothing
		}else{
			echo "<script>setTimeout(function(){document.location='".$_GET['redirect']."';},1);</script>";	
		}
	}
}else{
	if($usuario==''){
		header("Location: index.php?modulo=login&redir=".$_SERVER['REQUEST_URI']);
	}else{
		header("Location: index.php?modulo=inicio");
	}
}