<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
*	Clase para manejar el comportamiento de los datos de la tabla CLIENTE.
*/
class ServicioHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    */
    protected $id = null;
    protected $nombre = null;
    protected $descripcion = null;

    /*
    *   Métodos para gestionar la cuenta del cliente.
    */
    
    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
    */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_servicio, nombre_servicio, descripcion_servicio
                FROM servicios
                WHERE nombre_servicio LIKE ?';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO servicios(nombre_servicio, descripcion_servicio)
                VALUES(?, ?)';
        $params = array($this->nombre, $this->descripcion);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_servicio, nombre_servicio, descripcion_servicio
                FROM servicios
                ORDER BY nombre_servicio';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_servicio, nombre_servicio, descripcion_servicio
                FROM servicios
                WHERE id_servicio = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE servicios
                SET nombre_servicio = ?, descripcion_servicio = ?
                WHERE id_servicio = ?';
        $params = array($this->nombre, $this->descripcion, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM servicios
                WHERE id_servicio = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function readServiciosMarcas()
    {
        $sql = 'SELECT marca_vehiculo, COUNT(citas.id_cita) coches
                FROM servicios, citas, marcas, vehiculos
                WHERE servicios.id_servicio = citas.id_servicio AND
                marcas.id_marca = vehiculos.id_marca AND
                vehiculos.id_vehiculo = citas.id_vehiculo AND
                servicios.id_servicio = ?
                GROUP BY marca_vehiculo ORDER BY coches DESC
                LIMIT 5;';
        $params = array($this->id);
        return Database::getRows($sql, $params);
    }
}
