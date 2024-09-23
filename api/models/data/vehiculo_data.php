<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/vehiculo_handler.php');

/*
* Clase para manejar el encapsulamiento de los datos de la tabla VEHICULO.
*/
class VehiculoData extends VehiculoHandler
{
    // Atributo genérico para manejo de errores.
    private $data_error = null;

    /*
    * Métodos para validar y establecer los datos.
    */

    // Método para validar y establecer el ID del vehículo.
    public function setId($value)
    {
        // Valida que el valor sea un número natural (entero positivo).
        if (Validator::validateNaturalNumber($value)) {
            // Si es válido, asigna el valor al atributo id.
            $this->id = $value;
            return true;
        } else {
            // Si no es válido, se establece un mensaje de error.
            $this->data_error = 'El identificador del vehículo es incorrecto';
            return false;
        }
    }

    // Método para validar y establecer el ID del cliente.
    public function setIdCliente($value)
    {
        // Valida que el valor sea un número natural.
        if (Validator::validateNaturalNumber($value)) {
            // Si es válido, asigna el valor al atributo id_cliente.
            $this->id_cliente = $value;
            return true;
        } else {
            // Si no es válido, se establece un mensaje de error.
            $this->data_error = 'El identificador del cliente es incorrecto';
            return false;
        }
    }

    // Método para validar y establecer el ID de la marca.
    public function setIdMarca($value)
    {
        // Valida que el valor sea un número natural.
        if (Validator::validateNaturalNumber($value)) {
            // Si es válido, asigna el valor al atributo id_marca.
            $this->id_marca = $value;
            return true;
        } else {
            // Si no es válido, se establece un mensaje de error.
            $this->data_error = 'El identificador de la marca es incorrecto';
            return false;
        }
    }

    // Método para validar y establecer el ID del modelo.
    public function setIdModelo($value)
    {
        // Valida que el valor sea un número natural.
        if (Validator::validateNaturalNumber($value)) {
            // Si es válido, asigna el valor al atributo id_modelo.
            $this->id_modelo = $value;
            return true;
        } else {
            // Si no es válido, se establece un mensaje de error.
            $this->data_error = 'El identificador del modelo es incorrecto';
            return false;
        }
    }

    // Método para validar y establecer la placa del vehículo.
    public function setPlaca($value)
    {
        // Expresión regular para validar la estructura de la placa (debe comenzar con P, A o AB, seguido de letras y números).
        $pattern = '/^(P|A|AB)\s[A-Z0-9]+$/';

        // Valida la placa utilizando la expresión regular.
        if (preg_match($pattern, $value)) {
            // Si es válida, asigna el valor al atributo placa.
            $this->placa = $value;
            return true;
        } else {
            // Si no es válida, se establece un mensaje de error.
            $this->data_error = 'La placa debe comenzar con P, A o AB, seguido de un espacio y luego una combinación de letras y números.';
            return false;
        }
    }

    // Método para validar y establecer el color del vehículo.
    public function setColor($value, $min = 2, $max = 50)
    {
        // Valida que el color sea una cadena de texto válida y que su longitud esté dentro del rango permitido.
        if (Validator::validateString($value) && Validator::validateLength($value, $min, $max)) {
            // Si es válido, asigna el valor al atributo color.
            $this->color = $value;
            return true;
        } else {
            // Si no es válido, se establece un mensaje de error.
            $this->data_error = 'El color debe tener una longitud entre ' . $min . ' y ' . $max . ' caracteres y puede contener letras y espacios';
            return false;
        }
    }

    // Método para validar y establecer el VIN del vehículo.
    public function setVin($value, $min = 2, $max = 50)
    {
        // Valida que el VIN sea una cadena alfanumérica válida y que su longitud esté dentro del rango permitido.
        if (Validator::validateAlphanumeric($value) && Validator::validateLength($value, $min, $max)) {
            // Si es válido, asigna el valor al atributo vin.
            $this->vin = $value;
            return true;
        } else {
            // Si no es válido, se establece un mensaje de error.
            $this->data_error = 'El VIN debe tener una longitud entre ' . $min . ' y ' . $max . ' caracteres y puede contener letras y números';
            return false;
        }
    }

    // Método para validar y establecer el año del vehículo.
    public function setAño($value, $min = 4, $max = 4)
    {
        // Valida que el año sea un número natural y que su longitud sea de 4 caracteres.
        if (Validator::validateNaturalNumber($value) && Validator::validateLength($value, $min, $max)) {
            // Si es válido, asigna el valor al atributo año.
            $this->año = $value;
            return true;
        } else {
            // Si no es válido, se establece un mensaje de error.
            $this->data_error = 'El año debe tener una longitud de ' . $min . ' caracteres y debe ser un número válido';
            return false;
        }
    }

    // Método para obtener el error de los datos en caso de que ocurra una validación incorrecta.
    public function getDataError()
    {
        // Retorna el mensaje de error almacenado en data_error.
        return $this->data_error;
    }
}
