<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Ruta raíz → redirige al login
$routes->get('/', 'Auth::login');
 
// Login: mostrar formulario (GET) y procesar (POST)
$routes->get('login',              'Auth::login');
$routes->post('auth/iniciarSesion', 'Auth::iniciarSesion');
 
// Registro: mostrar formulario (GET) y procesar (POST)
$routes->get('auth/registro',       'Auth::registro');
$routes->post('auth/guardarRegistro','Auth::guardarRegistro');
 
// Logout
$routes->get('auth/logout', 'Auth::cerrarSesion');
 
// -------------------------------------------------------
// RUTAS DEL DASHBOARD
// -------------------------------------------------------
$routes->get('dashboard', 'Dashboard::index');
 


// -------------------------------------------------------
// RUTAS DEL PANEL ADMIN
// -------------------------------------------------------
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    // Lista de materias
    $routes->get('materias', 'AdminController::listarMaterias');
    // Mostrar formulario de creación
    $routes->get('materias/crear', 'AdminController::crearMateria');
    // Procesar formulario de creación
    $routes->post('materias/guardar', 'AdminController::guardarMateria');
    // Mostrar formulario de edición
    $routes->get('materias/editar/(:num)', 'AdminController::editarMateria/$1');
    // Procesar formulario de edición
    $routes->post('materias/actualizar/(:num)', 'AdminController::actualizarMateria/$1');
    // Eliminar una materia
    $routes->get('materias/eliminar/(:num)', 'AdminController::eliminarMateria/$1');
});

// -------------------------------------------------------
// RUTAS ALUMNO
// -------------------------------------------------------
$routes->group('alumno', ['filter' => 'alumno'], function($routes) {
    $routes->get('mis-carreras',                    'InscripcionesController::misCarreras');
    $routes->post('inscribirse-carrera',            'InscripcionesController::inscribirseACarrera');
    $routes->get('baja-carrera/(:num)',             'InscripcionesController::darseDeBajaCarrera/$1');
    $routes->get('mis-materias',                 'InscripcionesController::misMaterias/$1');
    $routes->get('inscribirse-materia/(:num)',   'InscripcionesController::inscribirseAMateria/$1/$2');
    $routes->get('baja-materia/(:num)',          'InscripcionesController::darseDeBajaMateria/$1/$2');
});


/**
 * RUTAS DOCENTE
 */
$routes->group('docente', ['filter' => 'docente'], function($routes) {
    // Lista de materias del docente
    $routes->get('materias',               'DocenteController::misMaterias');
    // Lista de alumnos de una materia del docente
    $routes->get('alumnos/(:num)',         'DocenteController::verAlumnos/$1');
    // Procesar registro de nota
    $routes->post('registrar-nota',        'DocenteController::registrarNota');
});