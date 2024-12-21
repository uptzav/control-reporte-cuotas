<?php
// Configurar conexión a la base de datos con mysqli
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_DATABASE'];
$port = $_ENV['DB_PORT']; // Opcional si usas el puerto predeterminado (3306)

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recibir datos del formulario
$dni = $_POST['dni'];
$cliente = $_POST['cliente'];
$telefono = $_POST['telefono'];
$manzana = $_POST['manzana'];
$lote = $_POST['lote'];
$num_cuotas = intval($_POST['num_cuotas']);
$precio_total = floatval($_POST['precio_total']);
$moneda = ($_POST['moneda'] === 'Soles') ? 'S/.' : '$'; // Guardar símbolo S/. o $
$cuota_inicial = floatval($_POST['cuota_inicial']);
$fecha_pago = $_POST['fecha_pago'];

// Calcular saldo del contrato
$saldo_contrato = $precio_total - $cuota_inicial;

try {
    // Insertar contrato en la tabla recordatorio_cuotas
    $stmt = $conn->prepare("
        INSERT INTO recordatorio_cuotas 
        (dni, cliente, telefono, manzana, lote, num_cuotas, precio_total, moneda, cuota_inicial, saldo_contrato) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssiidddd", $dni, $cliente, $telefono, $manzana, $lote, $num_cuotas, $precio_total, $moneda, $cuota_inicial, $saldo_contrato);
    $stmt->execute();

    // Obtener el ID del contrato recién creado
    $recordatorio_id = $conn->insert_id;

    // Calcular el monto de cada cuota
    $monto_cuota = $saldo_contrato / $num_cuotas;

    // Insertar las cuotas en recordatorio_detalle_cuotas
    $stmt_cuotas = $conn->prepare("
        INSERT INTO recordatorio_detalle_cuotas 
        (recordatorio_id, num_cuota, monto_cuota, fecha_pago, estado) 
        VALUES (?, ?, ?, ?, 'Pendiente')
    ");

    $fecha_actual = new DateTime($fecha_pago);
    for ($i = 1; $i <= $num_cuotas; $i++) {
        $fecha_pago_actual = $fecha_actual->format('Y-m-d');
        $stmt_cuotas->bind_param("iids", $recordatorio_id, $i, $monto_cuota, $fecha_pago_actual);
        $stmt_cuotas->execute();

        // Avanzar al próximo mes
        $fecha_actual->modify('+1 month');
    }

    // Cerrar las conexiones
    $stmt->close();
    $stmt_cuotas->close();
    $conn->close();

    // Redirigir al usuario con un mensaje de éxito
    header("Location: registro_exitoso.php");
    exit();
} catch (Exception $e) {
    die("Error al guardar los datos: " . $e->getMessage());
}
?>
