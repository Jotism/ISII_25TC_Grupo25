<?php

namespace App\Controllers;

use App\Models\MateriaModel;

class Materias extends BaseController
{

    // consultarMateriasDisponibles($id_usuario, $id_carrera)
    // Ruta: GET /materias/{id_usuario}/{id_carrera}
    // Recibe ambos ids por parámetro de URL.
    // Delega en el modelo el filtrado por carrera y exclusión
    // de materias ya inscriptas.

    public function consultarMateriasDisponibles(int $id_usuario, int $id_carrera)
    {
        if (!session()->get('logueado')) {
            return redirect()->to('/login');
        }

        $modelo   = new MateriaModel();
        $materias = $modelo->consultarMateriasDisponibles($id_usuario, $id_carrera);

        return view('inscripciones/materias', [
            'materias'   => $materias,
            'id_usuario' => $id_usuario,
            'id_carrera' => $id_carrera,
        ]);
    }
}