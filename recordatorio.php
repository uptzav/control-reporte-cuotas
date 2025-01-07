<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}


// Conexión a la base de datos
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE'], $_ENV['DB_PORT']);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para cuotas vencidas y próximas a vencer
$sql = "
    SELECT rc.cliente, rc.telefono, rdc.num_cuota, rdc.fecha_pago, rdc.monto_cuota,
           DATEDIFF(CURDATE(), rdc.fecha_pago) AS dias_retraso,
           DATEDIFF(rdc.fecha_pago, CURDATE()) AS dias_recordatorio,
           rdc.estado
    FROM recordatorio_detalle_cuotas rdc
    JOIN recordatorio_cuotas rc ON rdc.recordatorio_id = rc.id
    WHERE rdc.estado = 'Pendiente'
    AND (DATEDIFF(CURDATE(), rdc.fecha_pago) > 0 OR DATEDIFF(rdc.fecha_pago, CURDATE()) <= 5);
";

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
    <title>Recordatorio de Pagos</title>

    <!-- CSS de Bootstrap y DataTables -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Recordatorio de Pagos</h2>
    <table id="tablaRecordatorios" class="table table-bordered table-striped text-center">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>N° Cuota</th>
                <th>Fecha de Pago</th>
                <th>Monto</th>
                <th>Días de Retraso</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>

            <tr>
                <td><?= htmlspecialchars($row['cliente']) ?></td>
                <td><?= htmlspecialchars($row['telefono']) ?></td>
                <td><?= htmlspecialchars($row['num_cuota']) ?></td>
                <td><?= htmlspecialchars($row['fecha_pago']) ?></td>
                <td>
               <?= number_format($row['monto_cuota'], 2) ?>
                </td>

                <td>
                    <?= ($row['dias_retraso'] > 0) 
                        ? $row['dias_retraso'] . ' días de retraso' 
                        : 'Próximo pago en ' . abs($row['dias_recordatorio']) . ' días' ?>
                </td>
                <td><?= htmlspecialchars($row['estado']) ?></td>
                <td>
                    <a href="#" onclick="enviarWhatsApp('<?= $row['telefono'] ?>', '<?= urlencode($row['cliente']) ?>', <?= $row['num_cuota'] ?>, <?= $row['monto_cuota'] ?>, '<?= $row['fecha_pago'] ?>'); return false;" 
                       class="btn btn-success btn-sm">
                        Enviar Recordatorio
                    </a>
                </td>
            </tr>

        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- JavaScript de Bootstrap, jQuery y DataTables -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    // Inicializar DataTables
    $(document).ready(function () {
        $('#tablaRecordatorios').DataTable({
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            }
        });
    });

    // Función para enviar mensaje por WhatsApp
    function enviarWhatsApp(telefono, cliente, cuota, monto, fecha) {
        const mensaje = `Estimado ${cliente}, le recordamos que debe pagar la cuota N°${cuota} por un monto de S/ ${monto} con fecha de pago ${fecha}.%0A%0AEste mensaje es enviado por *Henko Group S.A.C.* para el proyecto *FINCA AMORENA*.`;
        const url = `https://wa.me/${telefono}?text=${mensaje}`;
        window.open(url, '_blank');
    }
</script>
</body>
</html>

<?php
$conn->close();
?>

