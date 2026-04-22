<?php

/**
 * MODELO: CarreraModel.php
 * ============================================================
 * UbicaciÃ³n: app/Models/CarreraModel.php
 *
 * Responsabilidad: consultas a la tabla "carrera".
 * Solo necesitamos traer todas las carreras para el dropdown.
 * ============================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class CarreraModel extends Model
{
    protected $table      = 'carrera';
    protected $primaryKey = 'id_carrera';
    protected $returnType = 'array';

    // ----------------------------------------------------------
    // getCarreras()
    // Retorna todas las carreras ordenadas por nombre.
    // Se usa para poblar el <select> en los formularios.
    // ----------------------------------------------------------
    public function getCarreras(): array
    {
        return $this->db->table('carrera')
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResultArray();
    }
}