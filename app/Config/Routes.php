<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Ruta raíz → redirige al login
$routes->get('/', 'Auth::login');
 
// Login: mostrar formulario (GET) y procesar (POST)
$routes->get('login',              'Auth::login');
$routes->post('auth/iniciarSesion', 'Auth::iniciarSesion');
 
// Registro: mostrar formulario (GET) y procesar (POST)
$routes->get('auth/registro',       'Auth::registro');
$routes->post('auth/guardarRegistro','Auth::guardarRegistro');
 
// Logout
$routes->get('auth/logout', 'Auth::logout');
 
// -------------------------------------------------------
// RUTAS DEL DASHBOARD
// -------------------------------------------------------
$routes->get('dashboard', 'Dashboard::index');
 
// -------------------------------------------------------
// RUTAS DE MATERIAS
// -------------------------------------------------------
// Sin parámetros: usa id_usuario de la sesión
$routes->get('materias/obtenerMateriasDisponibles',
             'Materias::obtenerMateriasDisponibles');
 
// Con parámetros opcionales por URL (idEstudiante / idCarrera)
$routes->get('materias/obtenerMateriasDisponibles/(:num)',
             'Materias::obtenerMateriasDisponibles/$1');
 
$routes->get('materias/obtenerMateriasDisponibles/(:num)/(:num)',
             'Materias::obtenerMateriasDisponibles/$1/$2');