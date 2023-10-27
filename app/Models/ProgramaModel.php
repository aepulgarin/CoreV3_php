<?php
namespace App\Models;
use Core\mainModel;
class ProgramaModel extends mainModel {
    public string $table='core_programas';
	public function grabarPermisos(){
		foreach ($this->permisos as $codigo => $nombre) {
			$mpermisos[]=$codigo;	
			$mval=$this->lee_todo("SELECT opcion FROM core_programas_opciones WHERE id_programa='{$this->id}' and opcion='$codigo' ");
			if(count($mval)>0){			
				$this->ejecuta_query("UPDATE core_programas_opciones set descripcion='$nombre' where id_programa='{$this->id}' and opcion='$codigo'");
			}else{				
				$this->id_p = $this->ejecuta_query("INSERT into core_programas_opciones (id_programa,opcion,descripcion) values ('{$this->id}','$codigo','$nombre')",'id');
			}
		}
		$this->ejecuta_query("DELETE FROM core_programas_opciones WHERE id_programa='{$this->id}' and opcion not in ('".implode("','",$mpermisos)."')");
	}

	public function getPermiso($id_programa,$opcion='',$muestra_error=true){
		
		if($opcion!=''){
			$sql_add=" and po.opcion='$opcion'";
		}else {
			$sql_add="";
		}

		$consulta="SELECT
		DISTINCT po.opcion, po.descripcion as nombre
		FROM
		core_usuarios u,
		core_usuarios_roles ur,
		core_roles r, 
		core_permisos p,
		core_programas_opciones po,
		core_programas pg 
		WHERE
		u.id=ur.id_usuario and
		ur.id_rol=r.id and
		r.id=p.id_rol and
		p.id_programa_opcion=po.id and
		po.id_programa=pg.id and
		u.usuario=? AND
		pg.id=? 
		$sql_add";
		$m=$this->lee_prepare($consulta,[
            $_SESSION['usuario']??'',
            $id_programa
        ]);
		if(count($m)>0){
			$this->permisos=$m;
			return true;	
		}else{
			return false;
		}
	}

	public function getOpciones(){
		return $this->lee_todo("SELECT a.id,a.programa, trim(b.opcion) opcion, lower(b.descripcion) as nombre, b.id as id_programa_opcion FROM core_programas a, core_programas_opciones b WHERE a.id=b.id_programa  order by 1,2");
	}

	public function logAcceso(){
		//$this->ejecuta_query("UPDATE nue_perpro set nro_ingresos=nro_ingresos+1 WHERE programa='{$this->programa}' AND usuario='".$_SESSION['usuario']."'");
	}

	public function eliminarPrograma(){
		$this->begin_work();
		$this->ejecuta_query("DELETE from core_programas_opciones where id_programa='{$this->id}'");

		$this->ejecuta_query("DELETE from core_permisos where id_programa_opcion not in (select id from core_programas_opciones)");
		$this->commit();
	}

	public function getMenuProgramas($sub){
		#menus dependientes
		$consulta="SELECT distinct up.id_menu as id, up.orden_menu as orden, up.nombre_menu  as nombre, up.id_menu_parent as id_sub, up.icono 
                    from v_usuarios_permisos as up where id_usuario=? and id_menu_parent=? order by 1,2";
		$mmenu=$this->lee_prepare($consulta,[
            $_SESSION['id_usuario']??'',
            $sub
        ]);
		for ($i=0; $i <count($mmenu) ; $i++) { 
			$idsub=$mmenu[$i]->id;
			$mmenu[$i]->sub=$this->getMenuProgramas($idsub);
			
			#programas del menu
			$consulta="SELECT distinct up.id_programa, up.programa, up.descripcion_programa as descripcion , up.orden_programa as orden, prog_icon 
                    from  v_usuarios_permisos as up where id_usuario=? and id_menu=? order by 4,3";
			$mmenu[$i]->progs=$this->lee_prepare($consulta,[
                $_SESSION['id_usuario']??'',
                $idsub
            ]);

		}
		return $mmenu;
	}
}