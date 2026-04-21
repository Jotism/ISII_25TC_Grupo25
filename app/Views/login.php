<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 380px;
        }
        h2 { margin-bottom: 1.5rem; text-align: center; color: #333; }
        label { display: block; margin-bottom: 0.3rem; color: #555; font-size: 0.9rem; }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-size: 1rem;
        }
        button {
            width: 100%;
            padding: 0.7rem;
            background: #3a7bd5;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
        }
        button:hover { background: #2f63b0; }
        .mensaje-error  { background: #fde8e8; color: #c0392b; padding: 0.6rem; border-radius: 4px; margin-bottom: 1rem; text-align: center; }
        .mensaje-exito  { background: #e8f5e9; color: #27ae60; padding: 0.6rem; border-radius: 4px; margin-bottom: 1rem; text-align: center; }
        .enlace { text-align: center; margin-top: 1rem; font-size: 0.9rem; }
        .enlace a { color: #3a7bd5; text-decoration: none; }
    </style>
</head>
<body>

<div class="card">
    <h2>Iniciar Sesión</h2>

    <!-- Mostrar mensaje si viene desde el controlador o desde redirect()->with() -->
    <?php
    // Mensaje pasado directamente (ej: credenciales inválidas)
    if (!empty($mensaje)) :
        $clase = ($tipo ?? '') === 'error' ? 'mensaje-error' : 'mensaje-exito';
    ?>
        <div class="<?= $clase ?>"><?= esc($mensaje) ?></div>
    <?php endif; ?>

    <!-- Mensaje pasado por redirect()->with() (ej: registro exitoso) -->
    <?php if (session()->getFlashdata('mensaje')) : ?>
        <div class="mensaje-exito"><?= esc(session()->getFlashdata('mensaje')) ?></div>
    <?php endif; ?>

    <!-- Formulario de login: envía POST al método iniciarSesion() -->
    <form action="<?= base_url('auth/iniciarSesion') ?>" method="post">
        <?= csrf_field() ?>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="usuario@ejemplo.com" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>

        <button type="submit">Ingresar</button>
    </form>

    <div class="enlace">
        ¿No tenés cuenta? <a href="<?= base_url('auth/registro') ?>">Registrarse</a>
    </div>
</div>

</body>
</html>