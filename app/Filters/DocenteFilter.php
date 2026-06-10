<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class DocenteFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // No hay sesión
        if (!session()->get('logueado')) {
            return redirect()->to('/login');
        }

        // No es administrador
        if (session()->get('id_perfil') != 3) {
            return redirect()->to('/dashboard');
        }
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {

    }
}