<?php
$plantilla="layout.html";
class estimacionController extends mainController{
	private $Business;
	
	public function __construct () {
		$this->Business = new estimacionBusiness();
	}

	function traerProyectos($parametros){
        $result= [];
        try {
            $result['data'] = $this->Business->traerProyectos($parametros['estado']);
            $result['success']  = true;
            $result['mensaje']  ='';
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
	}
    function traerProyecto($parametros){
        $result= [];
        try {
            $result['data'] = $this->Business->traerProyecto((object)$parametros);
            $result['success']  = true;
            $result['mensaje']  ='';
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
	}
    function borrarProyecto($parametros){
        $result= [];
        try {
            $result['data'] = $this->Business->borrarProyecto((object)$parametros);
            $result['success']  = true;
            $result['mensaje']  ='';
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
	}
    function actualizaProyecto($parametros){
        $result= [];
        try {
            if($parametros['id']==0) {
                $result['data'] = $this->Business->crearProyecto((object)$parametros);
                $result['mensaje']  ='Proyecto creado';
            }else{
                $result['data'] = $this->Business->actualizarProyecto((object)$parametros);
                $result['mensaje']  ='Proyecto actualizado';
            }
            $result['success']  = true;

        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
	}
    function estimarProyecto($parametros){
        $result= [];
        try {
            $result['data'] = $this->Business->realizarEstimacion((object)$parametros);
            $result['mensaje']  ='';
            $result['success']  = true;

        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
	}
    function traerClientes($parametros){
        $result= [];
        try {
            $result['data'] = $this->Business->traerClientes($parametros['estado']);
            $result['success']  = true;
            $result['mensaje']  ='';
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
    }
    function traerInfluenciasProyecto($parametros){
        $result= [];
        try {
            $result['data'] = $this->Business->traerInfluenciasProyecto($parametros['id']);
            $result['success']  = true;
            $result['mensaje']  ='';
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
    }
    function actualizarInfluenciasProyecto($parametros){
        $result= [];
        try {
            $result['data'] = $this->Business->actualizarInfluenciasProyecto((object)$parametros);
            $result['success']  = true;
            $result['mensaje']  ='';
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
    }

    function insertarEntidadProyecto($parametros){
        $result= [];
        try {
            $result['data'] = $this->Business->insertarEntidadProyecto((object)$parametros);
            $result['success']  = true;
            $result['mensaje']  ='';
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
    }
    function actualizarEntidadProyecto($parametros){
        $result= [];
        try {
            if($parametros['id']==0) {
                $result['data'] = $this->Business->insertarEntidadProyecto((object)$parametros);
                $result['mensaje']  ='Entidad creada';
            }else{
                $result['data'] = $this->Business->actualizarEntidadProyecto((object)$parametros);
                $result['mensaje']  ='Entidad actualizada';
            }
            $result['success'] = true;
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
    }
    function traerEntidadesProyecto($parametros){
        $result= [];
        try {
            $result['data'] = $this->Business->traerEntidadesProyecto($parametros['id']);
            $result['success']  = true;
            $result['mensaje']  ='';
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
    }
    function borrarEntidadProyecto($parametros){
        $result= [];
        try {
            $result['data'] = $this->Business->borrarEntidadProyecto((object)$parametros);
            $result['success']  = true;
            $result['mensaje']  ='';
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
    }

    function insertarFuncionProyecto($parametros){
        $result= [];
        try {
            $result['data'] = $this->Business->insertarFuncionProyecto((object)$parametros);
            $result['success']  = true;
            $result['mensaje']  ='';
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
    }
    function actualizarFuncionProyecto($parametros){
        $result= [];
        try {
            if($parametros['id']==0) {
                $result['data'] = $this->Business->insertarFuncionProyecto((object)$parametros);
                $result['mensaje']  ='Funcion creada';
            }else{
                $result['data'] = $this->Business->actualizarFuncionProyecto((object)$parametros);
                $result['mensaje']  ='Funcion actualizada';
            }
            $result['success'] = true;
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
    }
    function traerFuncionesProyecto($parametros){
        $result= [];
        try {
            $result['data'] = $this->Business->traerFuncionesProyecto($parametros['id']);
            $result['success']  = true;
            $result['mensaje']  ='';
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
    }
    function borrarFuncionProyecto($parametros){
        $result= [];
        try {
            $result['data'] = $this->Business->borrarFuncionProyecto((object)$parametros);
            $result['success']  = true;
            $result['mensaje']  ='';
        }catch (Exception $exception){
            $result['success'] = false;
            $result['mensaje'] = $exception->getMessage();
        }
        $this->response($result);
    }
}