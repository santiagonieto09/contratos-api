<?php

namespace App\domain\services;

use App\dataAccess\entity\Usuario;
use App\dataAccess\repository\UsuarioRepositoryInterface;
use App\domain\exception\ConflictoException;
use App\presentation\DTO\RegistrarUsuarioDTO;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Servicio de autenticación.
 * 
 * Maneja la lógica de negocio relacionada con la autenticación
 * y registro de usuarios.
 */
class AuthServicio
{
    /**
     * Constructor del servicio.
     * 
     * @param UsuarioRepositoryInterface $usuarioRepository Repositorio de usuarios
     * @param UserPasswordHasherInterface $passwordHasher Hasher de contraseñas
     */
    public function __construct(
        private readonly UsuarioRepositoryInterface $usuarioRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    /**
     * Registra un nuevo usuario.
     * 
     * @param RegistrarUsuarioDTO $dto Datos del usuario a registrar
     * @return Usuario Usuario registrado
     * @throws ConflictoException Si el email ya está registrado
     */
    public function registrar(RegistrarUsuarioDTO $dto): Usuario
    {
        // Verificar si el email ya existe
        if ($this->usuarioRepository->existePorEmail($dto->email)) {
            throw new ConflictoException('El email ya está registrado');
        }

        // Crear usuario
        $usuario = new Usuario();
        $usuario->setEmail($dto->email);
        
        // Hashear contraseña
        $passwordHasheada = $this->passwordHasher->hashPassword($usuario, $dto->password);
        $usuario->setPassword($passwordHasheada);
        $usuario->setRoles(['ROLE_USER']);

        // Guardar usuario
        $this->usuarioRepository->guardar($usuario);

        return $usuario;
    }

    /**
     * Busca un usuario por su email.
     * 
     * @param string $email Email del usuario
     * @return Usuario|null Usuario encontrado o null
     */
    public function buscarPorEmail(string $email): ?Usuario
    {
        return $this->usuarioRepository->buscarPorEmail($email);
    }
}
