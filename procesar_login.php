<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar las variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servername = $_ENV['DB_HOST'];
$dbUsername = $_ENV['DB_USERNAME'];
$dbPassword = $_ENV['DB_PASSWORD'];
$dbName = $_ENV['DB_DATABASE'];
$port = $_ENV['DB_PORT'];

// Conexión a la base de datos
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName, $port);

// Verifica la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verifica si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpia y escapa los datos enviados por el usuario
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = trim($_POST['password']);

    // Consulta SQL para obtener el usuario, la contraseña y el rol
    $sql = "SELECT id, password, rol FROM usuarios WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica si se encontró un usuario
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verifica la contraseña usando password_verify
        if (password_verify($password, $user['password'])) {
            // Configura la sesión del usuario
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['rol']; // Almacena el rol en la sesión

            // Redirige al panel de administración
            header("Location: admin_panel.php");
            exit;
        } else {
            // Contraseña incorrecta
            echo "<script>alert('Contraseña incorrecta.'); window.location.href='login.php';</script>";
        }
    } else {
        // Usuario no encontrado
        echo "<script>alert('Usuario no encontrado.'); window.location.href='login.php';</script>";
    }

    // Cierra la declaración
    $stmt->close();
}

// Cierra la conexión a la base de datos
$conn->close();
?>
