<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Materias — Docente</title>
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body>

<header>
    <strong>GestAcad — Mis Materias</strong>
    <span>
        <a href="<?= base_url('dashboard') ?>">← Dashboard</a>
        &nbsp;|&nbsp;
        <a href="<?= base_url('auth/logout') ?>">Cerrar sesión</a>
    </span>
</header>

<div class="contenido">
    <h2>Mis materias</h2>

    <?php if (empty($materias)) : ?>
        <div class="sin-materias">No tenés materias asignadas todavía.</div>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Año</th>
                    <th>Cuatrimestre</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materias as $m) : ?>
                <tr>
                    <td><?= esc($m['nombre']) ?></td>
                    <td><?= esc($m['anio_cursada']) ?>°</td>
                    <td><span class="badge"><?= esc($m['id_cuatrimestre']) ?>° cuat.</span></td>
                    <td>
                        <a href="<?= base_url('docente/alumnos/' . $m['id_materia']) ?>"
                           class="btn-accion btn-accion--azul">
                            Ver alumnos y cargar notas
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>