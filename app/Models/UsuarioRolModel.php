<?php

namespace App\Models;

use Core\mainModel;

class UsuarioRolModel extends mainModel
{
    public string $table='core_usuarios';
    public function readAllByUsuario(int $id_usuario): array
    {
        return $this->lee_prepare(
            "SELECT a.nombre, a.descripcion 
            FROM core_roles a, core_usuarios_roles b, core_usuarios c 
            where a.id=b.id_rol and b.id_usuario=c.id and c.usuario=? and a.estado=?",[
            $id_usuario,
            'A'
        ]);
    }
    public function readAllByRol(int $id_rol): array
    {
        return $this->lee_prepare("SELECT u.id as id_usuario 
                        FROM core_usuarios_roles ur, core_usuarios u 
                        where u.id=ur.id_usuario and ur.id_rol=? and u.estado=?",[
            $id_rol,
            'A'
        ]);
    }
}