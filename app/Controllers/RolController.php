<?php
use App\Business\RolBusiness;
$plantilla="layout.html";
class RolController extends \Core\mainController{
    public RolBusiness $Rol;
    public function __construct () {
        $this->Rol = new RolBusiness();
    }
	function buscarRol($parametros){
		$nombre=$parametros['nombre'];
		$modelo= new rolModel($nombre);
		$this->response($modelo);
	}
	function traerRoles($parametros){
        $this->defaultMethod(
            $this->Rol->traerRoles((object)$parametros)
        );
	}
	function grabarrol($parametros){
		$frm=$parametros['frm'];
		$modelo= new rolModel($frm['rol']);
		$modelo->nombre=strtoupper($frm['rol']);
		$modelo->descripcion=ucfirst(strtolower($frm['descripcion']));
		$modelo->estado=strtoupper($frm['estado']);
		$modelo->usuarios=$frm['usuarios'];
		$modelo->begin_work();
		$modelo->grabar();
        /*
         * if(is_array($this->permisos)){
			$m=$this->permisos;
			for ($i=0; $i <count($m) ; $i++) {

			}
		}
         * */
		$respuesta["mensaje"]='exitoso';
		$modelo->commit();
		$this->response($respuesta);	
	}
	function grabarPermisos($parametros){

		$nombre=$parametros['nombre'];
		$permisos=$parametros['permisos'];
		$eliminar=$parametros['eliminar'];

		$modelo= new rolModel($nombre);
		if($modelo->existe!='S'){
			$respuesta["mensaje"]='rol '.$nombre.' no existe, verifique';
		}else{
			$modelo->begin_work();
			#organiza array de permisos para enviarlo al modelo.

			if (empty($permisos)) 
				$permisos = array();				

			$conteo = count($permisos);
			for ($i=0; $i < $conteo ; $i++) { 

		
				list($id_programa,$opcion, $id_programa_opcion)=explode("-",$permisos[$i]);
				if($id_programa!='' && $id_programa>0){
					$mpermisos[$i]['id_programa']=$id_programa;
					$mpermisos[$i]['opcion']=$opcion;
					$mpermisos[$i]['id_programa_opcion']=$id_programa_opcion;
				}
			}
			$modelo->permisos=$mpermisos;

			#elimina permimos del rol
			if($eliminar=='S'){
				$modelo->eliminarPermisos();	
			}
			$modelo->grabarPermisos();
			$respuesta["mensaje"]='exitoso';
			$modelo->commit();	
		}
		$this->response($respuesta);	
	}
}	

