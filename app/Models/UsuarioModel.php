<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    // Tabla asociada a este modelo
    protected $table = 'usuarios';

    // Clave primaria de la tabla
    protected $primaryKey = 'id_usuario';

    // Tipo de retorno de las consultas: array asociativo
    protected $returnType = 'array';

    // Campos que se pueden insertar/actualizar (protección contra mass assignment)
    protected $allowedFields = [
        'nombre',
        'apellido',
        'dni',
        'email',
        'pass',
        'id_perfil',
    ];

    // -------------------------------------------------------
    // VALIDARCREDENCIALES
    // Busca en la tabla usuarios con el email y contraseña dados.
    // Retorna el registro del usuario si existe, o false si no.
    // -------------------------------------------------------
    public function validarCredenciales(string $email, string $contrasena)
    {
        $usuario = $this->db->table('usuarios')
            ->where('email', $email)
            ->where('pass', $contrasena)
            ->get()
            ->getRowArray(); // Retorna un array o null

        return $usuario ?: false; // Si es null → false
    }

    // -------------------------------------------------------
    // INSERTARUSUARIO
    // Inserta un nuevo usuario en la tabla usuarios.
    // Recibe un array con: nombre, apellido, dni, email, pass, id_perfil
    // -------------------------------------------------------
    public function insertarUsuario(array $datos): bool
    {
        return $this->db->table('usuarios')->insert($datos);
    }
}