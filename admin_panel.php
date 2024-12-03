<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos base */
        body {
            display: flex;
            margin: 0;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: var(--sidebar-bg-color, #4CAF50);
            color: var(--sidebar-text-color, #ffffff);
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
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
            padding: 20px;
            background-color: var(--content-bg-color, #f8f9fa);
            color: var(--content-text-color, #212529);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Variables para modo claro */
        :root {
            --sidebar-bg-color: #4CAF50;
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
        <h2>Admin Panel</h2>
    </br>
        <a href="?page=menu_crud">Menú Reporte de Cuotas</a>
        <a href="?page=formulario">Formulario</a>
        <button id="toggle-dark-mode" class="btn btn-sm mt-3" style="width: 100%;">Modo Oscuro</button>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <?php
            // Rutas de los archivos PHP
            $page = $_GET['page'] ?? 'home';
            $files = [
                'formulario' => 'formulario.php',
                'menu_crud' => 'menu_crud.php',
                'guardar_datos' => 'guardar_datos.php',
                'reporte_vista_previa' => 'reporte_vista_previa.php',
                'editar' => 'editar.php',
                'home' => 'inicio.php', // Archivo de bienvenida
            ];

            // Incluir el archivo correspondiente
            if (array_key_exists($page, $files)) {
                include $files[$page];
            } else {
                echo "<p>Página no encontrada.</p>";
            }
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Alternar entre modo claro y oscuro
        document.getElementById('toggle-dark-mode').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            this.textContent = document.body.classList.contains('dark-mode') ? 'Modo Claro' : 'Modo Oscuro';
        });
    </script>
</body>
</html>
