<?php

namespace App\presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO para el registro de usuarios.
 * 
 * Contiene los datos necesarios para registrar un nuevo usuario
 * con validaciones integradas.
 */
class RegistrarUsuarioDTO
{
    /**
     * Email del usuario.
     */
    #[Assert\NotBlank(message: 'El email es obligatorio')]
    #[Assert\Email(message: 'El email "{{ value }}" no es válido')]
    public string $email;

    /**
     * Contraseña del usuario.
     */
    #[Assert\NotBlank(message: 'La contraseña es obligatoria')]
    #[Assert\Length(
        min: 6,
        minMessage: 'La contraseña debe tener al menos {{ limit }} caracteres'
    )]
    public string $password;

    /**
     * Crea una instancia del DTO desde un array.
     * 
     * @param array<string, mixed> $datos Datos del usuario
     * @return self Instancia del DTO
     */
    public static function desdeArray(array $datos): self
    {
        $dto = new self();
        $dto->email = $datos['email'] ?? '';
        $dto->password = $datos['password'] ?? '';

        return $dto;
    }
}
