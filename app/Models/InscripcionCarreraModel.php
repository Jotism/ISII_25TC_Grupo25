<?php

namespace App\Models;

use CodeIgniter\Model;

class InscripcionCarreraModel extends Model
{
    protected $table      = 'inscripcion_carrera';
    protected $primaryKey = 'id_usuario';
    protected $returnType = 'array';


    // consultarCarrerasInscripto($id_usuario)

    public function consultarCarrerasInscripto(int $id_usuario): array
    {
        return $this->db->table('inscripcion_carrera ic')
            ->join('carrera c', 'c.id_carrera = ic.id_carrera', 'inner')
            ->select('c.id_carrera, c.nombre')
            ->where('ic.id_usuario', $id_usuario)
            ->orderBy('c.nombre', 'ASC')
            ->get()
            ->getResultArray();
    }


    // generarInscripcion($id_usuario, $id_carrera)

    public function generarInscripcion(int $id_usuario, int $id_carrera): bool
    {
        $existe = $this->db->table('inscripcion_carrera')
            ->where('id_usuario', $id_usuario)
            ->where('id_carrera', $id_carrera)
            ->countAllResults();

        if ($existe > 0) return false;

        $this->db->table('inscripcion_carrera')->insert([
            'id_usuario' => $id_usuario,
            'id_carrera' => $id_carrera,
        ]);

        return true;
    }


    // darseDeBaja($id_usuario, $id_carrera)
    // Elimina la inscripción a la carrera.
    // También elimina inscripciones a materias de esa carrera.

    public function darseDeBaja(int $id_usuario, int $id_carrera): void
    {
        // Borrar inscripciones a materias de esa carrera
        $materiasDeCarrera = $this->db->table('materia_carrera')
            ->select('id_materia')
            ->where('id_carrera', $id_carrera)
            ->get()
            ->getResultArray();

        foreach ($materiasDeCarrera as $m) {
            // Obtener el id_inscripcion de la materia
            $inscripcion = $this->db->table('inscripcion_materia')
                ->select('id_inscripcion')
                ->where('id_usuario', $id_usuario)
                ->where('id_materia', $m['id_materia'])
                ->get()
                ->getRowArray();

            if ($inscripcion) {
                // Borrar la nota primero para no violar la FK
                $this->db->table('nota_cursada')
                    ->where('id_inscripcion', $inscripcion['id_inscripcion'])
                    ->delete();
            }

            // Borrar inscripción a la materia
            $this->db->table('inscripcion_materia')
                ->where('id_usuario', $id_usuario)
                ->where('id_materia', $m['id_materia'])
                ->delete();
        }

        // Borrar inscripción a la carrera
        $this->db->table('inscripcion_carrera')
            ->where('id_usuario', $id_usuario)
            ->where('id_carrera', $id_carrera)
            ->delete();
    }
}