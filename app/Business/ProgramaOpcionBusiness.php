<?php
namespace App\Business;
use Core\mainBusines;
use App\Models\ProgramaOpcionModel;
class ProgramaOpcionBusiness extends \Core\mainBusiness{
    public ProgramaOpcionModel $ProgramaOpcion;
    public $token;

    public function __construct () {
        $this->ProgramaOpcion = new ProgramaOpcionModel();
    }

    public function traerOpciones($param):array
    {
        return $this->ProgramaOpcion->readManyByColumn((object)['id_programa'=>$param->id_programa],'opcion');
    }

    public function grabarOpcionPrograma(object $param):int
    {
        return $this->ProgramaOpcion->createOne([
            $param->{'id-programa'},
            $param->opcion,
            $param->descripcion,
        ]);
    }

    public function borrarOpcionPrograma(object $param):int
    {
        return $this->ProgramaOpcion->deleteOneById($param->id);
    }

}