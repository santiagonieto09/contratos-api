<?php

namespace App\domain\services\paymentMethod;

/**
 * Estrategia de pago PayPal.
 * 
 * Implementa el método de pago PayPal con las siguientes características:
 * - Interés del 1% sobre el saldo pendiente
 * - Tarifa de pago del 2% sobre el monto de la cuota
 */
class EstrategiaPayPal implements MetodoPagoInterface
{
    /**
     * Tasa de interés mensual sobre saldo pendiente (1%).
     */
    private const TASA_INTERES = 0.01;

    /**
     * Tasa de tarifa de pago (2%).
     */
    private const TASA_TARIFA = 0.02;

    /**
     * {@inheritdoc}
     */
    public function obtenerNombre(): string
    {
        return 'paypal';
    }

    /**
     * {@inheritdoc}
     */
    public function obtenerNombreDescriptivo(): string
    {
        return 'PayPal';
    }

    /**
     * Calcula el interés del 1% sobre el saldo pendiente.
     * 
     * {@inheritdoc}
     */
    public function calcularInteresSaldoPendiente(float $saldoPendiente): float
    {
        return round($saldoPendiente * self::TASA_INTERES, 2);
    }

    /**
     * Calcula la tarifa del 2% sobre el monto de la cuota.
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
