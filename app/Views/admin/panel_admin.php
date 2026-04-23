!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin — Materias</title>
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body>

<header>
    <strong>Panel Admin — Gestión de Materias</strong>
    <span>
        <a href="<?= base_url('dashboard') ?>">← Dashboard</a>
        &nbsp;|&nbsp;
        <a href="<?= base_url('auth/logout') ?>">Cerrar sesión</a>
    </span>
</header>

<div class="contenido contenido--ancho">

    <!-- Mensaje flash de éxito (después de crear, editar o eliminar) -->
    <?php if (session()->getFlashdata('mensaje')) : ?>
        <div class="mensaje-exito"><?= esc(session()->getFlashdata('mensaje')) ?></div>
    <?php endif; ?>

    <!-- Título y botón para crear nueva materia -->
    <div class="panel-encabezado">
        <h2>Listado de materias</h2>
        <a href="<?= base_url('admin/materias/crear') ?>" class="btn-accion btn-accion--verde">
            + Nueva materia
        </a>
    </div>

    <?php if (empty($materias)) : ?>
        <div class="sin-materias">No hay materias cargadas todavía.</div>
    <?php else : ?>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Año</th>
                    <th>Cuatrimestre</th>
                    <th>Carrera</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materias as $m) : ?>
                <tr>
                    <td><?= esc($m['id_materia']) ?></td>
                    <td><?= esc($m['nombre']) ?></td>
                    <td><?= esc($m['anio_cursada']) ?>°</td>
                    <td><span class="badge"><?= esc($m['id_cuatrimestre']) ?>° cuat.</span></td>
                    <td><?= esc($m['nombre_carrera'] ?? '—') ?></td>
                    <td class="acciones">
                        <!-- Botón editar: va al formulario de edición -->
                        <a href="<?= base_url('admin/materias/editar/' . $m['id_materia']) ?>"
                           class="btn-accion btn-accion--azul">
                            Editar
                        </a>

                        <!-- Botón eliminar: pide confirmación antes de borrar -->
                        <a href="<?= base_url('admin/materias/eliminar/' . $m['id_materia']) ?>"
                           class="btn-accion btn-accion--rojo"
                           onclick="return confirm('¿Seguro que querés eliminar esta materia?')">
                            Eliminar
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