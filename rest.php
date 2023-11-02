<?php
require_once __DIR__ . '/vendor/autoload.php';
include_once ("core/config.php");

use App\Business\TokenBusiness;
use Core\mainModel;
use Core\mainController;
use App\Controllers\LoginController;
use App\Business\LoginBusiness;
use App\Models\ProgramaModel;

session_start();

//sanitizar parametros GET
$Core=new  mainController();
$_REQUEST=$Core->sanitize($_REQUEST);

$tipo = $_REQUEST['tipo']??'';
$modulo = (trim($_REQUEST['modulo']??''));
$nameModel =($modulo);
$metodo = $_REQUEST['metodo']??'';
$token = $_REQUEST['token']??'';
$parametros = $_REQUEST['parametros']??'';
$user = $_REQUEST['user']??'';

$Programa = new ProgramaModel();
$data=$Programa->readOneByColumn('programa',($modulo));
if($user!=''){
	$_SESSION['usuario']=$user;
}

if($data->autenticado=='S'){
	if($token==''){
		header(':', true, '401');
		die("No envio token - $modulo::$metodo");
	}else{
		$Token = new TokenBusiness();
		$valido=$Token->verificarToken($token);
		if(!$valido){
            $Login = new LoginBusiness();
			//$Login->logOut();
			die("Token incorrecto o caducado");
		}
	}
}

$rest = loadClass($nameModel);
$parametros = $rest->sanitize($parametros);
$rest->{$metodo}($parametros);

function loadClass($name) {
	$clase =$name.'Controller';
	$controlador = __DIR__."/app/Controllers/{$name}Controller.php";
	if(file_exists($controlador)){
		require_once($controlador);
	}else{
		print_r($_REQUEST);
		die("-");
	}
    
    return new $clase();
}