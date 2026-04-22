<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <!--
        VISTA: login.php
        Ubicación: app/Views/login.php
        CSS externo: public/css/styles.css
    -->
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body class="centrado">

<div class="card">
    <h2>Iniciar Sesión</h2>

    <?php if (!empty($mensaje)) : ?>
        <div class="<?= ($tipo ?? '') === 'error' ? 'mensaje-error' : 'mensaje-exito' ?>">
            <?= esc($mensaje) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('mensaje')) : ?>
        <div class="mensaje-exito"><?= esc(session()->getFlashdata('mensaje')) ?></div>
    <?php endif; ?>

    <form action="<?= base_url('auth/iniciarSesion') ?>" method="post">
        <?= csrf_field() ?>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="usuario@ejemplo.com" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>

        <button type="submit" class="btn btn--azul">Ingresar</button>
    </form>

    <div class="enlace">
        ¿No tenés cuenta? <a href="<?= base_url('auth/registro') ?>">Registrarse</a>
    </div>
</div>

</body>
</html>