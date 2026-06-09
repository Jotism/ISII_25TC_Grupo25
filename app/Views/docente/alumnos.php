<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alumnos — Cargar Notas</title>
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body>

<header>
    <strong>GestAcad — Cargar Notas</strong>
    <span>
        <a href="<?= base_url('docente/materias') ?>">← Mis Materias</a>
        &nbsp;|&nbsp;
        <a href="<?= base_url('auth/logout') ?>">Cerrar sesión</a>
    </span>
</header>

<div class="contenido contenido--ancho">

    <?php if (session()->getFlashdata('mensaje')) : ?>
        <div class="mensaje-exito"><?= esc(session()->getFlashdata('mensaje')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="mensaje-error"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <h2>Alumnos de: <strong><?= esc($materia['nombre']) ?></strong></h2>

    <?php if (empty($alumnos)) : ?>
        <div class="sin-materias">No hay alumnos inscriptos en esta materia.</div>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Apellido y Nombre</th>
                    <th>Nota</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alumnos as $alumno) : ?>
                <tr>
                    <td><?= esc($alumno['dni']) ?></td>
                    <td><?= esc($alumno['apellido']) ?>, <?= esc($alumno['nombre']) ?></td>
                    <td>
                        <?= $alumno['nota'] !== null
                            ? esc($alumno['nota'])
                            : '<span style="color:#aaa">Sin nota</span>' ?>
                    </td>
                    <td>
                        <?php if ($alumno['estado'] === 'Aprobado') : ?>
                            <span class="badge" style="background:#e8f5e9; color:#27ae60">Aprobado</span>
                        <?php elseif ($alumno['estado'] === 'Desaprobado') : ?>
                            <span class="badge" style="background:#fde8e8; color:#c0392b">Desaprobado</span>
                        <?php else : ?>
                            <span style="color:#aaa">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $alumno['fecha_nota'] ? esc($alumno['fecha_nota']) : '—' ?></td>
                    <td>
                        <!--
                            Formulario individual por alumno.
                            Envía id_docente, id_materia, id_alumno y nota por POST.
                            Todo por parámetros, nada cae de arriba.
                        -->
                        <form action="<?= base_url('docente/registrar-nota') ?>" method="post"
                              style="display:flex; gap:0.5rem; align-items:center">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id_docente"    value="<?= esc($id_docente) ?>">
                            <input type="hidden" name="id_materia"    value="<?= esc($materia['id_materia']) ?>">
                            <input type="hidden" name="id_alumno"     value="<?= esc($alumno['id_usuario']) ?>">
                            <input type="hidden" name="nombre_alumno" value="<?= esc($alumno['nombre'] . ' ' . $alumno['apellido']) ?>">
                            <input type="number" name="nota" min="1" max="10"
                                   value="<?= esc($alumno['nota'] ?? '') ?>"
                                   placeholder="1-10"
                                   style="width:65px; padding:0.3rem 0.5rem; border:1px solid #ccc; border-radius:4px">
                            <button type="submit" class="btn-accion btn-accion--azul">
                                <?= $alumno['nota'] !== null ? 'Actualizar' : 'Guardar' ?>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<script>
    // Forzar recarga de página al volver atrás en el historial del navegador (bfcache)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            window.location.reload();
        }
    });
</script>

</body>
</html>