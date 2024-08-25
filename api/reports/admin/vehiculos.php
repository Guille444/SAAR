<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');
// Se incluyen las clases para la transferencia y acceso a datos.
require_once('../../models/data/vehiculo_data.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;
// Se instancia la entidad correspondiente.
$vehiculo = new VehiculoData;

// Se obtienen los registros de los vehículos por marca.
if ($dataVehiculos = $vehiculo->readByMarca()) {
    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Reporte de vehículos registrados');

    // Establecer color de fondo del encabezado (Negro)
    $pdf->setFillColor(0, 0, 0);
    $pdf->SetTextColor(255, 255, 255); // Texto blanco
    
    // Se establece la fuente para los encabezados.
    $pdf->setFont('Arial', 'B', 11);
    
    // Se imprimen las celdas con los encabezados.
    $pdf->cell(55, 10, $pdf->encodeString('Cliente'), 1, 0, 'C', 1);
    $pdf->cell(45, 10, $pdf->encodeString('Marca'), 1, 0, 'C', 1);
    $pdf->cell(45, 10, $pdf->encodeString('Modelo'), 1, 0, 'C', 1);
    $pdf->cell(45, 10, $pdf->encodeString('Placa'), 1, 1, 'C', 1);
    
    // Restablecer el color del texto a negro para los datos.
    $pdf->SetTextColor(0, 0, 0);
    $pdf->setFont('Arial', '', 11);
    
    // Variable para contar el número de filas
    $rowCount = 0;
    // Máximo de filas por página
    $maxRowsPerPage = 18;
    
    // Se recorren los registros fila por fila.
    foreach ($dataVehiculos as $rowVehiculo) {
        $cliente = $pdf->encodeString($rowVehiculo['nombre_cliente'] . ' ' . $rowVehiculo['apellido_cliente']);
        
        // Se imprimen las celdas con los datos de los vehículos.
        $pdf->cell(55, 10, $cliente, 1, 0, 'C');
        $pdf->cell(45, 10, $pdf->encodeString($rowVehiculo['marca_vehiculo']), 1, 0, 'C');
        $pdf->cell(45, 10, $pdf->encodeString($rowVehiculo['modelo_vehiculo']), 1, 0, 'C');
        $pdf->cell(45, 10, $pdf->encodeString($rowVehiculo['placa_vehiculo']), 1, 1, 'C');

        // Incrementa el contador de filas
        $rowCount++;
        
        // Si el número de filas supera el máximo permitido por página, crea una nueva página
        if ($rowCount % $maxRowsPerPage == 0) {
            $pdf->AddPage();
            // Reimprime los encabezados en la nueva página
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255); // Texto blanco
            $pdf->setFont('Arial', 'B', 11);
            $pdf->cell(55, 10, $pdf->encodeString('Cliente'), 1, 0, 'C', 1);
            $pdf->cell(45, 10, $pdf->encodeString('Marca'), 1, 0, 'C', 1);
            $pdf->cell(45, 10, $pdf->encodeString('Modelo'), 1, 0, 'C', 1);
            $pdf->cell(45, 10, $pdf->encodeString('Placa'), 1, 1, 'C', 1);
            $pdf->SetTextColor(0, 0, 0); // Texto negro para los datos
            $pdf->setFont('Arial', '', 11);
        }
    }
    
    // Se llama implícitamente al método footer() y se envía el documento al navegador web.
    $pdf->output('I', 'vehiculos.pdf');
} else {
    print('No hay vehículos registrados');
}
?>
