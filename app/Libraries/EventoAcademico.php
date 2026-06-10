<?php

namespace App\Libraries;

use SplObserver;
use SplSubject;

class EventoAcademico implements SplSubject
{
    private array $observers = [];

    private string $tipoEvento;
    private array $datos = [];

    public function attach(SplObserver $observer): void
    {
        $this->observers[] = $observer;
    }

    public function detach(SplObserver $observer): void
    {
        foreach ($this->observers as $key => $obs) {
            if ($obs === $observer) {
                unset($this->observers[$key]);
            }
        }
    }

    public function notify(): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function disparar(string $tipo, array $datos)
    {
        $this->tipoEvento = $tipo;
        $this->datos = $datos;

        $this->notify();
    }

    public function getTipoEvento(): string
    {
        return $this->tipoEvento;
    }

    public function getDatos(): array
    {
        return $this->datos;
    }
}