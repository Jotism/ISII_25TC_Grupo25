<?php

namespace App\Controllers;

use App\Models\InscripcionCarreraModel;
use App\Models\InscripcionMateriaModel;
use App\Models\MateriaModel;
use App\Models\CarreraModel;
use App\Models\MateriaCarreraModel;
use App\Models\NotaModel;
use App\Libraries\EventoAcademico;
use App\Observers\NotificadorAlumno;

class InscripcionesController extends BaseController
{
    // ----------------------------------------------------------
    // PRIVADO: verificarSesion()
    // Siempre toma el id_usuario de la sesión para validar accesos.
    // ----------------------------------------------------------
    private function verificarSesion(int $id_usuario = null)
    {
        if (!session()->get('logueado')) {
            return redirect()->to('/login');
        }

        // Solo permite el acceso a Alumnos (perfil ID = 2)
        if (session()->get('id_perfil') != 2) {
            return redirect()->to('/dashboard');
        }

        // Seguridad: Evitar que un alumno acceda a los datos de otro (IDOR)
        if ($id_usuario !== null && $id_usuario !== (int) session()->get('id_usuario')) {
            return redirect()->to('/dashboard');
        }

        return null;
    }

    // SECCIÓN: CARRERAS

    // misCarreras()
    // Ruta: GET /mis-carreras
    // Muestra la carrera del alumno logueado (solo una).
    public function misCarreras()
    {
        $redireccion = $this->verificarSesion();
        if ($redireccion) return $redireccion;

        $id_usuario = session()->get('id_usuario');

        $modeloInscripcion = model(InscripcionCarreraModel::class);
        $modeloCarrera     = model(CarreraModel::class);

        // Obtener inscripción a carrera del alumno (sin JOIN)
        $carrerasInscripto = $modeloInscripcion->consultarCarrerasInscripto($id_usuario);
        $inscripcion       = $carrerasInscripto[0] ?? null;

        $miCarrera = null;
        if ($inscripcion) {
            $id_carrera = (int) $inscripcion['id_carrera'];
            // Consultar datos de la carrera
            $miCarrera = $modeloCarrera->find($id_carrera);
        }

        // Todas las carreras disponibles para el select
        $todasCarreras = $modeloCarrera->getCarreras();

        return view('inscripciones/mis_carreras', [
            'miCarrera'     => $miCarrera,
            'todasCarreras' => $todasCarreras,
        ]);
    }

    // inscribirseACarrera()
    // Ruta: POST /inscribirse-carrera
    public function inscribirseACarrera()
    {
        $redireccion = $this->verificarSesion();
        if ($redireccion) return $redireccion;

        $id_usuario = session()->get('id_usuario');
        $id_carrera = (int) $this->request->getPost('id_carrera');

        $modelo    = model(InscripcionCarreraModel::class);
        $resultado = $modelo->generarInscripcion($id_usuario, $id_carrera);

        if (!$resultado) {
            return redirect()->to('/mis-carreras')
                ->with('error', 'Ya estás inscripto en esa carrera.');
        }

        //***Observador***
        $modeloCarrera = model(CarreraModel::class);
        $mailAlumno = session()->get('email');
        $nombreCarrera = $modeloCarrera->obtenerNombreCarrera($id_carrera);

        $evento = new EventoAcademico();

        $evento->attach(new NotificadorAlumno());

        $evento->disparar(
            'INSCRIPCION_CARRERA',
            [
                'correo' => $mailAlumno,
                'carrera' => $nombreCarrera
            ]
        );
        //***Observador***

        return redirect()->to('/mis-carreras')
            ->with('mensaje', 'Te inscribiste a la carrera correctamente.');
    }

    // darseDeBajaCarrera()
    // Ruta: GET /baja-carrera/{id_carrera}
    // Da de baja al alumno de su carrera actual.
    // También elimina sus inscripciones a materias de esa carrera y sus notas.
    public function darseDeBajaCarrera(int $id_carrera)
    {
        $redireccion = $this->verificarSesion();
        if ($redireccion) return $redireccion;

        $id_usuario = session()->get('id_usuario');

        $db = \Config\Database::connect();
        $db->transStart();

        $materiaCarreraModel     = model(MateriaCarreraModel::class);
        $inscripcionMateriaModel = model(InscripcionMateriaModel::class);
        $notaModel               = model(NotaModel::class);
        $inscripcionCarreraModel = model(InscripcionCarreraModel::class);

        // 1. Obtener todas las materias asociadas a la carrera
        $materiasDeCarrera = $materiaCarreraModel->obtenerMateriasPorCarrera($id_carrera);

        // 2. Iterar y borrar las inscripciones a materias y sus notas
        foreach ($materiasDeCarrera as $m) {
            $id_materia  = (int) $m['id_materia'];
            $inscripcion = $inscripcionMateriaModel->buscarInscripcion($id_usuario, $id_materia);

            if ($inscripcion) {
                $id_inscripcion = (int) $inscripcion['id_inscripcion'];
                // Borrar la nota primero para no violar la FK
                $notaModel->where('id_inscripcion', $id_inscripcion)->delete();
                // Borrar la inscripción de la materia
                $inscripcionMateriaModel->eliminarInscripcionMateriaDirecta($id_usuario, $id_materia);
            }
        }

        // 3. Borrar inscripción a la carrera
        $inscripcionCarreraModel->darseDeBajaDirecta($id_usuario, $id_carrera);

        $db->transComplete();

        return redirect()->to('/mis-carreras')
            ->with('mensaje', 'Te diste de baja de la carrera.');
    }

    // SECCIÓN: MATERIAS

    // misMaterias()
    // Ruta: GET /mis-materias/{id_usuario}
    public function misMaterias(int $id_usuario)
    {
        $redireccion = $this->verificarSesion($id_usuario);
        if ($redireccion) return $redireccion;

        // 1. Obtener la carrera del alumno
        $miCarrera = $this->obtenerCarreraDeAlumno($id_usuario);
        if (!$miCarrera) {
            return redirect()->to('/mis-carreras')
                ->with('error', 'Primero tenés que inscribirte a una carrera.');
        }

        $id_carrera = (int) $miCarrera['id_carrera'];

        // 2. Obtener materias en las que ya está inscripto con sus notas
        $materiasInscripto = $this->obtenerMateriasInscriptasDeAlumno($id_usuario);

        // Extraer los IDs de las materias inscriptas
        $inscriptoIds = array_column($materiasInscripto, 'id_materia');

        // 3. Obtener materias de la carrera que el alumno tiene disponibles para inscribirse
        $materiasDisponibles = $this->obtenerMateriasDisponiblesDeAlumno($id_usuario, $id_carrera, $inscriptoIds);

        return view('inscripciones/mis_materias', [
            'materiasDisponibles' => $materiasDisponibles,
            'materiasInscripto'   => $materiasInscripto,
            'miCarrera'           => $miCarrera,
        ]);
    }

    // inscribirseAMateria()
    // Ruta: GET /inscribirse-materia/{id_materia}/{id_usuario}
    public function inscribirseAMateria(int $id_materia, int $id_usuario)
    {
        $redireccion = $this->verificarSesion($id_usuario);
        if ($redireccion) return $redireccion;

        // Obtener la carrera del alumno
        $miCarrera = $this->obtenerCarreraDeAlumno($id_usuario);
        if (!$miCarrera) {
            return redirect()->to('/mis-materias/' . $id_usuario)
                ->with('error', 'Primero tenés que inscribirte a una carrera.');
        }

        $id_carrera = (int) $miCarrera['id_carrera'];

        // Verificar si la materia pertenece a la carrera del alumno
        $materiaCarreraModel = model(MateriaCarreraModel::class);
        $relaciones          = $materiaCarreraModel->obtenerCarrerasPorMateria($id_materia);

        $pertenece = false;
        foreach ($relaciones as $rel) {
            if ((int) $rel['id_carrera'] === $id_carrera) {
                $pertenece = true;
                break;
            }
        }

        if (!$pertenece) {
            return redirect()->to('/mis-materias/' . $id_usuario)
                ->with('error', 'La materia no pertenece a tu carrera.');
        }

        $modeloInscripcionMateria = model(InscripcionMateriaModel::class);
        $resultado = $modeloInscripcionMateria->generarInscripcion($id_usuario, $id_materia);

        if (!$resultado) {
            return redirect()->to('/mis-materias/' . $id_usuario)
                ->with('error', 'Ya estás inscripto en esa materia.');
        }

        $modeloMateria = model(MateriaModel::class);
        $nombreMateria = $modeloMateria->obtenerNombreMateria($id_materia);

        //***Observador***
        $mailAlumno = session()->get('email');

        $evento = new EventoAcademico();

        $evento->attach(new NotificadorAlumno());

        $evento->disparar(
            'INSCRIPCION_MATERIA',
            [
                'correo' => $mailAlumno,
                'materia' => $nombreMateria
            ]
        );
        //***Observador***

        return redirect()->to('/mis-materias/' . $id_usuario)
            ->with('nombre_materia', $nombreMateria);
    }

    // darseDeBajaMateria()
    // Ruta: GET /baja-materia/{id_materia}/{id_usuario}
    public function darseDeBajaMateria(int $id_materia, int $id_usuario)
    {
        $redireccion = $this->verificarSesion($id_usuario);
        if ($redireccion) return $redireccion;

        $db = \Config\Database::connect();
        $db->transStart();

        $modeloInscripcionMateria = model(InscripcionMateriaModel::class);
        $modeloNota               = model(NotaModel::class);

        // Buscar la inscripción para borrar la nota primero
        $inscripcion = $modeloInscripcionMateria->buscarInscripcion($id_usuario, $id_materia);
        if ($inscripcion) {
            $id_inscripcion = (int) $inscripcion['id_inscripcion'];
            // Borrar la nota para no violar la FK
            $modeloNota->where('id_inscripcion', $id_inscripcion)->delete();
        }

        // Borrar la inscripción a la materia
        $modeloInscripcionMateria->eliminarInscripcionMateriaDirecta($id_usuario, $id_materia);

        $db->transComplete();

        return redirect()->to('/mis-materias/' . $id_usuario)
            ->with('mensaje', 'Te diste de baja de la materia.');
    }

    // ----------------------------------------------------------
    // MÉTODOS AUXILIARES DE SOPORTE (REFRACTORIZACIÓN DE LÓGICA)
    // ----------------------------------------------------------

    // Obtener datos de la carrera del alumno
    private function obtenerCarreraDeAlumno(int $id_usuario): ?array
    {
        $modeloInscripcionCarrera = model(InscripcionCarreraModel::class);
        $modeloCarrera            = model(CarreraModel::class);

        $carreras    = $modeloInscripcionCarrera->consultarCarrerasInscripto($id_usuario);
        $inscripcion = $carreras[0] ?? null;

        if (!$inscripcion) {
            return null;
        }

        return $modeloCarrera->find((int) $inscripcion['id_carrera']);
    }

    // Obtener materias en las que está inscripto el alumno con sus notas correspondientes en PHP
    private function obtenerMateriasInscriptasDeAlumno(int $id_usuario): array
    {
        $modeloInscripcionMateria = model(InscripcionMateriaModel::class);
        $modeloMateria            = model(MateriaModel::class);
        $modeloNota               = model(NotaModel::class);

        // Obtener inscripciones de materias (sin JOIN)
        $inscripciones = $modeloInscripcionMateria->obtenerInscripcionesPorUsuario($id_usuario);

        $materiasInscripto = [];
        foreach ($inscripciones as $ins) {
            $id_materia     = (int) $ins['id_materia'];
            $id_inscripcion = (int) $ins['id_inscripcion'];

            // Consultar datos de la materia
            $materiaRaw = $modeloMateria->find($id_materia);
            if (!$materiaRaw) continue;

            // Consultar la nota (si existe) asociada a esa inscripción
            $notaRaw = $modeloNota->obtenerNotaPorInscripcion($id_inscripcion);

            $materiasInscripto[] = [
                'id_materia'      => $id_materia,
                'nombre'          => $materiaRaw['nombre'],
                'id_cuatrimestre' => $materiaRaw['id_cuatrimestre'],
                'nota'            => $notaRaw['nota'] ?? null,
                'estado'          => $notaRaw['estado'] ?? null,
                'fecha_nota'      => $notaRaw['fecha_nota'] ?? null,
            ];
        }

        // Ordenar por cuatrimestre
        usort($materiasInscripto, function ($a, $b) {
            return $a['id_cuatrimestre'] <=> $b['id_cuatrimestre'];
        });

        return $materiasInscripto;
    }

    // Obtener materias de la carrera que no han sido inscritas aún por el alumno
    private function obtenerMateriasDisponiblesDeAlumno(int $id_usuario, int $id_carrera, array $inscriptoIds): array
    {
        $materiaCarreraModel = model(MateriaCarreraModel::class);
        $materiaModel        = model(MateriaModel::class);

        // Obtener todos los IDs de materias que pertenecen a la carrera
        $relaciones = $materiaCarreraModel->obtenerMateriasPorCarrera($id_carrera);

        $materiasDisponibles = [];
        foreach ($relaciones as $rel) {
            $id_materia = (int) $rel['id_materia'];

            // Si el alumno ya está inscripto, la salteamos
            if (in_array($id_materia, $inscriptoIds)) {
                continue;
            }

            // Consultar los datos de la materia
            $materiaRaw = $materiaModel->find($id_materia);
            if (!$materiaRaw) continue;

            $materiasDisponibles[] = [
                'id_materia'      => $id_materia,
                'nombre'          => $materiaRaw['nombre'],
                'id_cuatrimestre' => $materiaRaw['id_cuatrimestre'],
            ];
        }

        // Ordenar por cuatrimestre, luego por nombre
        usort($materiasDisponibles, function ($a, $b) {
            if ($a['id_cuatrimestre'] === $b['id_cuatrimestre']) {
                return strcmp($a['nombre'], $b['nombre']);
            }
            return $a['id_cuatrimestre'] <=> $b['id_cuatrimestre'];
        });

        return $materiasDisponibles;
    }
}