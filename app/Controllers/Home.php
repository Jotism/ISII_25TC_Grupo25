<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
{
    try {
        $db = \Config\Database::connect();
        return "Conectado a la base de datos";
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}
}