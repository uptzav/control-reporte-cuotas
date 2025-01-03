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
    <title>Formulario de Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Procesa los datos del formulario
        $cliente = $_POST['cliente'] ?? '';
        $dni = $_POST['dni'] ?? '';
        $fecha_contrato = $_POST['fecha_contrato'] ?? '';
        $modo_pago = $_POST['modo_pago'] ?? '';
        $manzana = $_POST['manzana'] ?? '';
        $lote = $_POST['lote'] ?? '';
        $area = $_POST['area'] ?? '';
        $moneda = $_POST['moneda'] ?? '';
        $total_venta = $_POST['total_venta'] ?? '';
        $inicial = $_POST['inicial'] ?? '';
        $cuota_mensual = $_POST['cuota_mensual'] ?? '';
        $fecha_deposito = $_POST['fecha_deposito'] ?? '';

    

  header('Location: menu_crud.php');
  exit;
    }
    ?>


    <header class="bg-light py-3">
        <div class="container text-center">
            <img src="images/logo_henko.png" alt="Logo Henko Group" class="logo" style="max-width: 200px; height: auto;">
        </div>
    </header>

    <main class="container mt-5">
        <h1 class="text-center mb-4">Formulario de Registro</h1>
        <form action="vista_previa.php" method="post" class="row g-3">
            <!-- Cliente y DNI -->
            <div class="col-md-6">
                <label for="cliente" class="form-label fw-bold">Cliente:</label>
                <input type="text" class="form-control" name="cliente" id="cliente" pattern="[a-zA-Z\s\/\-]+" required title="Solo se permiten letras, espacios, / y -">
            </div>
            <div class="col-md-6">
                <label for="dni" class="form-label fw-bold">DNI:</label>
                <input type="text" class="form-control" name="dni" id="dni" maxlength="8" pattern="\d{8}" required title="El DNI debe contener exactamente 8 dígitos">
            </div>

            <!-- Fecha de Contrato y Modo de Pago -->
            <div class="col-md-6">
                <label for="fecha_contrato" class="form-label fw-bold">Fecha Firma del Contrato:</label>
                <input type="date" class="form-control" id="fecha_contrato" name="fecha_contrato" required>
            </div>
            <div class="col-md-6">
                <label for="modo_pago" class="form-label fw-bold">Modo de Pago:</label>
                <select class="form-select" id="modo_pago" name="modo_pago" required>
                    <option value="Depósito">Depósito</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="Efectivo">Efectivo</option>
                </select>
            </div>

            <!-- Manzana y Lote -->
            <div class="col-md-6">
                <label for="manzana" class="form-label fw-bold">Manzana:</label>
                <input type="text" class="form-control" name="manzana" id="manzana" maxlength="2" required>
            </div>
            <div class="col-md-6">
                <label for="lote" class="form-label fw-bold">Lote:</label>
                <input type="text" class="form-control" name="lote" id="lote" maxlength="2" pattern="\d{1,2}" required title="El lote debe contener solo números y un máximo de 2 dígitos">
            </div>

            <!-- Área y Moneda -->
            <div class="col-md-6">
                <label for="area" class="form-label fw-bold">Área:</label>
                <input type="text" class="form-control" name="area" id="area" maxlength="6" pattern="\d{1,3}(\.\d+)?" required title="Ingrese un número con hasta 3 dígitos enteros y decimales opcionales">
            </div>
            <div class="col-md-6">
                <label for="moneda" class="form-label fw-bold">Moneda:</label>
                <select class="form-select" id="moneda" name="moneda" required>
                    <option value="S/. ">Soles</option>
                    <option value="$ ">Dólares</option>
                </select>
            </div>

            <!-- Total Venta, Inicial, y Cuota Mensual -->
            <div class="col-md-4">
                <label for="total_venta" class="form-label fw-bold">Total Venta:</label>
                <input type="number" step="0.01" class="form-control" id="total_venta" name="total_venta" required>
            </div>
            <div class="col-md-4">
                <label for="inicial" class="form-label fw-bold">Inicial:</label>
                <input type="number" step="0.01" class="form-control" id="inicial" name="inicial" required>
            </div>
            <div class="col-md-4">
                <label for="cuota_mensual" class="form-label fw-bold">Cuota Mensual:</label>
                <input type="number" step="0.01" class="form-control" id="cuota_mensual" name="cuota_mensual" required>
            </div>

            <!-- Fecha de Depósito -->
            <div class="col-md-6">
                <label for="fecha_deposito" class="form-label fw-bold">Fecha de Depósito:</label>
                <input type="date" id="fecha_deposito" name="fecha_deposito" class="form-control" required>
            </div>

            <!-- Botón Enviar -->
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-success btn-lg w-100 " >Enviar</button>
            </div>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    const totalVentaInput = document.getElementById('total_venta');
    const inicialInput = document.getElementById('inicial');
    const cuotaMensualInput = document.getElementById('cuota_mensual');

    function validateFields() {
        const totalVenta = parseFloat(totalVentaInput.value) || 0;
        const inicial = parseFloat(inicialInput.value) || 0;
        const saldoRestante = totalVenta - inicial;

        if (inicial > totalVenta) {
            alert('El monto inicial no puede ser mayor que el Total Venta.');
            inicialInput.value = '';
        }

    }

    totalVentaInput.addEventListener('input', validateFields);
    inicialInput.addEventListener('input', validateFields);
    cuotaMensualInput.addEventListener('input', validateFields);
</script>

<script>
    document.getElementById("manzana").addEventListener("input", function() {
        if (this.value.length > 2) {
            this.value = this.value.slice(0, 2); // Limita el texto a 2 caracteres
        }
    });
</script>

<script>
    document.getElementById("dni").addEventListener("input", function() {
        this.value = this.value.replace(/[^0-9]/g, ''); // Elimina cualquier carácter que no sea un número
        if (this.value.length > 8) {
            this.value = this.value.slice(0, 8); // Limita a 8 caracteres
        }
    });
</script>

<script>
    document.getElementById("cliente").addEventListener("input", function() {
        this.value = this.value.replace(/[^a-zA-Z\s\/\-]/g, ''); // Permite letras, espacios, "/", y "-"
    });
</script>

<script>
    document.getElementById("lote").addEventListener("input", function() {
        this.value = this.value.replace(/[^0-9]/g, ''); // Elimina cualquier carácter que no sea un número
        if (this.value.length > 8) {
            this.value = this.value.slice(0, 2); // Limita a 2 caracteres
        }
    });
</script>

<script>
    document.getElementById("lote").addEventListener("input", function() {
        this.value = this.value.replace(/[^0-9]/g, ''); // Elimina cualquier carácter que no sea un número
        if (this.value.length > 8) {
            this.value = this.value.slice(0, 3); // Limita a 2 caracteres
        }
    });
</script>

<script>
    document.getElementById("area").addEventListener("input", function() {
        // Permite solo números, un punto decimal y un máximo de 3 enteros
        this.value = this.value.replace(/[^0-9.]/g, ''); // Elimina caracteres no permitidos
        const parts = this.value.split('.');
        
        if (parts[0].length > 3) {
            this.value = parts[0].slice(0, 3) + (parts[1] ? '.' + parts[1] : ''); // Limita a 3 dígitos enteros
        }

        if (parts[1] && parts[1].length > 3) {
            parts[1] = parts[1].slice(0, 3); // Limita los decimales a 2 si es necesario
            this.value = parts.join('.');
        }
    });
</script>

</body>
</html>








