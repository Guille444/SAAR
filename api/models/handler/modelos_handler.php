<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
*	Clase para manejar el comportamiento de los datos de la tabla PRODUCTO.
*   Esta clase permite realizar las operaciones básicas de la tabla modelos, 
*   incluyendo búsqueda, inserción, actualización, eliminación y lectura de registros.
*/
class ModeloHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    *   Estos atributos corresponden a las columnas de la tabla modelos.
    */
    protected $id = null;        // Identificador único del modelo.
    protected $nombre = null;    // Nombre del modelo del vehículo.
    protected $marca = null;     // Marca a la que pertenece el modelo.

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
    *   Cada método corresponde a una operación que se puede realizar sobre la tabla modelos.
    */

    // Método para buscar registros en la tabla modelos.
    public function searchRows()
    {
        // Se prepara la sentencia SQL para buscar modelos que coincidan con el valor de búsqueda.
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_modelo, modelo_vehiculo, marca_vehiculo
                FROM modelos
                INNER JOIN marcas USING(id_marca)
                WHERE modelo_vehiculo LIKE ?
                ORDER BY modelo_vehiculo';
        $params = array($value); // Se parametriza la consulta para evitar inyecciones SQL.
        return Database::getRows($sql, $params); // Se ejecuta la consulta y se devuelven los resultados.
    }

    // Método para crear un nuevo registro en la tabla modelos.
    public function createRow()
    {
        // Sentencia SQL para insertar un nuevo modelo.
        $sql = 'INSERT INTO modelos(modelo_vehiculo, id_marca)
                VALUES(?, ?)';
        $params = array($this->nombre, $this->marca); // Se asignan los valores del nuevo modelo.
        return Database::executeRow($sql, $params); // Se ejecuta la consulta de inserción.
    }

    // Método para leer todos los registros de la tabla modelos.
    public function readAll()
    {
        // Sentencia SQL para seleccionar todos los modelos junto con sus respectivas marcas.
        $sql = 'SELECT id_modelo, modelo_vehiculo, marca_vehiculo
                FROM modelos
                INNER JOIN marcas USING(id_marca)
                ORDER BY modelo_vehiculo'; // Los resultados se ordenan por nombre de modelo.
        return Database::getRows($sql); // Se ejecuta la consulta y se devuelven todos los registros.
    }

    // Método para leer un solo registro de la tabla modelos por su ID.
    public function readOne()
    {
        // Sentencia SQL para seleccionar un modelo específico por su identificador único.
        $sql = 'SELECT id_modelo, modelo_vehiculo, id_marca
                FROM modelos
                WHERE id_modelo = ?';
        $params = array($this->id); // Se especifica el ID del modelo a buscar.
        return Database::getRow($sql, $params); // Se ejecuta la consulta y se devuelve el registro encontrado.
    }

    // Método para actualizar un registro existente en la tabla modelos.
    public function updateRow()
    {
        // Sentencia SQL para actualizar los datos de un modelo existente.
        $sql = 'UPDATE modelos
                SET modelo_vehiculo = ?, id_marca = ?
                WHERE id_modelo = ?';
        $params = array($this->nombre, $this->marca, $this->id); // Se asignan los valores actualizados del modelo.
        return Database::executeRow($sql, $params); // Se ejecuta la consulta de actualización.
    }

    // Método para eliminar un registro de la tabla modelos.
    public function deleteRow()
    {
        // Sentencia SQL para eliminar un modelo por su identificador único.
        $sql = 'DELETE FROM modelos
                WHERE id_modelo = ?';
        $params = array($this->id); // Se especifica el ID del modelo a eliminar.
        return Database::executeRow($sql, $params); // Se ejecuta la consulta de eliminación.
    }

    // Método para obtener todos los modelos registrados en la base de datos.
    public function getAllModelos()
    {
        // Sentencia SQL para seleccionar todos los modelos de la tabla.
        $sql = 'SELECT id_modelo, modelo_vehiculo 
                FROM modelos';
        return Database::getRows($sql); // Se ejecuta la consulta y se devuelven los resultados.
    }

    // Método para obtener el top 3 de modelos con más vehículos registrados.
    public function TopVehiculosPorModelos()
    {
        // Sentencia SQL para contar la cantidad de vehículos por modelo y ordenar de mayor a menor.
        $sql = 'SELECT COUNT(id_vehiculo) cantidad, modelo_vehiculo
                FROM vehiculos
                INNER JOIN modelos USING (id_modelo)
                GROUP BY modelo_vehiculo
                ORDER BY cantidad desc
                LIMIT 3;'; // Se limita el resultado a los 3 modelos con más vehículos.
        return Database::getRows($sql); // Se ejecuta la consulta y se devuelven los resultados.
    }
}
