<?php
class estimacionBusiness extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new estimacionModel();
        $this->Model->wait();
    }
    public function traerProyectos($estado='A'){
		return $this->Model->getProyectos($estado);
	}
    public function traerProyecto($param){
        return $this->Model->getProyecto($param);
	}
    public function crearProyecto($param){
		return $this->Model->insertProyecto($param);
	}
    public function actualizarProyecto($param){
		return $this->Model->updateProyecto($param);
	}
    public function borrarProyecto($param){
		return $this->Model->deleteProyecto($param);
	}

    public function traerClientes($param){
		return $this->Model->getClientes($param);
	}
    public function traerInfluenciasProyecto($param){
		return $this->Model->getInfluenciasProyecto($param);
	}
    public function actualizarInfluenciasProyecto($param){
        $this->prueba();
        $this->Model->begin_work();
        $this->Model->clearInfluenciasProyecto($param->id);
        foreach ($param->influencias as $key => $valor) {
            $key = substr($key,1);
            $this->Model->setInfluenciaProyecto($param->id, $key, $valor);
        }
        $this->Model->commit();
        return true;
	}

    public function traerEntidadesProyecto($param){
        return $this->Model->traerEntidadesProyecto($param);
    }
    public function insertarEntidadProyecto($param){
        return $this->Model->insertEntidadProyecto($param);
    }
    public function actualizarEntidadProyecto($param){
        return $this->Model->updateEntidadProyecto($param);
    }
    public function borrarEntidadProyecto($param){
        return $this->Model->deleteEntidadProyecto($param);
    }

    public function traerFuncionesProyecto($param){
        return $this->Model->traerFuncionesProyecto($param);
    }
    public function insertarFuncionProyecto($param){
        return $this->Model->insertFuncionProyecto($param);
    }
    public function actualizarFuncionProyecto($param){
        return $this->Model->updateFuncionProyecto($param);
    }
    public function borrarFuncionProyecto($param){
        return $this->Model->deleteFuncionProyecto($param);
    }
    public function realizarEstimacion($param){
        require ("EstimacionPF.php");
        $Ej= new EstimacionPF();
        $Ej->influencia =$this->organizarIncluenciasEstimacion($param->id);
        $Ej->funciones  =$this->organizarFuncionesEstimacion($param->id);
        $Ej->valorHora  =74000;
        $Ej->calcular();
        return ['EH'=>$Ej->EH, 'Semanas'=>$Ej->Semanas, 'Precio'=>$Ej->Precio];
    }
    private function organizarIncluenciasEstimacion($idProyecto){
        $influencias = $this->traerInfluenciasProyecto($idProyecto);
        $retorna=array();
        for ($a=0;$a<count($influencias);$a++){
            $retorna[$a] = (object) ['orden'=>($a+1),'atributo'=>$influencias[$a]->atributo,'influencia'=>round($influencias[$a]->valor)];
        }
        return $retorna;
    }
    private function organizarFuncionesEstimacion($idProyecto){
        $funciones = $this->traerFuncionesProyecto($idProyecto);
        $retorna=array();
        for ($a=0;$a<count($funciones);$a++){
            $retorna[$a]=$funciones[$a];
            $retorna[$a]->orden=$a+1;
        }
        return $retorna;
    }
    private function prueba(){
        $tabla='funciones';
        $data =$this->Model->lee_todo("SELECT COLUMN_NAME, COLUMN_DEFAULT, DATA_TYPE,COLUMN_KEY,EXTRA
                FROM INFORMATION_SCHEMA.COLUMNS c
                 WHERE  TABLE_SCHEMA = 'u630788401_alla_db' AND TABLE_NAME = '$tabla'
                 order by ORDINAL_POSITION");

        $myClass = new class{};

        foreach ($data as $key =>$col){
            $myClass->{$col->column_name} = $col->column_default;
        }

        print_r($myClass);

        die();

    }

}