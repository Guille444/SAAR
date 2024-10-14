<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre que maneja la lógica del cliente.
require_once('../../models/handler/cliente_handler.php');

/*
* Clase para manejar el encapsulamiento de los datos de la tabla CLIENTE.
*/
class ClienteData extends ClienteHandler
{
    // Atributo genérico para manejo de errores.
    private $data_error = null;

    /*
    * Métodos para validar y establecer los datos del cliente.
    */
    public function setId($value)
    {
        // Valida que el ID sea un número natural.
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value; // Asigna el valor al atributo id.
            return true;
        } else {
            $this->data_error = 'El identificador del cliente es incorrecto'; // Mensaje de error.
            return false;
        }
    }

    public function setNombre($value, $min = 2, $max = 50)
    {
        // Valida que el nombre sea alfabético.
        if (!Validator::validateAlphabetic($value)) {
            $this->data_error = 'El nombre debe ser un valor alfabético'; // Mensaje de error.
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->nombre = $value; // Asigna el valor al atributo nombre.
            return true;
        } else {
            $this->data_error = 'El nombre debe tener una longitud entre ' . $min . ' y ' . $max; // Mensaje de error.
            return false;
        }
    }

    public function setApellido($value, $min = 2, $max = 50)
    {
        // Valida que el apellido sea alfabético.
        if (!Validator::validateAlphabetic($value)) {
            $this->data_error = 'El apellido debe ser un valor alfabético'; // Mensaje de error.
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->apellido = $value; // Asigna el valor al atributo apellido.
            return true;
        } else {
            $this->data_error = 'El apellido debe tener una longitud entre ' . $min . ' y ' . $max; // Mensaje de error.
            return false;
        }
    }

    public function setAlias($value, $min = 6, $max = 25)
    {
        // Valida que el alias sea alfanumérico.
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'El alias debe ser un valor alfanumérico'; // Mensaje de error.
            return false;
        } elseif ($this->checkDuplicate2($value, $this->id)) {
            $this->data_error = 'El alias ingresado ya existe'; // Mensaje de error si el alias ya existe.
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->alias = $value; // Asigna el valor al atributo alias.
            return true;
        } else {
            $this->data_error = 'El alias debe tener una longitud entre ' . $min . ' y ' . $max; // Mensaje de error.
            return false;
        }
    }

    public function setCorreo($value, $min = 8, $max = 100)
    {
        // Valida que el correo tenga un formato válido.
        if (!Validator::validateEmail($value)) {
            $this->data_error = 'El correo no es válido'; // Mensaje de error.
            return false;
        } elseif (!Validator::validateLength($value, $min, $max)) {
            $this->data_error = 'El correo debe tener una longitud entre ' . $min . ' y ' . $max; // Mensaje de error.
            return false;
        } elseif ($this->checkDuplicate($value, $this->id)) {
            $this->data_error = 'El correo ingresado ya existe'; // Mensaje de error si el correo ya existe.
            return false;
        } else {
            $this->correo = $value; // Asigna el valor al atributo correo.
            return true;
        }
    }

    public function setClave($value)
    {
        // Valida que la contraseña cumpla con los requisitos de seguridad.
        if (Validator::validatePassword($value)) {
            $this->clave = password_hash($value, PASSWORD_DEFAULT); // Asigna y encripta la contraseña.
            return true;
        } else {
            $this->data_error = Validator::getPasswordError(); // Mensaje de error sobre la contraseña.
            return false;
        }
    }

    public function setContacto($value)
    {
        // Valida que el teléfono tenga el formato correcto.
        if (Validator::validatePhone($value)) {
            $this->contacto = $value; // Asigna el valor al atributo contacto.
            return true;
        } else {
            $this->data_error = 'El teléfono debe tener el formato (2, 6, 7)###-####'; // Mensaje de error.
            return false;
        }
    }

    public function setEstado($value)
    {
        // Valida que el estado sea un valor booleano.
        if (Validator::validateBoolean($value)) {
            $this->estado = $value; // Asigna el valor al atributo estado.
            return true;
        } else {
            $this->data_error = 'Estado incorrecto'; // Mensaje de error.
            return false;
        }
    }

    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error; // Retorna el mensaje de error.
    }
}
