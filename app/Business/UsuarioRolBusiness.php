<?php
namespace App\Business;
use App\Models\UsuarioRolModel;
use Carbon\Carbon;

class UsuarioRolBusiness extends \Core\mainBusiness{
	public UsuarioRolModel $UsuarioRol;
    public function __construct () {
        $this->UsuarioRol = new UsuarioRolModel();
    }
    public function grabarRolesUsuario(object $param):void{
        $this->borrarRolesUsuario($param->id_usuario);
        foreach ($param->roles as $rol){
            $this->grabarRolUsuario((object)[
                'id_usuario'=>$param->id_usuario,
                'id_rol'=>$rol
            ]);
        }
    }
    public function grabarRolUsuario(object $param):int{
        return $this->UsuarioRol->createOne([
            $param->id_usuario,
            $param->id_rol
        ]);
    }
    public function borrarRolesUsuario(int $id_usuario):int{
        return $this->UsuarioRol->deleteManyByColumn((object)[
            'id_usuario'=>$id_usuario
        ]);
    }

}