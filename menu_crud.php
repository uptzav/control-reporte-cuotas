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
// Eliminar un registro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id_cuotas = $_POST['id_cuotas'];
    $sql_delete = "DELETE FROM cuotas WHERE id_cuotas = $id_cuotas";
    if ($conn->query($sql_delete) === TRUE) {
        echo "<script>alert('Registro eliminado exitosamente.');</script>";
    } else {
        echo "<script>alert('Error al eliminar el registro: {$conn->error}');</script>";
    }
}

// Consultar datos
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
            cuotas.moneda,
            detalle_cuotas.num_cuota,
            detalle_cuotas.fecha_pago,
            detalle_cuotas.cuota_mensual,
            detalle_cuotas.monto_restante,
            detalle_cuotas.fecha_deposito,
            detalle_cuotas.cancelado_a_la_fecha	

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
    <title>Menú CRUD Responsivo</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">

    <style>
        /* Variables CSS para los colores de la tabla */
        :root {
            --table-bg-color: #ffffff;
            --table-text-color: #212529;
            --table-header-bg: #4CAF50;
            --table-header-text: #ffffff;
            --table-border-color: #dddddd;
        }

        /* Variables para el modo oscuro */
        .dark-mode {
            --table-bg-color: #FEFFFE;
            --table-text-color: #FEFFFE;
            --table-header-bg: #333333;
            --table-header-text: #81c784;
            --table-border-color: #555555;
        }

        /* Estilo de la tabla */
        .table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--table-bg-color);
            color: var(--table-text-color);
        }

        .table th, .table td {         
            border: 1px solid var(--table-border-color);
            padding: 10px;
            text-align: center;
        }

        .table th {
            background-color: var(--table-header-bg);
            color: var(--table-header-text);
        }

        /* Espaciado adicional en la página */
        .container {
            margin-top: 20px;
        }

        /* Contenedor para organizar los botones */
        .action-buttons .btn {
            
            display: flex; /* Flexbox para organizar los botones */
            justify-content: space-between; /* Distribuye los botones uniformemente */
            gap: 5px; /* Espacio entre botones */
            padding: 5px 10px; /* Tamaño compacto */
            font-size: 0.85rem; /* Texto más pequeño */
            width: 100%; /* Botones con igual tamaño */
            text-align: center; /* Asegura que el texto esté centrado */
            
        }
    

        td .action-buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: row; /* Los botones se apilan verticalmente */
            gap: 5px;
            height: 100%;
            width: 100%;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Menú Reporte de Cuotas</h1>
        
        <!-- Menú de navegación -->
        <nav class="mb-4">
            <a href="formulario.php" class="btn btn-primary">Ir al Formulario</a>
            <a href="menu_crud.php" class="btn btn-success">Ir al CRUD</a>
        </nav>

        <!-- Tabla responsiva -->
        <div class="table-responsive">
            <h2>Datos de Cuotas y Detalles</h2>
            <table id="crudTable" class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Registro No</th>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th>Num Cuota</th>
                        <th>Fecha Pago</th>
                        <th>Cuota Mensual</th>
                        <th>Monto Restante</th>
                        <th>Fecha de Depósito</th>
                        <th>Cancelado a la Fecha</th>

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
                        <td><?= $row['num_cuota'] ?></td>
                        <td><?= date('d/m/Y', strtotime($row['fecha_pago'])) ?></td>
                        <td><?= $row['moneda'], number_format($row['cuota_mensual'], 2) ?></td>
                        <td><?= $row['moneda'], number_format($row['monto_restante'], 2) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['fecha_deposito'])) ?></td>
                        <td><?= $row['moneda'], number_format($row['cancelado_a_la_fecha'], 2) ?></td>
                        <td>
                        <div class="action-buttons">
                        <a href="reporte_vista_previa.php?id=<?= $row['id_cuotas'] ?>" class="btn btn-info btn-sm">Ver</a>
                            <a href="editar.php?id=<?= $row['id_cuotas'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="id_cuotas" value="<?= $row['id_cuotas'] ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este registro?')">Eliminar</button>                              
                            </form>   
                            <a href="javascript:void(0);" class="btn btn-success btn-sm" onclick="printReport('reporte_vista_previa.php?id=<?= $row['id_cuotas']; ?>')">Descargar PDF</a>

                            </div>               
                        </td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!--imprimir--> 
    <script>
    function printReport(url) {
        // Abre la vista previa en una nueva ventana o pestaña
        const printWindow = window.open(url, '_blank');

        // Espera a que la página cargue completamente
        printWindow.onload = function () {
            // Llama al diálogo de impresión
            printWindow.print();
        };
    }
</script>

    <!-- Scripts de DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#crudTable').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json"
                },
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],              
            });
        });
    </script>
</body>
</html>