<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


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
$port = $_ENV['DB_PORT'];

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id_cuotas = $_GET['id'];

    $sql_cuotas = "SELECT * FROM cuotas WHERE id_cuotas = $id_cuotas";
    $result_cuotas = $conn->query($sql_cuotas);

    if ($result_cuotas->num_rows > 0) {
        $registro = $result_cuotas->fetch_assoc();
    } else {
        die("No se encontró el registro con ID: $id_cuotas.");
    }

    $sql_detalle = "SELECT * FROM detalle_cuotas WHERE id_cuotas = $id_cuotas ORDER BY num_cuota";
    $result_detalle = $conn->query($sql_detalle);
    $detalle_cuotas = $result_detalle->fetch_all(MYSQLI_ASSOC);
} else {
    die("ID no especificado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = $_POST['cliente'];
    $dni = $_POST['dni'];
    $fecha_contrato = $_POST['fecha_contrato'];
    $manzana = $_POST['manzana'];
    $lote = $_POST['lote'];
    $area = $_POST['area'];
    $total_venta = $_POST['total_venta'];
    $inicial = $_POST['inicial'];
    $saldo_contrato = $total_venta - $inicial;

    $sql_update_cuotas = "UPDATE cuotas SET 
                            cliente = '$cliente',
                            dni = '$dni',
                            fecha_contrato = '$fecha_contrato',
                            manzana = '$manzana',
                            lote = '$lote',
                            area = $area,
                            total_venta = $total_venta,
                            inicial = $inicial,
                            saldo_contrato = $saldo_contrato 
                          WHERE id_cuotas = $id_cuotas";

    if ($conn->query($sql_update_cuotas) === TRUE) {
        $saldo_restante = $saldo_contrato;
        $cancelado_a_la_fecha = $inicial;

        foreach ($_POST['detalle_cuotas'] as $id_detalle => $detalle) {
            $num_cuota = $detalle['num_cuota'];
            $fecha_pago = $detalle['fecha_pago'];
            $cuota_mensual = $detalle['cuota_mensual'];
            $monto_restante = max(0, $saldo_restante - $cuota_mensual);
            $cancelado_a_la_fecha += $cuota_mensual;
            $fecha_deposito = $detalle['fecha_deposito'];
            $modo_pago = $detalle['modo_pago'];

            $sql_update_detalle = "UPDATE detalle_cuotas SET 
                                    num_cuota = $num_cuota,
                                    fecha_pago = '$fecha_pago',
                                    cuota_mensual = $cuota_mensual,
                                    monto_restante = $monto_restante,
                                    fecha_deposito = '$fecha_deposito',
                                    cancelado_a_la_fecha = $cancelado_a_la_fecha,
                                    modo_pago = '$modo_pago'
                                  WHERE id_detalle_cuotas = $id_detalle";

            $conn->query($sql_update_detalle);
            $saldo_restante = $monto_restante;
        }

        echo "<script>alert('Registro y cuotas actualizados correctamente.');</script>";
        echo "<script>window.location.href = 'menu_crud.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el registro: {$conn->error}');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Editar Registro</h1>

        <form method="POST" action="">
            <h3>Información Principal</h3>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="cliente" class="form-label">Cliente:</label>
                    <input type="text" class="form-control" id="cliente" name="cliente" value="<?= htmlspecialchars($registro['cliente']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="dni" class="form-label">DNI:</label>
                    <input type="text" class="form-control" id="dni" name="dni" value="<?= htmlspecialchars($registro['dni']) ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="fecha_contrato" class="form-label">Fecha Firma del Contrato:</label>
                    <input type="date" class="form-control" id="fecha_contrato" name="fecha_contrato" value="<?= htmlspecialchars($registro['fecha_contrato']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="manzana" class="form-label">Manzana:</label>
                    <input type="text" class="form-control" id="manzana" name="manzana" value="<?= htmlspecialchars($registro['manzana']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="lote" class="form-label">Lote:</label>
                    <input type="text" class="form-control" id="lote" name="lote" value="<?= htmlspecialchars($registro['lote']) ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="area" class="form-label">Área:</label>
                    <input type="number" step="0.01" class="form-control" id="area" name="area" value="<?= htmlspecialchars($registro['area']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="total_venta" class="form-label">Total Venta:</label>
                    <input type="number" step="0.01" class="form-control" id="total_venta" name="total_venta" value="<?= htmlspecialchars($registro['total_venta']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="inicial" class="form-label">Inicial:</label>
                    <input type="number" step="0.01" class="form-control" id="inicial" name="inicial" value="<?= htmlspecialchars($registro['inicial']) ?>" required>
                </div>
            </div>

            <h3>Detalle de Cuotas</h3>
            <?php foreach ($detalle_cuotas as $detalle): ?>
                <div class="border p-3 mb-3">
                    <h5>Cuota <?= htmlspecialchars($detalle['num_cuota']) ?></h5>
                    <input type="hidden" name="detalle_cuotas[<?= $detalle['id_detalle_cuotas'] ?>][num_cuota]" value="<?= htmlspecialchars($detalle['num_cuota']) ?>">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="fecha_pago_<?= $detalle['id_detalle_cuotas'] ?>" class="form-label">Fecha de Pago:</label>
                            <input type="date" class="form-control" id="fecha_pago_<?= $detalle['id_detalle_cuotas'] ?>" name="detalle_cuotas[<?= $detalle['id_detalle_cuotas'] ?>][fecha_pago]" value="<?= htmlspecialchars($detalle['fecha_pago']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="cuota_mensual_<?= $detalle['id_detalle_cuotas'] ?>" class="form-label">Cuota Mensual:</label>
                            <input type="number" step="0.01" class="form-control" id="cuota_mensual_<?= $detalle['id_detalle_cuotas'] ?>" name="detalle_cuotas[<?= $detalle['id_detalle_cuotas'] ?>][cuota_mensual]" value="<?= htmlspecialchars($detalle['cuota_mensual']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_deposito_<?= $detalle['id_detalle_cuotas'] ?>" class="form-label">Fecha de Depósito:</label>
                            <input type="date" class="form-control" id="fecha_deposito_<?= $detalle['id_detalle_cuotas'] ?>" name="detalle_cuotas[<?= $detalle['id_detalle_cuotas'] ?>][fecha_deposito]" value="<?= htmlspecialchars($detalle['fecha_deposito']) ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                    <div class="col-md-4">
    <label for="modo_pago_<?= $detalle['id_detalle_cuotas'] ?>" class="form-label">Modo de Pago:</label>
    <select class="form-control" id="modo_pago_<?= $detalle['id_detalle_cuotas'] ?>" name="detalle_cuotas[<?= $detalle['id_detalle_cuotas'] ?>][modo_pago]" required>
        <option value="Efectivo" <?= $detalle['modo_pago'] === 'Efectivo' ? 'selected' : '' ?>>Efectivo</option>
        <option value="Transferencia" <?= $detalle['modo_pago'] === 'Transferencia' ? 'selected' : '' ?>>Transferencia</option>
        <option value="Depósito" <?= $detalle['modo_pago'] === 'Depósito' ? 'selected' : '' ?>>Depósito</option>
    </select>
</div>

                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="menu_crud.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>

</html>
