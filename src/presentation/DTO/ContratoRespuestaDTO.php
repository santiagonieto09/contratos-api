<?php

namespace App\presentation\DTO;

use App\dataAccess\entity\Contrato;

/**
 * DTO para la respuesta de contratos.
 * 
 * Formatea los datos de un contrato para la respuesta de la API.
 */
class ContratoRespuestaDTO
{
    /**
     * ID del contrato.
     */
    public string $id;

    /**
     * Número del contrato.
     */
    public string $numeroContrato;

    /**
     * Fecha del contrato.
     */
    public string $fechaContrato;

    /**
     * Valor total del contrato.
     */
    public float $valorTotal;

    /**
     * Método de pago.
     */
    public string $metodoPago;

    /**
     * Número de meses.
     */
    public int $numeroMeses;

    /**
     * Fecha de creación.
     */
    public string $creadoEn;

    /**
     * Cuotas del contrato.
     * 
     * @var array<CuotaRespuestaDTO>
     */
    public array $cuotas = [];

    /**
     * Crea una instancia desde una entidad Contrato.
     * 
     * @param Contrato $contrato Entidad contrato
     * @param bool $incluirCuotas Si se deben incluir las cuotas
     * @return self Instancia del DTO
     */
    public static function desdeEntidad(Contrato $contrato, bool $incluirCuotas = true): self
    {
        $dto = new self();
        $dto->id = (string) $contrato->getId();
        $dto->numeroContrato = $contrato->getNumeroContrato();
        $dto->fechaContrato = $contrato->getFechaContrato()->format('Y-m-d');
        $dto->valorTotal = $contrato->getValorTotalFloat();
        $dto->metodoPago = $contrato->getMetodoPago();
        $dto->numeroMeses = $contrato->getNumeroMeses();
        $dto->creadoEn = $contrato->getCreadoEn()->format('Y-m-d H:i:s');

        if ($incluirCuotas) {
            foreach ($contrato->getCuotas() as $cuota) {
                $dto->cuotas[] = CuotaRespuestaDTO::desdeEntidad($cuota);
            }
        }

        return $dto;
    }

    /**
     * Convierte el DTO a array.
     * 
     * @return array<string, mixed> Datos del DTO
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'numeroContrato' => $this->numeroContrato,
            'fechaContrato' => $this->fechaContrato,
            'valorTotal' => $this->valorTotal,
            'metodoPago' => $this->metodoPago,
            'numeroMeses' => $this->numeroMeses,
            'creadoEn' => $this->creadoEn,
            'cuotas' => array_map(fn(CuotaRespuestaDTO $c) => $c->toArray(), $this->cuotas),
        ];
    }
}
