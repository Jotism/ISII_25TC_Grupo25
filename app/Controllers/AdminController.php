<?php


namespace App\Controllers;

use App\Models\AdminMateriaModel;
use App\Models\CarreraModel;

class AdminController extends BaseController
{

    // MÉTODO PRIVADO: verificarAdmin()

    private function verificarAdmin()
    {
        // Si no hay sesión activa → al login
        if (!session()->get('logueado')) {
            return redirect()->to('/login');
        }

        // Si hay sesión pero no es admin → al dashboard
        if (session()->get('id_perfil') != 1) {
            return redirect()->to('/dashboard');
        }

        return null; // Todo OK, puede continuar
    }

    // listarMaterias()

    public function listarMaterias()
    {
        $redireccion = $this->verificarAdmin();
        if ($redireccion) return $redireccion;

        $modelo   = new AdminMateriaModel();
        $materias = $modelo->getMaterias();  // trae materias con nombre de carrera

        return view('admin/panel_admin', [
            'materias' => $materias,
        ]);
    }

    // crearMateria()

    public function crearMateria()
    {
        $redireccion = $this->verificarAdmin();
        if ($redireccion) return $redireccion;

        $modeloCarrera = new CarreraModel();
        $carreras      = $modeloCarrera->getCarreras(); // para el <select>

        return view('admin/crear_materia', [
            'carreras' => $carreras,
        ]);
    }

    // Recibe los datos del formulario de creación.
    // Inserta en tabla "materias" y luego en "materia_carrera".

    public function guardarMateria()
    {
        $redireccion = $this->verificarAdmin();
        if ($redireccion) return $redireccion;

        // Datos del formulario
        $nombre          = $this->request->getPost('nombre');
        $anio_cursada    = $this->request->getPost('anio_cursada');
        $id_cuatrimestre = $this->request->getPost('id_cuatrimestre');
        $id_carrera      = $this->request->getPost('id_carrera');

        $modelo = new AdminMateriaModel();

        // Paso 1: insertar en tabla "materias" y obtener el id generado
        $id_materia = $modelo->insertarMateria([
            'nombre'          => $nombre,
            'anio_cursada'    => $anio_cursada,
            'id_cuatrimestre' => $id_cuatrimestre,
        ]);

        // Paso 2: insertar en tabla "materia_carrera" para asociar carrera
        $modelo->insertarMateriaCarrera($id_materia, $id_carrera);

        // Volver al panel con mensaje de éxito
        return redirect()->to('/admin/materias')->with('mensaje', 'Materia creada correctamente.');
    }


    // Muestra el formulario pre-cargado con los datos actuales.

    public function editarMateria($id)
    {
        $redireccion = $this->verificarAdmin();
        if ($redireccion) return $redireccion;

        $modelo        = new AdminMateriaModel();
        $modeloCarrera = new CarreraModel();

        $materia  = $modelo->getMateriaConCarrera($id); // datos de la materia
        $carreras = $modeloCarrera->getCarreras();       // todas las carreras para el select

        return view('admin/editar_materia', [
            'materia'  => $materia,
            'carreras' => $carreras,
        ]);
    }

    // Recibe los datos del formulario de edición y actualiza BD.

    public function actualizarMateria($id)
    {
        $redireccion = $this->verificarAdmin();
        if ($redireccion) return $redireccion;

        $nombre          = $this->request->getPost('nombre');
        $anio_cursada    = $this->request->getPost('anio_cursada');
        $id_cuatrimestre = $this->request->getPost('id_cuatrimestre');
        $id_carrera      = $this->request->getPost('id_carrera');

        $modelo = new AdminMateriaModel();

        // Paso 1: actualizar datos de la materia
        $modelo->actualizarMateria($id, [
            'nombre'          => $nombre,
            'anio_cursada'    => $anio_cursada,
            'id_cuatrimestre' => $id_cuatrimestre,
        ]);

        // Paso 2: actualizar la carrera asociada
        // (borra la relación anterior y crea la nueva)
        $modelo->actualizarMateriaCarrera($id, $id_carrera);

        return redirect()->to('/admin/materias')->with('mensaje', 'Materia actualizada correctamente.');
    }

    // Elimina la materia de las tablas materias y materia_carrera.

    public function eliminarMateria($id)
    {
        $redireccion = $this->verificarAdmin();
        if ($redireccion) return $redireccion;

        $modelo = new AdminMateriaModel();

        // Paso 1: borrar relación en materia_carrera primero (integridad)
        $modelo->eliminarMateriaCarrera($id);

        // Paso 2: borrar la materia
        $modelo->eliminarMateria($id);

        return redirect()->to('/admin/materias')->with('mensaje', 'Materia eliminada.');
    }
}