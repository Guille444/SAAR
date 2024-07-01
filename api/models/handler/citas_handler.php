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
}
