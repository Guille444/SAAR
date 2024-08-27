<?php
// Incluir el archivo con la clase Report que maneja la creación de PDFs
require_once('../../helpers/report.php');
// Incluir el archivo que contiene la clase para acceder a los datos de los administradores
require_once('../../models/data/administrador_data.php');

// Crear una instancia de la clase Report
$pdf = new Report;
// Crear una instancia de la clase AdministradorData para acceder a los datos
$administrador = new AdministradorData;

// Obtener los datos de los administradores
if ($dataAdministradores = $administrador->readAdministradores()) {
    // Iniciar el reporte con título
    $pdf->startReport('Reporte de Empleados');

    // Definir los anchos de las columnas
    $colWidths = [30, 30, 30, 55, 35]; // Nombres, Apellidos, Alias, Correo, Rol
    $totalWidth = array_sum($colWidths); // Calcular el ancho total de la tabla
    $pageWidth = $pdf->GetPageWidth(); // Calcular el ancho de la página
    $leftMargin = ($pageWidth - $totalWidth) / 2; // Calcular el margen izquierdo para centrar la tabla

    // Establecer colores y fuente para el encabezado de la tabla
    $pdf->setFillColor(0, 0, 0); // Fondo negro
    $pdf->SetTextColor(255, 255, 255); // Texto blanco
    $pdf->setFont('Arial', 'B', 10); // Fuente en negrita para el encabezado

    // Ajustar la posición de impresión para centrar la tabla
    $pdf->SetX($leftMargin);
    
    // Imprimir los encabezados de la tabla
    $pdf->cell($colWidths[0], 10, 'Nombre', 1, 0, 'C', 1);
    $pdf->cell($colWidths[1], 10, 'Apellido', 1, 0, 'C', 1);
    $pdf->cell($colWidths[2], 10, 'Alias', 1, 0, 'C', 1);
    $pdf->cell($colWidths[3], 10, 'Correo', 1, 0, 'C', 1);
    $pdf->cell($colWidths[4], 10, 'Rol', 1, 1, 'C', 1);

    // Restablecer colores y fuente para los datos
    $pdf->setTextColor(0, 0, 0); // Texto negro
    $pdf->setFont('Arial', '', 10); // Fuente normal para los datos

    $counter = 0; // Contador para controlar el número de filas por página

    // Recorrer los datos de los administradores y agregar las filas a la tabla
    foreach ($dataAdministradores as $row) {
        // Verificar si se ha alcanzado el límite de filas en una página
        if ($counter == 17) {
            // Agregar una nueva página y reimprimir los encabezados
            $pdf->AddPage();
            $pdf->SetX($leftMargin);
            $pdf->setFillColor(0, 0, 0); // Fondo negro
            $pdf->SetTextColor(255, 255, 255); // Texto blanco
            $pdf->setFont('Arial', 'B', 10); // Fuente en negrita
            $pdf->cell($colWidths[0], 10, 'Nombre', 1, 0, 'C', 1);
            $pdf->cell($colWidths[1], 10, 'Apellido', 1, 0, 'C', 1);
            $pdf->cell($colWidths[2], 10, 'Alias', 1, 0, 'C', 1);
            $pdf->cell($colWidths[3], 10, 'Correo', 1, 0, 'C', 1);
            $pdf->cell($colWidths[4], 10, 'Rol', 1, 1, 'C', 1);
            $pdf->setTextColor(0, 0, 0); // Texto negro
            $pdf->setFont('Arial', '', 10); // Fuente normal
            $pdf->setFillColor(255, 255, 255); // Fondo blanco
            $counter = 0; // Reiniciar el contador de filas
        }
        // Ajustar la posición de impresión y agregar los datos de la fila
        $pdf->SetX($leftMargin);
        $pdf->cell($colWidths[0], 10, $pdf->encodeString($row['nombre_administrador']), 1, 0, 'C');
        $pdf->cell($colWidths[1], 10, $pdf->encodeString($row['apellido_administrador']), 1, 0, 'C');
        $pdf->cell($colWidths[2], 10, $pdf->encodeString($row['alias_administrador']), 1, 0, 'C');
        $pdf->cell($colWidths[3], 10, $pdf->encodeString($row['correo_administrador']), 1, 0, 'C');
        $pdf->cell($colWidths[4], 10, $pdf->encodeString($row['nombre_rol']), 1, 1, 'C');
        $counter++; // Incrementar el contador de filas
    }

    // Generar y mostrar el PDF en el navegador
    $pdf->output('I', 'reporte_administradores.pdf');
} else {
    // Si no hay datos disponibles, mostrar un mensaje
    print('No hay datos disponibles');
}
?>