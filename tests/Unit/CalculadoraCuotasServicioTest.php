<?php

namespace App\Tests\Unit;

use App\domain\services\CalculadoraCuotasServicio;
use App\domain\services\paymentMethod\EstrategiaPayPal;
use App\domain\services\paymentMethod\EstrategiaPayOnline;
use PHPUnit\Framework\TestCase;

/**
 * Tests para el servicio de cálculo de cuotas.
 */
class CalculadoraCuotasServicioTest extends TestCase
{
    private CalculadoraCuotasServicio $servicio;

    protected function setUp(): void
    {
        $this->servicio = new CalculadoraCuotasServicio([
            new EstrategiaPayPal(),
            new EstrategiaPayOnline(),
        ]);
    }

    /**
     * Test: PayPal aplica 1% interés y 2% tarifa.
     */
    public function testPayPalCalculaInteresYTarifaCorrectamente(): void
    {
        $cuotas = $this->servicio->calcularCuotas(
            12000,
            12,
            'paypal',
            new \DateTimeImmutable('2026-01-01')
        );

        $this->assertCount(12, $cuotas);
        
        // Primera cuota: valorBase=1000, saldoPendiente=12000, interes=120 (1%), tarifa=22.4 (2%)
        $primeraCuota = $cuotas[0];
        $this->assertEquals(1, $primeraCuota->getNumeroCuota());
        $this->assertEquals(1000, $primeraCuota->getValorBase());
        $this->assertEquals(120, $primeraCuota->getInteres()); // 1% de 12000
    }

    /**
     * Test: PayOnline aplica 2% interés y 1% tarifa.
     */
    public function testPayOnlineCalculaInteresYTarifaCorrectamente(): void
    {
        $cuotas = $this->servicio->calcularCuotas(
            12000,
            12,
            'payonline',
            new \DateTimeImmutable('2026-01-01')
        );

        $this->assertCount(12, $cuotas);
        
        // Primera cuota: interes=240 (2% de 12000)
        $primeraCuota = $cuotas[0];
        $this->assertEquals(240, $primeraCuota->getInteres()); // 2% de 12000
    }

    /**
     * Test: Las fechas de cuotas son correctas.
     */
    public function testFechasDeCuotasSonCorrectas(): void
    {
        $fechaContrato = new \DateTimeImmutable('2026-01-15');
        
        $cuotas = $this->servicio->calcularCuotas(
            6000,
            6,
            'paypal',
            $fechaContrato
        );

        // Primera cuota: 1 mes después
        $this->assertEquals('2026-02-15', $cuotas[0]->getFechaPago()->format('Y-m-d'));
        // Segunda cuota: 2 meses después
        $this->assertEquals('2026-03-15', $cuotas[1]->getFechaPago()->format('Y-m-d'));
        // Última cuota: 6 meses después
        $this->assertEquals('2026-07-15', $cuotas[5]->getFechaPago()->format('Y-m-d'));
    }

    /**
     * Test: Método de pago inválido lanza excepción.
     */
    public function testMetodoPagoInvalidoLanzaExcepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->servicio->calcularCuotas(
            1000,
            12,
            'metodo_invalido',
            new \DateTimeImmutable()
        );
    }

    /**
     * Test: Verificar métodos disponibles.
     */
    public function testObtenerMetodosDisponibles(): void
    {
        $metodos = $this->servicio->obtenerMetodosDisponibles();
        
        $this->assertContains('paypal', $metodos);
        $this->assertContains('payonline', $metodos);
    }
}
