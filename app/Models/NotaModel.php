<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\EventoAcademico;
use App\Observers\NotificadorAlumno;

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
    // obtenerNotaPorInscripcion(id_inscripcion)
    // ----------------------------------------------------------
    public function obtenerNotaPorInscripcion(int $id_inscripcion): ?array
    {
        return $this->db->table($this->table)
            ->where('id_inscripcion', $id_inscripcion)
            ->get()
            ->getRowArray();
    }

    // ----------------------------------------------------------
    // registrarNotaDirecta(id_inscripcion, nota)
    // Inserta o actualiza la nota de una inscripción.
    // Solo interactúa con la tabla nota_cursada.
    // ----------------------------------------------------------
    public function registrarNotaDirecta(int $id_inscripcion, int $nota): bool
    {
        // Calcular estado según la nota
        $estado = ($nota >= 6) ? 'Aprobado' : 'Desaprobado';

        // Verificar si ya existe una nota registrada para esta inscripción
        $existeNota = $this->db->table($this->table)
            ->where('id_inscripcion', $id_inscripcion)
            ->countAllResults();

        if ($existeNota > 0) {
            // Actualizar la nota existente
            $this->db->table($this->table)
                ->where('id_inscripcion', $id_inscripcion)
                ->update([
                    'nota'       => $nota,
                    'estado'     => $estado,
                    'fecha_nota' => date('Y-m-d'),
                ]);
        } else {
            // Insertar una nueva nota
            $this->db->table($this->table)->insert([
                'id_inscripcion' => $id_inscripcion,
                'nota'           => $nota,
                'estado'         => $estado,
                'fecha_nota'     => date('Y-m-d'),
            ]);
        }
        
        $this->notificarObservador($id_inscripcion, $nota);
        return true;
    }

    public function eliminarNotaPorInscripcion(int $id_inscripcion): bool
    {
        return $this->db->table('nota_cursada')
            ->where('id_inscripcion', $id_inscripcion)
            ->delete();
    }

    private function notificarObservador(int $id_inscripcion, int $nota): void
    {
        $evento = new EventoAcademico();

        $evento->attach(new NotificadorAlumno());

        $evento->disparar(
            'NOTA_REGISTRADA',
            [
                'id_inscripcion' => $id_inscripcion,
                'nota' => $nota
            ]
        );
    }
}