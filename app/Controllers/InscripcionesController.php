<?php

namespace App\Controllers;

use App\Models\InscripcionCarreraModel;
use App\Models\MateriaModel;
use App\Models\CarreraModel;

class InscripcionesController extends BaseController
{
    // ----------------------------------------------------------
    // PRIVADO: verificarSesion()
    // Siempre toma el id_usuario de la sesión, nunca de la URL.
    // ----------------------------------------------------------
    private function verificarSesion()
    {
        if (!session()->get('logueado')) {
            return redirect()->to('/login');
        }
        return null;
    }

    // SECCIÓN: CARRERAS

    // misCarreras()
    // Ruta: GET /mis-carreras
    // Muestra la carrera del alumno logueado (solo una).
    // Si no tiene carrera → muestra formulario para inscribirse.

    public function misCarreras()
    {
        $redireccion = $this->verificarSesion();
        if ($redireccion) return $redireccion;

        // id_usuario SIEMPRE desde sesión
        $id_usuario = session()->get('id_usuario');

        $modeloInscripcion = new InscripcionCarreraModel();
        $modeloCarrera     = new CarreraModel();

        // Carrera actual del alumno (una sola)
        $carreras   = $modeloInscripcion->consultarCarrerasInscripto($id_usuario);
        $miCarrera  = $carreras[0] ?? null; // tomar la primera (o null si no tiene)

        // Todas las carreras disponibles para el dropdown
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

        $id_usuario = session()->get('id_usuario'); // desde sesión
        $id_carrera = (int) $this->request->getPost('id_carrera');

        $modelo    = new InscripcionCarreraModel();
        $resultado = $modelo->inscribirseACarrera($id_usuario, $id_carrera);

        if (!$resultado) {
            return redirect()->to('/mis-carreras')
                ->with('error', 'Ya estás inscripto en esa carrera.');
        }

        return redirect()->to('/mis-carreras')
            ->with('mensaje', 'Te inscribiste a la carrera correctamente.');
    }


    // darseDeBaraCarrera()
    // Ruta: GET /baja-carrera/{id_carrera}
    // Da de baja al alumno de su carrera actual.
    // También elimina sus inscripciones a materias de esa carrera.

    public function darseDeBajaCarrera(int $id_carrera)
    {
        $redireccion = $this->verificarSesion();
        if ($redireccion) return $redireccion;

        $id_usuario = session()->get('id_usuario');

        $modelo = new InscripcionCarreraModel();
        $modelo->darseDeBaja($id_usuario, $id_carrera);

        return redirect()->to('/mis-carreras')
            ->with('mensaje', 'Te diste de baja de la carrera.');
    }

    // SECCIÓN: MATERIAS

    // misMaterias()
    // Ruta: GET /mis-materias
    // Muestra materias disponibles de la carrera del alumno
    // y las materias en las que ya está inscripto.

    public function misMaterias()
    {
        $redireccion = $this->verificarSesion();
        if ($redireccion) return $redireccion;

        $id_usuario = session()->get('id_usuario');

        // Obtener la carrera del alumno primero
        $modeloInscripcion = new InscripcionCarreraModel();
        $carreras          = $modeloInscripcion->consultarCarrerasInscripto($id_usuario);
        $miCarrera         = $carreras[0] ?? null;

        if (!$miCarrera) {
            // Si no tiene carrera, no puede ver materias
            return redirect()->to('/mis-carreras')
                ->with('error', 'Primero tenés que inscribirte a una carrera.');
        }

        $id_carrera = $miCarrera['id_carrera'];

        $modeloMateria      = new MateriaModel();
        $materiasDisponibles = $modeloMateria->consultarMateriasDisponibles($id_usuario, $id_carrera);
        $materiasInscripto  = $modeloMateria->obtenerMateriasInscripto($id_usuario);

        return view('inscripciones/mis_materias', [
            'materiasDisponibles' => $materiasDisponibles,
            'materiasInscripto'   => $materiasInscripto,
            'miCarrera'           => $miCarrera,
        ]);
    }


    // inscribirseAMateria()
    // Ruta: POST /inscribirse-materia

    public function inscribirseAMateria()
    {
        $redireccion = $this->verificarSesion();
        if ($redireccion) return $redireccion;

        $id_usuario = session()->get('id_usuario');
        $id_materia = (int) $this->request->getPost('id_materia');

        $modelo    = new MateriaModel();
        $resultado = $modelo->generarInscripcion($id_usuario, $id_materia);

        if (!$resultado) {
            return redirect()->to('/mis-materias')
                ->with('error', 'Ya estás inscripto en esa materia.');
        }

        $nombreMateria = $modelo->obtenerNombreMateria($id_materia);

        return redirect()->to('/mis-materias')
            ->with('nombre_materia', $nombreMateria);
    }


    // darseDeBajaMateria()
    // Ruta: GET /baja-materia/{id_materia}

    public function darseDeBajaMateria(int $id_materia)
    {
        $redireccion = $this->verificarSesion();
        if ($redireccion) return $redireccion;

        $id_usuario = session()->get('id_usuario');

        $modelo = new MateriaModel();
        $modelo->eliminarInscripcionMateria($id_usuario, $id_materia);

        return redirect()->to('/mis-materias')
            ->with('mensaje', 'Te diste de baja de la materia.');
    }
}