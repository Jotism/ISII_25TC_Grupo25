<?php


namespace App\Controllers;

use App\Models\MateriaModel;

class Materias extends BaseController
{
    public function obtenerMateriasDisponibles()
    {
        // Verificar sesión activa
        if (!session()->get('logueado')) {
            return redirect()->to('/login');
        }

        $idEstudiante = session()->get('id_usuario');


        $modelo   = new MateriaModel();
        $materias = $modelo->obtenerMateriasDisponibles($idEstudiante);

        return view('materias', ['materias' => $materias]);
    }
}