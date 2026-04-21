<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
        }
        header {
            background: #3a7bd5;
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header span { font-size: 0.9rem; }
        header a { color: #fff; text-decoration: underline; font-size: 0.9rem; }
        .contenido {
            max-width: 600px;
            margin: 3rem auto;
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 { color: #333; margin-bottom: 0.5rem; }
        p  { color: #666; margin-bottom: 2rem; }
        .btn-materias {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: #3a7bd5;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            font-size: 1rem;
        }
        .btn-materias:hover { background: #2f63b0; }
        .mensaje-exito { background: #e8f5e9; color: #27ae60; padding: 0.6rem; border-radius: 4px; margin-bottom: 1.5rem; }
    </style>
</head>
<body>

<!-- Barra superior con nombre de usuario y logout -->
<header>
    <strong>Sistema Académico</strong>
    <span>
        Hola, <?= esc(session()->get('nombre')) ?> &nbsp;|&nbsp;
        <a href="<?= base_url('auth/logout') ?>">Cerrar sesión</a>
    </span>
</header>

<div class="contenido">

    <!-- Mensaje "Acceso autorizado" pasado por redirect()->with() desde el controlador -->
    <?php if (session()->getFlashdata('mensaje')) : ?>
        <div class="mensaje-exito"><?= esc(session()->getFlashdata('mensaje')) ?></div>
    <?php endif; ?>

    <!-- Mensaje principal del dashboard -->
    <h1>Bienvenido</h1>
    <p>Estás en tu panel de alumno. Podés consultar las materias disponibles.</p>

    <!-- Botón que llama a obtenerMateriasDisponibles() en el controlador Materias -->
    <a href="<?= base_url('materias/obtenerMateriasDisponibles') ?>" class="btn-materias">
        Ver materias disponibles
    </a>

</div>

</body>
</html>