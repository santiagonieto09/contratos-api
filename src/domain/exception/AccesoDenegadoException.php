<?php

namespace App\domain\exception;

/**
 * Excepción para accesos denegados.
 */
class AccesoDenegadoException extends \Exception
{
    /**
     * Constructor de la excepción.
     * 
     * @param string $mensaje Mensaje de error
     */
    public function __construct(string $mensaje = 'No tiene permiso para realizar esta acción')
    {
        parent::__construct($mensaje);
    }
}
