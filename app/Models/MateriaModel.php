<?php

namespace App\Models;

use CodeIgniter\Model;

class MateriaModel extends Model
{
    protected $table      = 'materias';
    protected $primaryKey = 'id_materia';
    protected $returnType = 'array';

    protected $allowedFields = ['nombre', 'anio_cursada', 'id_cuatrimestre'];


    // consultarMateriasDisponibles($id_usuario, $id_carrera)
    // Materias de la carrera que el alumno AÚN NO tiene.

    public function consultarMateriasDisponibles(int $id_usuario, int $id_carrera): array
    {
        $subquery = $this->db->table('inscripcion_materia')
            ->select('id_materia')
            ->where('id_usuario', $id_usuario)
            ->getCompiledSelect();

        return $this->db->table('materia_carrera mc')
            ->join('materias m', 'm.id_materia = mc.id_materia', 'inner')
            ->select('m.id_materia, m.nombre, m.id_cuatrimestre')
            ->where('mc.id_carrera', $id_carrera)
            ->where("m.id_materia NOT IN ($subquery)")
            ->orderBy('m.id_cuatrimestre', 'ASC')
            ->orderBy('m.nombre', 'ASC')
            ->get()
            ->getResultArray();
    }


    // obtenerMateriasInscripto($id_usuario)
    // Materias en las que YA está inscripto el alumno.

    public function obtenerMateriasInscripto(int $id_usuario): array
    {
        return $this->db->table('inscripcion_materia im')
            ->join('materias m', 'm.id_materia = im.id_materia', 'inner')
            ->select('m.id_materia, m.nombre, m.id_cuatrimestre')
            ->where('im.id_usuario', $id_usuario)
            ->orderBy('m.id_cuatrimestre', 'ASC')
            ->get()
            ->getResultArray();
    }


    // generarInscripcion($id_usuario, $id_materia)

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


    // eliminarInscripcionMateria($id_usuario, $id_materia)

    public function eliminarInscripcionMateria(int $id_usuario, int $id_materia): void
    {
        $this->db->table('inscripcion_materia')
            ->where('id_usuario', $id_usuario)
            ->where('id_materia', $id_materia)
            ->delete();
    }


    // obtenerNombreMateria($id_materia)

    public function obtenerNombreMateria(int $id_materia): string
    {
        $resultado = $this->db->table('materias')
            ->select('nombre')
            ->where('id_materia', $id_materia)
            ->get()
            ->getRowArray();

        return $resultado['nombre'] ?? 'Materia desconocida';
    }

    // Mantenido por compatibilidad con el admin
    public function obtenerMateriasDisponibles(int $idEstudiante): array
    {
        return $this->db->table('inscripcion_carrera ic')
            ->join('materia_carrera mc', 'mc.id_carrera = ic.id_carrera', 'inner')
            ->join('materias m', 'm.id_materia = mc.id_materia', 'inner')
            ->where('ic.id_usuario', $idEstudiante)
            ->select('m.id_materia, m.nombre, m.anio_cursada, m.id_cuatrimestre')
            ->orderBy('m.anio_cursada', 'ASC')
            ->get()
            ->getResultArray();
    }
}