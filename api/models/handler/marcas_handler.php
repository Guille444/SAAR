<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla Marca.
 */
class MarcaHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_marca, marca_vehiculo
                FROM marcas
                WHERE marca_vehiculo LIKE ?
                ORDER BY marca_vehiculo';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO marcas(marca_vehiculo)
                VALUES(?)';
        $params = array($this->nombre);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_marca, marca_vehiculo
                FROM marcas
                ORDER BY marca_vehiculo';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_marca, marca_vehiculo
                FROM marcas
                WHERE id_marca = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }


    public function updateRow()
    {
        $sql = 'UPDATE marcas
                SET marca_vehiculo = ?
                WHERE id_marca = ?';
        $params = array($this->nombre, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM marcas
                WHERE id_marca = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function checkDuplicate($value)
    {
        $sql = 'SELECT id_marca
                FROM marcas
                WHERE nombre_marca = ?';
        $params = array($value);
        return Database::getRow($sql, $params);
    }

    public function checkDuplicate2($value)
    {
        $sql = 'SELECT id_marca
                FROM marcas
                WHERE correo_marca = ?';
        $params = array($value);
        return Database::getRow($sql, $params);
    }

    // Método para obtener todas las marcas
    public function getAllMarcas()
    {
        $sql = 'SELECT id_marca, marca_vehiculo 
                FROM marcas';
        return Database::getRows($sql);
    }

    public function TopVehiculosPorMarcas()
    {
        $sql = 'SELECT COUNT(id_vehiculo) cantidad, marca_vehiculo
                FROM vehiculos
                INNER JOIN marcas USING (id_marca)
                GROUP BY marca_vehiculo
                ORDER BY cantidad desc
                LIMIT 3;';
        return Database::getRows($sql);
    }

    public function readMarcasModelos()
    {
        $sql = 'SELECT modelo_vehiculo, COUNT(id_vehiculo) coches
                FROM marcas, modelos, vehiculos
                WHERE marcas.id_marca = modelos.id_marca AND
                modelos.id_modelo = vehiculos.id_modelo AND
                marcas.id_marca = ?
                GROUP BY modelo_vehiculo;';
        $params = array($this->id);
        return Database::getRows($sql, $params);
    }
}
