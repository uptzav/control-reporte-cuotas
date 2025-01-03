
<?php
// Conectar a la base de datos
$servername = "localhost";
$username = "root";  // Usuario de MySQL
$password = "";  // Contraseña de MySQL
$dbname = "control_reporte_cuotas";  // Nombre de tu base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST['username'] ?? '';
    $password_plain = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? '';

    // Validar los datos ingresados
    if (!empty($usuario) && !empty($password_plain) && !empty($rol)) {
        // Encriptar la contraseña
        $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

        // Preparar y ejecutar la consulta para insertar el usuario
        $sql = "INSERT INTO usuarios (username, password, rol) VALUES ('$usuario', '$password_hashed', '$rol')";

        if ($conn->query($sql) === TRUE) {
            echo "Nuevo usuario creado con rol de $rol.";
        } else {
            echo "Error al crear el usuario: " . $conn->error;
        }
    } else {
        echo "Por favor, completa todos los campos.";
    }
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Crear Nuevo Usuario</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Nombre de Usuario</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol</label>
                <select class="form-select" id="rol" name="rol" required>
                    <option value="" disabled selected>Selecciona un rol</option>
                    <option value="administrador">Administrador</option>
                    <option value="usuario">Usuario</option>
                    <option value="moderador">Moderador</option>
                    <!-- Puedes agregar más roles aquí -->
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Crear Usuario</button>
        </form>
    </div>
</body>
</html>
