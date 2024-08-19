<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/roles_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla Marca.
 */
class RolesData extends RolesHandler
{
    /*
     *  Atributos adicionales.
     */
    private $data_error = null;
    private $filename = null;

    /*
     *  Métodos para validar y establecer los datos.
     */
    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la categoría es incorrecto';
            return false;
        }
    }

    public function setNombre($value, $min = 2, $max = 50)
    {
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'El nombre debe ser un valor alfanumérico';
            return false;
        }
        // Primero, verificar si el nombre ya existe
        elseif ($this->checkDuplicate($value)) {
            $this->data_error = 'El nombre del rol ingresada ya existe';
            return false;
        }
        // Después, validar la longitud del nombre
        elseif (Validator::validateLength($value, $min, $max)) {
            $this->nombre = $value;
            return true;
        }
        // Si la longitud del nombre no es válida
        else {
            $this->data_error = 'El nombre debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }


    /*
     *  Métodos para obtener los atributos adicionales.
     */
    public function getDataError()
    {
        return $this->data_error;
    }

    public function getFilename()
    {
        return $this->filename;
    }
}
