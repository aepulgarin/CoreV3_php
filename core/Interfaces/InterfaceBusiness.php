<?php

namespace Core\Interfaces;

interface InterfaceBusiness
{
    public function Crear(object $data);
    public function LeerUno(object $data):object;
    public function LeerTodos(object $data):array;
    public function ActualizarUno(object $data);
    public function BorrarUno(object $data);

}