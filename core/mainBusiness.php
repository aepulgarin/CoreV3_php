<?php
namespace Core;
class mainBusiness {
	public $Model;

    public function traerUsuario($usuario_){
        $data=$this->Usuario->readOneByColumn('usuario',strtolower($usuario_));
        $data->existe = isset($data->id);
        return $data;
    }

	function __destruct(){
		$this->Model = null;
		unset($this->Model); 
	}
}
