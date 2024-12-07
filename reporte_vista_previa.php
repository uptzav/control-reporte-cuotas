<?php
require_once __DIR__ . '/vendor/autoload.php';
// Configurar conexión a la base de datos
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_DATABASE'];
$port = $_ENV['DB_PORT'];

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar que se haya recibido un ID
if (isset($_GET['id'])) {
    $id_cuotas = $_GET['id'];

    // Consultar datos de la tabla `cuotas`
    $sql_cuotas = "SELECT * FROM cuotas WHERE id_cuotas = $id_cuotas";
    $result_cuotas = $conn->query($sql_cuotas);
    if ($result_cuotas->num_rows > 0) {
        $cuota = $result_cuotas->fetch_assoc();
    } else {
        die("No se encontraron datos para el ID especificado.");
    }

    // Consultar datos de la tabla `detalle_cuotas`
    $sql_detalle = "SELECT * FROM detalle_cuotas WHERE id_cuotas = $id_cuotas ORDER BY num_cuota";
    $result_detalle = $conn->query($sql_detalle);
    $cuotas = $result_detalle->fetch_all(MYSQLI_ASSOC);
} else {
    die("ID de cuota no especificado.");
}
$conn->close();
// Verificar si se solicita el PDF
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/vista_previa.css">
    <title>Reporte de Cuotas</title>
</head>
<body>
<div class="container">
    <!-- Encabezado con logo -->
    <header>
        <div class="logo-container">
            <img src="images/logo_henko.png" alt="Logo Henko Group" class="logo">
        </div>
        <h1>HENKO GROUP S.A.C.</h1>
        <p>Proyecto: <strong>Finca Amorena</strong></p>
        
    </header>

    <!-- Información del Cliente y Lote -->
    <section class="info">
        <div class="info-elaboracion">
            <p><strong>ELABORACIÓN:</strong> ING. SANTOS GOMEZ CH.</p>
            <p><strong>FECHA:</strong> <?= htmlspecialchars($cuota['fecha_actual']) ?></p>
            <p><strong>VERSIÓN:</strong> 001</p>
            <p><strong>REGISTRO Nº:</strong> <?= htmlspecialchars($cuota['registro_no']) ?></p>
        </div>
        <div class="info-cliente">
            <p><strong>CLIENTE:</strong> <?= htmlspecialchars($cuota['cliente']) ?></p>
            <p><strong>DNI:</strong> <?= htmlspecialchars($cuota['dni']) ?></p>
            <p><strong>FECHA FIRMA CONTRATO:</strong> <?= htmlspecialchars($cuota['fecha_contrato']) ?></p>
            <p><strong>MANZANA:</strong> <?= htmlspecialchars($cuota['manzana']) ?> <strong>Lote:</strong> <?= htmlspecialchars($cuota['lote']) ?></p>
            <p><strong>ÁREA:</strong> <?= htmlspecialchars($cuota['area']) ?> m²</p>
        </div>
        <div class="info-lote">
   <p><strong>TOTAL VENTA: </strong><?= htmlspecialchars($cuota['moneda']) ?> <?= number_format($cuota['total_venta']) ?> </p>
            <p><strong>INICIAL:</strong><?= htmlspecialchars($cuota['moneda']) ?><?= number_format($cuota['inicial'], 2) ?></p>
            <p><strong>SALDO CONTRATO:</strong><?= htmlspecialchars($cuota['moneda']) ?><?= number_format($cuota['saldo_contrato'], 2) ?></p>
        </div>
    </section>
    <!-- Cronograma de Pagos -->
    <section class="cronograma">
        <h3>Cronograma de Pagos</h3>
        <table>
            <thead>
                <tr>
                    <th>N° Cuota</th>
                    <th>Fecha de Pago</th>
                    <th>Cuota Mensual</th>
                    <th>Monto Restante</th>
                    <th>Fecha de Depósito</th>
                    <th>Cancelado a la Fecha</th>
                    <th>Monto Abonado a la Fecha</th>
                    <th>Modo de Pago</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cuotas as $detalle): ?>
                    <tr>
                        <td><?= $detalle['num_cuota'] ?></td>
                        <td><?= htmlspecialchars($detalle['fecha_pago']) ?></td>
                        <td><?= htmlspecialchars($cuota['moneda']) ?><?= number_format($detalle['cuota_mensual'], 2) ?></td>
                        <td><?= htmlspecialchars($cuota['moneda']) ?><?= number_format($detalle['monto_restante'], 2) ?></td>
                        <td><?= htmlspecialchars($detalle['fecha_deposito']) ?></td>
                        <td><?= htmlspecialchars($cuota['moneda']) ?><?= number_format($detalle['cancelado_a_la_fecha'], 2) ?></td>
                        <td><?= htmlspecialchars($cuota['moneda']) ?><?= number_format($detalle['monto_abonado'], 2) ?></td>
                        <td><?= htmlspecialchars($detalle['modo_pago']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <footer class="footer">
        <div class="footer-left">
            <p>VºBº SUBGERENTE</p>
            <img src="images/firma.png" alt="Firma Subgerente" class="firma-img">
        </div>
    <div class="footer-right">
    <img src="images/logo_amorena.png" alt="Logo Amorena" class="footer-img" style="max-width: 280px; height: auto;">
</div>
</div>
</body>
</html>
