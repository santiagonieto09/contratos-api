<?php

namespace App\domain\services;

use App\domain\exception\ValidacionException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Servicio de validación de DTOs.
 * 
 * Encapsula la lógica de validación para mantener
 * los controladores limpios (thin controllers).
 */
class ValidadorServicio
{
    /**
     * Constructor del servicio.
     * 
     * @param ValidatorInterface $validator Validador de Symfony
     */
    public function __construct(
        private readonly ValidatorInterface $validator
    ) {
    }

    /**
     * Valida un objeto DTO.
     * 
     * @param object $dto Objeto a validar
     * @throws ValidacionException Si hay errores de validación
     */
    public function validar(object $dto): void
    {
        $errores = $this->validator->validate($dto);

        if (count($errores) > 0) {
            $mensajesError = [];
            foreach ($errores as $error) {
                $mensajesError[$error->getPropertyPath()] = $error->getMessage();
            }
            throw new ValidacionException($mensajesError);
        }
    }

    /**
     * Decodifica y valida JSON del request.
     * 
     * @param string|null $contenido Contenido JSON
     * @return array<string, mixed> Datos decodificados
     * @throws ValidacionException Si el JSON es inválido
     */
    public function decodificarJson(?string $contenido): array
    {
        if (empty($contenido)) {
            throw new ValidacionException(
                ['json' => 'El cuerpo de la petición está vacío'],
                'Datos JSON inválidos'
            );
        }

        $datos = json_decode($contenido, true);

        if ($datos === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new ValidacionException(
                ['json' => 'El JSON proporcionado no es válido: ' . json_last_error_msg()],
                'Datos JSON inválidos'
            );
        }

        return $datos ?? [];
    }
}
