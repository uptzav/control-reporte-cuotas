<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Conectar a la base de datos
require_once __DIR__ . '/vendor/autoload.php';
// Configurar conexión a la base de datos
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_DATABASE'];
$port = $_ENV['DB_PORT']; // Opcional si usas el puerto predeterminado (3306)

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar el formulario de creación si se envía
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['edit_id'])) {
    $usuario = $_POST['username'] ?? '';
    $password_plain = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? '';

    // Validar los datos ingresados
    if (!empty($usuario) && !empty($password_plain) && !empty($rol)) {
        // Encriptar la contraseña
        $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

        // Insertar el usuario
        $sql = "INSERT INTO usuarios (username, password, rol) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $usuario, $password_hashed, $rol);

        if ($stmt->execute()) {
            echo "<script>alert('Usuario creado exitosamente.'); window.location.href = 'admin_panel.php?page=usuarios';</script>";
        } else {
            echo "Error al crear el usuario: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Por favor, completa todos los campos.";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $usuario = $_POST['username'] ?? '';
    $password_plain = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? '';

    // Validar los datos ingresados
    if (!empty($usuario) && !empty($rol)) {
        // Encriptar la contraseña solo si se ha cambiado
        if (!empty($password_plain)) {
            $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET username=?, password=?, rol=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $usuario, $password_hashed, $rol, $id);
        } else {
            $sql = "UPDATE usuarios SET username=?, rol=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $usuario, $rol, $id);
        }

        if ($stmt->execute()) {
            echo "<script>alert('Usuario actualizado exitosamente.'); window.location.href = 'admin_panel.php?page=usuarios';</script>";
        } else {
            echo "Error al actualizar el usuario: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Por favor, completa todos los campos.";
    }
}

// Lógica para eliminar usuario
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    // Preparar y ejecutar la consulta para eliminar al usuario
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('Usuario eliminado exitosamente.'); window.location.href = 'admin_panel.php?page=usuarios';</script>";
    } else {
        echo "Error al eliminar el usuario: " . $stmt->error;
    }
    $stmt->close();
}

// Obtener la lista de usuarios
$sql = "SELECT id, username, rol FROM usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<header class="header">
<div class="container text-center">
            <img src="images/logo_henko.png" alt="Logo Henko Group" class="logo" style="max-width: 200px; height: auto;">
        </div>
        <nav class="nav">
            <a href="logout.php" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </nav>
</header>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Lista de Usuarios</h2>
        <!-- Botón para abrir el modal -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createUserModal">Crear Nuevo Usuario</button>
        
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['rol']; ?></td>
                            <td>
                                <!-- Botón para editar usuario con el ID correspondiente -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal" data-id="<?php echo $row['id']; ?>" data-username="<?php echo $row['username']; ?>" data-rol="<?php echo $row['rol']; ?>">Editar</button>
                                <a href="usuarios.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>

                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No hay usuarios registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
    </div>

    <!-- Modal para crear usuario -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createUserModalLabel">Crear Nuevo Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
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
                                <!-- Agrega más roles aquí -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar usuario -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Campo oculto para el ID -->
                        <input type="hidden" id="edit_id" name="edit_id">
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Contraseña (opcional)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="edit_rol" class="form-label">Rol</label>
                            <select class="form-select" id="edit_rol" name="rol" required>
                                <option value="administrador">Administrador</option>
                                <option value="usuario">Usuario</option>
                                <option value="moderador">Moderador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
var editModal = document.getElementById('editUserModal');
editModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget; // El botón que activó el modal
    var userId = button.getAttribute('data-id');
    var username = button.getAttribute('data-username');  // Corregido
    var rol = button.getAttribute('data-rol');

    // Asignar los valores a los campos del modal
    var modalId = editModal.querySelector('#edit_id');
    var modalUsername = editModal.querySelector('#edit_username');
    var modalRol = editModal.querySelector('#edit_rol');

    modalId.value = userId;
    modalUsername.value = username;
    modalRol.value = rol;
});

</script>
</body>
</html>
<?php
$conn->close();
?>







