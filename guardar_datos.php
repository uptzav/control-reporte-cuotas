<?php
// Configurar conexión a la base de datos
require_once __DIR__ . '/vendor/autoload.php';

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


$moneda = isset($_POST['moneda']) ? $_POST['moneda'] : null;

// Procesar los datos del formulario
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
    $moneda = $_POST['moneda'];
    $registro_no = uniqid(); // Genera un ID único para el registro

    // Validar que la moneda sea válida


    // Validar que la moneda sea válida
    if (!in_array($moneda, ['S/. ', '$ '])) {
        die("Moneda inválida. Seleccione 'S/. ' o '$ '.");
    }
    
    // Cálculos iniciales
    $saldo_contrato = $total_venta - $inicial;
    $fecha_actual = date("Y-m-d"); // Fecha de elaboración
    $cuotas = [];
    $saldo_restante = $saldo_contrato;
    $fecha_pago = new DateTime($fecha_contrato);
    $monto_abonado = $inicial;
    $cancelado_a_la_fecha = $inicial;

    for ($i = 1; $saldo_restante > 0; $i++) {
        $monto_pagado = ($saldo_restante >= $cuota_mensual) ? $cuota_mensual : $saldo_restante;
        $saldo_restante -= $monto_pagado;
        $monto_abonado += $monto_pagado;

        $cuotas[] = [
            'num_cuota' => $i,
            'fecha_pago' => $fecha_pago->format("Y-m-d"),
            'cuota_mensual' => $monto_pagado,
            'monto_restante' => $saldo_restante,
            'fecha_deposito' => $fecha_Deposito,
            'cancelado_a_la_fecha' => $cancelado_a_la_fecha,
            'monto_abonado' => $monto_abonado,
            'modo_pago' => $modo_pago,
            'moneda' => $moneda,
        ];
        $cancelado_a_la_fecha = $monto_abonado;
        $fecha_pago->modify('+1 month'); // Incrementa 1 mes
    }

    // Insertar datos en la tabla 'cuotas'
    $sql_cuotas = "INSERT INTO cuotas (fecha_actual, registro_no, cliente, dni, fecha_contrato, manzana, lote, area, total_venta, inicial, saldo_contrato, saldo_actual, moneda)
    VALUES ('$fecha_actual', '$registro_no', '$cliente', '$dni', '$fecha_contrato', '$manzana', '$lote', $area, $total_venta, $inicial, $saldo_contrato, $saldo_actual, '$moneda')";
    
    if ($conn->query($sql_cuotas) === TRUE) {
        echo "Datos del contrato guardados correctamente.";
    } else {
        echo "Error: " . $sql_cuotas . "<br>" . $conn->error;
    }

    // Insertar datos en la tabla 'detalle_cuotas'
    foreach ($cuotas as $cuota) {
        $sql_detalle = "INSERT INTO detalle_cuotas (id_cuotas, num_cuota, fecha_pago, cuota_mensual, moneda_cuota_mensual, monto_restante, fecha_deposito, cancelado_a_la_fecha, monto_abonado, modo_pago)
VALUES ($id_cuotas, {$cuota['num_cuota']}, '{$cuota['fecha_pago']}', {$cuota['cuota_mensual']}, '$moneda_cuota_mensual', {$cuota['monto_restante']}, '{$cuota['fecha_deposito']}', {$cuota['cancelado_a_la_fecha']}, {$cuota['monto_abonado']}, '{$cuota['modo_pago']}')";


        if (!$conn->query($sql_detalle)) {
            echo "Error al guardar detalle de cuotas: " . $conn->error;
        }
    }
}

$conn->close();
?>
