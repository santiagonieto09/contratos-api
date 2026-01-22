<?php

namespace App\domain\services;

use App\domain\models\CuotaModelo;
use App\domain\services\paymentMethod\MetodoPagoInterface;

/**
 * Servicio para calcular las cuotas de un contrato.
 * 
 * Utiliza el patrón Strategy para aplicar diferentes métodos de pago
 * y calcular los intereses y tarifas correspondientes.
 */
class CalculadoraCuotasServicio
{
    /**
     * Métodos de pago disponibles indexados por nombre.
     * 
     * @var array<string, MetodoPagoInterface>
     */
    private array $metodosIndexados = [];

    /**
     * Constructor del servicio.
     * 
     * @param iterable<MetodoPagoInterface> $metodosDisponibles Métodos de pago inyectados
     */
    public function __construct(iterable $metodosDisponibles)
    {
        foreach ($metodosDisponibles as $metodo) {
            $this->metodosIndexados[$metodo->obtenerNombre()] = $metodo;
        }
    }

    /**
     * Calcula las cuotas para un contrato dado.
     * 
     * Fórmula por cuota:
     * - valorBase = valorTotal / numeroMeses
     * - saldoPendiente = valorTotal - (valorBase * (numeroCuota - 1))
     * - interes = saldoPendiente * tasaInteres
     * - montoCuota = valorBase + interes
     * - tarifaPago = montoCuota * tasaTarifa
     * - total = montoCuota + tarifaPago
     * - fechaPago = fechaContrato + numeroCuota meses
     * 
     * @param float $valorTotal Valor total del contrato
     * @param int $numeroMeses Número de meses para pagar
     * @param string $metodoPago Nombre del método de pago ('paypal' o 'payonline')
     * @param \DateTimeImmutable $fechaContrato Fecha del contrato
     * @return array<CuotaModelo> Lista de cuotas calculadas
     * @throws \InvalidArgumentException Si el método de pago no es válido
     */
    public function calcularCuotas(
        float $valorTotal,
        int $numeroMeses,
        string $metodoPago,
        \DateTimeImmutable $fechaContrato
    ): array {
        $estrategia = $this->obtenerEstrategia($metodoPago);
        $cuotas = [];
        $valorBase = round($valorTotal / $numeroMeses, 2);

        for ($i = 1; $i <= $numeroMeses; $i++) {
            // Calcular saldo pendiente antes de esta cuota
            $saldoPendiente = $valorTotal - ($valorBase * ($i - 1));
            
            // Calcular interés sobre saldo pendiente
            $interes = $estrategia->calcularInteresSaldoPendiente($saldoPendiente);
            
            // Monto de la cuota (base + interés)
            $montoCuota = $valorBase + $interes;
            
            // Tarifa de pago sobre el monto de la cuota
            $tarifaPago = $estrategia->calcularTarifaPago($montoCuota);
            
            // Total a pagar
            $total = round($montoCuota + $tarifaPago, 2);
            
            // Fecha de pago (i meses despues del contrato)
            $fechaPago = $fechaContrato->modify("+{$i} month");

            $cuota = new CuotaModelo();
            $cuota->setNumeroCuota($i)
                ->setValorBase($valorBase)
                ->setInteres($interes)
                ->setTarifaPago($tarifaPago)
                ->setTotal($total)
                ->setFechaPago($fechaPago);

            $cuotas[] = $cuota;
        }

        return $cuotas;
    }

    /**
     * Obtiene la estrategia de pago por su nombre.
     * 
     * @param string $nombreMetodo Nombre del método de pago
     * @return MetodoPagoInterface Estrategia de pago
     * @throws \InvalidArgumentException Si el método no existe
     */
    public function obtenerEstrategia(string $nombreMetodo): MetodoPagoInterface
    {
        $nombreNormalizado = strtolower($nombreMetodo);
        
        if (!isset($this->metodosIndexados[$nombreNormalizado])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Método de pago "%s" no válido. Métodos disponibles: %s',
                    $nombreMetodo,
                    implode(', ', array_keys($this->metodosIndexados))
                )
            );
        }

        return $this->metodosIndexados[$nombreNormalizado];
    }

    /**
     * Obtiene los nombres de todos los métodos de pago disponibles.
     * 
     * @return array<string> Lista de nombres de métodos
     */
    public function obtenerMetodosDisponibles(): array
    {
        return array_keys($this->metodosIndexados);
    }

    /**
     * Verifica si un método de pago es válido.
     * 
     * @param string $nombreMetodo Nombre del método a verificar
     * @return bool True si el método existe
     */
    public function esMetodoValido(string $nombreMetodo): bool
    {
        return isset($this->metodosIndexados[strtolower($nombreMetodo)]);
    }
}
