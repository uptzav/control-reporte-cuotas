<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Formulario de Registro</h2>
        <form action="vista_previa.php" method="post">
            <div class="mb-3">
                <label for="cliente" class="form-label">Cliente:</label>
                <input type="text" class="form-control" id="cliente" name="cliente" required>
            </div>
            <div class="mb-3">
                <label for="dni" class="form-label">DNI:</label>
                <input type="text" class="form-control" id="dni" name="dni" required>
            </div>
            <div class="mb-3">
                <label for="fecha_contrato" class="form-label">Fecha Firma del Contrato:</label>
                <input type="date" class="form-control" id="fecha_contrato" name="fecha_contrato" required>
            </div>
            <div class="mb-3">
                <label for="manzana" class="form-label">Manzana:</label>
                <input type="text" class="form-control" id="manzana" name="manzana" required>
            </div>
            <div class="mb-3">
                <label for="lote" class="form-label">Lote:</label>
                <input type="text" class="form-control" id="lote" name="lote" required>
            </div>
            <div class="mb-3">
                <label for="area" class="form-label">Área:</label>
                <input type="text" class="form-control" id="area" name="area" required>
            </div>
            <div class="mb-3">
                <label for="total_venta" class="form-label">Total Venta (S/):</label>
                <input type="number" step="0.01" class="form-control" id="total_venta" name="total_venta" required>
            </div>
            <div class="mb-3">
                <label for="inicial" class="form-label">Inicial (S/):</label>
                <input type="number" step="0.01" class="form-control" id="inicial" name="inicial" required>
            </div>
            <div class="mb-3">
                <label for="cuota_mensual" class="form-label">Cuota Mensual (S/):</label>
                <input type="number" step="0.01" class="form-control" id="cuota_mensual" name="cuota_mensual" required>
            </div>
            <div class="mb-3">
                <label for="modo_pago" class="form-label">Modo de Pago:</label>
                <select class="form-control" id="modo_pago" name="modo_pago" required>
                    <option value="Depósito">Depósito</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="Efectivo">Efectivo</option>
                </select>
            </div>
            <div class="form-group">
        <label for="fecha_deposito">Fecha de Depósito:</label>
        <input type="date" id="fecha_deposito" name="fecha_deposito" class="form-control" required>
    </div>

    <button type="submit" class="btn-submit">Enviar</button>
</form>
        </form>
    </div>
</body>
</html>
