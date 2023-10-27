<?php
class estimacionModel extends mainModel {
	public function __construct(){
        $this->exceptionMode='throw';
		$this->Conectarse();
	}
	public function getProyectos($estado='A'){
		$consulta = "SELECT p.*, concat(u.nombre,' ','',u.apellido) as cliente, u.id as id_cliente FROM proyectos p, usuario u  where p.id_usuario =u.id and p.estado='$estado'";
		return $this->lee_todo($consulta);
	}
    public function getProyecto($param){
		$consulta = "SELECT p.*, concat(u.nombre,' ','',u.apellido) as cliente, u.id as id_cliente FROM proyectos p, usuario u  where p.id_usuario =u.id and p.id='{$param->id}'";
		return $this->lee_uno($consulta);
	}
    public function insertProyecto($param){
		$consulta = "insert into proyectos (nombre, descripcion, id_usuario, estado) 
            values ('{$param->nombre}','{$param->descripcion}','{$param->cliente}','A')";
		return $this->ejecuta_query($consulta,'id');
	}
    public function updateProyecto($param){
		$consulta = "Update proyectos set nombre='{$param->nombre}', descripcion='{$param->descripcion}', id_usuario='{$param->cliente}' where id='{$param->id}'";
		return $this->ejecuta_query($consulta);
	}
    public function deleteProyecto($param){
		$consulta = "DELETE from proyectos where id='{$param->id}'";
		return $this->ejecuta_query($consulta);
	}
    public function getClientes($estado='A'){
        $consulta = "SELECT u.* FROM usuario u  where u.estado='$estado'";
        return $this->lee_todo($consulta);
    }
    public function getInfluenciasProyecto($idProyecto){
        $consulta = "select i.id, i.atributo , ifnull(pi2.valor,0) as valor 
                        from influencias i 
                            left join proyecto_influencia pi2 on (i.id=pi2.id_influencia and pi2.id_proyecto ='$idProyecto') 
                        order by i.id";
        return $this->lee_todo($consulta);
    }
    public function clearInfluenciasProyecto($idProyecto){
        $consulta = "DELETE from proyecto_influencia  where id_proyecto ='$idProyecto'";
        return $this->ejecuta_query($consulta);
    }
    public function setInfluenciaProyecto($idProyecto, $influencia, $valor){
        $consulta = "insert into proyecto_influencia (id_proyecto, id_influencia, valor) 
            values ('$idProyecto','$influencia','$valor')";
        return $this->ejecuta_query($consulta,'id');
    }

    #Entidades
    public function traerEntidadesProyecto($param){
        $consulta = "select * from entidades_proyecto where id_proyecto='{$param}'";
        return $this->lee_todo($consulta);
    }
    public function insertEntidadProyecto($param){
        $consulta = "insert into entidades_proyecto (id_proyecto, entidad) 
            values ('{$param->id_proyecto}','{$param->entidad}')";
        return $this->ejecuta_query($consulta,'id');
    }
    public function updateEntidadProyecto($param){
        $consulta = "update entidades_proyecto set entidad='{$param->entidad}' 
            where id_proyecto='{$param->id_proyecto}' and id='{$param->id}'";
        return $this->ejecuta_query($consulta,'id');
    }
    public function deleteEntidadProyecto($param){
        $consulta = "DELETE from entidades_proyecto where id='{$param->id}'";
        return $this->ejecuta_query($consulta);
    }

    #Funciones
    public function traerFuncionesProyecto($param){
        $consulta = "select * from funciones where id_proyecto='{$param}'";
        return $this->lee_todo($consulta);
    }
    public function insertFuncionProyecto($param){
        $consulta = "insert into funciones (id_proyecto, funcion, entidades, entradas, salidas,tipo) 
            values ('{$param->id_proyecto}','{$param->funcion}','{$param->entidades}','{$param->entradas}','{$param->salidas}','{$param->tipo}')";
        return $this->ejecuta_query($consulta,'id');
    }
    public function updateFuncionProyecto($param){
        $consulta = "update funciones set 
                     funcion='{$param->funcion}', 
                     entidades='{$param->entidades}', 
                     entradas='{$param->entradas}', 
                     salidas='{$param->salidas}',
                     tipo ='{$param->tipo}'
            where id_proyecto='{$param->id_proyecto}' and id='{$param->id}'";
        return $this->ejecuta_query($consulta,'id');
    }
    public function deleteFuncionProyecto($param){
        $consulta = "DELETE from funciones where id='{$param->id}'";
        return $this->ejecuta_query($consulta);
    }
}  
