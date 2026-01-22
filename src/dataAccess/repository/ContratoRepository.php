<?php

namespace App\dataAccess\repository;

use App\dataAccess\entity\Contrato;
use App\dataAccess\entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Implementación Doctrine del repositorio de contratos.
 * 
 * Proporciona acceso a los datos de contratos utilizando
 * Doctrine ORM como capa de persistencia.
 */
class ContratoRepository implements ContratoRepositoryInterface
{
    /**
     * Repositorio de entidades Doctrine.
     * 
     * @var EntityRepository<Contrato>
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
        $this->repository = $entityManager->getRepository(Contrato::class);
    }

    /**
     * {@inheritdoc}
     */
    public function guardar(Contrato $contrato): void
    {
        $this->entityManager->persist($contrato);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function buscarPorId(string $id): ?Contrato
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function buscarPorUsuario(Usuario $usuario): array
    {
        return $this->repository->findBy(
            ['usuario' => $usuario],
            ['creadoEn' => 'DESC']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buscarPorNumeroContrato(string $numeroContrato): ?Contrato
    {
        return $this->repository->findOneBy(['numeroContrato' => $numeroContrato]);
    }

    /**
     * {@inheritdoc}
     */
    public function existePorNumeroContrato(string $numeroContrato): bool
    {
        return $this->buscarPorNumeroContrato($numeroContrato) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function eliminar(Contrato $contrato): void
    {
        $this->entityManager->remove($contrato);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function buscarTodos(): array
    {
        return $this->repository->findAll();
    }
}
