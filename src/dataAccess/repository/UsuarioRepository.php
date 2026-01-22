<?php

namespace App\dataAccess\repository;

use App\dataAccess\entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Implementación Doctrine del repositorio de usuarios.
 * 
 * Proporciona acceso a los datos de usuarios utilizando
 * Doctrine ORM como capa de persistencia.
 */
class UsuarioRepository implements UsuarioRepositoryInterface
{
    /**
     * Repositorio de entidades Doctrine.
     * 
     * @var EntityRepository<Usuario>
     */
    private EntityRepository $repository;

    /**
     * Constructor del repositorio.
     * 
     * @param EntityManagerInterface $entityManager Entity Manager de Doctrine
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        $this->repository = $entityManager->getRepository(Usuario::class);
    }

    /**
     * {@inheritdoc}
     */
    public function guardar(Usuario $usuario): void
    {
        $this->entityManager->persist($usuario);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function buscarPorId(string $id): ?Usuario
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function buscarPorEmail(string $email): ?Usuario
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

    /**
     * {@inheritdoc}
     */
    public function existePorEmail(string $email): bool
    {
        return $this->buscarPorEmail($email) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function eliminar(Usuario $usuario): void
    {
        $this->entityManager->remove($usuario);
        $this->entityManager->flush();
    }
}
