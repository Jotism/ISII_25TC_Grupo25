<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
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
            max-width: 420px;
        }
        h2 { margin-bottom: 1.5rem; text-align: center; color: #333; }
        label { display: block; margin-bottom: 0.3rem; color: #555; font-size: 0.9rem; }
        input[type="text"],
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
            background: #27ae60;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
        }
        button:hover { background: #219a52; }
        .enlace { text-align: center; margin-top: 1rem; font-size: 0.9rem; }
        .enlace a { color: #3a7bd5; text-decoration: none; }
        .nota { font-size: 0.8rem; color: #888; margin-top: -0.7rem; margin-bottom: 1rem; }
    </style>
</head>
<body>

<div class="card">
    <h2>Crear cuenta</h2>

    <!-- Formulario de registro: envía POST al método guardarRegistro() -->
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
        <!-- Nota: id_perfil = 2 (Alumno) se asigna en el controlador, no en el formulario -->

        <button type="submit">Registrarse</button>
    </form>

    <div class="enlace">
        ¿Ya tenés cuenta? <a href="<?= base_url('auth/login') ?>">Iniciar sesión</a>
    </div>
</div>

</body>
</html>