<?php

namespace App\domain\services\paymentMethod;

/**
 * Estrategia de pago PayOnline.
 * 
 * Implementa el método de pago PayOnline con las siguientes características:
 * - Interés del 2% sobre el saldo pendiente
 * - Tarifa de pago del 1% sobre el monto de la cuota
 */
class EstrategiaPayOnline implements MetodoPagoInterface
{
    /**
     * Tasa de interés mensual sobre saldo pendiente (2%).
     */
    private const TASA_INTERES = 0.02;

    /**
     * Tasa de tarifa de pago (1%).
     */
    private const TASA_TARIFA = 0.01;

    /**
     * {@inheritdoc}
     */
    public function obtenerNombre(): string
    {
        return 'payonline';
    }

    /**
     * {@inheritdoc}
     */
    public function obtenerNombreDescriptivo(): string
    {
        return 'PayOnline';
    }

    /**
     * Calcula el interés del 2% sobre el saldo pendiente.
     * 
     * {@inheritdoc}
     */
    public function calcularInteresSaldoPendiente(float $saldoPendiente): float
    {
        return round($saldoPendiente * self::TASA_INTERES, 2);
    }

    /**
     * Calcula la tarifa del 1% sobre el monto de la cuota.
     * 
     * {@inheritdoc}
     */
    public function calcularTarifaPago(float $montoCuota): float
    {
        return round($montoCuota * self::TASA_TARIFA, 2);
    }

    /**
     * {@inheritdoc}
     */
    public function obtenerTasaInteres(): float
    {
        return self::TASA_INTERES;
    }

    /**
     * {@inheritdoc}
     */
    public function obtenerTasaTarifa(): float
    {
        return self::TASA_TARIFA;
    }
}
