<?php
// Incluir el archivo con la clase Report que maneja la creación de PDFs
require_once('../../helpers/report.php');
// Incluir el archivo que contiene la clase para acceder a los datos de los servicios
require_once('../../models/data/servicio_data.php');

// Crear una instancia de la clase Report
$pdf = new Report;

// Crear una instancia de la clase ServicioData para acceder a los datos
$servicio = new ServicioData;

// Obtener los datos de los servicios
if ($dataServicios = $servicio->readServicios()) {
    // Iniciar el reporte con título
    $pdf->startReport('Reporte de Servicios');

    // Definir los anchos de las columnas ajustados
    $colWidths = [55, 90]; // Nombre, Descripción

    // Calcular el ancho total de la tabla
    $totalWidth = array_sum($colWidths);

    // Calcular el margen izquierdo para centrar la tabla
    $pageWidth = $pdf->GetPageWidth();
    $leftMargin = ($pageWidth - $totalWidth) / 2;

    // Establecer color para el encabezado de la tabla (fondo negro y texto blanco)
    $pdf->setFillColor(0, 0, 0); // Negro
    $pdf->setTextColor(255, 255, 255); // Blanco
    $pdf->setFont('Arial', 'B', 10); // Fuente para el encabezado

    // Ajustar la posición de impresión para centrar la tabla
    $pdf->SetX($leftMargin);

    // Imprimir los encabezados de la tabla
    $pdf->cell($colWidths[0], 10, utf8_decode('Nombre'), 1, 0, 'C', 1);
    $pdf->cell($colWidths[1], 10, utf8_decode('Descripción'), 1, 1, 'C', 1);

    // Restablecer colores y fuente para los datos
    $pdf->setTextColor(0, 0, 0); // Negro
    $pdf->setFont('Arial', '', 10); // Fuente para los datos

    // Inicializar un contador de filas
    $counter = 0;

    // Recorrer los datos de los servicios y agregar las filas a la tabla
    foreach ($dataServicios as $row) {
        // Verificar si se ha alcanzado el límite de filas en una página
        if ($counter == 18) {
            // Si es así, agregar una nueva página
            $pdf->AddPage();
            // Ajustar la posición de impresión para centrar la tabla en la nueva página
            $pdf->SetX($leftMargin);
            // Reimprimir los encabezados de la tabla en la nueva página
            $pdf->setFillColor(0, 0, 0); // Negro
            $pdf->setTextColor(255, 255, 255); // Blanco
            $pdf->setFont('Arial', 'B', 10); // Fuente para el encabezado
            $pdf->cell($colWidths[0], 10, utf8_decode('Nombre'), 1, 0, 'C', 1);
            $pdf->cell($colWidths[1], 10, utf8_decode('Descripción'), 1, 1, 'C', 1);
            // Restablecer colores y fuente para las filas
            $pdf->setTextColor(0, 0, 0); // Negro
            $pdf->setFont('Arial', '', 10); // Fuente para los datos
            $counter = 0;
        }

        // Ajustar la posición de impresión para centrar la tabla
        $pdf->SetX($leftMargin);

        // Imprimir las celdas para cada columna
        $pdf->cell($colWidths[0], 10, utf8_decode($row['nombre_servicio']), 1, 0, 'C');
        $pdf->cell($colWidths[1], 10, utf8_decode($row['descripcion_servicio']), 1, 1, 'C');

        // Incrementar el contador de filas
        $counter++;
    }

    // Generar y mostrar el PDF en el navegador
    $pdf->output('I', 'reporte_servicios.pdf');
} else {
    // Si no hay datos disponibles, mostrar un mensaje
    print('No hay datos disponibles');
}
?>
