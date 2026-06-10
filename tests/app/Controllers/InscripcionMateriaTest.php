<?php

namespace Tests\app\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Config\Factories;
use App\Models\InscripcionCarreraModel;
use App\Models\InscripcionMateriaModel;
use App\Models\MateriaModel;
use App\Models\CarreraModel;
use App\Models\MateriaCarreraModel;

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
     * Caso Correcto: Envío de GET válido. Registrar la inscripción exitosa.
     * 
     * @dataProvider proveedorInscripcionesExitosas
     */
    public function testInscripcionExitosa($idUsuario, $idMateria, $materiaNombre)
    {
        // 1. Mock de InscripcionCarreraModel
        $mockInscripcionCarrera = $this->createMock(InscripcionCarreraModel::class);
        $mockInscripcionCarrera->method('consultarCarrerasInscripto')
            ->with($idUsuario)
            ->willReturn([
                ['id_usuario' => $idUsuario, 'id_carrera' => 1]
            ]);
        Factories::injectMock('models', InscripcionCarreraModel::class, $mockInscripcionCarrera);

        // 2. Mock de CarreraModel
        $mockCarrera = $this->createMock(CarreraModel::class);
        $mockCarrera->method('obtenerCarreraPorId')
            ->with(1)
            ->willReturn(['id_carrera' => 1, 'nombre' => 'Tecnicatura en Programación']);
        Factories::injectMock('models', CarreraModel::class, $mockCarrera);

        // 3. Mock de MateriaCarreraModel
        $mockMateriaCarrera = $this->createMock(MateriaCarreraModel::class);
        $mockMateriaCarrera->method('obtenerCarrerasPorMateria')
            ->with($idMateria)
            ->willReturn([
                ['id_materia' => $idMateria, 'id_carrera' => 1]
            ]);
        Factories::injectMock('models', MateriaCarreraModel::class, $mockMateriaCarrera);

        // 4. Mock de MateriaModel
        $mockMateria = $this->createMock(MateriaModel::class);
        $mockMateria->method('obtenerNombreMateria')
            ->with($idMateria)
            ->willReturn($materiaNombre);
        Factories::injectMock('models', MateriaModel::class, $mockMateria);

        // 5. Mock de InscripcionMateriaModel
        $mockInscripcionMateria = $this->createMock(InscripcionMateriaModel::class);
        $mockInscripcionMateria->method('generarInscripcion')
            ->with($idUsuario, $idMateria)
            ->willReturn(true);
        Factories::injectMock('models', InscripcionMateriaModel::class, $mockInscripcionMateria);

        // 6. Realizar petición GET simulando sesión del alumno activa
        $sessionData = [
            'logueado'   => true,
            'id_usuario' => $idUsuario,
            'id_perfil'  => 2, // Alumno
            'email'      => 'alumno@ejemplo.com',
        ];

        $response = $this->withSession($sessionData)
            ->get("alumno/inscribirse-materia/{$idMateria}");

        // 7. Aserciones: Redirección correcta y flashdata cargado
        $response->assertRedirectTo("/alumno/mis-materias/");
        $response->assertSessionHas('nombre_materia', $materiaNombre);
    }

    /**
     * Excepción 1: Alumno ya inscripto. Validar que falle con el mensaje correspondiente.
     * 
     * @dataProvider proveedorInscripcionesDuplicadas
     */
    public function testAlumnoYaInscripto($idUsuario, $idMateria)
    {
        // 1. Mock de InscripcionCarreraModel
        $mockInscripcionCarrera = $this->createMock(InscripcionCarreraModel::class);
        $mockInscripcionCarrera->method('consultarCarrerasInscripto')
            ->with($idUsuario)
            ->willReturn([
                ['id_usuario' => $idUsuario, 'id_carrera' => 1]
            ]);
        Factories::injectMock('models', InscripcionCarreraModel::class, $mockInscripcionCarrera);

        // 2. Mock de CarreraModel
        $mockCarrera = $this->createMock(CarreraModel::class);
        $mockCarrera->method('obtenerCarreraPorId')
            ->with(1)
            ->willReturn(['id_carrera' => 1, 'nombre' => 'Tecnicatura en Programación']);
        Factories::injectMock('models', CarreraModel::class, $mockCarrera);

        // 3. Mock de MateriaCarreraModel
        $mockMateriaCarrera = $this->createMock(MateriaCarreraModel::class);
        $mockMateriaCarrera->method('obtenerCarrerasPorMateria')
            ->with($idMateria)
            ->willReturn([
                ['id_materia' => $idMateria, 'id_carrera' => 1]
            ]);
        Factories::injectMock('models', MateriaCarreraModel::class, $mockMateriaCarrera);

        // 4. Mock de InscripcionMateriaModel
        $mockInscripcionMateria = $this->createMock(InscripcionMateriaModel::class);
        $mockInscripcionMateria->method('generarInscripcion')
            ->with($idUsuario, $idMateria)
            ->willReturn(false);
        Factories::injectMock('models', InscripcionMateriaModel::class, $mockInscripcionMateria);

        // 5. Realizar petición GET
        $sessionData = [
            'logueado'   => true,
            'id_usuario' => $idUsuario,
            'id_perfil'  => 2, // Alumno
            'email'      => 'alumno@ejemplo.com',
        ];

        $response = $this->withSession($sessionData)
            ->get("alumno/inscribirse-materia/{$idMateria}");

        // 6. Aserciones: Redirección con mensaje de error adecuado en flashdata
        $response->assertRedirectTo("/alumno/mis-materias/");
        $response->assertSessionHas('error', 'Ya estás inscripto en esa materia.');
    }

    /**
     * Excepción 2: Materia que no pertenece a la carrera del alumno. Validar que el sistema ataje el error.
     * 
     * @dataProvider proveedorMateriasAjenas
     */
    public function testMateriaNoPerteneceACarrera($idUsuario, $idMateria)
    {
        // 1. Mock de InscripcionCarreraModel
        $mockInscripcionCarrera = $this->createMock(InscripcionCarreraModel::class);
        $mockInscripcionCarrera->method('consultarCarrerasInscripto')
            ->with($idUsuario)
            ->willReturn([
                ['id_usuario' => $idUsuario, 'id_carrera' => 1]
            ]);
        Factories::injectMock('models', InscripcionCarreraModel::class, $mockInscripcionCarrera);

        // 2. Mock de CarreraModel
        $mockCarrera = $this->createMock(CarreraModel::class);
        $mockCarrera->method('obtenerCarreraPorId')
            ->with(1)
            ->willReturn(['id_carrera' => 1, 'nombre' => 'Tecnicatura en Programación']);
        Factories::injectMock('models', CarreraModel::class, $mockCarrera);

        // 3. Mock de MateriaCarreraModel (devuelve arreglo vacío, es decir, no pertenece a la carrera 1)
        $mockMateriaCarrera = $this->createMock(MateriaCarreraModel::class);
        $mockMateriaCarrera->method('obtenerCarrerasPorMateria')
            ->with($idMateria)
            ->willReturn([]);
        Factories::injectMock('models', MateriaCarreraModel::class, $mockMateriaCarrera);

        // 4. Mock de InscripcionMateriaModel para evitar conexión real
        $mockInscripcionMateria = $this->createMock(InscripcionMateriaModel::class);
        $mockInscripcionMateria->expects($this->never())->method('generarInscripcion');
        Factories::injectMock('models', InscripcionMateriaModel::class, $mockInscripcionMateria);

        // 5. Realizar petición GET
        $sessionData = [
            'logueado'   => true,
            'id_usuario' => $idUsuario,
            'id_perfil'  => 2, // Alumno
            'email'      => 'alumno@ejemplo.com',
        ];

        $response = $this->withSession($sessionData)
            ->get("alumno/inscribirse-materia/{$idMateria}");

        // 6. Aserciones: Redirección con error de pertenencia en flashdata
        $response->assertRedirectTo("/alumno/mis-materias/");
        $response->assertSessionHas('error', 'La materia no pertenece a tu carrera.');
    }

    /**
     * Proveedores de datos para las pruebas
     */
    public static function proveedorInscripcionesExitosas(): array
    {
        return [
            'Inscripción Alumno 9 en Programación I (ID 6)' => [9, 6, 'Programación I'],
            'Inscripción Alumno 10 en Álgebra I (ID 7)'     => [10, 7, 'Álgebra I'],
        ];
    }

    public static function proveedorInscripcionesDuplicadas(): array
    {
        return [
            'Alumno 9 ya inscripto en Materia 6'  => [9, 6],
            'Alumno 12 ya inscripto en Materia 8' => [12, 8],
        ];
    }

    public static function proveedorMateriasAjenas(): array
    {
        return [
            'Materia ajena ID 99 para Alumno 9'   => [9, 99],
            'Materia ajena ID 105 para Alumno 10' => [10, 105],
        ];
    }
}
