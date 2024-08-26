<?php
// Incluir el archivo con la clase Report que maneja la creación de PDFs
require_once('../../helpers/report.php');
// Incluir el archivo que contiene la clase para acceder a los datos de los administradores
require_once('../../models/data/administrador_data.php');
require_once('../../models/data/roles_data.php');
// Crear una instancia de la clase Report
$pdf = new Report;
// Crear una instancia de la clase AdministradorData para acceder a los datos
$administrador = new AdministradorData;
$rol = new RolesData;
// Verifica si se ha proporcionado el parámetro 'idRol'
if (isset($_GET['idRol'])) {
    // Establece el ID del rol y verifica si es válido
    if ($rol->setId($_GET['idRol']) && $administrador->setRol($_GET['idRol'])) {
        // Verifica si el rol existe en la base de datos
        if ($rowRol = $rol->readOne()) {
            // Inicia el reporte y establece el título
            $pdf->startReport('Empleados con el rol: ' . $rowRol['nombre_rol']);
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
                $pdf->cell($colWidths[0], 10, 'Nombre', 1, 0, 'C', 1);
                $pdf->cell($colWidths[1], 10, 'Apellido', 1, 0, 'C', 1);
                $pdf->cell($colWidths[2], 10, 'Alias', 1, 0, 'C', 1);
                $pdf->cell($colWidths[3], 10, 'Correo', 1, 1, 'C', 1);
                // Restablece colores y fuente para los datos
                $pdf->setTextColor(0, 0, 0); // Texto negro
                $pdf->setFont('Arial', '', 11); // Fuente normal para los datos
                $pdf->setFillColor(255, 255, 255); // Fondo blanco para los datos
                $counter = 0;
                // Recorre los datos de los administradores y agrega las filas a la tabla
                foreach ($dataAdministradores as $rowAdministrador) {
                    // Verifica si se ha alcanzado el límite de filas en una página
                    if ($counter == 18) {
                        // Agrega una nueva página y reimprime los encabezados
                        $pdf->AddPage();
                        $pdf->SetX($leftMargin);
                        // Reimprime los encabezados con los estilos
                        $pdf->setFillColor(0, 0, 0); // Fondo negro
                        $pdf->SetTextColor(255, 255, 255); // Texto blanco
                        $pdf->setFont('Arial', 'B', 11); // Fuente en negrita
                        $pdf->cell($colWidths[0], 10, 'Nombre', 1, 0, 'C', 1);
                        $pdf->cell($colWidths[1], 10, 'Apellido', 1, 0, 'C', 1);
                        $pdf->cell($colWidths[2], 10, 'Alias', 1, 0, 'C', 1);
                        $pdf->cell($colWidths[3], 10, 'Correo', 1, 1, 'C', 1);
                        // Restablece los estilos para los datos
                        $pdf->setTextColor(0, 0, 0); // Texto negro
                        $pdf->setFont('Arial', '', 11); // Fuente normal
                        $pdf->setFillColor(255, 255, 255); // Fondo blanco
                        $counter = 0;
                    }
                    // Ajusta la posición de impresión y agrega los datos de la fila
                    $pdf->SetX($leftMargin);
                    $pdf->cell($colWidths[0], 10, utf8_decode($rowAdministrador['nombre_administrador']), 1, 0, 'C', 1);
                    $pdf->cell($colWidths[1], 10, utf8_decode($rowAdministrador['apellido_administrador']), 1, 0, 'C', 1);
                    $pdf->cell($colWidths[2], 10, utf8_decode($rowAdministrador['alias_administrador']), 1, 0, 'C', 1);
                    $pdf->cell($colWidths[3], 10, utf8_decode($rowAdministrador['correo_administrador']), 1, 1, 'C', 1);
                    $counter++;
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
