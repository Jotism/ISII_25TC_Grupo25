<?php

namespace App\Models;

use CodeIgniter\Model;

class MateriaCarreraModel extends Model
{
    protected $table      = 'materia_carrera';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_materia',
        'id_carrera',
    ];

    // Obtener todas las relaciones
    public function obtenerRelaciones(): array
    {
        return $this->db->table($this->table)
            ->get()
            ->getResultArray();
    }

    // Obtener carreras asociadas a una materia
    public function obtenerCarrerasPorMateria(int $id_materia): array
    {
        return $this->db->table($this->table)
            ->where('id_materia', $id_materia)
            ->get()
            ->getResultArray();
    }

    // Obtener materias asociadas a una carrera
    public function obtenerMateriasPorCarrera(int $id_carrera): array
    {
        return $this->db->table($this->table)
            ->where('id_carrera', $id_carrera)
            ->get()
            ->getResultArray();
    }

    // Insertar relación
    public function asociarMateriaCarrera(int $id_materia, int $id_carrera): bool
    {
        return $this->db->table($this->table)->insert([
            'id_materia' => $id_materia,
            'id_carrera' => $id_carrera,
        ]);
    }

    // Eliminar relación por materia
    public function eliminarPorMateria(int $id_materia): void
    {
        $this->db->table($this->table)
            ->where('id_materia', $id_materia)
            ->delete();
    }

    // Eliminar relación específica
    public function eliminarRelacion(int $id_materia, int $id_carrera): void
    {
        $this->db->table($this->table)
            ->where('id_materia', $id_materia)
            ->where('id_carrera', $id_carrera)
            ->delete();
    }
}
