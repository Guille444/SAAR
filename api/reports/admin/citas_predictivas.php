<?php
// Incluir el archivo con la clase Report que maneja la creación de PDFs
require_once('../../helpers/report.php');
// Incluir el archivo que contiene la clase para acceder a los datos de las citas
require_once('../../models/data/citas_data.php');

// Crear una instancia de la clase Report
$pdf = new Report;
// Crear una instancia de la clase CitasData para acceder a los datos
$cita = new CitasData;

// Obtener los datos de las citas mensuales
if ($dataCitas = $cita->readCitasMensuales()) {
    // Iniciar el reporte con título
    $pdf->startReport('Reporte Predictivo de Citas');

    // Definir los anchos de las columnas
    $colWidths = [60, 45, 60]; // Mes, Cantidad de Citas, Predicción
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
    $pdf->cell($colWidths[0], 10, $pdf->encodeString('Mes'), 1, 0, 'C', 1);
    $pdf->cell($colWidths[1], 10, $pdf->encodeString('Cantidad de Citas'), 1, 0, 'C', 1);
    $pdf->cell($colWidths[2], 10, $pdf->encodeString('Predicción (Próximo Mes)'), 1, 1, 'C', 1);

    // Restablecer colores y fuente para los datos
    $pdf->setTextColor(0, 0, 0); // Negro
    $pdf->setFont('Arial', '', 10); // Fuente para los datos

    // Array para traducir meses del inglés al español
    $mesesEnEspañol = [
        '01' => 'Enero',
        '02' => 'Febrero',
        '03' => 'Marzo',
        '04' => 'Abril',
        '05' => 'Mayo',
        '06' => 'Junio',
        '07' => 'Julio',
        '08' => 'Agosto',
        '09' => 'Septiembre',
        '10' => 'Octubre',
        '11' => 'Noviembre',
        '12' => 'Diciembre'
    ];

    // Recorrer los datos y agregar las filas a la tabla
    foreach ($dataCitas as $row) {
        // Convertir el mes numérico en nombre del mes en español
        $fecha = DateTime::createFromFormat('Y-m', $row['mes_numero']);
        $mesNumero = $fecha->format('m'); // Obtener el número del mes
        $mesEspañol = $mesesEnEspañol[$mesNumero]; // Obtener el nombre del mes en español
        $anio = $fecha->format('Y'); // Obtener el año

        // Calcular la predicción para el próximo mes (aumento del 10% en este caso)
        $prediccion = round($row['cantidad_citas'] * 1.1);

        // Ajustar la posición de impresión para centrar la tabla
        $pdf->SetX($leftMargin); // Ajustar la posición horizontal

        // Imprimir las celdas para cada columna
        $pdf->cell($colWidths[0], 10, $pdf->encodeString("$mesEspañol $anio"), 1, 0, 'C');
        $pdf->cell($colWidths[1], 10, $pdf->encodeString($row['cantidad_citas']), 1, 0, 'C');
        $pdf->cell($colWidths[2], 10, $pdf->encodeString($prediccion), 1, 1, 'C');
    }

    // Generar y mostrar el PDF en el navegador
    ob_clean(); // Limpiar el búfer de salida
    $pdf->output('I', 'reporte_predictivo_citas_proximo_mes.pdf');
} else {
    // Si no hay datos disponibles, mostrar un mensaje
    print('No hay datos disponibles');
}
?>