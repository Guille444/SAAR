<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
*	Clase para manejar el comportamiento de los datos de la tabla CITAS.
*/
class CitasHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    */
    protected $id = null;
    protected $id_cliente = null;
    protected $id_vehiculo = null;
    protected $id_servicio = null;
    protected $fecha_cita = null;
    protected $estado_cita = null;

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
    */

    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT c.id_cita,
                   CONCAT(cl.nombre_cliente, " ", cl.apellido_cliente) AS cliente,
                   DATE_FORMAT(c.fecha_cita, "%d-%m-%Y") AS fecha,
                   c.estado_cita,
                   v.placa_vehiculo,
                   s.nombre_servicio
            FROM citas c
            INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
            INNER JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
            INNER JOIN servicios s ON c.id_servicio = s.id_servicio
            WHERE cl.nombre_cliente LIKE ?
               OR CONCAT(cl.nombre_cliente, " ", cl.apellido_cliente) LIKE ?
            ORDER BY c.fecha_cita DESC';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function PorcentajeEstadoCitas()
    {
        $sql = 'SELECT estado_cita, ROUND((COUNT(id_cita) * 100.0 / (SELECT COUNT(id_cita) FROM citas)), 2) porcentaje
                FROM citas
                GROUP BY estado_cita ORDER BY porcentaje DESC;';
        return Database::getRows($sql);
    }


    public function readAll()
    {
        $sql = 'SELECT c.id_cita,
                   CONCAT(cl.nombre_cliente, " ", cl.apellido_cliente) AS cliente,
                   DATE_FORMAT(c.fecha_cita, "%d-%m-%Y") AS fecha,
                   c.estado_cita,
                   v.placa_vehiculo,
                   s.nombre_servicio
            FROM citas c
            INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
            INNER JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
            INNER JOIN servicios s ON c.id_servicio = s.id_servicio
            ORDER BY c.fecha_cita DESC, c.estado_cita DESC;';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT c.id_cita,
                   CONCAT(cl.nombre_cliente, " ", cl.apellido_cliente) AS cliente,
                   DATE_FORMAT(c.fecha_cita, "%d-%m-%Y") AS fecha,
                   c.estado_cita,
                   v.placa_vehiculo,
                   s.nombre_servicio
            FROM citas c
            INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
            INNER JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
            INNER JOIN servicios s ON c.id_servicio = s.id_servicio
            WHERE c.id_cita = ?';
        $params = array($this->id);
        $data = Database::getRow($sql, $params);

        return $data;
    }

    public function updateRow()
    {
        $sql = 'UPDATE citas
                SET estado_cita = ?
                WHERE id_cita = ?';
        $params = array($this->estado_cita, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM citas
                WHERE id_cita = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    //Todo lo de los graficos predictivos

    public function PrediccionGananciaAnual()
    {
        $sql = 'SELECT YEAR(fecha_cita) AS Año, SUM(cantidad * precio_unitario) AS Ganancias
                FROM detalle_citas, piezas, citas
                WHERE piezas.id_pieza = detalle_citas.id_pieza AND
                detalle_citas.id_cita = citas.id_cita AND
                estado_cita = "Completada"
                GROUP BY Año;';
        $rows = Database::getRows($sql);
 
        // Preparar datos para la predicción
        $x = []; // Array para almacenar los meses consecutivos
        $y = []; // Array para almacenar las ganancias correspondientes
        $i = 1;  // Variable para numerar los meses consecutivos
        
        foreach ($rows as $row) {
            $x[] = $i++;
            $y[] = $row['Ganancias'];
        }

        // Aplicar promedio móvil para suavizar los datos
        $window_size = 3;
        $smoothed_y = $this->movingAverage($y, $window_size);

         // Calcular los parámetros de la regresión lineal
         $N = count($x); // Número de datos
         $sumX = array_sum($x); // Suma de todos los valores de $x
         $sumY = array_sum($smoothed_y); // Suma de todos los valores suavizados de $y
         $sumXY = $this->sumProduct($x, $smoothed_y); // Suma del producto de $x y los valores suavizados de $y
         $sumX2 = $this->sumSquare($x);

         $m = ($N * $sumXY - $sumX * $sumY) / ($N * $sumX2 - $sumX * $sumX);
 
        // Calcular la intersección (b) de la línea de regresión
        $b = ($sumY - $m * $sumX) / $N;

          // Predecir ganancias futuras (por ejemplo, para los próximos 12 meses)
          $predictions = []; // Array para almacenar las predicciones
          $currentYear = intval(date('Y')); // Año actual

          for ($j = 0; $j < 3; $j++) {
            $predictedYear = $currentYear + $j; // Calcular el año predicho
 
            // Agregar la predicción al array de predicciones
            $predictions[] = [
                'Año' => $predictedYear, // Año predicho
                'Ganancias' => $m * ($i + $j) + $b // Calcular las ganancias predichas
            ];
        }

         // Retornar el array de predicciones
         return array_merge($predictions);
    }

    private function movingAverage($data, $window_size)
    {
        $result = []; // Array para almacenar los datos suavizados
        $data_count = count($data); // Número de datos en el array original
 
        // Calcular el promedio móvil
        for ($i = 0; $i < $data_count; $i++) {
            // Extraer una ventana de datos del array original
            $window = array_slice($data, max(0, $i - $window_size + 1), $window_size);
            // Calcular el promedio de la ventana y agregarlo al array de resultados
            $result[] = array_sum($window) / count($window);
        }
 
        // Retornar el array de datos suavizados
        return $result;
    }

    private function sumProduct($x, $y)
    {
        $sum = 0; // Variable para almacenar la suma
 
        // Recorrer los arrays y calcular la suma del producto de sus elementos
        for ($i = 0; $i < count($x); $i++) {
            $sum += $x[$i] * $y[$i];
        }
 
        // Retornar la suma del producto
        return $sum;
    }

    private function sumSquare($x)
    {
        $sum = 0; // Variable para almacenar la suma
 
        // Recorrer el array y calcular la suma de los cuadrados de sus elementos
        for ($i = 0; $i < count($x); $i++) {
            $sum += $x[$i] * $x[$i];
        }

        // Retornar la suma de los cuadrados
        return $sum;
    }
}
