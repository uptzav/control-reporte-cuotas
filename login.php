<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: menu_crud.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
            <div class="text-center mb-4">
                <img src="images/logo_henko.png" alt="Henko Logo" class="img-fluid" style="max-width: 150px;">
            </div>
            <h1 class="text-center mb-4">Iniciar Sesión</h1>
            <form action="procesar_login.php" method="POST" novalidate>
                <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                    <div class="invalid-feedback">Por favor ingresa tu usuario.</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="invalid-feedback">Por favor ingresa tu contraseña.</div>
                </div>
                <button type="submit" class="btn btn-success w-100">Ingresar</button>
            </form>
        </div>
    </div>

    <!-- Modal de ejemplo -->
    <div class="modal fade" id="loginErrorModal" tabindex="-1" aria-labelledby="loginErrorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginErrorModalLabel">Error de Inicio de Sesión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Usuario o contraseña incorrectos. Inténtalo de nuevo.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Bootstrap validation
        (function () {
            'use strict';
            const forms = document.querySelectorAll('form');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // Mostrar modal de error (Ejemplo)
        // const loginError = true; // Cambia esto según tu lógica del backend
        // if (loginError) {
        //     const loginErrorModal = new bootstrap.Modal(document.getElementById('loginErrorModal'));
        //     loginErrorModal.show();
        // }
    </script>
</body>
</html>
