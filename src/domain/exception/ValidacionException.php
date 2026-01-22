<?php

namespace App\domain\exception;

/**
 * Excepción para errores de validación.
 * 
 * Contiene los detalles de los errores de validación
 * para ser procesados por el controlador.
 */
class ValidacionException extends \Exception
{
    /**
     * Errores de validación.
     * 
     * @var array<string, string>
     */
    private array $errores;

    /**
     * Constructor de la excepción.
     * 
     * @param array<string, string> $errores Errores de validación
     * @param string $mensaje Mensaje general
     */
    public function __construct(array $errores, string $mensaje = 'Errores de validación')
    {
        parent::__construct($mensaje);
        $this->errores = $errores;
    }

    /**
     * Obtiene los errores de validación.
     * 
     * @return array<string, string> Errores
     */
    public function getErrores(): array
    {
        return $this->errores;
    }
}
