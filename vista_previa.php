<?php
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
    $registro_no = uniqid(); // Genera un ID único para el registro


    // Cálculos
    $saldo_contrato = $total_venta - $inicial;
    $fecha_actual = date("d/m/Y"); // Fecha de elaboración
    $cuotas = [];
    $saldo_restante = $saldo_contrato;
    $fecha_pago = new DateTime($fecha_contrato);
    $monto_abonado = $inicial;
    $cancelado_a_la_fecha = $inicial;

    for ($i = 1; $saldo_restante > 0; $i++) {
        $monto_pagado = ($saldo_restante >= $cuota_mensual) ? $cuota_mensual : $saldo_restante;
        $saldo_restante -= $monto_pagado;
        $monto_abonado += $monto_pagado;

        $fecha_deposito = ($i <= 2) ? $fecha_pago->format("d/m/Y") : ''; // Fechas de depósito para las dos primeras cuotas
        
        $cuotas[] = [
            'num_cuota' => $i,
            'fecha_pago' => $fecha_pago->format("d/m/Y"),
            'cuota_mensual' => $monto_pagado,
            'monto_restante' => $saldo_restante,
            'fecha_deposito' => $fecha_Deposito,
            'cancelado_a_la_fecha' => $cancelado_a_la_fecha,
            'monto_abonado' => $monto_abonado,
            'modo_pago' => $modo_pago,
        ];
        $cancelado_a_la_fecha = $monto_abonado;
        $fecha_pago->modify('+1 month'); // Incrementa 1 mes
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Reporte de Cuotas</title>
</head>

<body>
<div class="container">
         <!-- Encabezado con logo -->
         <header>
            <div class="logo-container">
                <img src="images/logo_henko.png" alt="Logo Henko Group" class="logo">
            </div>
            <h1>HENKO GROUP S.A.C.</h1>
            <p>Proyecto: <strong>Finca Amorena</strong></p>
        </header>
 <!-- Información del Cliente y Lote -->
 <section class="info">
 <div class="info-cliente">
        <p><strong>Cliente:</strong> <?= htmlspecialchars($cliente) ?></p>
        <p><strong>DNI:</strong> <?= htmlspecialchars($dni) ?></p>
        <p><strong>Fecha Firma del Contrato:</strong> <?= htmlspecialchars($fecha_contrato) ?></p>
        <p><strong>Manzana:</strong> <?= htmlspecialchars($manzana) ?> <strong>Lote:</strong> <?= htmlspecialchars($lote) ?></p>
        <p><strong>Área:</strong> <?= htmlspecialchars($area) ?> m²</p>
        </div>

<div class="info-lote">
        <p><strong>Version:</strong> 001</p>
        <p><strong>Total Venta:</strong> S/ <?= number_format($total_venta, 2) ?></p>
        <p><strong>Inicial:</strong> S/ <?= number_format($inicial, 2) ?></p>
        <p><strong>Saldo Contrato:</strong> S/ <?= number_format($saldo_contrato, 2) ?></p>
        <p><strong>Fecha de Elaboración:</strong> <?= $fecha_actual ?></p>
 
</div>

</section>  


   <!-- Cronograma de Pagos -->
   <section class="cronograma">
            <h3>Cronograma de Pagos</h3>
            <table>
                <thead>
                    <tr>
                        <th>N° Cuota</th>
                        <th>Fecha de Pago</th>
                        <th>Cuota Mensual</th>
                        <th>Monto Restante</th>
                        <th>Fecha de Depósito</th>
                        <th>Cancelado a la Fecha</th>
                        <th>Monto Abonado a la Fecha</th>
                        <th>Modo de Pago</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cuotas as $cuota): ?>
                        <tr>
                            <td><?= $cuota['num_cuota'] ?></td>
                            <td><?= $cuota['fecha_pago'] ?></td>
                            <td>S/ <?= number_format($cuota['cuota_mensual'], 2) ?></td>
                            <td>S/ <?= number_format($cuota['monto_restante'], 2) ?></td>
                            <td><?= $cuota['fecha_deposito'] ?></td>
                            <td>S/ <?= number_format($cuota['cancelado_a_la_fecha'], 2) ?></td>
                            <td class="highlight">S/ <?= number_format($cuota['monto_abonado'], 2) ?></td>
                            <td><?= $cuota['modo_pago'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
   </section>

   <footer class="footer">
    <div class="footer-left">
    <p>VºBº SUBGERENTE</p>
        <img src="images/firma.png" alt="Firma Subgerente" class="firma-img">

    </div>
    <div class="footer-right">
        <img src="images/logo_amorena.png" alt="Logo Amorena" class="footer-img">
    </div>
</footer>

   
    </div>
</body>
</html>
