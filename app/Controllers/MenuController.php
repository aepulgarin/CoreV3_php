<?php
use App\Business\MenuBusiness;
$plantilla="layout.html";
class MenuController extends \Core\mainController{
    private MenuBusiness $Menu;
    public function __construct () {
        $this->Menu = new MenuBusiness();
    }
    function traerMenus($param){
        $this->defaultMethod(
            $this->Menu->traerMenus()
        );
    }

    public function grabarMenu($params){
        $this->defaultMethod(
            $this->Menu->grabarMenu((object) $params)
        );
    }
    public function borrarMenu($params){
        $this->defaultMethod(
            $this->Menu->borrarMenu((object) $params)
        );
    }

	}
