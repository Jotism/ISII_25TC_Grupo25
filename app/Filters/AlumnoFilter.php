<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AlumnoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logueado')) {
            return redirect()->to('/login');
        }

        if (session()->get('id_perfil') != 2) {
            return redirect()->to('/dashboard');
        }

        $idUsuario = $request->getUri()->getSegment(3);
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
    }
}