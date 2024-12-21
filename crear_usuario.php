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

// Datos del usuario
$usuario = "admin";  // Nombre de usuario
$password_plain = "admin321";  // Contraseña en texto plano
$rol = "administrador";  // Rol del usuario

// Encriptar la contraseña con password_hash()
$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

// Preparar la consulta para insertar el usuario con el rol de administrador
$sql = "INSERT INTO usuarios (username, password, rol) VALUES ('$usuario', '$password_hashed', '$rol')";

// Ejecutar la consulta
if ($conn->query($sql) === TRUE) {
    echo "Nuevo usuario creado con rol de administrador.";
} else {
    echo "Error al crear el usuario: " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>









