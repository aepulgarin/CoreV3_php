<?php
use App\Business\MenuBusiness;
$plantilla="layout.html";
class MenuController extends \Core\mainController{
    private MenuBusiness $Menu;
    public function __construct () {
        $this->Menu = new MenuBusiness();
    }

		function buscarModulo($parametros){
			$modulo=ucfirst(strtolower($parametros['modulo']));
			$modelo= new menuModel($modulo);
			$this->response($modelo);
		}

		function buscarModuloId($parametros){
			$modelo= new menuModel();
			$this->Menu->getDatosId($parametros['id']);
			$this->response($modelo);
		}

		function grabarModulo($parametros){
			$frm=$parametros['frm'];
			$modulo=ucfirst(strtolower($frm['nombre-modulo']));
			$modelo= new menuModel($modulo);
			$modelo->id_sub=intval($frm['menu-modulo']);
			$modelo->icono=$frm['icono-modulo'];
			$modelo->orden=intval($frm['orden-modulo']);
			$modelo->grabarModulo();
			$this->response(array("mensaje"=>"exitoso"));
		}

		function traerMenus($param){
			$this->defaultMethod(
                $this->Menu->traerMenus()
            );
		}
		
		function traerSubmenu(){
			$modelo= new menuModel();
			$data=$modelo->getSubmemus();
			$this->response($data);
		}

	}
