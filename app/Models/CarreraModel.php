<?php

namespace App\Models;

use CodeIgniter\Model;

class CarreraModel extends Model
{
    protected $table      = 'carrera';
    protected $primaryKey = 'id_carrera';
    protected $returnType = 'array';

    // Retorna todas las carreras ordenadas por nombre.

    public function getCarreras(): array
    {
        return $this->db->table('carrera')
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResultArray();
    }
}