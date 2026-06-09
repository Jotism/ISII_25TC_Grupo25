<?php

namespace App\Models;

use CodeIgniter\Model;

class InscripcionCarreraModel extends Model
{
    protected $table      = 'inscripcion_carrera';
    protected $primaryKey = 'id_usuario';
    protected $returnType = 'array';


    // consultarCarrerasInscripto($id_usuario)
    // Devuelve los registros de inscripción a carrera del usuario.
    // Solo consulta la tabla inscripcion_carrera.
    public function consultarCarrerasInscripto(int $id_usuario): array
    {
        return $this->db->table($this->table)
            ->where('id_usuario', $id_usuario)
            ->get()
            ->getResultArray();
    }


    // generarInscripcion($id_usuario, $id_carrera)
    public function generarInscripcion(int $id_usuario, int $id_carrera): bool
    {
        $existe = $this->db->table($this->table)
            ->where('id_usuario', $id_usuario)
            ->where('id_carrera', $id_carrera)
            ->countAllResults();

        if ($existe > 0) return false;

        $this->db->table($this->table)->insert([
            'id_usuario' => $id_usuario,
            'id_carrera' => $id_carrera,
        ]);

        return true;
    }


    // darseDeBajaDirecta($id_usuario, $id_carrera)
    // Elimina la inscripción a la carrera.
    // Solo interactúa con la tabla inscripcion_carrera.
    public function darseDeBajaDirecta(int $id_usuario, int $id_carrera): void
    {
        $this->db->table($this->table)
            ->where('id_usuario', $id_usuario)
            ->where('id_carrera', $id_carrera)
            ->delete();
    }
}