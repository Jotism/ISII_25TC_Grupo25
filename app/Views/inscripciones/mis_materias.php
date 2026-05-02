<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Materias</title>
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

<div class="contenido contenido--ancho">

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="mensaje-error"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('nombre_materia')) : ?>
        <div class="comprobante">
            <h3>✅ Inscripción exitosa</h3>
            <p><strong>Materia:</strong> <?= esc(session()->getFlashdata('nombre_materia')) ?></p>
            <p><strong>Fecha:</strong> <?= date('d/m/Y') ?></p>
            <p><strong>Carrera:</strong> <?= esc($miCarrera['nombre']) ?></p>
        </div>
    <?php endif; ?>

    <p style="color:#666; margin-bottom:1.5rem">
        Carrera: <strong><?= esc($miCarrera['nombre']) ?></strong>
    </p>

    <!-- SECCIÓN 1: Materias en las que ya estoy inscripto -->
    <h2>Materias inscriptas</h2>

    <?php if (empty($materiasInscripto)) : ?>
        <div class="sin-materias" style="margin-bottom:2rem">
            Todavía no estás inscripto en ninguna materia.
        </div>
    <?php else : ?>
        <table style="margin-bottom:2rem">
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Cuatrimestre</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materiasInscripto as $m) : ?>
                <tr>
                    <td><?= esc($m['nombre']) ?></td>
                    <td><span class="badge"><?= esc($m['id_cuatrimestre']) ?>° cuat.</span></td>
                    <td>
                        <a href="<?= base_url('baja-materia/' . $m['id_materia']) ?>"
                           class="btn-accion btn-accion--rojo"
                           onclick="return confirm('¿Seguro que querés darte de baja de <?= esc($m['nombre']) ?>?')">
                            Darme de baja
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- SECCIÓN 2: Materias disponibles para inscribirse -->
    <h2>Materias disponibles</h2>

    <?php if (empty($materiasDisponibles)) : ?>
        <div class="sin-materias">
            No hay materias disponibles. Ya estás inscripto en todas.
        </div>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Cuatrimestre</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materiasDisponibles as $m) : ?>
                <tr>
                    <td><?= esc($m['nombre']) ?></td>
                    <td><span class="badge"><?= esc($m['id_cuatrimestre']) ?>° cuat.</span></td>
                    <td>
                        <form action="<?= base_url('inscribirse-materia') ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id_materia" value="<?= esc($m['id_materia']) ?>">
                            <button type="submit" class="btn-accion btn-accion--verde">
                                Inscribirme
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

</body>
</html>