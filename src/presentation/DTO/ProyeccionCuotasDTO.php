<?php

namespace App\presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO para solicitar proyección de cuotas sin crear contrato.
 * 
 * Permite simular el cálculo de cuotas antes de crear un contrato.
 */
class ProyeccionCuotasDTO
{
    /**
     * Valor total del contrato.
     */
    #[Assert\NotNull(message: 'El valor total es obligatorio')]
    #[Assert\Positive(message: 'El valor total debe ser positivo')]
    public float $valorTotal;

    /**
     * Número de meses para el pago.
     */
    #[Assert\NotNull(message: 'El número de meses es obligatorio')]
    #[Assert\Positive(message: 'El número de meses debe ser positivo')]
    #[Assert\LessThanOrEqual(
        value: 120,
        message: 'El número de meses no puede exceder {{ compared_value }}'
    )]
    public int $numeroMeses;

    /**
     * Método de pago (paypal o payonline).
     */
    #[Assert\NotBlank(message: 'El método de pago es obligatorio')]
    #[Assert\Choice(
        choices: ['paypal', 'payonline'],
        message: 'El método de pago debe ser "paypal" o "payonline"'
    )]
    public string $metodoPago;

    /**
     * Fecha del contrato (opcional, por defecto hoy).
     */
    public ?\DateTimeImmutable $fechaContrato = null;

    /**
     * Crea una instancia del DTO desde un array.
     * 
     * @param array<string, mixed> $datos Datos de la proyección
     * @return self Instancia del DTO
     */
    public static function desdeArray(array $datos): self
    {
        $dto = new self();
        $dto->valorTotal = (float) ($datos['valorTotal'] ?? 0);
        $dto->numeroMeses = (int) ($datos['numeroMeses'] ?? 0);
        $dto->metodoPago = strtolower($datos['metodoPago'] ?? '');
        $dto->fechaContrato = isset($datos['fechaContrato']) 
            ? new \DateTimeImmutable($datos['fechaContrato']) 
            : new \DateTimeImmutable();

        return $dto;
    }
}
