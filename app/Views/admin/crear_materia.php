<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Materia</title>
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body>

<header>
    <strong>Panel Admin — Nueva Materia</strong>
    <span>
        <a href="<?= base_url('admin/materias') ?>">← Volver al listado</a>
        &nbsp;|&nbsp;
        <a href="<?= base_url('auth/logout') ?>">Cerrar sesión</a>
    </span>
</header>

<div class="contenido">
    <h2>Crear nueva materia</h2>

    <!--
        El formulario envía POST a guardarMateria()
        Incluye csrf_field() para seguridad (requerido por CI4)
    -->
    <form action="<?= base_url('admin/materias/guardar') ?>" method="post">
        <?= csrf_field() ?>

        <label for="nombre">Nombre de la materia</label>
        <input type="text" id="nombre" name="nombre" placeholder="Ej: Programación I" required>

        <label for="anio_cursada">Año de cursada</label>
        <input type="text" id="anio_cursada" name="anio_cursada" placeholder="Ej: 1" required>

        <label for="id_cuatrimestre">Cuatrimestre</label>
        <input type="text" id="id_cuatrimestre" name="id_cuatrimestre" placeholder="1 o 2" required>

        <!--
            Dropdown de carrera: se genera dinámicamente desde $carreras.
            Al guardar, se usa este id_carrera para insertar en materia_carrera.
        -->
        <label for="id_carrera">Carrera</label>
        <select id="id_carrera" name="id_carrera" required>
            <option value="">-- Seleccioná una carrera --</option>
            <?php foreach ($carreras as $carrera) : ?>
                <option value="<?= esc($carrera['id_carrera']) ?>">
                    <?= esc($carrera['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn--verde" style="margin-top:1rem">Guardar materia</button>
    </form>
</div>

</body>
</html>