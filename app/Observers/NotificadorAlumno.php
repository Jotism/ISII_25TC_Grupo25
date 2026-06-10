<?php

namespace App\Observers;

use SplObserver;
use SplSubject;
use App\Models\CarreraModel;
use App\Models\MateriaModel;
use App\Models\UsuarioModel;
use App\Models\InscripcionMateriaModel;

class NotificadorAlumno implements SplObserver
{
    public function update(SplSubject $subject): void
    {
        $datos = $subject->getDatos();

        $email = \Config\Services::email();

        $email->setFrom(
            'gestacad.noreply@gmail.com',
            'GestAcad'
        );

        $modeloUsuario = model(UsuarioModel::Class);
        $mailAlumno = null;

        switch ($subject->getTipoEvento()) {

            case 'INSCRIPCION_CARRERA':
                
                $modeloCarrera = model(CarreraModel::class);
                $nombreCarrera = $modeloCarrera->obtenerNombreCarrera($datos['id_carrera']);

                $mailAlumno = $modeloUsuario->obtenerEmailUsuario($datos['id_usuario']);

                $email->setSubject('Inscripción a carrera');
                $email->setMessage(
                    "Te has inscrito correctamente a {$nombreCarrera}"
                );
                break;

            case 'INSCRIPCION_MATERIA':
                
                $modeloMateria = model(MateriaModel::class);
                $nombreMateria = $modeloMateria->obtenerNombreMateria($datos['id_materia']);

                $mailAlumno = $modeloUsuario->obtenerEmailUsuario($datos['id_usuario']);

                $email->setSubject('Inscripción a materia');
                $email->setMessage(
                    "Te has inscrito correctamente a {$nombreMateria}"
                );
                break;

            case 'NOTA_REGISTRADA':
                
                $modeloInscripcion = model(InscripcionMateriaModel::class);
                $inscripcion = $modeloInscripcion->buscarInscripcionPorID($datos['id_inscripcion']);
                
                $id_usuario = $inscripcion['id_usuario'];
                $mailAlumno = $modeloUsuario->obtenerEmailUsuario($id_usuario);

                $modeloMateria = model(MateriaModel::class);
                $nombreMateria = $modeloMateria->obtenerNombreMateria($inscripcion['id_materia']);

                $email->setSubject('Nueva calificación');
                $email->setMessage(
                    "Tu nota en {$nombreMateria} es {$datos['nota']}"
                );
                break;
        }


        $email->setTo($mailAlumno);

        if ($email->send()) {

            log_message('info', 'Correo enviado');

        } else {

            log_message('error', $email->printDebugger(['headers']));
            
            echo $email->printDebugger(['headers']);
        }
    }
}