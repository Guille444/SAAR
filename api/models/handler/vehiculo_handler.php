<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
*	Clase para manejar el comportamiento de los datos de la tabla CITAS.
*/
class VehiculoHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    */
    protected $id;
    protected $id_cliente;
    protected $id_marca;
    protected $id_modelo;
    protected $placa;
    protected $color;
    protected $vin;
    protected $año;

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
    */

    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT v.id_vehiculo, v.placa_vehiculo, v.color_vehiculo, v.vin_motor, m.modelo_vehiculo, 
            CONCAT(c.nombre_cliente, " ", c.apellido_cliente) AS nombre_completo, ma.marca_vehiculo
            FROM vehiculos v
            INNER JOIN modelos m ON v.id_modelo = m.id_modelo
            INNER JOIN clientes c ON v.id_cliente = c.id_cliente
            INNER JOIN marcas ma ON v.id_marca = ma.id_marca
            WHERE CONCAT(c.nombre_cliente, " ", c.apellido_cliente) LIKE ?
            ORDER BY m.modelo_vehiculo';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT v.id_vehiculo, v.placa_vehiculo, v.color_vehiculo, v.vin_motor, m.modelo_vehiculo,
            CONCAT(c.nombre_cliente, " ", c.apellido_cliente) AS nombre_completo, ma.marca_vehiculo
            FROM vehiculos v
            INNER JOIN modelos m ON v.id_modelo = m.id_modelo
            INNER JOIN clientes c ON v.id_cliente = c.id_cliente
            INNER JOIN marcas ma ON v.id_marca = ma.id_marca
            ORDER BY m.modelo_vehiculo;';
        return Database::getRows($sql);
    }

    public function readAllByClient()
    {
        $sql = 'SELECT v.id_vehiculo, v.placa_vehiculo, v.color_vehiculo, v.vin_motor, mo.modelo_vehiculo, 
            ma.marca_vehiculo, v.año_vehiculo
            FROM vehiculos v
            INNER JOIN modelos mo ON v.id_modelo = mo.id_modelo
            INNER JOIN marcas ma ON v.id_marca = ma.id_marca
            WHERE v.id_cliente = ?
            ORDER BY mo.modelo_vehiculo;';
        return Database::getRows($sql, [$_SESSION['idCliente']]);
    }

    public function readOne()
    {
        $sql = 'SELECT * FROM vehiculos 
            WHERE id_vehiculo = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function createRow()
    {
        // Consulta SQL para insertar un nuevo vehículo en la base de datos
        $sql = 'INSERT INTO vehiculos (id_marca, id_modelo, id_cliente, placa_vehiculo, año_vehiculo, color_vehiculo, vin_motor) 
            VALUES (?, ?, ?, ?, ?, ?, ?)';
        // Parámetros para la consulta SQL
        $params = array(
            $this->id_marca,    // ID de la marca del vehículo
            $this->id_modelo,   // ID del modelo del vehículo
            $_SESSION['idCliente'], // ID del cliente que está logueado
            $this->placa,       // Placa del vehículo
            $this->año,         // Año del vehículo
            $this->color,       // Color del vehículo
            $this->vin          // VIN del motor
        );
        // Ejecuta la consulta SQL con los parámetros proporcionados
        return Database::executeRow($sql, $params);
    }


    public function updateRow()
    {
        $sql = 'UPDATE vehiculos 
                SET id_cliente = ?, id_marca = ?, id_modelo = ?, placa_vehiculo = ?, color_vehiculo = ?, vin_motor = ?, año_vehiculo = ? 
                WHERE id_vehiculo = ?';
        $params = array($this->id_cliente, $this->id_marca, $this->id_modelo, $this->placa, $this->color, $this->vin, $this->año, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM vehiculos 
                WHERE id_vehiculo = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    // Método para obtener todas las marcas
    public function getAllMarcas()
    {
        $sql = 'SELECT id_marca, marca_vehiculo 
                FROM marcas';
        return Database::getRows($sql);
    }

    // Método para obtener todos los modelos
    public function getAllModelos()
    {
        $sql = 'SELECT id_modelo, modelo_vehiculo 
               FROM modelos';
        return Database::getRows($sql);
    }

    public function getModelosByMarca($id_marca)
    {
        $sql = "SELECT id_modelo, modelo_vehiculo 
                FROM modelos 
                WHERE id_marca = ? 
                ORDER BY modelo_vehiculo";
        return Database::getRows($sql, [$id_marca]);
    }

    public function searchRowsVehiculos()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT v.id_vehiculo, v.placa_vehiculo, v.color_vehiculo, v.vin_motor, m.modelo_vehiculo,  v.año_vehiculo,
            CONCAT(c.nombre_cliente, " ", c.apellido_cliente) AS nombre_completo, ma.marca_vehiculo
            FROM vehiculos v
            INNER JOIN modelos m ON v.id_modelo = m.id_modelo
            INNER JOIN clientes c ON v.id_cliente = c.id_cliente
            INNER JOIN marcas ma ON v.id_marca = ma.id_marca
            WHERE v.id_cliente = ? AND placa_vehiculo LIKE ?
            ORDER BY m.modelo_vehiculo';
        $params = array($_SESSION['idCliente'], $value);
        return Database::getRows($sql, $params);
    }

    public function readByMarca()
    {
        $sql = 'SELECT m.marca_vehiculo AS marca_vehiculo, mo.modelo_vehiculo AS modelo_vehiculo, v.placa_vehiculo, 
            c.nombre_cliente, c.apellido_cliente
            FROM vehiculos v
            INNER JOIN marcas m ON v.id_marca = m.id_marca
            INNER JOIN modelos mo ON v.id_modelo = mo.id_modelo
            INNER JOIN clientes c ON v.id_cliente = c.id_cliente
            ORDER BY m.marca_vehiculo, mo.modelo_vehiculo';
        return Database::getRows($sql);
    }
}
