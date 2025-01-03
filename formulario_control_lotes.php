
<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Contratos y Cuotas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Registro de Contratos</h2>
        <form action="guardar_contrato.php" method="POST">
            <div class="row">
                <!-- DNI -->
                <div class="col-md-6 mb-3">
                    <label for="dni" class="form-label">DNI</label>
                    <input type="text" class="form-control" id="dni" name="dni" placeholder="Ingrese el DNI" required>
                </div>
                <!-- Cliente -->
                <div class="col-md-6 mb-3">
                    <label for="cliente" class="form-label">Nombre del Cliente</label>
                    <input type="text" class="form-control" id="cliente" name="cliente" placeholder="Ingrese el nombre completo" required>
                </div>
            </div>
            <div class="row">
                <!-- Teléfono -->
                <div class="col-md-6 mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ingrese el número de teléfono" required>
                </div>
                <!-- Manzana -->
                <div class="col-md-6 mb-3">
                    <label for="manzana" class="form-label">Manzana</label>
                    <input type="text" class="form-control" id="manzana" name="manzana" placeholder="Ingrese la manzana" required>
                </div>
            </div>
            <div class="row">
                <!-- Lote -->
                <div class="col-md-6 mb-3">
                    <label for="lote" class="form-label">Lote</label>
                    <input type="text" class="form-control" id="lote" name="lote" placeholder="Ingrese el lote" required>
                </div>
                <!-- Número de Cuotas -->
                <div class="col-md-6 mb-3">
                    <label for="num_cuotas" class="form-label">Número de Cuotas</label>
                    <input type="number" class="form-control" id="num_cuotas" name="num_cuotas" placeholder="Ingrese el número de cuotas" required>
                </div>
            </div>
            <div class="row">
                <!-- Precio Total -->
                <div class="col-md-6 mb-3">
                    <label for="precio_total" class="form-label">Precio Total</label>
                    <input type="number" step="0.01" class="form-control" id="precio_total" name="precio_total" placeholder="Ingrese el precio total" required>
                </div>
                <!-- Moneda -->
                <div class="mb-3">
                    <label for="moneda" class="form-label">Moneda</label>
                    <select class="form-select" id="moneda" name="moneda" required>
                        <option value="Soles">S/.</option>
                        <option value="Dólares">$</option>
                    </select>
                </div>
                
            </div>
            <div class="row">
                <!-- Cuota Inicial -->
                <div class="col-md-6 mb-3">
                    <label for="cuota_inicial" class="form-label">Cuota Inicial</label>
                    <input type="number" step="0.01" class="form-control" id="cuota_inicial" name="cuota_inicial" placeholder="Ingrese la cuota inicial" required>
                </div>
                <!-- Fecha de la Primera Cuota -->
                <div class="col-md-6 mb-3">
                    <label for="fecha_pago" class="form-label">Fecha de la Primera Cuota</label>
                    <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" required>
                </div>
            </div>
            <!-- Botón Enviar -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Registrar Contrato</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




 











