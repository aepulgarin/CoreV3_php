<?php
namespace App\Business;
use App\Models\MenuModel;
class MenuBusiness extends \Core\mainBusiness
{
    public MenuModel $Menu;
    public function __construct () {
        $this->Menu = new MenuModel();
    }
    public function traerMenus():array
    {
        return $this->Menu->readManyByColumn(
            (object)['1'=>1],
            'orden');
    }
    public function grabarMenu(object $param):int
    {
        return $this->Menu->createOne([
            $param->id_sub,
            '',
            $param->des_mod,
            $param->orden,
        ]);
    }

    public function borrarMenu(object $param):int
    {
        return $this->Menu->deleteOneById($param->id);
    }
}