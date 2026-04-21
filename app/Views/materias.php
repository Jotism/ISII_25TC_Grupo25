<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Materias Disponibles</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
        }
        header {
            background: #3a7bd5;
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header a { color: #fff; text-decoration: underline; font-size: 0.9rem; }
        .contenido {
            max-width: 700px;
            margin: 2rem auto;
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 { margin-bottom: 1.5rem; color: #333; }
        .sin-materias { color: #888; text-align: center; padding: 2rem; font-size: 1.1rem; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #3a7bd5; color: #fff; }
        th, td { padding: 0.7rem 1rem; text-align: left; border-bottom: 1px solid #eee; }
        tr:hover td { background: #f7f9ff; }
        .volver {
            display: inline-block;
            margin-top: 1.5rem;
            color: #3a7bd5;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .volver:hover { text-decoration: underline; }
        .badge {
            display: inline-block;
            background: #e8f0fe;
            color: #3a7bd5;
            border-radius: 12px;
            padding: 0.1rem 0.6rem;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<!-- Barra superior -->
<header>
    <strong>GestAcad — Materias</strong>
    <a href="<?= base_url('auth/logout') ?>">Cerrar sesión</a>
</header>

<div class="contenido">
    <h2>Materias disponibles</h2>

    <?php if (empty($materias)) : ?>
        <!-- Mensaje requerido por el enunciado cuando no hay materias -->
        <div class="sin-materias">No hay materias disponibles</div>

    <?php else : ?>
        <!-- Tabla con las materias retornadas por el modelo -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Materia</th>
                    <th>Año de cursada</th>
                    <th>Cuatrimestre</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materias as $materia) : ?>
                <tr>
                    <td><?= esc($materia['id_materia']) ?></td>
                    <td><?= esc($materia['nombre']) ?></td>
                    <td><?= esc($materia['anio_cursada']) ?>°</td>
                    <td>
                        <span class="badge"><?= esc($materia['id_cuatrimestre']) ?>° cuatrimestre</span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

    <!-- Volver al dashboard -->
    <a class="volver" href="<?= base_url('dashboard') ?>">← Volver al dashboard</a>
</div>

</body>
</html>