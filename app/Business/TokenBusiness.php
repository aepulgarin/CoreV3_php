<?php
namespace App\Business;
use App\Business\UsuarioBusiness;
use App\Models\TokenModel;
use Carbon\Carbon;

class TokenBusiness extends \Core\mainBusiness{
	public TokenModel $Token;
    public $token;
    public function __construct () {
        $this->Token = new TokenModel();
    	$this->Token->wait();
    }

    public function crearToken($id_usuario,$password):string{
		$token=sha1($id_usuario.$password.rand().Time());
        $rows=$this->grabarToken($id_usuario,$token);
        if($rows==0){
            throw new \Exception("error en creacion de token");
        }
		return $token;
	}

	public function grabarToken($id_usuario,$token):int{
        $dominio=$_SERVER['SERVER_NAME'];
        $vigencia =Carbon::now()->addHours(10)->format('Y-m-d H:i:s');
        $hora =Carbon::now()->format('Y-m-d H:i:s');
        $valida=$this->getToken($id_usuario);
        if(isset($valida->id)){
            $rows=$this->Token->updateOneById([
                $id_usuario,
                $dominio,
                $token,
                $vigencia,
                $hora,
            ],$valida->id);
        }else{
            $rows=$this->Token->createOne([
                $id_usuario,
                $dominio,
                $token,
                $vigencia,
                $hora,
            ]);
        }
        return $rows;
	}
    public function getToken($id_usuario){
        return $this->Token->readOneByColumn('id_usuario',$id_usuario);
    }
    function verificarToken($token):bool{
        $Usuario = new UsuarioBusiness();
        $username=$_SESSION['usuario']??'';
        $datos = $Usuario->traerUsuario($username);

        if($datos->existe){
            $data=$this->getToken($datos->id);
            $ahora=Carbon::now()->format('Y-m-d H:i:s');
            $fechaValida = Carbon::parse($data->vigencia)->gte($ahora);
            $valido= ($fechaValida && $token== $data->token);
        }else{
            $valido=false;
        }
        return $valido;
    }
}