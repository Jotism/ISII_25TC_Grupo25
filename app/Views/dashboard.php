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

    <?php if (session()->get('id_perfil') == 1) : ?>
        <p>Estás en tu panel de admin.</p>
        <a href="<?= base_url('admin/materias') ?>" class="btn-materias" style="background:#6c3483">
            ⚙ Panel Admin
        </a>

    <?php elseif(session()->get('id_perfil') == 2) : ?>
        <p>Estás en tu panel de alumno. Podés consultar las materias disponibles.</p>
        <a href="<?= base_url('materias/obtenerMateriasDisponibles') ?>" class="btn-materias">
            Ver materias disponibles
        </a>
    <?php endif; ?>

</div>

</body>
</html>