<?php
// Incluir el archivo con la clase Report que maneja la creación de PDFs
require_once('../../helpers/report.php');
// Incluir el archivo que contiene la clase para acceder a los datos de los clientes
require_once('../../models/data/cliente_data.php');

// Crear una instancia de la clase Report
$pdf = new Report;
// Crear una instancia de la clase ClienteData para acceder a los datos
$cliente = new ClienteData;

// Obtener los datos de los clientes
if ($dataClientes = $cliente->readClientes()) {
    // Iniciar el reporte con título
    $pdf->startReport('Reporte de Clientes');

    // Definir los anchos de las columnas ajustados
    $colWidths = [30, 30, 60, 30, 30]; // Nombres, Apellidos, Correo, Contacto, Estado

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
    $pdf->cell($colWidths[0], 10, $pdf->encodeString('Nombre'), 1, 0, 'C', 1);
    $pdf->cell($colWidths[1], 10, $pdf->encodeString('Apellido'), 1, 0, 'C', 1);
    $pdf->cell($colWidths[2], 10, $pdf->encodeString('Correo'), 1, 0, 'C', 1);
    $pdf->cell($colWidths[3], 10, $pdf->encodeString('Contacto'), 1, 0, 'C', 1);
    $pdf->cell($colWidths[4], 10, $pdf->encodeString('Estado'), 1, 1, 'C', 1); // Estado (Activo/Inactivo)

    // Restablecer colores y fuente para los datos
    $pdf->setTextColor(0, 0, 0); // Negro
    $pdf->setFont('Arial', '', 10); // Fuente para los datos

    // Inicializar un contador de filas
    $counter = 0;

    // Recorrer los datos de los clientes y agregar las filas a la tabla
    foreach ($dataClientes as $row) {
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
            $pdf->cell($colWidths[0], 10, $pdf->encodeString('Nombre'), 1, 0, 'C', 1);
            $pdf->cell($colWidths[1], 10, $pdf->encodeString('Apellido'), 1, 0, 'C', 1);
            $pdf->cell($colWidths[2], 10, $pdf->encodeString('Correo'), 1, 0, 'C', 1);
            $pdf->cell($colWidths[3], 10, $pdf->encodeString('Contacto'), 1, 0, 'C', 1);
            $pdf->cell($colWidths[4], 10, $pdf->encodeString('Estado'), 1, 1, 'C', 1); // Reimprimir el encabezado de estado
            // Restablecer colores y fuente para las filas
            $pdf->setTextColor(0, 0, 0); // Negro
            $pdf->setFont('Arial', '', 10); // Fuente para los datos
            $counter = 0;
        }

        // Ajustar la posición de impresión para centrar la tabla
        $pdf->SetX($leftMargin);

        // Imprimir las celdas para cada columna
        $pdf->cell($colWidths[0], 10, $pdf->encodeString($row['nombre_cliente']), 1, 0, 'C');
        $pdf->cell($colWidths[1], 10, $pdf->encodeString($row['apellido_cliente']), 1, 0, 'C');
        $pdf->cell($colWidths[2], 10, $pdf->encodeString($row['correo_cliente']), 1, 0, 'C');
        $pdf->cell($colWidths[3], 10, $pdf->encodeString($row['contacto_cliente']), 1, 0, 'C');
        $pdf->cell($colWidths[4], 10, $pdf->encodeString($row['estado_cliente'] ? 'Activo' : 'Inactivo'), 1, 1, 'C'); // Mostrar estado como Activo/Inactivo

        // Incrementar el contador de filas
        $counter++;
    }

    // Generar y mostrar el PDF en el navegador
    $pdf->output('I', 'reporte_clientes.pdf');
} else {
    // Si no hay datos disponibles, mostrar un mensaje
    print('No hay datos disponibles');
}
