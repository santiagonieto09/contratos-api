<?php

namespace App\domain\services;

use App\dataAccess\entity\Contrato;
use App\dataAccess\entity\Cuota;
use App\dataAccess\entity\Usuario;
use App\dataAccess\repository\ContratoRepositoryInterface;
use App\domain\exception\AccesoDenegadoException;
use App\domain\exception\ConflictoException;
use App\domain\exception\RecursoNoEncontradoException;
use App\domain\models\CuotaModelo;
use App\presentation\DTO\CrearContratoDTO;

/**
 * Servicio de dominio para la gestión de contratos.
 * 
 * Maneja la lógica de negocio relacionada con contratos,
 * incluyendo la creación y generación de cuotas.
 */
class ContratoServicio
{
    /**
     * Constructor del servicio.
     * 
     * @param ContratoRepositoryInterface $contratoRepository Repositorio de contratos
     * @param CalculadoraCuotasServicio $calculadoraCuotas Servicio de cálculo de cuotas
     */
    public function __construct(
        private readonly ContratoRepositoryInterface $contratoRepository,
        private readonly CalculadoraCuotasServicio $calculadoraCuotas
    ) {
    }

    /**
     * Crea un nuevo contrato con sus cuotas generadas.
     * 
     * @param CrearContratoDTO $dto Datos para crear el contrato
     * @param Usuario $usuario Usuario propietario del contrato
     * @return Contrato Contrato creado con sus cuotas
     * @throws ConflictoException Si el número de contrato ya existe
     * @throws \InvalidArgumentException Si el método de pago no es válido
     */
    public function crearContrato(CrearContratoDTO $dto, Usuario $usuario): Contrato
    {
        // Verificar si el número de contrato ya existe
        if ($this->existeNumeroContrato($dto->numeroContrato)) {
            throw new ConflictoException('El número de contrato ya existe');
        }

        // Validar método de pago
        if (!$this->calculadoraCuotas->esMetodoValido($dto->metodoPago)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Método de pago "%s" no válido. Métodos disponibles: %s',
                    $dto->metodoPago,
                    implode(', ', $this->calculadoraCuotas->obtenerMetodosDisponibles())
                )
            );
        }

        // Crear entidad contrato
        $contrato = new Contrato();
        $contrato->setNumeroContrato($dto->numeroContrato)
            ->setFechaContrato($dto->fechaContrato)
            ->setValorTotal($dto->valorTotal)
            ->setMetodoPago($dto->metodoPago)
            ->setNumeroMeses($dto->numeroMeses)
            ->setUsuario($usuario);

        // Generar cuotas
        $cuotasModelo = $this->calculadoraCuotas->calcularCuotas(
            $dto->valorTotal,
            $dto->numeroMeses,
            $dto->metodoPago,
            $dto->fechaContrato
        );

        // Convertir modelos a entidades y agregar al contrato
        foreach ($cuotasModelo as $cuotaModelo) {
            $cuota = $this->convertirModeloACuota($cuotaModelo);
            $contrato->agregarCuota($cuota);
        }

        // Persistir contrato
        $this->contratoRepository->guardar($contrato);

        return $contrato;
    }

    /**
     * Obtiene un contrato por su ID.
     * 
     * @param string $id ID del contrato
     * @return Contrato|null Contrato encontrado o null
     */
    public function obtenerPorId(string $id): ?Contrato
    {
        return $this->contratoRepository->buscarPorId($id);
    }

    /**
     * Obtiene un contrato por ID verificando que pertenece al usuario.
     * 
     * @param string $id ID del contrato
     * @param Usuario $usuario Usuario que solicita
     * @return Contrato Contrato encontrado
     * @throws RecursoNoEncontradoException Si el contrato no existe
     * @throws AccesoDenegadoException Si el contrato no pertenece al usuario
     */
    public function obtenerPorIdConPermiso(string $id, Usuario $usuario): Contrato
    {
        $contrato = $this->contratoRepository->buscarPorId($id);

        if (!$contrato) {
            throw new RecursoNoEncontradoException('Contrato', $id);
        }

        if ($contrato->getUsuario()?->getId()->toString() !== $usuario->getId()->toString()) {
            throw new AccesoDenegadoException('No tiene permiso para ver este contrato');
        }

        return $contrato;
    }

    /**
     * Obtiene todos los contratos de un usuario.
     * 
     * @param Usuario $usuario Usuario propietario
     * @return array<Contrato> Lista de contratos
     */
    public function obtenerPorUsuario(Usuario $usuario): array
    {
        return $this->contratoRepository->buscarPorUsuario($usuario);
    }

    /**
     * Proyecta las cuotas de un contrato existente.
     * 
     * @param Contrato $contrato Contrato a proyectar
     * @return array<CuotaModelo> Lista de cuotas proyectadas
     */
    public function proyectarCuotasContrato(Contrato $contrato): array
    {
        return $this->calculadoraCuotas->calcularCuotas(
            $contrato->getValorTotalFloat(),
            $contrato->getNumeroMeses(),
            $contrato->getMetodoPago(),
            $contrato->getFechaContrato()
        );
    }

    /**
     * Proyecta cuotas sin crear un contrato (simulación).
     * 
     * @param float $valorTotal Valor total
     * @param int $numeroMeses Número de meses
     * @param string $metodoPago Método de pago
     * @param \DateTimeImmutable $fechaContrato Fecha del contrato
     * @return array<CuotaModelo> Lista de cuotas proyectadas
     */
    public function proyectarCuotas(
        float $valorTotal,
        int $numeroMeses,
        string $metodoPago,
        \DateTimeImmutable $fechaContrato
    ): array {
        return $this->calculadoraCuotas->calcularCuotas(
            $valorTotal,
            $numeroMeses,
            $metodoPago,
            $fechaContrato
        );
    }

    /**
     * Verifica si un número de contrato ya existe.
     * 
     * @param string $numeroContrato Número a verificar
     * @return bool True si ya existe
     */
    public function existeNumeroContrato(string $numeroContrato): bool
    {
        return $this->contratoRepository->existePorNumeroContrato($numeroContrato);
    }

    /**
     * Convierte un modelo de cuota a entidad.
     * 
     * @param CuotaModelo $modelo Modelo de cuota
     * @return Cuota Entidad cuota
     */
    private function convertirModeloACuota(CuotaModelo $modelo): Cuota
    {
        $cuota = new Cuota();
        $cuota->setNumeroCuota($modelo->getNumeroCuota())
            ->setValorBase($modelo->getValorBase())
            ->setInteres($modelo->getInteres())
            ->setTarifaPago($modelo->getTarifaPago())
            ->setTotal($modelo->getTotal())
            ->setFechaPago($modelo->getFechaPago());

        return $cuota;
    }
}
