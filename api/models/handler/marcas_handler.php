<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
 *  Clase para manejar el comportamiento de los datos de la tabla Marca.
 *  Esta clase proporciona métodos para realizar las operaciones básicas SCRUD (search, create, read, update, delete)
 *  en la tabla 'marcas' de la base de datos.
 */
class MarcaHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     *  Estos atributos almacenan la información de las marcas, como el ID y el nombre de la marca.
     */
    protected $id = null; // ID de la marca
    protected $nombre = null; // Nombre de la marca

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     *  Estos métodos permiten interactuar con la tabla 'marcas' en la base de datos.
     */
    
    // Método para buscar marcas en la base de datos según un criterio de búsqueda.
    public function searchRows()
    {
        // Se utiliza un patrón de búsqueda con comodines para encontrar coincidencias parciales.
        $value = '%' . Validator::getSearchValue() . '%';
        // Consulta SQL para buscar marcas cuyo nombre coincida con el valor de búsqueda.
        $sql = 'SELECT id_marca, marca_vehiculo
                FROM marcas
                WHERE marca_vehiculo LIKE ?
                ORDER BY marca_vehiculo';
        $params = array($value);
        // Se ejecuta la consulta y se devuelven las filas resultantes.
        return Database::getRows($sql, $params);
    }

    // Método para crear una nueva marca en la base de datos.
    public function createRow()
    {
        // Consulta SQL para insertar una nueva marca en la tabla.
        $sql = 'INSERT INTO marcas(marca_vehiculo)
                VALUES(?)';
        $params = array($this->nombre);
        // Se ejecuta la consulta y se inserta la nueva fila.
        return Database::executeRow($sql, $params);
    }

    // Método para leer todas las marcas de la base de datos.
    public function readAll()
    {
        // Consulta SQL para seleccionar todas las marcas ordenadas por nombre.
        $sql = 'SELECT id_marca, marca_vehiculo
                FROM marcas
                ORDER BY marca_vehiculo';
        // Se ejecuta la consulta y se devuelven todas las filas.
        return Database::getRows($sql);
    }

    // Método para leer una sola marca según su ID.
    public function readOne()
    {
        // Consulta SQL para seleccionar una marca específica por su ID.
        $sql = 'SELECT id_marca, marca_vehiculo
                FROM marcas
                WHERE id_marca = ?';
        $params = array($this->id);
        // Se ejecuta la consulta y se devuelve la fila correspondiente.
        return Database::getRow($sql, $params);
    }

    // Método para actualizar los datos de una marca existente.
    public function updateRow()
    {
        // Consulta SQL para actualizar el nombre de una marca específica por su ID.
        $sql = 'UPDATE marcas
                SET marca_vehiculo = ?
                WHERE id_marca = ?';
        $params = array($this->nombre, $this->id);
        // Se ejecuta la consulta y se actualiza la fila correspondiente.
        return Database::executeRow($sql, $params);
    }

    // Método para eliminar una marca de la base de datos.
    public function deleteRow()
    {
        // Consulta SQL para eliminar una marca específica por su ID.
        $sql = 'DELETE FROM marcas
                WHERE id_marca = ?';
        $params = array($this->id);
        // Se ejecuta la consulta y se elimina la fila correspondiente.
        return Database::executeRow($sql, $params);
    }

    // Método para verificar si existe una marca duplicada según su nombre.
    public function checkDuplicate($value)
    {
        // Consulta SQL para verificar si ya existe una marca con el mismo nombre.
        $sql = 'SELECT id_marca
                FROM marcas
                WHERE nombre_marca = ?';
        $params = array($value);
        // Se ejecuta la consulta y se devuelve la fila si existe una duplicación.
        return Database::getRow($sql, $params);
    }

    // Método para verificar si existe una marca duplicada según su correo.
    public function checkDuplicate2($value)
    {
        // Consulta SQL para verificar si ya existe una marca con el mismo correo.
        $sql = 'SELECT id_marca
                FROM marcas
                WHERE correo_marca = ?';
        $params = array($value);
        // Se ejecuta la consulta y se devuelve la fila si existe una duplicación.
        return Database::getRow($sql, $params);
    }

    // Método para obtener todas las marcas registradas en la base de datos.
    public function getAllMarcas()
    {
        // Consulta SQL para seleccionar todas las marcas.
        $sql = 'SELECT id_marca, marca_vehiculo 
                FROM marcas';
        // Se ejecuta la consulta y se devuelven todas las filas.
        return Database::getRows($sql);
    }

    // Método para obtener las tres marcas con más vehículos registrados.
    public function TopVehiculosPorMarcas()
    {
        // Consulta SQL para contar la cantidad de vehículos por marca y ordenarlas de mayor a menor.
        $sql = 'SELECT COUNT(id_vehiculo) cantidad, marca_vehiculo
                FROM vehiculos
                INNER JOIN marcas USING (id_marca)
                GROUP BY marca_vehiculo
                ORDER BY cantidad desc
                LIMIT 3;';
        // Se ejecuta la consulta y se devuelven las filas correspondientes.
        return Database::getRows($sql);
    }

    // Método para leer modelos y contar los vehículos por modelo para una marca específica.
    public function readMarcasModelos()
    {
        // Consulta SQL para obtener los modelos y la cantidad de vehículos por modelo para una marca específica.
        $sql = 'SELECT modelo_vehiculo, COUNT(id_vehiculo) coches
                FROM marcas, modelos, vehiculos
                WHERE marcas.id_marca = modelos.id_marca AND
                modelos.id_modelo = vehiculos.id_modelo AND
                marcas.id_marca = ?
                GROUP BY modelo_vehiculo;';
        $params = array($this->id);
        // Se ejecuta la consulta y se devuelven las filas correspondientes.
        return Database::getRows($sql, $params);
    }
}
