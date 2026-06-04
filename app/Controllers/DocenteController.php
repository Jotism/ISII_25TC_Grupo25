<?php

namespace App\Controllers;

use App\Models\NotaModel;
use App\Models\MateriaModel;

class DocenteController extends BaseController
{
    // ----------------------------------------------------------
    // PRIVADO: verificarDocente()
    // Solo permite acceso a usuarios con id_perfil = 3
    // ----------------------------------------------------------
    private function verificarDocente()
    {
        if (!session()->get('logueado')) {
            return redirect()->to('/login');
        }

        if (session()->get('id_perfil') != 3) {
            return redirect()->to('/dashboard');
        }

        return null;
    }

    // ----------------------------------------------------------
    // misMaterias()
    // Trae las materias donde id_usuario = id del docente logueado.
    // ----------------------------------------------------------
    public function misMaterias()
    {
        $redireccion = $this->verificarDocente();
        if ($redireccion) return $redireccion;

        $id_docente = session()->get('id_usuario');

        $modelo   = new MateriaModel();
        $materias = $modelo->obtenerMateriasPorDocente($id_docente);

        return view('docente/mis_materias', [
            'materias' => $materias,
        ]);
    }

    // ----------------------------------------------------------
    // verAlumnos($id_materia)
    // Muestra la lista de alumnos con sus notas actuales.
    // ----------------------------------------------------------
    public function verAlumnos(int $id_materia)
    {
        $redireccion = $this->verificarDocente();
        if ($redireccion) return $redireccion;

        $id_docente = session()->get('id_usuario');

        $modeloNota   = new NotaModel();
        $modeloMateria = new MateriaModel();

        $alumnos = $modeloNota->obtenerAlumnosInscriptos($id_materia);
        $materia = $modeloMateria->find($id_materia);

        return view('docente/alumnos', [
            'alumnos'    => $alumnos,
            'materia'    => $materia,
            'id_docente' => $id_docente,
        ]);
    }

    // ----------------------------------------------------------
    // registrarNota()
    // Valida el rango de la nota (1-10) antes de guardar.
    // ----------------------------------------------------------
    public function registrarNota()
    {
        $redireccion = $this->verificarDocente();
        if ($redireccion) return $redireccion;

        $id_docente  = session()->get('id_usuario');
        $id_materia  = (int) $this->request->getPost('id_materia');
        $id_alumno   = (int) $this->request->getPost('id_alumno');
        $nota        = (int) $this->request->getPost('nota');

        // Validar rango de nota
        if ($nota < 1 || $nota > 10) {
            return redirect()->to("/docente/alumnos/$id_materia")
                ->with('error', 'La nota debe ser un valor entre 1 y 10.');
        }

        $modelo    = new NotaModel();
        $resultado = $modelo->registrarNota($id_docente, $id_materia, $id_alumno, $nota);

        if (!$resultado) {
            return redirect()->to("/docente/alumnos/$id_materia")
                ->with('error', 'No se pudo registrar la nota. Verificá los datos.');
        }

        $nombreAlumno = $this->request->getPost('nombre_alumno');

        return redirect()->to("/docente/alumnos/$id_materia")
            ->with('mensaje', "Nota de $nombreAlumno registrada correctamente.");
    }
}