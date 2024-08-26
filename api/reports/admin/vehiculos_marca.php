<?php
// Incluir el archivo con la clase Report que maneja la creación de PDFs
require_once('../../helpers/report.php');
// Incluir el archivo que contiene la clase para acceder a los datos de los vehículos
require_once('../../models/data/vehiculo_data.php');
// Incluir el archivo que contiene la clase para acceder a los datos de las marcas
require_once('../../models/data/marcas_data.php');

// Crear una instancia de la clase Report para generar el PDF
$pdf = new Report;
// Crear instancias de las clases para manejar datos de vehículos y marcas
$vehiculo = new VehiculoData;
$marca = new MarcaData;

// Verifica si se ha proporcionado el parámetro 'idMarca'
if (isset($_GET['idMarca'])) {
    // Establece el ID de la marca en el objeto MarcaData y verifica si es válido
    if ($marca->setId($_GET['idMarca']) && $vehiculo->setIdMarca($_GET['idMarca'])) {
        // Verifica si la marca existe en la base de datos
        if ($rowMarca = $marca->readOne()) {
            // Inicia el reporte y establece el título con el nombre de la marca
            $pdf->startReport('Vehículos de la Marca: ' . $rowMarca['marca_vehiculo']);
            // Obtiene los datos de los vehículos asociados a la marca especificada
            if ($dataVehiculos = $vehiculo->vehiculosPorMarca()) {
                // Define los anchos de las columnas para el reporte
                $colWidths = [40, 40, 40, 30, 30];
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
                $pdf->cell($colWidths[0], 10, 'Placa', 1, 0, 'C', 1);
                $pdf->cell($colWidths[1], 10, 'Marca', 1, 0, 'C', 1);
                $pdf->cell($colWidths[2], 10, 'Modelo', 1, 0, 'C', 1);
                $pdf->cell($colWidths[3], 10, 'Año', 1, 0, 'C', 1);
                $pdf->cell($colWidths[4], 10, 'Color', 1, 1, 'C', 1);
                
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
                        $pdf->cell($colWidths[0], 10, 'Placa', 1, 0, 'C', 1);
                        $pdf->cell($colWidths[1], 10, 'Marca', 1, 0, 'C', 1);
                        $pdf->cell($colWidths[2], 10, 'Modelo', 1, 0, 'C', 1);
                        $pdf->cell($colWidths[3], 10, 'Año', 1, 0, 'C', 1);
                        $pdf->cell($colWidths[4], 10, 'Color', 1, 1, 'C', 1);
                        $pdf->setTextColor(0, 0, 0); // Color del texto de los datos (negro)
                        $pdf->setFont('Arial', '', 11); // Fuente para los datos (Arial, normal, tamaño 11)
                        $counter = 0; // Reinicia el contador de filas
                    }
                    // Ajusta la posición de impresión y agrega los datos de la fila
                    $pdf->SetX($leftMargin);
                    $pdf->cell($colWidths[0], 10, utf8_decode($rowVehiculo['placa_vehiculo']), 1, 0, 'C');
                    $pdf->cell($colWidths[1], 10, utf8_decode($rowVehiculo['marca_vehiculo']), 1, 0, 'C');
                    $pdf->cell($colWidths[2], 10, utf8_decode($rowVehiculo['modelo_vehiculo']), 1, 0, 'C');
                    $pdf->cell($colWidths[3], 10, utf8_decode($rowVehiculo['año_vehiculo']), 1, 0, 'C');
                    $pdf->cell($colWidths[4], 10, utf8_decode($rowVehiculo['color_vehiculo']), 1, 1, 'C');
                    $counter++; // Incrementa el contador de filas
                }
            } else {
                // Mensaje si no hay vehículos para la marca
                $pdf->cell(0, 10, $pdf->encodeString('No hay vehículos para la marca'), 1, 1, 'C');
            }
            // Genera el PDF y lo envía al navegador
            $pdf->output('I', 'vehiculos_marca.pdf');
        } else {
            // Mensaje si la marca no existe
            print('Marca inexistente');
        }
    } else {
        // Mensaje si el ID de la marca es incorrecto
        print('Marca incorrecta');
    }
} else {
    // Mensaje si no se ha proporcionado un ID de marca
    print('Debe seleccionar una marca');
}
?>
