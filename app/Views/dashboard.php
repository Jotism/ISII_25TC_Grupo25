<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body>

<header>
    <strong>Sistema Académico</strong>
    <span>
        Hola, <?= esc(session()->get('nombre')) ?> &nbsp;|&nbsp;
        <a href="<?= base_url('auth/logout') ?>">Cerrar sesión</a>
    </span>
</header>

<div class="contenido contenido--centrado">

    <?php if (session()->getFlashdata('mensaje')) : ?>
        <div class="mensaje-exito"><?= esc(session()->getFlashdata('mensaje')) ?></div>
    <?php endif; ?>

    <h1>Bienvenido</h1>
    <p>Estás en tu panel de alumno. Podés consultar las materias disponibles.</p>

    <a href="<?= base_url('materias/obtenerMateriasDisponibles') ?>" class="btn-materias">
        Ver materias disponibles
    </a>

</div>

</body>
</html>