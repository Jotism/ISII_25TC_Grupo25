<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <!--
        VISTA: registro.php
        Ubicación: app/Views/registro.php
        CSS externo: public/css/styles.css
    -->
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body class="centrado">

<div class="card card--wide">
    <h2>Crear cuenta</h2>

    <form action="<?= base_url('auth/guardarRegistro') ?>" method="post">
        <?= csrf_field() ?>

        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" placeholder="Juan" required>

        <label for="apellido">Apellido</label>
        <input type="text" id="apellido" name="apellido" placeholder="Pérez" required>

        <label for="dni">DNI</label>
        <input type="text" id="dni" name="dni" placeholder="12345678" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="juan@ejemplo.com" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>

        <button type="submit" class="btn btn--verde">Registrarse</button>
    </form>

    <div class="enlace">
        ¿Ya tenés cuenta? <a href="<?= base_url('auth/login') ?>">Iniciar sesión</a>
    </div>
</div>

</body>
</html>