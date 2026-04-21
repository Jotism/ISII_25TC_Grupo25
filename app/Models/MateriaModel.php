<?php

namespace App\Models;

use CodeIgniter\Model;

class MateriaModel extends Model
{
    protected $table      = 'materias';
    protected $primaryKey = 'id_materia';
    protected $returnType = 'array';

    protected $allowedFields = [
        'nombre',
        'anio_cursada',
        'id_cuatrimestre',
    ];

    public function obtenerMateriasDisponibles(int $idEstudiante): array
    {
        return $this->db->table('inscripcion_carrera ic')
            ->join('materia_carrera mc', 'mc.id_carrera = ic.id_carrera', 'inner')
            ->join('materias m',         'm.id_materia  = mc.id_materia',  'inner')
            ->where('ic.id_usuario', $idEstudiante)
            ->select('m.id_materia, m.nombre, m.anio_cursada, m.id_cuatrimestre')
            ->orderBy('m.anio_cursada',    'ASC')
            ->orderBy('m.id_cuatrimestre', 'ASC')
            ->get()
            ->getResultArray();
    }
}