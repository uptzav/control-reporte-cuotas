<?php
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
           DATEDIFF(rdc.fecha_pago, CURDATE()) AS dias_recordatorio
    FROM recordatorio_detalle_cuotas rdc
    JOIN recordatorio_cuotas rc ON rdc.recordatorio_id = rc.id
    WHERE rdc.estado = 'Pendiente'
    AND (DATEDIFF(CURDATE(), rdc.fecha_pago) > 0 OR DATEDIFF(rdc.fecha_pago, CURDATE()) <= 5);
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio de Pagos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Recordatorio de Pagos</h2>
    <table class="table table-bordered table-striped text-center">
        <thead>
            <tr>
                <th>cliente</th>
                <th>Teléfono</th>
                <th>N° Cuota</th>
                <th>Fecha de Pago</th>
                <th>Monto</th>
                <th>Días de Retraso</th>
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
                <td>S/ <?= number_format($row['monto_cuota'], 2) ?></td>
                <td>
                    <?= ($row['dias_retraso'] > 0) 
                        ? $row['dias_retraso'] . ' días de retraso' 
                        : 'Próximo en ' . abs($row['dias_recordatorio']) . ' días' ?>
                </td>
                <td>
                    <a href="https://wa.me/<?= $row['telefono'] ?>?text=Estimado%20<?= urlencode($row['cliente']) ?>,%20le%20recordamos%20que%20debe%20pagar%20la%20cuota%20N°%20<?= $row['num_cuota'] ?>%20por%20un%20monto%20de%20S/%20<?= $row['monto_cuota'] ?>%20con%20fecha%20de%20pago%20<?= $row['fecha_pago'] ?>.%0A%0AEste%20mensaje%20es%20enviado%20por%20*Henko%20Group%20S.A.C.*%20para%20el%20proyecto%20*FINCA%20AMORENA*." 
                       target="_blank" class="btn btn-success btn-sm">
                       Enviar Recordatorio
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
$conn->close();
?>
