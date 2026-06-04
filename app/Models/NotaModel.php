<?php

namespace App\Models;

use CodeIgniter\Model;

class NotaModel extends Model
{
    protected $table      = 'nota_cursada';
    protected $primaryKey = 'id_nota';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_inscripcion',
        'nota',
        'estado',
        'fecha_nota',
    ];

    // ----------------------------------------------------------
    // registrarNota(id_docente, id_materia, id_alumno, nota)

    // 1. Verifica que el docente sea titular de la materia
    // 2. Obtiene el id_inscripcion del alumno en esa materia
    // 3. Calcula el estado según la nota (>= 6 Aprobado)
    // 4. Inserta en nota_cursada
    // ----------------------------------------------------------
    public function registrarNota(int $id_docente, int $id_materia, int $id_alumno, int $nota): bool
    {
        // Validar que el docente sea titular de la materia
        $esTitular = $this->db->table('materias')
            ->where('id_materia', $id_materia)
            ->where('id_usuario', $id_docente)
            ->countAllResults();

        if (!$esTitular) return false;

        // Obtener el id_inscripcion del alumno en esa materia
        $inscripcion = $this->db->table('inscripcion_materia')
            ->select('id_inscripcion')
            ->where('id_usuario', $id_alumno)
            ->where('id_materia', $id_materia)
            ->get()
            ->getRowArray();

        if (!$inscripcion) return false;

        // Calcular estado según la nota
        $estado = ($nota >= 6) ? 'Aprobado' : 'Desaprobado';

        // Verificar si ya existe una nota registrada para esta inscripción
        $existeNota = $this->db->table('nota_cursada')
            ->where('id_inscripcion', $inscripcion['id_inscripcion'])
            ->countAllResults();

        if ($existeNota > 0) {
            // Actualizar la nota existente
            $this->db->table('nota_cursada')
                ->where('id_inscripcion', $inscripcion['id_inscripcion'])
                ->update([
                    'nota'       => $nota,
                    'estado'     => $estado,
                    'fecha_nota' => date('Y-m-d'),
                ]);
        } else {
            // Insertar una nueva nota
            $this->db->table('nota_cursada')->insert([
                'id_inscripcion' => $inscripcion['id_inscripcion'],
                'nota'           => $nota,
                'estado'         => $estado,
                'fecha_nota'     => date('Y-m-d'),
            ]);
        }

        return true;
    }

    // ----------------------------------------------------------
    // obtenerAlumnosInscriptos(id_materia)
    //
    // Trae la lista de alumnos inscriptos a la materia
    // con su nota y estado si ya la tienen cargada.
    // Si un alumno no tiene nota todavía, igual aparece en la lista.
    // ----------------------------------------------------------
    public function obtenerAlumnosInscriptos(int $id_materia): array
    {
        return $this->db->table('inscripcion_materia im')
            ->join('usuarios u',      'u.id_usuario = im.id_usuario', 'inner')
            ->join('nota_cursada nc', 'nc.id_inscripcion = im.id_inscripcion', 'left')
            ->select('u.id_usuario, u.nombre, u.apellido, u.dni, nc.nota, nc.estado, nc.fecha_nota')
            ->where('im.id_materia', $id_materia)
            ->orderBy('u.apellido', 'ASC')
            ->get()
            ->getResultArray();
    }
}