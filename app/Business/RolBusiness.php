<?php
namespace App\Business;
use App\Models\RolModel;

class RolBusiness extends \Core\mainBusiness
{
    public RolModel $Rol;
    public function __construct()
    {
        $this->Rol = new RolModel();
    }
    public  function traerLista($param):array{
        return $this->Rol->readManyByColumn(
            (object)[
                'estado'=>$param->estado??'A'
            ],'nombre'
        );
    }

    public function traerRoles($param):array
    {
        return $this->Rol->readManyByColumn(
            (object)[
                'estado'=>$param->estado??'A'
            ],'nombre'
        );
    }
}