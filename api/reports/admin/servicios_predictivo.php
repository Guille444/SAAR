<?php
// Incluir el archivo con la clase Report que maneja la creación de PDFs
require_once('../../helpers/report.php');
// Incluir el archivo que contiene la clase para acceder a los datos de los servicios
require_once('../../models/data/servicio_data.php');

// Crear una instancia de la clase Report
$pdf = new Report;
// Crear una instancia de la clase ServicioData para acceder a los datos
$servicio = new ServicioData;

// Obtener los datos de los servicios más solicitados
if ($dataServicios = $servicio->readServiciosMasSolicitados()) {
    // Iniciar el reporte con título
    $pdf->startReport('Reporte Predictivo de Demanda de Servicios');

    // Definir los anchos de las columnas
    $colWidths = [60, 45, 45]; // Nombre del Servicio, Demandas Previas, Predicción (3 Meses)
    $totalWidth = array_sum($colWidths); // Ancho total de la tabla

    // Establecer color para el encabezado de la tabla
    $pdf->setFillColor(0, 0, 0); // Negro
    $pdf->setTextColor(255, 255, 255); // Blanco
    $pdf->setFont('Arial', 'B', 10); // Fuente para el encabezado

    // Ajustar la posición de impresión para centrar la tabla
    $pageWidth = $pdf->GetPageWidth(); // Ancho total de la página
    $leftMargin = ($pageWidth - $totalWidth) / 2; // Margen izquierdo para centrar la tabla
    $pdf->SetX($leftMargin); // Ajustar la posición horizontal

    // Imprimir los encabezados de la tabla
    $pdf->cell($colWidths[0], 10, $pdf->encodeString('Nombre del Servicio'), 1, 0, 'C', 1);
    $pdf->cell($colWidths[1], 10, $pdf->encodeString('Demandas Previas'), 1, 0, 'C', 1);
    $pdf->cell($colWidths[2], 10, $pdf->encodeString('Predicción (3 Meses)'), 1, 1, 'C', 1);

    // Restablecer colores y fuente para los datos
    $pdf->setTextColor(0, 0, 0); // Negro
    $pdf->setFont('Arial', '', 10); // Fuente para los datos

    // Recorrer los datos de los servicios y agregar las filas a la tabla
    foreach ($dataServicios as $row) {
        // Ajustar la posición de impresión para centrar la tabla
        $pdf->SetX($leftMargin); // Ajustar la posición horizontal

        // Imprimir las celdas para cada columna
        $pdf->cell($colWidths[0], 10, $pdf->encodeString($row['nombre_servicio']), 1, 0, 'C');
        $pdf->cell($colWidths[1], 10, $pdf->encodeString($row['demandas_previas']), 1, 0, 'C');
        $pdf->cell($colWidths[2], 10, $pdf->encodeString($row['prediccion_tres_meses']), 1, 1, 'C');
    }

    // Generar y mostrar el PDF en el navegador
    $pdf->output('I', 'reporte_predictivo_3_meses.pdf');
} else {
    // Si no hay datos disponibles, mostrar un mensaje
    print('No hay datos disponibles');
}
?>