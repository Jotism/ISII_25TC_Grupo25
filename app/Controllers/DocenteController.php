<?php

namespace App\Controllers;

use App\Models\NotaModel;
use App\Models\MateriaModel;
use App\Models\InscripcionMateriaModel;
use App\Models\UsuarioModel;
use App\Libraries\EventoAcademico;
use App\Observers\NotificadorAlumno;

class DocenteController extends BaseController
{
    // ----------------------------------------------------------
    // misMaterias()
    // Trae las materias donde id_usuario = id del docente logueado.
    // ----------------------------------------------------------
    public function misMaterias()
    {
        $id_docente = session()->get('id_usuario');

        $modelo   = model(MateriaModel::class);
        $materias = $modelo->obtenerMateriasPorDocente($id_docente);

        return view('docente/mis_materias', [
            'materias' => $materias,
        ]);
    }

    // ----------------------------------------------------------
    // verAlumnos($id_materia)
    // Muestra la lista de alumnos con sus notas actuales.
    // Realiza el join de datos en PHP.
    // ----------------------------------------------------------
    public function verAlumnos(int $id_materia)
    {
        $id_docente = session()->get('id_usuario');

        $modeloMateria          = model(MateriaModel::class);
        $inscripcionMateriaModel = model(InscripcionMateriaModel::class);
        $usuarioModel           = model(UsuarioModel::class);
        $notaModel              = model(NotaModel::class);

        $materia = $modeloMateria->find($id_materia);
        if (!$materia) {
            return redirect()->to('/docente/materias')->with('error', 'Materia no encontrada.');
        }

        // Obtener todas las inscripciones asociadas a la materia
        $inscripciones = $inscripcionMateriaModel->obtenerInscripcionesPorMateria($id_materia);

        // Cruce de datos en PHP
        $alumnos = [];
        foreach ($inscripciones as $ins) {
            $id_inscripcion = (int) $ins['id_inscripcion'];
            $id_usuario     = (int) $ins['id_usuario'];

            // Obtener los datos del alumno de la tabla usuarios
            $alumnoRaw = $usuarioModel->find($id_usuario);
            if (!$alumnoRaw) continue;

            // Obtener la nota asociada de la tabla nota_cursada
            $notaRaw = $notaModel->obtenerNotaPorInscripcion($id_inscripcion);

            $alumnos[] = [
                'id_usuario' => $id_usuario,
                'nombre'     => $alumnoRaw['nombre'],
                'apellido'   => $alumnoRaw['apellido'],
                'dni'        => $alumnoRaw['dni'],
                'nota'       => $notaRaw['nota'] ?? null,
                'estado'     => $notaRaw['estado'] ?? null,
                'fecha_nota' => $notaRaw['fecha_nota'] ?? null,
            ];
        }

        // Ordenar alfabéticamente por apellido
        usort($alumnos, function ($a, $b) {
            return strcmp($a['apellido'], $b['apellido']);
        });

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
        $id_docente  = (int) session()->get('id_usuario');
        $id_materia  = (int) $this->request->getPost('id_materia');
        $id_alumno   = (int) $this->request->getPost('id_alumno');
        $nota        = (int) $this->request->getPost('nota');

        // Validar rango de nota
        if ($nota < 1 || $nota > 10) {
            return redirect()->to("/docente/alumnos/$id_materia")
                ->with('error', 'La nota debe ser un valor entre 1 y 10.');
        }

        $materiaModel           = model(MateriaModel::class);
        $inscripcionMateriaModel = model(InscripcionMateriaModel::class);
        $notaModel              = model(NotaModel::class);

        // 1. Validar que el docente sea titular de la materia
        $materia = $materiaModel->find($id_materia);
        if (!$materia || (int) $materia['id_usuario'] !== $id_docente) {
            return redirect()->to("/docente/alumnos/$id_materia")
                ->with('error', 'No se pudo registrar la nota. Verificá los datos.');
        }

        // 2. Obtener el id_inscripcion del alumno en esa materia
        $inscripcion = $inscripcionMateriaModel->buscarInscripcion($id_alumno, $id_materia);
        if (!$inscripcion) {
            return redirect()->to("/docente/alumnos/$id_materia")
                ->with('error', 'No se pudo registrar la nota. Verificá los datos.');
        }

        // 3. Registrar o actualizar la nota
        $id_inscripcion = (int) $inscripcion['id_inscripcion'];
        $resultado = $notaModel->registrarNotaDirecta($id_inscripcion, $nota);

        if (!$resultado) {
            return redirect()->to("/docente/alumnos/$id_materia")
                ->with('error', 'No se pudo registrar la nota. Verificá los datos.');
        }

        $nombreAlumno = $this->request->getPost('nombre_alumno');

        //***Observador***
        $usuarioModel = model(UsuarioModel::class);
        $mailAlumno = $usuarioModel->obtenerEmailUsuario($id_alumno);

        $evento = new EventoAcademico();

        $evento->attach(new NotificadorAlumno());

        $evento->disparar(
            'NOTA_REGISTRADA',
            [
                'correo' => $mailAlumno,
                'materia' => (string) $materia['nombre'],
                'nota' => $nota  
            ]
        );
        //***Observador***

        return redirect()->to("/docente/alumnos/$id_materia")
            ->with('mensaje', "Nota de $nombreAlumno registrada correctamente.");
    }
}