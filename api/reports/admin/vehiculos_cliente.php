<?php
// Incluir el archivo con la clase Report que maneja la creación de PDFs
require_once('../../helpers/report.php');
// Incluir el archivo que contiene la clase para acceder a los datos de los vehículos
require_once('../../models/data/vehiculo_data.php');
// Incluir el archivo que contiene la clase para acceder a los datos de los clientes
require_once('../../models/data/cliente_data.php');

// Crear una instancia de la clase Report para generar el PDF
$pdf = new Report;
// Crear instancias de las clases para manejar datos de vehículos y clientes
$vehiculo = new VehiculoData;
$cliente = new ClienteData;

// Verifica si se ha proporcionado el parámetro 'idCliente'
if (isset($_GET['idCliente'])) {
    $idCliente = $_GET['idCliente'];
    
    // Establece el ID del cliente en el objeto ClienteData y verifica si es válido
    if ($cliente->setId($idCliente) && $vehiculo->setIdCliente($idCliente)) {
        // Verifica si el cliente existe en la base de datos
        if ($rowCliente = $cliente->readOne()) {
            // Inicia el reporte y establece el título con el nombre del cliente
            $pdf->startReport('Vehículos del Cliente: ' . $rowCliente['nombre_cliente'] . ' ' . $rowCliente['apellido_cliente']);
            
            // Obtiene los datos de los vehículos asociados al cliente especificado
            if ($dataVehiculos = $vehiculo->vehiculosPorCliente()) {
                // Define los anchos de las columnas para el reporte
                $colWidths = [30, 40, 40, 30, 30];
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
                $pdf->cell($colWidths[0], 10, $pdf->encodeString('Placa'), 1, 0, 'C', 1);
                $pdf->cell($colWidths[1], 10, $pdf->encodeString('Marca'), 1, 0, 'C', 1);
                $pdf->cell($colWidths[2], 10, $pdf->encodeString('Modelo'), 1, 0, 'C', 1);
                $pdf->cell($colWidths[3], 10, $pdf->encodeString('Año'), 1, 0, 'C', 1);
                $pdf->cell($colWidths[4], 10, $pdf->encodeString('Color'), 1, 1, 'C', 1);
                
                // Restablece colores y fuente para los datos de la tabla
                $pdf->setTextColor(0, 0, 0); // Color del texto de los datos (negro)
                $pdf->setFont('Arial', '', 11); // Fuente para los datos (Arial, normal, tamaño 11)
                $pdf->setFillColor(255, 255, 255); // Color de fondo de las filas de datos (blanco)
                $counter = 0; // Contador para controlar el número de filas por página
                
                // Recorre los datos de los vehículos y agrega las filas a la tabla
                foreach ($dataVehiculos as $rowVehiculo) {
                    // Verifica si se ha alcanzado el límite de filas en una página (18 en este caso)
                    if ($counter == 18) {
                        // Agrega una nueva página y reimprime los encabezados
                        $pdf->AddPage();
                        $pdf->SetX($leftMargin);
                        $pdf->setFillColor(0, 0, 0); // Color de fondo del encabezado (negro)
                        $pdf->setTextColor(255, 255, 255); // Color del texto del encabezado (blanco)
                        $pdf->setFont('Arial', 'B', 11); // Fuente para el encabezado (Arial, negrita, tamaño 11)
                        $pdf->cell($colWidths[0], 10, $pdf->encodeString('Placa'), 1, 0, 'C', 1);
                        $pdf->cell($colWidths[1], 10, $pdf->encodeString('Marca'), 1, 0, 'C', 1);
                        $pdf->cell($colWidths[2], 10, $pdf->encodeString('Modelo'), 1, 0, 'C', 1);
                        $pdf->cell($colWidths[3], 10, $pdf->encodeString('Año'), 1, 0, 'C', 1);
                        $pdf->cell($colWidths[4], 10, $pdf->encodeString('Color'), 1, 1, 'C', 1);
                        $pdf->setTextColor(0, 0, 0); // Color del texto de los datos (negro)
                        $pdf->setFont('Arial', '', 11); // Fuente para los datos (Arial, normal, tamaño 11)
                        $counter = 0; // Reinicia el contador de filas
                    }
                    // Ajusta la posición de impresión y agrega los datos de la fila
                    $pdf->SetX($leftMargin);
                    $pdf->cell($colWidths[0], 10, $pdf->encodeString($rowVehiculo['placa_vehiculo']), 1, 0, 'C');
                    $pdf->cell($colWidths[1], 10, $pdf->encodeString($rowVehiculo['marca_vehiculo']), 1, 0, 'C');
                    $pdf->cell($colWidths[2], 10, $pdf->encodeString($rowVehiculo['modelo_vehiculo']), 1, 0, 'C');
                    $pdf->cell($colWidths[3], 10, $pdf->encodeString($rowVehiculo['año_vehiculo']), 1, 0, 'C');
                    $pdf->cell($colWidths[4], 10, $pdf->encodeString($rowVehiculo['color_vehiculo']), 1, 1, 'C');
                    $counter++; // Incrementa el contador de filas
                }
            } else {
                // Mensaje si no hay vehículos para el cliente
                $pdf->cell(0, 10, $pdf->encodeString('No hay vehículos para el cliente'), 1, 1, 'C');
            }
            // Genera el PDF y lo envía al navegador
            $pdf->output('I', 'vehiculos_cliente.pdf');
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