<?php
namespace App\Business;

use App\Business\UsuarioBusiness;
use App\Business\TokenBusiness;

class LoginBusiness extends \Core\mainBusiness{
	public $Usuario;
	public $Token;
    public function __construct () {
        $this->Usuario = new UsuarioBusiness();
    	$this->Token = new TokenBusiness();
    }
    function autenticar($param):array{
        $username=$param->usuario??'';
        $password=$param->password??'';

        $datos = $this->Usuario->traerUsuario($username);
        //$this->logOut();

        if($datos->existe){
            if($this->Usuario->validarContrasena($password, $datos)){
                $token=$this->Token->crearToken($datos->id,$password);
                $this->iniciarSession($datos);
                $info=[
                    "usuario"=>$username,
                    "token"=>$token,
                    "nombre"=>$datos->nombre,
                    "apellidos"=>$datos->apellidos
                ];
            }else{
                throw new \Exception( 'Usuario o Contraseña no valida');
            }
        }else{
            throw new \Exception('Usuario o Contraseña no valida');
        }
        return $info;
    }
	public function iniciarSession($data){
		$_SESSION['id_usuario']=$data->id;
        $_SESSION['usuario']=$data->usuario;
        $_SESSION['nombreusu']=$data->nombre;
	}
	public function logOut():void{
		session_destroy();
        session_start();
	}
}