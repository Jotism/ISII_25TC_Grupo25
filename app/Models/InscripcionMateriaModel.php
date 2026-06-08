<?php

namespace App\Models;

use CodeIgniter\Model;

class InscripcionMateriaModel extends Model
{
     public function consultarMateriasInscripto(int $id_usuario, int $id_carrera): string
    {
        return $this->db->table('inscripcion_materia')
            ->select('id_materia')
            ->where('id_usuario', $id_usuario)
            ->getCompiledSelect();  
    }
}