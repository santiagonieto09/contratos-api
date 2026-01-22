<?php

namespace App\presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO para la creación de contratos.
 * 
 * Contiene los datos necesarios para crear un nuevo contrato
 * con validaciones integradas.
 */
class CrearContratoDTO
{
    /**
     * Número único del contrato.
     */
    #[Assert\NotBlank(message: 'El número de contrato es obligatorio')]
    #[Assert\Length(
        min: 1,
        max: 50,
        minMessage: 'El número de contrato debe tener al menos {{ limit }} caracter',
        maxMessage: 'El número de contrato no puede exceder {{ limit }} caracteres'
    )]
    public string $numeroContrato;

    /**
     * Fecha del contrato.
     */
    #[Assert\NotNull(message: 'La fecha del contrato es obligatoria')]
    public \DateTimeImmutable $fechaContrato;

    /**
     * Valor total del contrato.
     */
    #[Assert\NotNull(message: 'El valor total es obligatorio')]
    #[Assert\Positive(message: 'El valor total debe ser positivo')]
    public float $valorTotal;

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
     * Crea una instancia del DTO desde un array.
     * 
     * @param array<string, mixed> $datos Datos del contrato
     * @return self Instancia del DTO
     */
    public static function desdeArray(array $datos): self
    {
        $dto = new self();
        $dto->numeroContrato = $datos['numeroContrato'] ?? '';
        $dto->fechaContrato = isset($datos['fechaContrato']) 
            ? new \DateTimeImmutable($datos['fechaContrato']) 
            : new \DateTimeImmutable();
        $dto->valorTotal = (float) ($datos['valorTotal'] ?? 0);
        $dto->metodoPago = strtolower($datos['metodoPago'] ?? '');
        $dto->numeroMeses = (int) ($datos['numeroMeses'] ?? 0);

        return $dto;
    }
}
