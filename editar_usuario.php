<?php
require_once __DIR__ . '/vendor/autoload.php'; // Asegúrate de incluir el autoloader de Composer

use Dotenv\Dotenv;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtener configuración de la base de datos desde el archivo .env
$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_DATABASE'];

// Conectar a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del usuario
$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID de usuario no especificado.");
}

// Obtener los datos del usuario
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Usuario no encontrado.");
}

$usuario = $result->fetch_assoc();

// Procesar el formulario de actualización
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $rol = $_POST['rol'] ?? '';
    $password_plain = $_POST['password'] ?? null; // Campo para la nueva contraseña

    if (!empty($username) && !empty($rol)) {
        // Comenzar la consulta de actualización
        $sql_update = "UPDATE usuarios SET username = ?, rol = ?";
        $params = [$username, $rol];
        $types = "ss";

        // Si se ingresó una nueva contraseña, actualizarla
        if (!empty($password_plain)) {
            $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);
            $sql_update .= ", password = ?";
            $params[] = $password_hashed;
            $types .= "s";
        }

        // Finalizar la consulta
        $sql_update .= " WHERE id = ?";
        $params[] = $id;
        $types .= "i";

        // Preparar y ejecutar la consulta
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param($types, ...$params);

        if ($stmt_update->execute()) {
            echo "<script>alert('Usuario actualizado exitosamente.'); window.location.href = 'usuarios.php';</script>";
        } else {
            echo "Error al actualizar el usuario: " . $conn->error;
        }
    } else {
        echo "Por favor, completa todos los campos obligatorios.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Usuario</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nombre de Usuario</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($usuario['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol</label>
                <select class="form-select" id="rol" name="rol" required>
                    <option value="administrador" <?php echo $usuario['rol'] === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                    <option value="usuario" <?php echo $usuario['rol'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                    <option value="moderador" <?php echo $usuario['rol'] === 'moderador' ? 'selected' : ''; ?>>Moderador</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Nueva Contraseña (opcional)</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Deja en blanco para no cambiar">
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="crud_usuarios.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
