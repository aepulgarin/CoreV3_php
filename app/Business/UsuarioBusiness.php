<?php
namespace App\Business;
use App\Models\UsuarioModel;
use Carbon\Carbon;

class UsuarioBusiness extends \Core\mainBusiness{
	public UsuarioModel $Usuario;
    public function __construct () {
        $this->Usuario = new UsuarioModel();
    }
    public function traerUsuario($usuario_){
        $data=$this->Usuario->readOneByColumn('usuario',strtolower($usuario_));
        $data->existe = isset($data->id);
        return $data;
    }
    public function	validarContrasena($password, $data):bool{
        return ($password===$data->usr_pass);
    }
    function traerLista($param):array{
        return $this->Usuario->readManyByColumn(
            (object)[
                'estado'=>$param->estado
            ],
            'usuario'
        );
    }
    function grabarUsuario($frm):bool{
        $data=$this->traerUsuario(trim(strtolower($frm->usuario)));
        $nuevo=(object)[
            'usuario'   => trim(strtolower($frm->usuario)),
            'nombre'   => ucwords(strtolower($frm->nombre)),
            'apellidos' => ucwords(strtolower($frm->apellidos)),
            'correo'    => strtolower($frm->correo),
            'estado'    => strtoupper($frm->estado??'I')
        ];

        $this->Usuario->begin_work();
        if($data->existe) {
            $this->Usuario->updateOneById([
                $nuevo->usuario,
                (($frm->password1??'')!='')?$this->encodePassword($frm->password1):$data->usr_pass,
                $nuevo->nombre,
                $nuevo->estado,
                $nuevo->apellidos,
                $nuevo->correo,
                Carbon::today()->addDays(20)->toDateString(),
            ],$data->id);
        }else{
            $this->Usuario->createOne([
                $nuevo->usuario,
                $this->encodePassword($frm->password1??''),
                $nuevo->nombre,
                $nuevo->estado,
                $nuevo->apellidos,
                $nuevo->correo,
                null
            ]);
        }
        $this->Usuario->commit();
        return true;
    }
    private function encodePassword($password):string{
        return sha1($password);
    }
    public function cambioContrasena(object $param):string{
        $respuesta='';
        $actual=$this->encodePassword($param->contrasena_actual);
        $nueva=$param->contrasena_nueva;
        $nueva2=$param->contrasena_nueva2;

        $data=$this->traerUsuario($_SESSION['usuario']);
        if($nueva===$nueva2 && $nueva!='' && $data->usr_pass===$actual){
            $data->password1 = $nueva;

            if($this->grabarUsuario($data))
            $respuesta='exitoso';
        }else{
            throw new \Exception("Contrase&ntilde;as no coinciden, verifique..");
        }
        return $respuesta;
    }
}