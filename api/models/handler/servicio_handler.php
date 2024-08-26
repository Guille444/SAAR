<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
*	Clase para manejar el comportamiento de los datos de la tabla SERVICIOS.
*/
class ServicioHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    *   Estos atributos representan las columnas de la tabla SERVICIOS en la base de datos.
    */
    protected $id = null;
    protected $nombre = null;
    protected $descripcion = null;

    /*
    *   Métodos para gestionar la cuenta del cliente.
    *   Aquí se pueden incluir métodos específicos para la lógica de negocio relacionada con los servicios.
    */

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
    *   Estos métodos interactúan con la base de datos para realizar las operaciones CRUD.
    */

    // Método para buscar registros en la tabla SERVICIOS basados en un valor de búsqueda.
    public function searchRows()
    {
        // Se utiliza un valor de búsqueda con comodines para encontrar coincidencias parciales.
        $value = '%' . Validator::getSearchValue() . '%';
        // Consulta SQL para seleccionar registros que coincidan con el valor de búsqueda.
        $sql = 'SELECT id_servicio, nombre_servicio, descripcion_servicio
                FROM servicios
                WHERE nombre_servicio LIKE ?';
        // Parámetros para la consulta SQL.
        $params = array($value);
        // Retorna los registros que coinciden con la consulta.
        return Database::getRows($sql, $params);
    }

    // Método para crear un nuevo registro en la tabla SERVICIOS.
    public function createRow()
    {
        // Consulta SQL para insertar un nuevo registro en la tabla.
        $sql = 'INSERT INTO servicios(nombre_servicio, descripcion_servicio)
                VALUES(?, ?)';
        // Parámetros que se van a insertar en la tabla.
        $params = array($this->nombre, $this->descripcion);
        // Ejecuta la consulta y retorna el resultado de la operación.
        return Database::executeRow($sql, $params);
    }

    // Método para leer todos los registros de la tabla SERVICIOS.
    public function readAll()
    {
        // Consulta SQL para seleccionar todos los registros de la tabla.
        $sql = 'SELECT id_servicio, nombre_servicio, descripcion_servicio
                FROM servicios
                ORDER BY nombre_servicio';
        // Retorna todos los registros de la tabla.
        return Database::getRows($sql);
    }

    // Método para leer un registro específico de la tabla SERVICIOS basado en su ID.
    public function readOne()
    {
        // Consulta SQL para seleccionar un registro por su ID.
        $sql = 'SELECT id_servicio, nombre_servicio, descripcion_servicio
                FROM servicios
                WHERE id_servicio = ?';
        // Parámetro que corresponde al ID del registro.
        $params = array($this->id);
        // Retorna el registro que coincide con el ID proporcionado.
        return Database::getRow($sql, $params);
    }

    // Método para actualizar un registro existente en la tabla SERVICIOS.
    public function updateRow()
    {
        // Consulta SQL para actualizar un registro en la tabla.
        $sql = 'UPDATE servicios
                SET nombre_servicio = ?, descripcion_servicio = ?
                WHERE id_servicio = ?';
        // Parámetros que se utilizarán para actualizar el registro.
        $params = array($this->nombre, $this->descripcion, $this->id);
        // Ejecuta la consulta y retorna el resultado de la operación.
        return Database::executeRow($sql, $params);
    }

    // Método para eliminar un registro de la tabla SERVICIOS.
    public function deleteRow()
    {
        // Consulta SQL para eliminar un registro de la tabla.
        $sql = 'DELETE FROM servicios
                WHERE id_servicio = ?';
        // Parámetro que corresponde al ID del registro a eliminar.
        $params = array($this->id);
        // Ejecuta la consulta y retorna el resultado de la operación.
        return Database::executeRow($sql, $params);
    }

    // Método para obtener la cantidad de vehículos por marca asociados a un servicio específico.
    public function readServiciosMarcas()
    {
        // Consulta SQL para contar la cantidad de vehículos por marca que han utilizado un servicio.
        $sql = 'SELECT marca_vehiculo, COUNT(citas.id_cita) coches
                FROM servicios, citas, marcas, vehiculos
                WHERE servicios.id_servicio = citas.id_servicio AND
                marcas.id_marca = vehiculos.id_marca AND
                vehiculos.id_vehiculo = citas.id_vehiculo AND
                servicios.id_servicio = ?
                GROUP BY marca_vehiculo ORDER BY coches DESC
                LIMIT 5;';
        // Parámetro que corresponde al ID del servicio.
        $params = array($this->id);
        // Retorna los resultados de la consulta.
        return Database::getRows($sql, $params);
    }

    // Método para leer todos los servicios disponibles.
    public function readServicios()
    {
        // Consulta SQL para seleccionar todos los servicios de la tabla.
        $sql = "SELECT id_servicio, nombre_servicio, descripcion_servicio 
                FROM servicios";
        // Retorna todos los registros de la tabla.
        return Database::getRows($sql);
    }
}
