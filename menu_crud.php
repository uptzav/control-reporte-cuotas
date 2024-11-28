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
$port = $_ENV['DB_PORT']; // Opcional si usas el puerto predeterminado (3306)

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Operación Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id_cuotas = $_POST['id_cuotas'];
    $sql_delete = "DELETE FROM cuotas WHERE id_cuotas = $id_cuotas";
    if ($conn->query($sql_delete) === TRUE) {
        echo "<script>alert('Registro eliminado exitosamente.');</script>";
    } else {
        echo "<script>alert('Error al eliminar el registro: {$conn->error}');</script>";
    }
}

// Consultar datos para mostrar en el CRUD
$sql = "SELECT 
            cuotas.id_cuotas,
            cuotas.registro_no,
            cuotas.cliente,
            cuotas.dni,
            cuotas.fecha_contrato,
            cuotas.manzana,
            cuotas.lote,
            cuotas.total_venta,
            cuotas.saldo_actual,
            detalle_cuotas.num_cuota,
            detalle_cuotas.fecha_pago,
            detalle_cuotas.cuota_mensual,
            detalle_cuotas.monto_restante
        FROM cuotas
        LEFT JOIN detalle_cuotas ON cuotas.id_cuotas = detalle_cuotas.id_cuotas
        ORDER BY cuotas.id_cuotas, detalle_cuotas.num_cuota";

$result = $conn->query($sql);
$datos = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú CRUD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Menú CRUD</h1>
        <nav class="mb-4">
            <a href="formulario.php" class="btn btn-primary">Ir al Formulario</a>
            <a href="menu_crud.php" class="btn btn-success">Ir al CRUD</a>
        </nav>

        <!-- Tabla CRUD -->
        <h2>Datos de Cuotas y Detalles</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Registro No</th>
                    <th>Cliente</th>
                    <th>DNI</th>
                    <th>Fecha Contrato</th>
                    <th>Manzana</th>
                    <th>Lote</th>
                    <th>Total Venta</th>
                    <th>Saldo Actual</th>
                    <th>Num Cuota</th>
                    <th>Fecha Pago</th>
                    <th>Cuota Mensual</th>
                    <th>Monto Restante</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos as $row): ?>
                <tr>
                    <td><?= $row['id_cuotas'] ?></td>
                    <td><?= $row['registro_no'] ?></td>
                    <td><?= $row['cliente'] ?></td>
                    <td><?= $row['dni'] ?></td>
                    <td><?= $row['fecha_contrato'] ?></td>
                    <td><?= $row['manzana'] ?></td>
                    <td><?= $row['lote'] ?></td>
                    <td>S/ <?= number_format($row['total_venta'], 2) ?></td>
                    <td>S/ <?= number_format($row['saldo_actual'], 2) ?></td>
                    <td><?= $row['num_cuota'] ?></td>
                    <td><?= $row['fecha_pago'] ?></td>
                    <td>S/ <?= number_format($row['cuota_mensual'], 2) ?></td>
                    <td>S/ <?= number_format($row['monto_restante'], 2) ?></td>
                    <td>
                        <!-- Botón Editar -->
                        <a href="editar.php?id=<?= $row['id_cuotas'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        
                        <!-- Botón Eliminar -->
                        <form method="POST" action="" class="d-inline">
                            <input type="hidden" name="id_cuotas" value="<?= $row['id_cuotas'] ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este registro?')">Eliminar</button>
                        </form>
                        
                        <!-- Botón Vista Previa -->
                        <a href="vista_previa.php?id=<?= $row['id_cuotas'] ?>" class="btn btn-info btn-sm">Vista Previa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
