<?php
namespace App\Business;
use Core\mainBusines;
use App\Models\ProgramaModel;
class ProgramaBusiness extends \Core\mainBusiness{
    public ProgramaModel $Programa;
    public $token;

    public function __construct () {
        $this->Programa = new ProgramaModel();
    }
    public function crearMenuLateral(){
        //carga del menu de permisos
		if(($_SESSION['usuario']??'')!=''){
			$dataMenuLateral = $_SESSION['menu_lateral']??[];
			if(count($dataMenuLateral)==0){
				//consulta menu de permisos
				$mPrograma=new ProgramaModel($this->modulo??'');
				$dataMenuLateral = $mPrograma->getMenuProgramas(0);
				$_SESSION['menu_lateral']=$dataMenuLateral;
			}

			$htmlMenu='';
			foreach($dataMenuLateral as $menus){
				$htmlMenu.="<li class='sidebar-header'>{$menus->nombre}</li>";
				foreach($menus->progs as $progs){
					$htmlMenu.="<li class='sidebar-item'>
									<a class='sidebar-link' href='index.php?modulo=".strtolower($progs->programa)."'>
										<i class='align-middle fa fa-{$progs->prog_icon}'></i> <span
											class='align-middle'>".ucwords(strtolower($progs->descripcion))."</span>
									</a>
								</li>";
				}
			}
			return $htmlMenu;
		}
    }

    public function crearLogAcceso(){
        $this->Programa->logAcceso();
    }

    public function getProgramaParameters($programa){
        return $this->Model;
    }

    public function traerProgramas():array
    {
        return $this->Programa->readManyByColumn(
            (object)['1'=>1],
            'programa');
    }

    public function grabarPrograma(object $param):string
    {
        $data=$this->Programa->readOneByColumn('programa',strtoupper($param->programa));
        $nuevo = [
            $param->descripcion,
            $param->id_menu,
            ($param->autenticado??'N'),
            strtoupper($param->programa),
            $param->orden,
            $param->prog_icon];

        if(isset($data->id)){
            $nro=$this->Programa->updateOneById($nuevo,$data->id);
            return "Programa {$param->programa} modificado exitoso. $nro";
        }else{
            $nro=$this->Programa->createOne($nuevo);
            return "Programa {$param->programa} creado exitoso. $nro";
        }
    }

    public function borrarPrograma(object $param):string
    {
        $nro= $this->Programa->deleteOneById($param->id);
        return "Programa eliminado. $nro";
    }
}