<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos para el footer sticky */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        main {
            flex: 1; /* Empuja el footer hacia abajo si el contenido no llena la pantalla */
        }

        footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 1rem 0;
            border-top: 1px solid #ddd;
        }
    </style>
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

    <footer>
        <p class="mb-0">Desarrollado por Henko Group S.A.C. - 2024</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>