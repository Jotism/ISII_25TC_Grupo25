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

    // VALIDARCREDENCIALES

    public function validarCredenciales(string $email, string $contrasena): array
    {
        $usuario = $this->db->table('usuarios')
            ->where('email', $email)
            ->where('pass', $contrasena)
            ->get()
            ->getRowArray();

        return $usuario ?: false;
    }

    // INSERTARUSUARIO

    public function insertarUsuario(array $datos): bool
    {
        return $this->db->table('usuarios')->insert($datos);
    }

    // OBTENEREMAILUSUARIO
    public function obtenerEmailUsuario(int $id_usuario): string
    {
        $resultado = $this->db->table('usuarios')
            ->select('email')
            ->where('id_usuario', $id_usuario)
            ->get()
            ->getRowArray();

        return $resultado['email'] ?? 'usuario no encontrado';
    }

    public function obtenerUsuarioPorId(int $id_usuario): array
    {
        return $this->db->table('usuarios')
            ->select('*')
            ->where('id_usuario', $id_usuario)
            ->get()
            ->getRowArray();
    }
}