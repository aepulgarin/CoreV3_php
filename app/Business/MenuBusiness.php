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
            'des_mod');
    }
}