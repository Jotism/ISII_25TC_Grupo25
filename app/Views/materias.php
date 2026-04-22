<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Materias Disponibles</title>
    <!--
        VISTA: materias.php
        Ubicación: app/Views/materias.php
        CSS externo: public/css/styles.css
    -->
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body>

<header>
    <strong>Sistema Académico — Materias</strong>
    <a href="<?= base_url('auth/logout') ?>">Cerrar sesión</a>
</header>

<div class="contenido">
    <h2>Materias disponibles</h2>

    <?php if (empty($materias)) : ?>
        <div class="sin-materias">No hay materias disponibles</div>
    <?php else : ?>
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
                    <td><span class="badge"><?= esc($materia['id_cuatrimestre']) ?>° cuatrimestre</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a class="volver" href="<?= base_url('dashboard') ?>">← Volver al dashboard</a>
</div>

</body>
</html>