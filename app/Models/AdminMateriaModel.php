<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminMateriaModel extends Model
{
    protected $table      = 'materias';
    protected $primaryKey = 'id_materia';
    protected $returnType = 'array';

    protected $allowedFields = [
        'nombre',
        'anio_cursada',
        'id_cuatrimestre',
    ];

    // ----------------------------------------------------------
    // getMaterias()
    // Retorna todas las materias con el nombre de su carrera.
    public function getMaterias(): array
    {
        return $this->db->table('materias m')
            ->join('materia_carrera mc', 'mc.id_materia = m.id_materia', 'left')
            ->join('carrera c',          'c.id_carrera  = mc.id_carrera',  'left')
            ->select('m.id_materia, m.nombre, m.anio_cursada, m.id_cuatrimestre, c.nombre AS nombre_carrera')
            ->orderBy('m.anio_cursada', 'ASC')
            ->orderBy('m.id_cuatrimestre', 'ASC')
            ->get()
            ->getResultArray();
    }

    // ----------------------------------------------------------
    // getMateriaConCarrera($id)
    // Retorna UNA materia con su id_carrera actual.
    // Se usa para pre-cargar el formulario de edición.
    // ----------------------------------------------------------
    public function getMateriaConCarrera(int $id): array
    {
        $resultado = $this->db->table('materias m')
            ->join('materia_carrera mc', 'mc.id_materia = m.id_materia', 'left')
            ->select('m.id_materia, m.nombre, m.anio_cursada, m.id_cuatrimestre, mc.id_carrera')
            ->where('m.id_materia', $id)
            ->get()
            ->getRowArray();

        return $resultado ?? [];
    }

    // ----------------------------------------------------------
    // insertarMateria($datos)
    // Inserta una fila en la tabla "materias".
    // Retorna el id_materia generado (necesario para materia_carrera).
    // ----------------------------------------------------------
    public function insertarMateria(array $datos): int
    {
        $this->db->table('materias')->insert($datos);
        return $this->db->insertID(); // id generado por PostgreSQL
    }

    // ----------------------------------------------------------
    // insertarMateriaCarrera($id_materia, $id_carrera)
    // Inserta la relación en la tabla "materia_carrera".
    // ----------------------------------------------------------
    public function insertarMateriaCarrera(int $id_materia, int $id_carrera): void
    {
        $this->db->table('materia_carrera')->insert([
            'id_materia' => $id_materia,
            'id_carrera' => $id_carrera,
        ]);
    }

    // ----------------------------------------------------------
    // actualizarMateria($id, $datos)
    // Actualiza nombre, anio_cursada e id_cuatrimestre.
    // ----------------------------------------------------------
    public function actualizarMateria(int $id, array $datos): void
    {
        $this->db->table('materias')
            ->where('id_materia', $id)
            ->update($datos);
    }

    // ----------------------------------------------------------
    // actualizarMateriaCarrera($id_materia, $id_carrera)
    // ----------------------------------------------------------
    public function actualizarMateriaCarrera(int $id_materia, int $id_carrera): void
    {
        // Borrar relación anterior
        $this->db->table('materia_carrera')
            ->where('id_materia', $id_materia)
            ->delete();

        // Insertar nueva relación
        $this->db->table('materia_carrera')->insert([
            'id_materia' => $id_materia,
            'id_carrera' => $id_carrera,
        ]);
    }

    // ----------------------------------------------------------
    // eliminarMateriaCarrera($id_materia)
    // Borra la relación en materia_carrera.
    // Se llama ANTES de eliminarMateria() para no violar FK.
    // ----------------------------------------------------------
    public function eliminarMateriaCarrera(int $id_materia): void
    {
        $this->db->table('materia_carrera')
            ->where('id_materia', $id_materia)
            ->delete();
    }

    // ----------------------------------------------------------
    // eliminarMateria($id)
    // Borra la materia de la tabla principal.
    // ----------------------------------------------------------
    public function eliminarMateria(int $id): void
    {
        $this->db->table('materias')
            ->where('id_materia', $id)
            ->delete();
    }
}