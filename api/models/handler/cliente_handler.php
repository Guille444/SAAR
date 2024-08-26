<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
*	Clase para manejar el comportamiento de los datos de la tabla CLIENTE.
*/
class ClienteHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    *   Cada atributo representa un campo en la tabla de clientes.
    */
    protected $id = null;
    protected $nombre = null;
    protected $apellido = null;
    protected $alias = null;
    protected $correo = null;
    protected $clave = null;
    protected $contacto = null;
    protected $estado = null;

    /*
    *   Método para verificar las credenciales del usuario.
    *   @param string $username El alias del cliente.
    *   @param string $password La contraseña del cliente.
    *   @return boolean Indica si las credenciales son correctas.
    */
    public function checkUser($username, $password)
    {
        // Consulta SQL para obtener el id, alias, clave y estado del cliente.
        $sql = 'SELECT id_cliente, alias_cliente, clave_cliente, estado_cliente
            FROM clientes
            WHERE alias_cliente = ?';
        $params = array($username); // Se establecen los parámetros para la consulta.
        $data = Database::getRow($sql, $params); // Se ejecuta la consulta.

        // Verificar si $data no es false y es un array antes de acceder a sus índices.
        if ($data && is_array($data) && password_verify($password, $data['clave_cliente'])) {
            // Si la contraseña es correcta, se almacenan los datos del cliente en los atributos de la clase.
            $this->id = $data['id_cliente'];
            $this->alias = $data['alias_cliente'];
            $this->estado = $data['estado_cliente'];
            return true;
        } else {
            return false; // Si las credenciales no son correctas, se retorna false.
        }
    }

    /*
    *   Método para verificar el estado de la cuenta del cliente.
    *   @return boolean Indica si la cuenta del cliente está activa.
    */
    public function checkStatus()
    {
        if ($this->estado) {
            // Si el estado del cliente es activo, se almacenan los datos en la sesión.
            $_SESSION['idCliente'] = $this->id;
            $_SESSION['aliasCliente'] = $this->alias;
            return true;
        } else {
            return false; // Si la cuenta está inactiva, se retorna false.
        }
    }

    /*
    *   Método para verificar la contraseña del cliente.
    *   @param string $password La contraseña actual del cliente.
    *   @return boolean Indica si la contraseña es correcta.
    */
    public function checkPassword($password)
    {
        // Consulta SQL para obtener la clave del cliente.
        $sql = 'SELECT clave_cliente
                FROM clientes
                WHERE id_cliente = ?';
        $params = array($_SESSION['idCliente']); // Se obtiene el ID del cliente desde la sesión.
        $data = Database::getRow($sql, $params); // Se ejecuta la consulta.

        // Se verifica si la contraseña coincide con el hash almacenado en la base de datos.
        if (password_verify($password, $data['clave_cliente'])) {
            return true;
        } else {
            return false; // Si la contraseña no coincide, se retorna false.
        }
    }

    /*
    *   Método para cambiar la contraseña del cliente.
    *   @return boolean Indica si la contraseña fue actualizada correctamente.
    */
    public function changePassword()
    {
        // Consulta SQL para actualizar la clave del cliente.
        $sql = 'UPDATE clientes
                SET clave_cliente = ?
                WHERE id_cliente = ?';
        $params = array($this->clave, $_SESSION['idCliente']); // Se establecen los parámetros para la consulta.
        return Database::executeRow($sql, $params); // Se ejecuta la consulta.
    }

    /*
    *   Método para cambiar el estado de la cuenta del cliente.
    *   @return boolean Indica si el estado fue actualizado correctamente.
    */
    public function changeStatus()
    {
        // Consulta SQL para actualizar el estado del cliente.
        $sql = 'UPDATE clientes
                SET estado_cliente = ?
                WHERE id_cliente = ?';
        $params = array($this->estado, $this->id); // Se establecen los parámetros para la consulta.
        return Database::executeRow($sql, $params); // Se ejecuta la consulta.
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
    *   Estos métodos permiten buscar, crear, leer, actualizar y eliminar registros de la tabla CLIENTE.
    */

    /*
    *   Método para buscar registros de clientes que coincidan con un valor específico.
    *   @return array Retorna un arreglo con los registros encontrados.
    */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%'; // Se establece el valor a buscar con comodines.
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, alias_cliente, correo_cliente, contacto_cliente
                FROM clientes
                WHERE apellido_cliente LIKE ?
                ORDER BY apellido_cliente';
        $params = array($value); // Se establecen los parámetros para la consulta.
        return Database::getRows($sql, $params); // Se ejecuta la consulta y se retornan los resultados.
    }

    /*
    *   Método para crear un nuevo registro de cliente.
    *   @return boolean Indica si el registro fue creado correctamente.
    */
    public function createRow()
    {
        // Consulta SQL para insertar un nuevo cliente en la base de datos.
        $sql = 'INSERT INTO clientes(nombre_cliente, apellido_cliente, alias_cliente, correo_cliente, clave_cliente, contacto_cliente)
                VALUES(?, ?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->apellido, $this->alias, $this->correo, $this->clave, $this->contacto); // Se establecen los parámetros.
        return Database::executeRow($sql, $params); // Se ejecuta la consulta.
    }

    /*
    *   Método para obtener todos los registros de la tabla CLIENTE.
    *   @return array Retorna un arreglo con todos los registros de clientes.
    */
    public function readAll()
    {
        // Consulta SQL para obtener todos los clientes.
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, alias_cliente, correo_cliente, contacto_cliente, estado_cliente
                FROM clientes
                ORDER BY apellido_cliente';
        return Database::getRows($sql); // Se ejecuta la consulta y se retornan los resultados.
    }

    /*
    *   Método para obtener un registro de cliente específico basado en su ID.
    *   @return array Retorna un arreglo con los datos del cliente.
    */
    public function readOne()
    {
        // Consulta SQL para obtener los datos de un cliente específico.
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, alias_cliente, correo_cliente, contacto_cliente, estado_cliente
                FROM clientes
                WHERE id_cliente = ?';
        $params = array($this->id); // Se establece el ID del cliente como parámetro.
        return Database::getRow($sql, $params); // Se ejecuta la consulta y se retorna el resultado.
    }

    /*
    *   Método para actualizar el estado de un cliente.
    *   @return boolean Indica si el estado fue actualizado correctamente.
    */
    public function updateRow()
    {
        // Consulta SQL para actualizar el estado de un cliente específico.
        $sql = 'UPDATE clientes
                SET estado_cliente = ?
                WHERE id_cliente = ?';
        $params = array($this->estado, $this->id); // Se establecen los parámetros para la consulta.
        return Database::executeRow($sql, $params); // Se ejecuta la consulta.
    }

    /*
    *   Método para eliminar un registro de cliente basado en su ID.
    *   @return boolean Indica si el registro fue eliminado correctamente.
    */
    public function deleteRow()
    {
        // Consulta SQL para eliminar un cliente específico de la base de datos.
        $sql = 'DELETE FROM clientes
                WHERE id_cliente = ?';
        $params = array($this->id); // Se establece el ID del cliente como parámetro.
        return Database::executeRow($sql, $params); // Se ejecuta la consulta.
    }

    /*
    *   Método para obtener el perfil del cliente actual desde la sesión.
    *   @return array Retorna un arreglo con los datos del perfil del cliente.
    */
    public function readProfile()
    {
        // Consulta SQL para obtener los datos del perfil del cliente.
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, alias_cliente, contacto_cliente, correo_cliente, clave_cliente
                FROM clientes
                WHERE id_cliente = ?';
        $params = array($_SESSION['idCliente']); // Se obtiene el ID del cliente desde la sesión.
        return Database::getRow($sql, $params); // Se ejecuta la consulta y se retorna el resultado.
    }

    /*
    *   Método para editar el perfil del cliente actual.
    *   @return boolean Indica si el perfil fue actualizado correctamente.
    */
    public function editProfile()
    {
        // Consulta SQL para actualizar los datos del perfil del cliente.
        $sql = 'UPDATE clientes
                SET nombre_cliente = ?, apellido_cliente = ?, alias_cliente = ?, contacto_cliente = ?, correo_cliente = ?  
                WHERE id_cliente = ?';
        $params = array($this->nombre, $this->apellido, $this->alias, $this->contacto, $this->correo, $_SESSION['idCliente']); // Se establecen los parámetros.
        return Database::executeRow($sql, $params); // Se ejecuta la consulta.
    }

    /*
    *   Método para verificar si un correo electrónico ya está registrado.
    *   @param string $value El correo electrónico a verificar.
    *   @param int $idCliente El ID del cliente (opcional).
    *   @return array Retorna un arreglo con los datos del cliente si ya existe, o null si no existe.
    */
    public function checkDuplicate($value, $idCliente = null)
    {
        if ($idCliente) {
            // Consulta SQL para verificar si el correo está registrado para otro cliente.
            $sql = 'SELECT id_cliente
                FROM clientes
                WHERE correo_cliente = ? AND id_cliente != ?';
            $params = array($value, $idCliente); // Se establecen los parámetros.
        } else {
            // Consulta SQL para verificar si el correo está registrado.
            $sql = 'SELECT id_cliente
                FROM clientes
                WHERE correo_cliente = ?';
            $params = array($value); // Se establece el parámetro.
        }
        return Database::getRow($sql, $params); // Se ejecuta la consulta.
    }

    /*
    *   Método para verificar si un alias ya está registrado.
    *   @param string $value El alias a verificar.
    *   @param int $idCliente El ID del cliente (opcional).
    *   @return array Retorna un arreglo con los datos del cliente si ya existe, o null si no existe.
    */
    public function checkDuplicate2($value, $idCliente = null)
    {
        if ($idCliente) {
            // Consulta SQL para verificar si el alias está registrado para otro cliente.
            $sql = 'SELECT id_cliente
                FROM clientes
                WHERE alias_cliente = ? AND id_cliente != ?';
            $params = array($value, $idCliente); // Se establecen los parámetros.
        } else {
            // Consulta SQL para verificar si el alias está registrado.
            $sql = 'SELECT id_cliente
                FROM clientes
                WHERE alias_cliente = ?';
            $params = array($value); // Se establece el parámetro.
        }
        return Database::getRow($sql, $params); // Se ejecuta la consulta.
    }

    /*
    *   Método para obtener las piezas asociadas a un cliente específico.
    *   @return array Retorna un arreglo con las piezas del cliente.
    */
    public function readPiezaCliente()
    {
        // Consulta SQL para obtener las piezas asociadas a un cliente específico.
        $sql = 'SELECT nombre_pieza, cantidad
                FROM clientes, piezas, detalle_citas, citas
                WHERE clientes.id_cliente = citas.id_cliente AND
                citas.id_cita = detalle_citas.id_cita and
                piezas.id_pieza = detalle_citas.id_pieza and
                clientes.id_cliente = ?
                GROUP BY nombre_pieza;';
        $params = array($this->id); // Se establece el ID del cliente como parámetro.
        return Database::getRows($sql, $params); // Se ejecuta la consulta y se retornan los resultados.
    }

    /*
    *   Método para obtener la lista de todos los clientes.
    *   @return array Retorna un arreglo con todos los registros de clientes.
    */
    public function readClientes()
    {
        // Consulta SQL para obtener todos los clientes.
        $sql = "SELECT nombre_cliente, apellido_cliente, correo_cliente, contacto_cliente, estado_cliente 
                FROM clientes";
        return Database::getRows($sql); // Se ejecuta la consulta y se retornan los resultados.
    }
}
