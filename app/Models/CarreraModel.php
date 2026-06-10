<?php

namespace App\Models;

use CodeIgniter\Model;

class CarreraModel extends Model
{
    protected $table      = 'carrera';
    protected $primaryKey = 'id_carrera';
    protected $returnType = 'array';

    // Retorna todas las carreras ordenadas por nombre.
    public function obtenerCarreras(): array
    {
        return $this->db->table('carrera')
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function obtenerNombreCarrera(int $id_carrera): string
    {
        $resultado = $this->db->table('carrera')
            ->select('nombre')
            ->where('id_carrera', $id_carrera)
            ->get()
            ->getRowArray();

        return $resultado['nombre'] ?? 'Carrera desconocida';
    }

    public function obtenerCarreraPorId(int $id_carrera): array
    {
        return $this->db->table('carrera')
            ->select('*')
            ->where('id_carrera', $id_carrera)
            ->get()
            ->getRowArray();
    }
}