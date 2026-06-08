<?php

namespace App\Models;

use CodeIgniter\Model;

class InscripcionMateriaModel extends Model
{ 
    protected $table      = 'inscripcion_materia';
    protected $primaryKey = 'id_inscripcion';
    protected $returnType = 'array';

    protected $allowedFields = ['fecha_inscripcion', 'id_usuario', 'id_materia'];

    // ----------------------------------------------------------
    // obtenerMateriasInscripto($id_usuario)
    // Materias en las que YA está inscripto el alumno.
    // ----------------------------------------------------------
    public function obtenerMateriasInscripto(int $id_usuario): array
    {
        return $this->db->table('inscripcion_materia im')
            ->join('materias m',      'm.id_materia = im.id_materia',           'inner')
            ->join('nota_cursada nc', 'nc.id_inscripcion = im.id_inscripcion',  'left')
            ->select('m.id_materia, m.nombre, m.id_cuatrimestre, nc.nota, nc.estado, nc.fecha_nota')
            ->where('im.id_usuario', $id_usuario)
            ->orderBy('m.id_cuatrimestre', 'ASC')
            ->get()
            ->getResultArray();
    }

    // ----------------------------------------------------------
    // generarInscripcion($id_usuario, $id_materia)
    // ----------------------------------------------------------
    public function generarInscripcion(int $id_usuario, int $id_materia): bool
    {
        $existe = $this->db->table('inscripcion_materia')
            ->where('id_usuario', $id_usuario)
            ->where('id_materia', $id_materia)
            ->countAllResults();

        if ($existe > 0) return false;

        $this->db->table('inscripcion_materia')->insert([
            'id_usuario' => $id_usuario,
            'id_materia' => $id_materia,
        ]);

        return true;
    }

    // ----------------------------------------------------------
    // eliminarInscripcionMateria($id_usuario, $id_materia)
    // ----------------------------------------------------------
    public function eliminarInscripcionMateria(int $id_usuario, int $id_materia): void
    {
        // Obtener el id_inscripcion de la materia
        $inscripcion = $this->db->table('inscripcion_materia')
            ->select('id_inscripcion')
            ->where('id_usuario', $id_usuario)
            ->where('id_materia', $id_materia)
            ->get()
            ->getRowArray();

        if ($inscripcion) {
            // Borrar la nota primero para no violar la FK
            $this->db->table('nota_cursada')
                ->where('id_inscripcion', $inscripcion['id_inscripcion'])
                ->delete();
        }

        // Borrar la inscripción a la materia
        $this->db->table('inscripcion_materia')
            ->where('id_usuario', $id_usuario)
            ->where('id_materia', $id_materia)
            ->delete();
    }
}