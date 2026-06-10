<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\EventoAcademico;
use App\Observers\NotificadorAlumno;

class InscripcionMateriaModel extends Model
{ 
    protected $table      = 'inscripcion_materia';
    protected $primaryKey = 'id_inscripcion';
    protected $returnType = 'array';

    protected $allowedFields = ['fecha_inscripcion', 'id_usuario', 'id_materia'];

    // ----------------------------------------------------------
    // obtenerInscripcionesPorUsuario($id_usuario)
    // Devuelve los registros de inscripción a materias del alumno.
    // Solo consulta la tabla inscripcion_materia.
    // ----------------------------------------------------------
    public function obtenerInscripcionesPorUsuario(int $id_usuario): array
    {
        return $this->db->table($this->table)
            ->where('id_usuario', $id_usuario)
            ->get()
            ->getResultArray();
    }

    // ----------------------------------------------------------
    // obtenerInscripcionesPorMateria($id_materia)
    // Devuelve las inscripciones de todos los alumnos en una materia.
    // Solo consulta la tabla inscripcion_materia.
    // ----------------------------------------------------------
    public function obtenerInscripcionesPorMateria(int $id_materia): array
    {
        return $this->db->table($this->table)
            ->where('id_materia', $id_materia)
            ->get()
            ->getResultArray();
    }

    // ----------------------------------------------------------
    // generarInscripcion($id_usuario, $id_materia)
    // ----------------------------------------------------------
    public function generarInscripcion(int $id_usuario, int $id_materia): bool
    {
        $existe = $this->db->table($this->table)
            ->where('id_usuario', $id_usuario)
            ->where('id_materia', $id_materia)
            ->countAllResults();

        if ($existe > 0) return false;

        $sql = "CALL sp_insertar_inscripcion_materia(?, ?)";

        $this->db->query($sql, [
            $id_usuario,
            $id_materia
        ]);

        $this->notificarObservador($id_usuario, $id_materia);

        return true;
    }

    // ----------------------------------------------------------
    // eliminarInscripcionMateriaDirecta($id_usuario, $id_materia)
    // Borra la inscripción a la materia de la tabla inscripcion_materia.
    // ----------------------------------------------------------
    public function eliminarInscripcionMateriaDirecta(int $id_usuario, int $id_materia): void
    {
        $this->db->table($this->table)
            ->where('id_usuario', $id_usuario)
            ->where('id_materia', $id_materia)
            ->delete();
    }

    // Método auxiliar para buscar inscripción por materia y alumno
    public function buscarInscripcion(int $id_usuario, int $id_materia): ?array
    {
        $query = $this->db->query(
            "SELECT * FROM buscar_inscripcion(?, ?)",
            [$id_usuario, $id_materia]
        );

        return $query->getRowArray();
    }

    public function buscarInscripcionPorID(int $id_inscripcion): ?array
    {
        return $this->db->table($this->table)
            ->where('id_inscripcion', $id_inscripcion)
            ->get()
            ->getRowArray();
    }

    private function notificarObservador(int $id_usuario, int $id_materia): void
    {
        $evento = new EventoAcademico();

        $evento->attach(new NotificadorAlumno());

        $evento->disparar(
            'INSCRIPCION_MATERIA',
            [
                'id_usuario' => $id_usuario,
                'id_materia' => $id_materia
            ]
        );
    }
}