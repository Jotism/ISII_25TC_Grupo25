<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Auth extends BaseController
{
    // LOGIN: Muestra el formulario de inicio de sesión
    public function login()
    {
        return view('login');
    }

    // INICIARSESION: Procesa el formulario de login

    public function iniciarSesion()
    {
        // Obtener datos del formulario
        $email      = $this->request->getPost('email');
        $contrasena = $this->request->getPost('password');

        // Instanciar el modelo de usuario
        $modelo = new UsuarioModel();

        // Llamar al método del modelo que valida credenciales
        $usuario = $modelo->validarCredenciales($email, $contrasena);

        if (!$usuario) {
            // Credenciales incorrectas → mensaje "Datos inválidos"
            return view('login', ['mensaje' => 'Datos inválidos', 'tipo' => 'error']);
        }

        // Credenciales correctas → iniciar sesión con CodeIgniter
        $session = session();
        $session->set([
            'id_usuario' => $usuario['id_usuario'],
            'nombre'     => $usuario['nombre'],
            'email'      => $usuario['email'],
            'id_perfil'  => $usuario['id_perfil'],
            'logueado'   => true,
        ]);

        // Redirigir al dashboard con mensaje "Acceso autorizado"
        return redirect()->to('/dashboard')->with('mensaje', 'Acceso autorizado');
    }


    // REGISTRO: Muestra el formulario de registro

    public function registro()
    {
        return view('registro');
    }


    // GUARDARREGISTRO: Procesa el formulario de registro (POST)

    public function guardarRegistro()
    {
        // Obtener datos del formulario
        $datos = [
            'nombre'    => $this->request->getPost('nombre'),
            'apellido'  => $this->request->getPost('apellido'),
            'dni'       => $this->request->getPost('dni'),
            'email'     => $this->request->getPost('email'),
            'pass'      => $this->request->getPost('password'),
            'id_perfil' => 2, // Siempre Alumno (según enunciado)
        ];

        // Instanciar el modelo e insertar el nuevo usuario
        $modelo = new UsuarioModel();
        if (!$modelo->insertarUsuario($datos)) {
        dd($modelo->errors());
        }

        // Redirigir al login con mensaje de éxito
        return redirect()->to('/login')->with('mensaje', 'Registro exitoso. Podés iniciar sesión.');
    }


    // CERRAR SESIÓN

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}