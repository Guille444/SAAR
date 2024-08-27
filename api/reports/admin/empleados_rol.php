<?php
// Incluir el archivo con la clase Report que maneja la creación de PDFs
require_once('../../helpers/report.php');
// Incluir el archivo que contiene la clase para acceder a los datos de los administradores
require_once('../../models/data/administrador_data.php');
require_once('../../models/data/roles_data.php');

// Crear una instancia de la clase Report
$pdf = new Report;
// Crear instancias de las clases para acceder a los datos
$administrador = new AdministradorData;
$rol = new RolesData;

// Verifica si se ha proporcionado el parámetro 'idRol'
if (isset($_GET['idRol'])) {
    // Establece el ID del rol y verifica si es válido
    if ($rol->setId($_GET['idRol']) && $administrador->setRol($_GET['idRol'])) {
        // Verifica si el rol existe en la base de datos
        if ($rowRol = $rol->readOne()) {
            // Inicia el reporte y establece el título
            $pdf->startReport('Empleados con el rol: ' . $pdf->encodeString($rowRol['nombre_rol']));
            
            // Obtiene los datos de los administradores con el rol especificado
            if ($dataAdministradores = $administrador->administradoresPorRol()) {
                // Define los anchos de las columnas
                $colWidths = [40, 40, 40, 60];
                $totalWidth = array_sum($colWidths);
                $pageWidth = $pdf->GetPageWidth();
                $leftMargin = ($pageWidth - $totalWidth) / 2;

                // Establece colores y fuente para el encabezado de la tabla
                $pdf->setFillColor(0, 0, 0); // Fondo negro
                $pdf->SetTextColor(255, 255, 255); // Texto blanco
                $pdf->setFont('Arial', 'B', 11); // Fuente en negrita para el encabezado

                // Ajusta la posición de impresión para centrar la tabla
                $pdf->SetX($leftMargin);
                // Imprime los encabezados de la tabla
                $pdf->cell($colWidths[0], 10, $pdf->encodeString('Nombre'), 1, 0, 'C', 1);
                $pdf->cell($colWidths[1], 10, $pdf->encodeString('Apellido'), 1, 0, 'C', 1);
                $pdf->cell($colWidths[2], 10, $pdf->encodeString('Alias'), 1, 0, 'C', 1);
                $pdf->cell($colWidths[3], 10, $pdf->encodeString('Correo'), 1, 1, 'C', 1);

                // Restablece colores y fuente para los datos
                $pdf->setTextColor(0, 0, 0); // Texto negro
                $pdf->setFont('Arial', '', 11); // Fuente normal para los datos
                $pdf->setFillColor(255, 255, 255); // Fondo blanco para los datos

                $counter = 0; // Contador para controlar el número de filas por página
                // Recorre los datos de los administradores y agrega las filas a la tabla
                foreach ($dataAdministradores as $rowAdministrador) {
                    // Verifica si se ha alcanzado el límite de filas en una página
                    if ($counter == 18) {
                        // Agrega una nueva página y reimprime los encabezados
                        $pdf->AddPage();
                        $pdf->SetX($leftMargin);
                        $pdf->setFillColor(0, 0, 0); // Fondo negro
                        $pdf->SetTextColor(255, 255, 255); // Texto blanco
                        $pdf->setFont('Arial', 'B', 11); // Fuente en negrita
                        $pdf->cell($colWidths[0], 10, $pdf->encodeString('Nombre'), 1, 0, 'C', 1);
                        $pdf->cell($colWidths[1], 10, $pdf->encodeString('Apellido'), 1, 0, 'C', 1);
                        $pdf->cell($colWidths[2], 10, $pdf->encodeString('Alias'), 1, 0, 'C', 1);
                        $pdf->cell($colWidths[3], 10, $pdf->encodeString('Correo'), 1, 1, 'C', 1);
                        $pdf->setTextColor(0, 0, 0); // Texto negro
                        $pdf->setFont('Arial', '', 11); // Fuente normal
                        $pdf->setFillColor(255, 255, 255); // Fondo blanco
                        $counter = 0; // Reinicia el contador de filas
                    }
                    // Ajusta la posición de impresión y agrega los datos de la fila
                    $pdf->SetX($leftMargin);
                    $pdf->cell($colWidths[0], 10, $pdf->encodeString($rowAdministrador['nombre_administrador']), 1, 0, 'C', 1);
                    $pdf->cell($colWidths[1], 10, $pdf->encodeString($rowAdministrador['apellido_administrador']), 1, 0, 'C', 1);
                    $pdf->cell($colWidths[2], 10, $pdf->encodeString($rowAdministrador['alias_administrador']), 1, 0, 'C', 1);
                    $pdf->cell($colWidths[3], 10, $pdf->encodeString($rowAdministrador['correo_administrador']), 1, 1, 'C', 1);
                    $counter++; // Incrementa el contador de filas
                }
            } else {
                // Mensaje si no hay administradores para el rol
                $pdf->cell(0, 10, $pdf->encodeString('No hay administradores para el rol'), 1, 1, 'C');
            }
            // Genera el PDF y lo envía al navegador
            $pdf->output('I', 'administradores_rol.pdf');
        } else {
            // Mensaje si el rol no existe
            print('Rol inexistente');
        }
    } else {
        // Mensaje si el ID del rol es incorrecto
        print('Rol incorrecto');
    }
} else {
    // Mensaje si no se ha proporcionado un ID de rol
    print('Debe seleccionar un rol');
}
?>