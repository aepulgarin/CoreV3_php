<?php
//namespace App\Controllers;
use App\Business\UsuarioBusiness;
$plantilla="layout.html";
class UsuarioController extends \Core\mainController{
	private UsuarioBusiness $Business;
	public function __construct () {
		$this->Business = new UsuarioBusiness();
	}
	function traerUsuario($parametros):void{
        $this->defaultMethod(
            $this->Business->traerUsuario($parametros['usuario'])
        );
	}
	function grabarUsuario($frm):void{
        $this->defaultMethod(
            $this->Business->grabarUsuario((object)$frm)
        );
	}
	function traerLista($parametros):void{
        $this->defaultMethod(
            $this->Business->traerLista((object)$parametros)
        );
	}
	function cambioContrasena($parametros):void{
        $this->defaultMethod(
            $this->Business->cambioContrasena((object)$parametros)
        );
	}
}	

