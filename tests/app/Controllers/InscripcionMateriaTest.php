<?php

namespace Tests\app\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Config\Factories;
use App\Models\InscripcionCarreraModel;
use App\Models\MateriaModel;

/**
 * @internal
 */
class InscripcionMateriaTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        $this->resetServices();
        Factories::reset();
        parent::setUp();
    }

    /**
     * Caso Feliz: Envío de POST válido. Registrar la inscripción exitosa.
     */
    public function testInscripcionExitosa()
    {
        $idUsuario = 9;
        $idMateria = 6;
        $materiaNombre = 'Programación I';

        // 1. Mock de InscripcionCarreraModel para simular que el alumno pertenece a una carrera
        $mockInscripcion = $this->createMock(InscripcionCarreraModel::class);
        $mockInscripcion->method('consultarCarrerasInscripto')
            ->with($idUsuario)
            ->willReturn([
                ['id_carrera' => 1, 'nombre' => 'Tecnicatura en Programación']
            ]);
        Factories::injectMock('models', InscripcionCarreraModel::class, $mockInscripcion);

        // 2. Mock de MateriaModel
        $mockMateria = $this->createMock(MateriaModel::class);
        
        // Simular que la materia pertenece a la carrera del alumno
        $mockMateria->method('perteneceACarrera')
            ->with($idMateria, 1)
            ->willReturn(true);
            
        // Simular inscripción exitosa
        $mockMateria->method('generarInscripcion')
            ->with($idUsuario, $idMateria)
            ->willReturn(true);
            
        // Simular obtener el nombre de la materia para el comprobante
        $mockMateria->method('obtenerNombreMateria')
            ->with($idMateria)
            ->willReturn($materiaNombre);

        Factories::injectMock('models', MateriaModel::class, $mockMateria);

        // 3. Realizar petición POST simulando sesión del alumno activa
        $sessionData = [
            'logueado'   => true,
            'id_usuario' => $idUsuario,
        ];

        $response = $this->withSession($sessionData)
            ->post('inscribirse-materia', [
                'id_materia' => $idMateria
            ]);

        // 4. Aserciones: Redirección correcta y flashdata cargado
        $response->assertRedirectTo('/mis-materias');
        $response->assertSessionHas('nombre_materia', $materiaNombre);
    }

    /**
     * Excepción 1: Alumno ya inscripto. Validar que falle con el mensaje correspondiente.
     */
    public function testAlumnoYaInscripto()
    {
        $idUsuario = 9;
        $idMateria = 6;

        // 1. Mock de InscripcionCarreraModel
        $mockInscripcion = $this->createMock(InscripcionCarreraModel::class);
        $mockInscripcion->method('consultarCarrerasInscripto')
            ->with($idUsuario)
            ->willReturn([
                ['id_carrera' => 1, 'nombre' => 'Tecnicatura en Programación']
            ]);
        Factories::injectMock('models', InscripcionCarreraModel::class, $mockInscripcion);

        // 2. Mock de MateriaModel
        $mockMateria = $this->createMock(MateriaModel::class);
        
        // Simular que la materia pertenece a la carrera
        $mockMateria->method('perteneceACarrera')
            ->with($idMateria, 1)
            ->willReturn(true);
            
        // Simular que ya está inscripto (generarInscripcion retorna false)
        $mockMateria->method('generarInscripcion')
            ->with($idUsuario, $idMateria)
            ->willReturn(false);

        Factories::injectMock('models', MateriaModel::class, $mockMateria);

        // 3. Realizar petición POST
        $sessionData = [
            'logueado'   => true,
            'id_usuario' => $idUsuario,
        ];

        $response = $this->withSession($sessionData)
            ->post('inscribirse-materia', [
                'id_materia' => $idMateria
            ]);

        // 4. Aserciones: Redirección con mensaje de error adecuado en flashdata
        $response->assertRedirectTo('/mis-materias');
        $response->assertSessionHas('error', 'Ya estás inscripto en esa materia.');
    }

    /**
     * Excepción 2: Materia que no pertenece a la carrera del alumno. Validar que el sistema ataje el error.
     */
    public function testMateriaNoPerteneceACarrera()
    {
        $idUsuario = 9;
        $idMateria = 99; // materia ajena

        // 1. Mock de InscripcionCarreraModel
        $mockInscripcion = $this->createMock(InscripcionCarreraModel::class);
        $mockInscripcion->method('consultarCarrerasInscripto')
            ->with($idUsuario)
            ->willReturn([
                ['id_carrera' => 1, 'nombre' => 'Tecnicatura en Programación']
            ]);
        Factories::injectMock('models', InscripcionCarreraModel::class, $mockInscripcion);

        // 2. Mock de MateriaModel
        $mockMateria = $this->createMock(MateriaModel::class);
        
        // Simular que la materia NO pertenece a la carrera (retorna false)
        $mockMateria->method('perteneceACarrera')
            ->with($idMateria, 1)
            ->willReturn(false);

        Factories::injectMock('models', MateriaModel::class, $mockMateria);

        // 3. Realizar petición POST
        $sessionData = [
            'logueado'   => true,
            'id_usuario' => $idUsuario,
        ];

        $response = $this->withSession($sessionData)
            ->post('inscribirse-materia', [
                'id_materia' => $idMateria
            ]);

        // 4. Aserciones: Redirección con error de pertenencia en flashdata
        $response->assertRedirectTo('/mis-materias');
        $response->assertSessionHas('error', 'La materia no pertenece a tu carrera.');
    }
}
