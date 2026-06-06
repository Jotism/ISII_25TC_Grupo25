<?php

namespace Tests\app\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Config\Factories;
use App\Models\NotaModel;

/**
 * @internal
 */
class RegistrarNotaTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        $this->resetServices();
        Factories::reset();
        parent::setUp();
    }

    /**
     * Caso Correcto: Profesor titular carga una nota válida (rango 1 a 10) a un alumno inscripto.
     * 
     * @dataProvider proveedorNotasValidas
     */
    public function testRegistrarNotaExitosa($notaValida)
    {
        $idDocente = 6;
        $idMateria = 6;
        $idAlumno = 9;
        $nombreAlumno = 'Valentina López';

        // 1. Mock de NotaModel para registrar la nota de forma exitosa
        $mockNota = $this->createMock(NotaModel::class);
        $mockNota->method('registrarNota')
            ->with($idDocente, $idMateria, $idAlumno, $notaValida)
            ->willReturn(true);

        Factories::injectMock('models', NotaModel::class, $mockNota);

        // 2. Ejecutar petición POST simulando sesión de docente activa
        $sessionData = [
            'logueado'   => true,
            'id_usuario' => $idDocente,
            'id_perfil'  => 3, // Docente
        ];

        $response = $this->withSession($sessionData)
            ->post('docente/registrar-nota', [
                'id_materia'    => $idMateria,
                'id_alumno'     => $idAlumno,
                'nota'          => $notaValida,
                'nombre_alumno' => $nombreAlumno
            ]);

        // 3. Aserciones: redirecciona y mensaje de éxito
        $response->assertRedirectTo("/docente/alumnos/{$idMateria}");
        $response->assertSessionHas('mensaje', "Nota de {$nombreAlumno} registrada correctamente.");
    }

    /**
     * Excepción 1: Nota fuera de rango (ej. un 11 o un 0).
     * 
     * @dataProvider proveedorNotasInvalidas
     */
    public function testNotaFueraDeRango($notaInvalida)
    {
        $idDocente = 6;
        $idMateria = 6;
        $idAlumno = 9;

        // No debería interactuar con el modelo ya que el controlador debe validar previamente
        $mockNota = $this->createMock(NotaModel::class);
        $mockNota->expects($this->never())->method('registrarNota');

        Factories::injectMock('models', NotaModel::class, $mockNota);

        // Petición POST
        $sessionData = [
            'logueado'   => true,
            'id_usuario' => $idDocente,
            'id_perfil'  => 3, // Docente
        ];

        $response = $this->withSession($sessionData)
            ->post('docente/registrar-nota', [
                'id_materia'    => $idMateria,
                'id_alumno'     => $idAlumno,
                'nota'          => $notaInvalida,
                'nombre_alumno' => 'Valentina López'
            ]);

        // Aserciones: Redirección con error de validación en rango
        $response->assertRedirectTo("/docente/alumnos/{$idMateria}");
        $response->assertSessionHas('error', 'La nota debe ser un valor entre 1 y 10.');
    }

    /**
     * Excepción 2: Usuario que intenta cargar la nota no es el profesor titular asignado a la cátedra.
     * 
     * @dataProvider proveedorDocentesNoTitulares
     */
    public function testNoEsProfesorTitular($idDocenteNoTitular)
    {
        $idMateria = 6;
        $idAlumno = 9;
        $nota = 8;

        // Mock de NotaModel: registrarNota devuelve false (simulando rechazo por titularidad)
        $mockNota = $this->createMock(NotaModel::class);
        $mockNota->method('registrarNota')
            ->with($idDocenteNoTitular, $idMateria, $idAlumno, $nota)
            ->willReturn(false);

        Factories::injectMock('models', NotaModel::class, $mockNota);

        // Petición POST
        $sessionData = [
            'logueado'   => true,
            'id_usuario' => $idDocenteNoTitular,
            'id_perfil'  => 3, // Docente
        ];

        $response = $this->withSession($sessionData)
            ->post('docente/registrar-nota', [
                'id_materia'    => $idMateria,
                'id_alumno'     => $idAlumno,
                'nota'          => $nota,
                'nombre_alumno' => 'Valentina López'
            ]);

        // Aserciones: Redirección y error al guardar la nota
        $response->assertRedirectTo("/docente/alumnos/{$idMateria}");
        $response->assertSessionHas('error', 'No se pudo registrar la nota. Verificá los datos.');
    }

    /**
     * Excepción 3: El alumno no posee una inscripción activa en esa materia.
     * 
     * @dataProvider proveedorAlumnosNoInscriptos
     */
    public function testAlumnoNoInscripto($idAlumnoInvalido)
    {
        $idDocente = 6;
        $idMateria = 6;
        $nota = 8;

        // Mock de NotaModel: registrarNota devuelve false (simulando rechazo porque no hay inscripción activa)
        $mockNota = $this->createMock(NotaModel::class);
        $mockNota->method('registrarNota')
            ->with($idDocente, $idMateria, $idAlumnoInvalido, $nota)
            ->willReturn(false);

        Factories::injectMock('models', NotaModel::class, $mockNota);

        // Petición POST
        $sessionData = [
            'logueado'   => true,
            'id_usuario' => $idDocente,
            'id_perfil'  => 3, // Docente
        ];

        $response = $this->withSession($sessionData)
            ->post('docente/registrar-nota', [
                'id_materia'    => $idMateria,
                'id_alumno'     => $idAlumnoInvalido,
                'nota'          => $nota,
                'nombre_alumno' => 'Alumno Desconocido'
            ]);

        // Aserciones: Redirección y error de registro
        $response->assertRedirectTo("/docente/alumnos/{$idMateria}");
        $response->assertSessionHas('error', 'No se pudo registrar la nota. Verificá los datos.');
    }

    /**
     * Proveedores de datos para las pruebas
     */

    public static function proveedorNotasValidas(): array
    {
        return [
            'Nota mínima permitida (1)' => [1],
            'Nota intermedia (6)'       => [6],
            'Nota máxima permitida (10)' => [10],
        ];
    }

    public static function proveedorNotasInvalidas(): array
    {
        return [
            'Nota justo arriba del límite (11)' => [11],
            'Nota en cero (0)'                  => [0],
            'Nota negativa (-5)'                => [-5],
            'Nota extremadamente alta (99)'     => [99],
        ];
    }

    public static function proveedorDocentesNoTitulares(): array
    {
        return [
            'Docente ID 100' => [100],
            'Docente ID 999' => [999],
        ];
    }

    public static function proveedorAlumnosNoInscriptos(): array
    {
        return [
            'Alumno ID 999' => [999],
            'Alumno ID -1'  => [-1],
        ];
    }
}
