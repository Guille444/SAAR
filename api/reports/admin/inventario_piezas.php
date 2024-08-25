<?php
// Incluir el archivo con la clase Report que maneja la creación de PDFs
require_once('../../helpers/report.php');
// Incluir el archivo que contiene la clase para acceder a los datos del inventario
require_once('../../models/data/inventario_data.php');
// Crear una instancia de la clase Report
$pdf = new Report;
// Crear una instancia de la clase InventarioData para acceder a los datos
$inventario = new InventarioData;
// Obtener los datos del inventario de piezas
if ($dataInventario = $inventario->readInventarioPiezas()) {
    $pdf->startReport('Reporte de Inventario de Piezas');
    // Definir los anchos de las columnas
    $colWidths = [60, 30, 30, 40]; // Anchos ajustados sin la columna de descripción
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
    $pdf->cell($colWidths[0], 10, utf8_decode('Nombre de la Pieza'), 1, 0, 'C', 1);
    $pdf->cell($colWidths[1], 10, utf8_decode('Cantidad'), 1, 0, 'C', 1);
    $pdf->cell($colWidths[2], 10, utf8_decode('Proveedor'), 1, 0, 'C', 1);
    $pdf->cell($colWidths[3], 10, utf8_decode('Fecha de Ingreso'), 1, 1, 'C', 1);
    // Restablecer colores y fuente para los datos
    $pdf->setTextColor(0, 0, 0); // Negro
    $pdf->setFont('Arial', '', 10); // Fuente para los datos
    // Inicializar un contador de filas
    $counter = 0;
    // Recorrer los datos del inventario y agregar las filas a la tabla
    foreach ($dataInventario as $row) {
        // Ajustar la posición de impresión para centrar la tabla
        $pdf->SetX($leftMargin);
        // Imprimir las celdas para cada columna
        $pdf->cell($colWidths[0], 10, utf8_decode($row['nombre_pieza']), 1, 0, 'C');
        $pdf->cell($colWidths[1], 10, $row['cantidad_disponible'], 1, 0, 'C');
        $pdf->cell($colWidths[2], 10, utf8_decode($row['proveedor']), 1, 0, 'C');
        $pdf->cell($colWidths[3], 10, $row['fecha_ingreso'], 1, 1, 'C');
        // Incrementar el contador de filas
        $counter++;
        // Verificar si se ha alcanzado el límite de filas en una página
        if ($counter == 18) {
            // Si es así, agregar una nueva página
            $pdf->addPage();
            // Ajustar la posición de impresión para centrar la tabla en la nueva página
            $pdf->SetX($leftMargin);
            // Reimprimir los encabezados de la tabla en la nueva página
            $pdf->setFillColor(0, 0, 0); // Negro
            $pdf->setTextColor(255, 255, 255); // Blanco
            $pdf->setFont('Arial', 'B', 10); // Fuente para el encabezado
            $pdf->cell($colWidths[0], 10, utf8_decode('Nombre de la Pieza'), 1, 0, 'C', 1);
            $pdf->cell($colWidths[1], 10, utf8_decode('Cantidad'), 1, 0, 'C', 1);
            $pdf->cell($colWidths[2], 10, utf8_decode('Proveedor'), 1, 0, 'C', 1);
            $pdf->cell($colWidths[3], 10, utf8_decode('Fecha de Ingreso'), 1, 1, 'C', 1);
            // Restablecer colores y fuente para las filas
            $pdf->setTextColor(0, 0, 0); // Negro
            $pdf->setFont('Arial', '', 10); // Fuente para los datos
            $counter = 0;
        }
    }
    // Generar y mostrar el PDF en el navegador
    $pdf->output('I', 'reporte_inventario_piezas.pdf');
} else {
    // Si no hay datos disponibles, mostrar un mensaje
    print('No hay datos disponibles');
}
?>
