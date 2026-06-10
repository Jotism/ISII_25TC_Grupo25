<?php

namespace App\Observers;

use SplObserver;
use SplSubject;

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

        $email->setTo($datos['correo']);

        switch ($subject->getTipoEvento()) {

            case 'INSCRIPCION_CARRERA':

                $email->setSubject('Inscripción a carrera');
                $email->setMessage(
                    "Te has inscrito correctamente a {$datos['carrera']}"
                );
                break;

            case 'INSCRIPCION_MATERIA':

                $email->setSubject('Inscripción a materia');
                $email->setMessage(
                    "Te has inscrito correctamente a {$datos['materia']}"
                );
                break;

            case 'NOTA_REGISTRADA':

                $email->setSubject('Nueva calificación');
                $email->setMessage(
                    "Tu nota en {$datos['materia']} es {$datos['nota']}"
                );
                break;
        }

        if ($email->send()) {

            log_message('info', 'Correo enviado');

        } else {

            log_message('error', $email->printDebugger(['headers']));
            
            echo $email->printDebugger(['headers']);
        }
    }
}