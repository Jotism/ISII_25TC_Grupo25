<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Carreras</title>
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body>

<header>
    <strong>GestAcad — Mis Carreras</strong>
    <span>
        <a href="<?= base_url('dashboard') ?>">← Dashboard</a>
        &nbsp;|&nbsp;
        <a href="<?= base_url('auth/logout') ?>">Cerrar sesión</a>
    </span>
</header>

<div class="contenido">

    <?php if (session()->getFlashdata('mensaje')) : ?>
        <div class="mensaje-exito"><?= esc(session()->getFlashdata('mensaje')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="mensaje-error"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <h2>Mi carrera</h2>

    <?php if ($miCarrera) : ?>
        <!-- El alumno YA tiene carrera → mostrarla con opción de baja -->
        <table>
            <thead>
                <tr>
                    <th>Carrera</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong><?= esc($miCarrera['nombre']) ?></strong></td>
                    <td>
                        <a href="<?= base_url('baja-carrera/' . $miCarrera['id_carrera']) ?>"
                           class="btn-accion btn-accion--rojo"
                           onclick="return confirm('¿Seguro? También perderás tus inscripciones a materias de esta carrera.')">
                            Darme de baja
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>

        <p style="margin-top:1rem; color:#666; font-size:0.9rem">
            Para inscribirte a materias andá a
            <a href="<?= base_url('mis-materias') ?>">Mis Materias</a>.
        </p>

    <?php else : ?>
        <!-- El alumno NO tiene carrera → mostrar formulario para inscribirse -->
        <div class="sin-materias" style="margin-bottom:1.5rem">
            No estás inscripto en ninguna carrera todavía.
        </div>

        <h2>Inscribirme a una carrera</h2>
        <form action="<?= base_url('inscribirse-carrera') ?>" method="post">
            <?= csrf_field() ?>
            <label for="id_carrera">Seleccioná una carrera</label>
            <select id="id_carrera" name="id_carrera" required>
                <option value="">-- Elegí una carrera --</option>
                <?php foreach ($todasCarreras as $carrera) : ?>
                    <option value="<?= esc($carrera['id_carrera']) ?>">
                        <?= esc($carrera['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn--verde" style="margin-top:1rem">
                Inscribirme
            </button>
        </form>

    <?php endif; ?>

</div>

</body>
</html>