<?php

namespace App\Controllers;

use App\Models\MateriaModel;
use App\Models\MateriaCarreraModel;
use App\Models\CarreraModel;

class AdminController extends BaseController
{

    // listarMaterias()
    public function listarMaterias()
    {
        $materiaModel        = new MateriaModel();
        $materiaCarreraModel = new MateriaCarreraModel();
        $carreraModel        = new CarreraModel();

        $materiasRaw = $materiaModel->obtenerMaterias();
        $relaciones  = $materiaCarreraModel->obtenerRelaciones();
        $carreras    = $carreraModel->obtenerCarreras();

        // Indexar carreras por su id para búsquedas rápidas
        $carrerasIndexed = [];
        foreach ($carreras as $c) {
            $carrerasIndexed[$c['id_carrera']] = $c['nombre'];
        }

        // Indexar relaciones de materia_carrera por id_materia
        $relacionesIndexed = [];
        foreach ($relaciones as $r) {
            $relacionesIndexed[$r['id_materia']] = $r['id_carrera'];
        }

        // Construir arreglo combinado en PHP
        $materias = [];
        foreach ($materiasRaw as $m) {
            $id_materia = (int) $m['id_materia'];
            $id_carrera = $relacionesIndexed[$id_materia] ?? null;
            $nombre_carrera = ($id_carrera !== null) ? ($carrerasIndexed[$id_carrera] ?? null) : null;

            $materias[] = [
                'id_materia'     => $id_materia,
                'nombre'         => $m['nombre'],
                'anio_cursada'   => $m['anio_cursada'],
                'id_cuatrimestre' => $m['id_cuatrimestre'],
                'nombre_carrera' => $nombre_carrera,
            ];
        }

        return view('admin/panel_admin', [
            'materias' => $materias,
        ]);
    }

    // crearMateria()
    public function crearMateria()
    {
        $modeloCarrera = new CarreraModel();
        $carreras      = $modeloCarrera->obtenerCarreras(); // para el <select>

        return view('admin/crear_materia', [
            'carreras' => $carreras,
        ]);
    }

    // guardarMateria()
    // Recibe los datos del formulario de creación.
    // Inserta en tabla "materias" y luego en "materia_carrera".
    public function guardarMateria()
    {
        // Datos del formulario
        $nombre          = $this->request->getPost('nombre');
        $anio_cursada    = $this->request->getPost('anio_cursada');
        $id_cuatrimestre = $this->request->getPost('id_cuatrimestre');
        $id_carrera      = (int) $this->request->getPost('id_carrera');

        $materiaModel        = new MateriaModel();
        $materiaCarreraModel = new MateriaCarreraModel();

        // Paso 1: insertar en tabla "materias"
        $id_materia = $materiaModel->registrarMateria(
            $nombre,
            $anio_cursada,
            $id_cuatrimestre
        );

        if ($id_materia === null) {
            return redirect()->to('/admin/materias')->with('error', 'Error al insertar.');
        }

        // Paso 2: insertar en tabla "materia_carrera" para asociar carrera
        $materiaCarreraModel->asociarMateriaCarrera($id_materia, $id_carrera);

        // Volver al panel con mensaje de éxito
        return redirect()->to('/admin/materias')->with('mensaje', 'Materia creada correctamente.');
    }

    // editarMateria($id)
    // Muestra el formulario pre-cargado con los datos actuales.
    public function editarMateria($id)
    {
        $materiaModel        = new MateriaModel();
        $materiaCarreraModel = new MateriaCarreraModel();
        $carreraModel        = new CarreraModel();

        $materiaRaw = $materiaModel->obtenerMateriaPorId($id);
        if (!$materiaRaw) {
            return redirect()->to('/admin/materias')->with('error', 'Materia no encontrada.');
        }

        $carrerasRel = $materiaCarreraModel->obtenerCarrerasPorMateria($id);
        $id_carrera = !empty($carrerasRel) ? $carrerasRel[0]['id_carrera'] : null;

        $materia = [
            'id_materia'      => $materiaRaw['id_materia'],
            'nombre'          => $materiaRaw['nombre'],
            'anio_cursada'    => $materiaRaw['anio_cursada'],
            'id_cuatrimestre' => $materiaRaw['id_cuatrimestre'],
            'id_carrera'      => $id_carrera,
        ];

        $carreras = $carreraModel->obtenerCarreras();       // todas las carreras para el select

        return view('admin/editar_materia', [
            'materia'  => $materia,
            'carreras' => $carreras,
        ]);
    }

    // actualizarMateria($id)
    // Recibe los datos del formulario de edición y actualiza BD.
    public function actualizarMateria($id)
    {
        $nombre          = $this->request->getPost('nombre');
        $anio_cursada    = $this->request->getPost('anio_cursada');
        $id_cuatrimestre = $this->request->getPost('id_cuatrimestre');
        $id_carrera      = (int) $this->request->getPost('id_carrera');

        $materiaModel        = new MateriaModel();
        $materiaCarreraModel = new MateriaCarreraModel();

        // Paso 1: actualizar datos de la materia
        $resultado = $materiaModel->actualizarMateria(
            $id,
            $nombre,
            $anio_cursada,
            $id_cuatrimestre
        );

        // Paso 2: actualizar la carrera asociada
        // (borra la relación anterior y crea la nueva)
        $materiaCarreraModel->eliminarPorMateria($id);
        $materiaCarreraModel->asociarMateriaCarrera($id, $id_carrera);

        return redirect()->to('/admin/materias')->with('mensaje', 'Materia actualizada correctamente.');
    }

    // eliminarMateria($id)
    // Elimina la materia de las tablas materias y materia_carrera.
    public function eliminarMateria($id)
    {
        $materiaModel        = new MateriaModel();
        $materiaCarreraModel = new MateriaCarreraModel();

        // Paso 1: borrar relación en materia_carrera primero
        $materiaCarreraModel->eliminarPorMateria($id);

        // Paso 2: borrar la materia
        $materiaModel->eliminarMateriaPorId($id);

        return redirect()->to('/admin/materias')->with('mensaje', 'Materia eliminada.');
    }
}