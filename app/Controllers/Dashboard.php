<?php

namespace App\Controllers;

class Dashboard extends BaseController
{

    // INDEX: Método principal del dashboard

    public function index()
    {
        // Verificar sesión activa
        if (!session()->get('logueado')) {
            return redirect()->to('/login');
        }

        // Mostrar vista del dashboard
        return view('dashboard');
    }
}