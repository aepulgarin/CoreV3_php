<?php

namespace Core\Interfaces;

interface InterfaceModel
{
    public function readAll(string $state):array;
    public function updateOne(object $data, int $id):int;
    public function deleteOne(int $id):int;

}