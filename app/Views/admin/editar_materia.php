<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Materia</title>
    <!--
        VISTA: editar_materia.php
        Ubicación: app/Views/admin/editar_materia.php
        Formulario pre-cargado con los datos actuales de la materia.
        Recibe:
          - $materia  (array) → datos actuales de la materia
          - $carreras (array) → todas las carreras para el dropdown
    -->
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body>

<header>
    <strong>Panel Admin — Editar Materia</strong>
    <span>
        <a href="<?= base_url('admin/materias') ?>">← Volver al listado</a>
        &nbsp;|&nbsp;
        <a href="<?= base_url('auth/logout') ?>">Cerrar sesión</a>
    </span>
</header>

<div class="contenido">
    <h2>Editar materia</h2>

    <!--
        El formulario envía POST a actualizarMateria($id)
        Los campos vienen pre-cargados con los valores de $materia
        El "selected" del dropdown compara id_carrera actual con cada opción
    -->
    <form action="<?= base_url('admin/materias/actualizar/' . $materia['id_materia']) ?>" method="post">
        <?= csrf_field() ?>

        <label for="nombre">Nombre de la materia</label>
        <input type="text" id="nombre" name="nombre"
               value="<?= esc($materia['nombre']) ?>" required>

        <label for="anio_cursada">Año de cursada</label>
        <input type="text" id="anio_cursada" name="anio_cursada"
               value="<?= esc($materia['anio_cursada']) ?>" required>

        <label for="id_cuatrimestre">Cuatrimestre</label>
        <input type="text" id="id_cuatrimestre" name="id_cuatrimestre"
               value="<?= esc($materia['id_cuatrimestre']) ?>" required>

        <!--
            Dropdown de carrera: marca como "selected" la carrera actual.
            Comparamos $materia['id_carrera'] con cada $carrera['id_carrera'].
        -->
        <label for="id_carrera">Carrera</label>
        <select id="id_carrera" name="id_carrera" required>
            <option value="">-- Seleccioná una carrera --</option>
            <?php foreach ($carreras as $carrera) : ?>
                <option value="<?= esc($carrera['id_carrera']) ?>"
                    <?= ($carrera['id_carrera'] == $materia['id_carrera']) ? 'selected' : '' ?>>
                    <?= esc($carrera['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn--azul" style="margin-top:1rem">Actualizar materia</button>
    </form>
</div>

</body>
</html>