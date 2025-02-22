<?php


session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}


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

// Capturar los valores enviados desde el formulario
$moneda = isset($_POST['moneda']) ? $_POST['moneda'] : 'No definido';

// Opcional: Validar que el valor sea correcto
if (!in_array($moneda, ['S/. ', '$ '])) {
    die('Moneda inválida.');
}

// Función para generar registro_no
function generarRegistroNo($conn) {
    $sql = "SELECT registro_no FROM cuotas ORDER BY id_cuotas DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ultimoRegistro = $row['registro_no'];
        $numero = (int)substr($ultimoRegistro, 4);
        $nuevoNumero = $numero + 1;
    } else {
        $nuevoNumero = 1;
    }
    return "CRC-" . str_pad($nuevoNumero, 3, "0", STR_PAD_LEFT);
}

// Procesar datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente = $_POST['cliente'];
    $dni = $_POST['dni'];
    $fecha_contrato = $_POST['fecha_contrato'];
    $manzana = $_POST['manzana'];
    $lote = $_POST['lote'];
    $area = $_POST['area'];
    $total_venta = $_POST['total_venta'];
    $inicial = $_POST['inicial'];
    $cuota_mensual = $_POST['cuota_mensual'];
    $modo_pago = $_POST['modo_pago'];
    $fecha_Deposito = $_POST['fecha_deposito'];
    $registro_no = generarRegistroNo($conn);
    $moneda = $_POST['moneda'];

    $saldo_contrato = $total_venta - $inicial;
    $fecha_actual = date("Y-m-d");
    $saldo_actual = $saldo_contrato;

    // Insertar datos en la tabla `cuotas`
    $sql_cuotas = "INSERT INTO cuotas (fecha_actual, registro_no, cliente, dni, fecha_contrato, manzana, lote, area, total_venta, inicial, saldo_contrato, saldo_actual, moneda)
    VALUES ('$fecha_actual', '$registro_no', '$cliente', '$dni', '$fecha_contrato', '$manzana', '$lote', $area, $total_venta, $inicial, $saldo_contrato, $saldo_actual, '$moneda')";

    if ($conn->query($sql_cuotas) === TRUE) {
        $id_cuotas = $conn->insert_id; // Obtener el ID del registro insertado
        echo "Datos del contrato guardados correctamente con registro_no: $registro_no.";

        // Calcular cuotas
        $cuotas = [];
        $fecha_pago = new DateTime($fecha_contrato);
        $cancelado_a_la_fecha = $inicial;
        $monto_abonado = $inicial;

        for ($i = 1; $saldo_actual > 0; $i++) {
            $monto_pagado = ($saldo_actual >= $cuota_mensual) ? $cuota_mensual : $saldo_actual;
            $saldo_actual -= $monto_pagado;
            $monto_abonado += $monto_pagado;
        
            $cuotas[] = [
                'num_cuota' => $i,
                'fecha_pago' => $fecha_pago->format("Y-m-d"),
                'cuota_mensual' => $monto_pagado,
                'monto_restante' => $saldo_actual,
                'fecha_deposito' => $fecha_Deposito,
                'cancelado_a_la_fecha' => $cancelado_a_la_fecha,
                'monto_abonado' => $monto_abonado,
                'modo_pago' => $modo_pago,
                
            ];
        
            $cancelado_a_la_fecha = $monto_abonado;
            $fecha_pago->modify('+1 month');
        }
        

        // Insertar cuotas en la tabla `detalle_cuotas`
        foreach ($cuotas as $cuota) {
            $sql_detalle = "INSERT INTO detalle_cuotas (id_cuotas, num_cuota, fecha_pago, cuota_mensual, monto_restante, fecha_deposito, cancelado_a_la_fecha, monto_abonado, modo_pago)
            VALUES ($id_cuotas, {$cuota['num_cuota']}, '{$cuota['fecha_pago']}', {$cuota['cuota_mensual']}, {$cuota['monto_restante']}, '{$cuota['fecha_deposito']}', {$cuota['cancelado_a_la_fecha']}, {$cuota['monto_abonado']}, '{$cuota['modo_pago']}')";

            if (!$conn->query($sql_detalle)) {
                echo "Error al guardar detalle de cuotas: " . $conn->error;
            }
        }
    } else {
        echo "Error al guardar datos del contrato: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/vista_previa.css">

    <title>Reporte de Cuotas</title>
</head>

<body>
<!-- Aquí está el botón -->
<a href="menu_crud.php" class="btn btn-success d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-return-left me-2"></i> Ir a Menu  </a>
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
        <p><strong>FECHA:</strong> <?= $fecha_actual ?></p>
        <p><strong>VERSIÓN:</strong> 001</p>
        <p><strong>REGISTRO Nº:</strong> <?= htmlspecialchars($registro_no) ?></p>
 </div>

 <div class="info-cliente">
        <p><strong>CLIENTE:</strong> <?= htmlspecialchars($cliente) ?></p>
        <p><strong>DNI:</strong> <?= htmlspecialchars($dni) ?></p>
        <p><strong>FECHA FIRMA CONTRATO:</strong> <?= htmlspecialchars($fecha_contrato) ?></p>
        <p><strong>MANZANA:</strong> <?= htmlspecialchars($manzana) ?> <strong>LOTE:</strong> <?= htmlspecialchars($lote) ?></p>
        <p><strong>ÁREA:</strong> <?= htmlspecialchars($area) ?> m²</p>
        </div>

        <div class="info-lote">
        <p><strong>TOTAL VENTA:</strong> <?= htmlspecialchars($moneda) ?> <?= number_format($_POST['total_venta'], 2) ?></p>
<p><strong>INICIAL:</strong> <?= htmlspecialchars($moneda) ?> <?= number_format($_POST['inicial'], 2) ?></p>
<p><strong>CUOTA MENSUAL:</strong> <?= htmlspecialchars($moneda) ?> <?= number_format($_POST['cuota_mensual'], 2) ?></p>

</div>

</section>  
   <!-- Cronograma de Pagos -->
   <section class="cronograma">
    <h3>Cronograma de Pagos</h3>
    <table class="table table-bordered table-striped">
    <thead class="table-success">
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
            <?php if (!empty($cuotas)): ?>
                <?php foreach ($cuotas as $cuota): ?>
                    <tr>
                        <td><?= htmlspecialchars($cuota['num_cuota']) ?></td>
                        <td><?= htmlspecialchars($cuota['fecha_pago']) ?></td>
                        <td><?= htmlspecialchars($moneda) ?> <?= number_format($cuota['cuota_mensual'], 2) ?></td>
                        <td><?= htmlspecialchars($moneda) ?> <?= number_format($cuota['monto_restante'], 2) ?></td>
                        <td><?= htmlspecialchars($cuota['fecha_deposito']) ?></td>
                        <td><?= htmlspecialchars($moneda) ?> <?= number_format($cuota['cancelado_a_la_fecha'], 2) ?></td>
                        <td class="highlight"><?= htmlspecialchars($moneda) ?> <?= number_format($cuota['monto_abonado'], 2) ?></td>
                        <td><?= htmlspecialchars($cuota['modo_pago']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No hay cuotas registradas.</td>
                </tr>
            <?php endif; ?>
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
</footer>
    </div>
</body>
</html>






