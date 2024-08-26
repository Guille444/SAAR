<?php
// Incluir el archivo con la clase Report que maneja la creación de PDFs
require_once('../../helpers/report.php');
// Incluir el archivo que contiene la clase para acceder a los datos de las citas
require_once('../../models/data/citas_data.php');
// Incluir el archivo que contiene la clase para acceder a los datos de los clientes
require_once('../../models/data/cliente_data.php');

// Crear una instancia de la clase Report para generar el PDF
$pdf = new Report;
// Crear instancias de las clases para manejar datos de citas y clientes
$cita = new CitasData;
$cliente = new ClienteData;

// Verifica si se ha proporcionado el parámetro 'idCliente'
if (isset($_GET['idCliente'])) {
    // Establece el ID del cliente en el objeto ClienteData y verifica si es válido
    if ($cliente->setId($_GET['idCliente']) && $cita->setIdCliente($_GET['idCliente'])) {
        // Verifica si el cliente existe en la base de datos
        if ($rowCliente = $cliente->readOne()) {
            // Inicia el reporte y establece el título con el nombre del cliente
            $pdf->startReport('Citas del Cliente: ' . $rowCliente['nombre_cliente'] . ' ' . $rowCliente['apellido_cliente']);
            
            // Obtiene los datos de las citas asociadas al cliente especificado
            if ($dataCitas = $cita->citasPorIdCliente()) {
                // Define los anchos de las columnas para el reporte
                $colWidths = [30, 40, 40, 60, 30];
                $totalWidth = array_sum($colWidths); // Calcula el ancho total de la tabla
                $pageWidth = $pdf->GetPageWidth(); // Obtiene el ancho de la página
                $leftMargin = ($pageWidth - $totalWidth) / 2; // Calcula el margen izquierdo para centrar la tabla

                // Establece colores y fuente para el encabezado de la tabla
                $pdf->setFillColor(0, 0, 0); // Color de fondo del encabezado (negro)
                $pdf->setDrawColor(0, 0, 0); // Color de los bordes (negro)
                $pdf->SetTextColor(255, 255, 255); // Color del texto del encabezado (blanco)
                $pdf->setFont('Arial', 'B', 11); // Fuente para el encabezado (Arial, negrita, tamaño 11)

                // Ajusta la posición de impresión para centrar la tabla
                $pdf->SetX($leftMargin);
                // Imprime los encabezados de la tabla con el color de fondo negro y texto blanco
                $pdf->cell($colWidths[0], 10, 'ID Cita', 1, 0, 'C', 1);
                $pdf->cell($colWidths[1], 10, 'Fecha Cita', 1, 0, 'C', 1);
                $pdf->cell($colWidths[2], 10, 'Placa Vehículo', 1, 0, 'C', 1);
                $pdf->cell($colWidths[3], 10, 'Servicio', 1, 0, 'C', 1);
                $pdf->cell($colWidths[4], 10, 'Estado', 1, 1, 'C', 1);

                // Restablece colores y fuente para los datos de la tabla
                $pdf->setTextColor(0, 0, 0); // Color del texto de los datos (negro)
                $pdf->setFont('Arial', '', 11); // Fuente para los datos (Arial, normal, tamaño 11)
                $pdf->setFillColor(255, 255, 255); // Color de fondo de las filas de datos (blanco)
                
                $counter = 0; // Contador para controlar el número de filas por página
                // Recorre los datos de las citas y agrega las filas a la tabla
                foreach ($dataCitas as $rowCita) {
                    // Verifica si se ha alcanzado el límite de filas en una página (18 en este caso)
                    if ($counter == 18) {
                        // Agrega una nueva página y reimprime los encabezados
                        $pdf->AddPage();
                        $pdf->SetX($leftMargin);
                        $pdf->setFillColor(0, 0, 0); // Color de fondo del encabezado (negro)
                        $pdf->setTextColor(255, 255, 255); // Color del texto del encabezado (blanco)
                        $pdf->setFont('Arial', 'B', 11); // Fuente para el encabezado (Arial, negrita, tamaño 11)
                        $pdf->cell($colWidths[0], 10, 'ID Cita', 1, 0, 'C', 1);
                        $pdf->cell($colWidths[1], 10, 'Fecha Cita', 1, 0, 'C', 1);
                        $pdf->cell($colWidths[2], 10, 'Placa Vehículo', 1, 0, 'C', 1);
                        $pdf->cell($colWidths[3], 10, 'Servicio', 1, 0, 'C', 1);
                        $pdf->cell($colWidths[4], 10, 'Estado', 1, 1, 'C', 1);
                        $pdf->setTextColor(0, 0, 0); // Color del texto de los datos (negro)
                        $pdf->setFont('Arial', '', 11); // Fuente para los datos (Arial, normal, tamaño 11)
                        $counter = 0; // Reinicia el contador de filas
                    }
                    // Ajusta la posición de impresión y agrega los datos de la fila
                    $pdf->SetX($leftMargin);
                    $pdf->cell($colWidths[0], 10, $rowCita['id_cita'], 1, 0, 'C');
                    $pdf->cell($colWidths[1], 10, $rowCita['fecha_cita'], 1, 0, 'C');
                    $pdf->cell($colWidths[2], 10, utf8_decode($rowCita['placa_vehiculo']), 1, 0, 'C');
                    $pdf->cell($colWidths[3], 10, utf8_decode($rowCita['nombre_servicio']), 1, 0, 'C');
                    $pdf->cell($colWidths[4], 10, utf8_decode($rowCita['estado_cita']), 1, 1, 'C');
                    $counter++; // Incrementa el contador de filas
                }
            } else {
                // Mensaje si no hay citas para el cliente
                $pdf->cell(0, 10, $pdf->encodeString('No hay citas para el cliente'), 1, 1, 'C');
            }
            // Genera el PDF y lo envía al navegador
            $pdf->output('I', 'citas_cliente.pdf');
        } else {
            // Mensaje si el cliente no existe
            print('Cliente inexistente');
        }
    } else {
        // Mensaje si el ID del cliente es incorrecto
        print('Cliente incorrecto');
    }
} else {
    // Mensaje si no se ha proporcionado un ID de cliente
    print('Debe seleccionar un cliente');
}
?>