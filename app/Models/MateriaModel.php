<?php

namespace App\Models;

use CodeIgniter\Model;

class MateriaModel extends Model
{
    protected $table      = 'materias';
    protected $primaryKey = 'id_materia';
    protected $returnType = 'array';

    protected $allowedFields = ['nombre', 'anio_cursada', 'id_cuatrimestre'];

    // ----------------------------------------------------------
    // obtenerNombreMateria($id_materia)
    // ----------------------------------------------------------
    public function obtenerNombreMateria(int $id_materia): string
    {
        $resultado = $this->db->table('materias')
            ->select('nombre')
            ->where('id_materia', $id_materia)
            ->get()
            ->getRowArray();

        return $resultado['nombre'] ?? 'Materia desconocida';
    }

    // ----------------------------------------------------------
    // obtenerMateriasPorDocente($id_docente)
    // Trae las materias de las que el docente es titular.
    // Corresponde al diagrama: obtenerMateriasPorDocente(id_docente)
    // ----------------------------------------------------------
    public function obtenerMateriasPorDocente(int $id_usuario): array
    {
        return $this->db->table("materias")
            ->select("id_materia, nombre, anio_cursada, id_cuatrimestre")
            ->where("id_usuario", $id_usuario)
            ->orderBy("anio_cursada", "ASC")
            ->get()
            ->getResultArray();
    }

    public function obtenerMateriaPorId(int $id_materia): array
    {
        return $this->db->table('materias')
            ->select('*')
            ->where('id_materia', $id_materia)
            ->get()
            ->getRowArray();
    }

    public function obtenerMaterias(): array
    {
        return $this->db->table('materias')
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function registrarMateria(string $nombre, int $anio_cursada, int $id_cuatrimestre): ?int
    {
        $resultado = $this->db->table('materias')->insert([
            'nombre'          => $nombre,
            'anio_cursada'    => $anio_cursada,
            'id_cuatrimestre' => $id_cuatrimestre,
        ]);

        if ($resultado) {
            return $this->db->insertID();
        }

        return null;
    }

    public function actualizarMateria(int $id_materia, string $nombre, int $anio_cursada, int $id_cuatrimestre): bool
    {
        return $this->db->table('materias')
            ->where('id_materia', $id_materia)
            ->update([
                'nombre'          => $nombre,
                'anio_cursada'    => $anio_cursada,
                'id_cuatrimestre' => $id_cuatrimestre,
            ]);
    }

    public function eliminarMateriaPorId(int $id_materia): bool
    {
        return $this->db->table('materias')
            ->where('id_materia', $id_materia)
            ->delete();
    }

}