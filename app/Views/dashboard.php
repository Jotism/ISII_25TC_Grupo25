<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body>

<header>
    <strong>GestAcad</strong>
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

    <?php elseif (session()->get('id_perfil') == 2) : ?>

        <p>¿Qué querés gestionar hoy?</p>

        <!-- Botón 1: Mis Carreras -->
        <a href="<?= base_url('mis-carreras') ?>" class="btn-materias" style="background:#2980b9; display:block; margin-bottom:1rem">
            🎓 Mis Carreras
        </a>

        <!-- Botón 2: Mis Materias -->
        <a href="<?= base_url('mis-materias') ?>" class="btn-materias" style="display:block">
            📚 Mis Materias
        </a>

    <?php endif; ?>

</div>

</body>
</html>