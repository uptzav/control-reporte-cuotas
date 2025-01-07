<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Obtener el rol del usuario desde la sesión
$userRole = $_SESSION['role'] ?? 'usuario'; // Por defecto, se asume "usuario"
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Estilos base */
        body {
            display: flex;
            margin: 0;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: var(--sidebar-bg-color, rgb(32, 75, 34));
            color: var(--sidebar-text-color, #ffffff);
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .sidebar a {
            color: var(--sidebar-text-color, #ffffff);
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: var(--sidebar-hover-color, #81c784);
        }
        .content {
            flex-grow: 1;
            background-color: var(--content-bg-color, #f8f9fa);
            color: var(--content-text-color, #212529);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        /* Variables para modo claro */
        :root {
            --sidebar-bg-color: rgb(51, 107, 52);
            --sidebar-text-color: #ffffff;
            --sidebar-hover-color: #81c784;
            --content-bg-color: #f8f9fa;
            --content-text-color: #212529;
        }
        /* Variables para modo oscuro */
        .dark-mode {
            --sidebar-bg-color: #333333;
            --sidebar-text-color: #cccccc;
            --sidebar-hover-color: #555555;
            --content-bg-color: #121212;
            --content-text-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Barra lateral -->
    <div class="sidebar">
        <!-- Logo redondo -->
        <img src="images/logo_blanco_henko.jpg" alt="Logo Henko" style="width: 120px; height: 120px; border-radius: 50%; display: block; margin: 0 auto; margin-bottom: 10px; object-fit: cover;">

        <br><br>
        <a href="?page=menu_crud">
            <i class="bi bi-file-earmark-text"></i> Reporte de Cuotas
        </a>
        <a href="?page=recordatorio">
            <i class="bi bi-calendar-check"></i> Recordatorio de Pagos
        </a>

        <!-- Mostrar el enlace de "Usuarios" solo si el rol es "administrador" -->
        <?php if ($userRole === 'administrador'): ?>
            <a href="?page=usuarios">
                <i class="bi bi-people"></i> Usuarios
            </a>
        <?php endif; ?>

        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

        <button id="toggle-dark-mode" class="btn btn-sm mt-3 btn-light w-100">
            <i class="bi bi-moon"></i> Modo Oscuro
        </button>
        <footer>
            <a href="logout.php" class="btn btn-dark w-100 mt-3">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </footer>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <?php
        $page = htmlspecialchars($_GET['page'] ?? 'menu_crud');
        $files = [
            'menu_crud' => 'menu_crud.php',
            'recordatorio' => 'recordatorio.php',
            // La página "usuarios" solo debe cargarse si el rol es "administrador"
            'usuarios' => $userRole === 'administrador' ? 'usuarios.php' : null,
        ];

        if (array_key_exists($page, $files) && $files[$page] !== null) {
            include $files[$page];
        } else {
            echo "<div class='alert alert-danger'>No tienes permiso para acceder a esta página o no existe.</div>";
        }
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('toggle-dark-mode').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const icon = this.querySelector('i');
            if (document.body.classList.contains('dark-mode')) {
                this.textContent = ' Modo Claro';
                icon.className = 'bi bi-sun';
            } else {
                this.textContent = ' Modo Oscuro';
                icon.className = 'bi bi-moon';
            }
        });
    </script>
</body>
</html>
