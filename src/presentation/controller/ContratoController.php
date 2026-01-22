<?php

namespace App\presentation\controller;

use App\dataAccess\entity\Usuario;
use App\domain\exception\AccesoDenegadoException;
use App\domain\exception\ConflictoException;
use App\domain\exception\RecursoNoEncontradoException;
use App\domain\exception\ValidacionException;
use App\domain\services\CalculadoraCuotasServicio;
use App\domain\services\ContratoServicio;
use App\domain\services\ValidadorServicio;
use App\presentation\DTO\ContratoRespuestaDTO;
use App\presentation\DTO\CrearContratoDTO;
use App\presentation\DTO\CuotaRespuestaDTO;
use App\presentation\DTO\ProyeccionCuotasDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controlador de contratos.
 * 
 * Controlador limpio (thin controller) que delega la lógica
 * a los servicios correspondientes.
 */
#[Route('/api/contratos')]
class ContratoController extends AbstractController
{
    /**
     * Constructor del controlador.
     * 
     * @param ContratoServicio $contratoServicio Servicio de contratos
     * @param CalculadoraCuotasServicio $calculadoraCuotas Servicio de cálculo de cuotas
     * @param ValidadorServicio $validador Servicio de validación
     */
    public function __construct(
        private readonly ContratoServicio $contratoServicio,
        private readonly CalculadoraCuotasServicio $calculadoraCuotas,
        private readonly ValidadorServicio $validador
    ) {
    }

    /**
     * Crea un nuevo contrato.
     */
    #[Route('', name: 'api_contratos_crear', methods: ['POST'])]
    public function crear(Request $request): JsonResponse
    {
        try {
            /** @var Usuario $usuario */
            $usuario = $this->getUser();

            $datos = $this->validador->decodificarJson($request->getContent());
            $dto = CrearContratoDTO::desdeArray($datos);
            $this->validador->validar($dto);

            $contrato = $this->contratoServicio->crearContrato($dto, $usuario);
            $respuesta = ContratoRespuestaDTO::desdeEntidad($contrato);

            return $this->json([
                'mensaje' => 'Contrato creado exitosamente',
                'contrato' => $respuesta->toArray()
            ], Response::HTTP_CREATED);

        } catch (ValidacionException $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'detalles' => $e->getErrores()
            ], Response::HTTP_BAD_REQUEST);
        } catch (ConflictoException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al crear contrato',
                'mensaje' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Lista todos los contratos del usuario autenticado.
     */
    #[Route('', name: 'api_contratos_listar', methods: ['GET'])]
    public function listar(): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $this->getUser();

        $contratos = $this->contratoServicio->obtenerPorUsuario($usuario);

        $respuesta = array_map(
            fn($contrato) => ContratoRespuestaDTO::desdeEntidad($contrato, false)->toArray(),
            $contratos
        );

        return $this->json([
            'contratos' => $respuesta,
            'total' => count($respuesta)
        ]);
    }

    /**
     * Obtiene un contrato por su ID.
     */
    #[Route('/{id}', name: 'api_contratos_obtener', methods: ['GET'])]
    public function obtener(string $id): JsonResponse
    {
        try {
            /** @var Usuario $usuario */
            $usuario = $this->getUser();

            $contrato = $this->contratoServicio->obtenerPorIdConPermiso($id, $usuario);
            $respuesta = ContratoRespuestaDTO::desdeEntidad($contrato);

            return $this->json([
                'contrato' => $respuesta->toArray()
            ]);

        } catch (RecursoNoEncontradoException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (AccesoDenegadoException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Obtiene las cuotas proyectadas de un contrato existente.
     */
    #[Route('/{id}/cuotas', name: 'api_contratos_cuotas', methods: ['GET'])]
    public function obtenerCuotas(string $id): JsonResponse
    {
        try {
            /** @var Usuario $usuario */
            $usuario = $this->getUser();

            $contrato = $this->contratoServicio->obtenerPorIdConPermiso($id, $usuario);
            $cuotasModelo = $this->contratoServicio->proyectarCuotasContrato($contrato);

            $cuotasRespuesta = array_map(
                fn($cuota) => CuotaRespuestaDTO::desdeModelo($cuota)->toArray(),
                $cuotasModelo
            );

            return $this->json([
                'contrato' => [
                    'id' => (string) $contrato->getId(),
                    'numeroContrato' => $contrato->getNumeroContrato(),
                    'valorTotal' => $contrato->getValorTotalFloat(),
                    'metodoPago' => $contrato->getMetodoPago(),
                    'numeroMeses' => $contrato->getNumeroMeses(),
                ],
                'cuotas' => $cuotasRespuesta,
                'resumen' => $this->calcularResumen($cuotasRespuesta)
            ]);

        } catch (RecursoNoEncontradoException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (AccesoDenegadoException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Proyecta cuotas sin crear un contrato (simulación pública).
     */
    #[Route('/proyeccion', name: 'api_contratos_proyeccion', methods: ['POST'])]
    public function proyectar(Request $request): JsonResponse
    {
        try {
            $datos = $this->validador->decodificarJson($request->getContent());
            $dto = ProyeccionCuotasDTO::desdeArray($datos);
            $this->validador->validar($dto);

            $fechaContrato = $dto->fechaContrato ?? new \DateTimeImmutable();
            $cuotasModelo = $this->contratoServicio->proyectarCuotas(
                $dto->valorTotal,
                $dto->numeroMeses,
                $dto->metodoPago,
                $fechaContrato
            );

            $cuotasRespuesta = array_map(
                fn($cuota) => CuotaRespuestaDTO::desdeModelo($cuota)->toArray(),
                $cuotasModelo
            );

            $estrategia = $this->calculadoraCuotas->obtenerEstrategia($dto->metodoPago);
            $resumen = $this->calcularResumen($cuotasRespuesta);
            $resumen['diferenciaSobreValorOriginal'] = round($resumen['totalAPagar'] - $dto->valorTotal, 2);

            return $this->json([
                'proyeccion' => [
                    'valorTotal' => $dto->valorTotal,
                    'numeroMeses' => $dto->numeroMeses,
                    'metodoPago' => $estrategia->obtenerNombreDescriptivo(),
                    'fechaContrato' => $fechaContrato->format('Y-m-d'),
                    'tasaInteres' => $estrategia->obtenerTasaInteres() * 100 . '%',
                    'tasaTarifa' => $estrategia->obtenerTasaTarifa() * 100 . '%',
                ],
                'cuotas' => $cuotasRespuesta,
                'resumen' => $resumen
            ]);

        } catch (ValidacionException $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'detalles' => $e->getErrores()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al calcular proyección',
                'mensaje' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtiene los métodos de pago disponibles.
     */
    #[Route('/metodos-pago', name: 'api_metodos_pago', methods: ['GET'], priority: 10)]
    public function metodosPago(): JsonResponse
    {
        $metodos = $this->calculadoraCuotas->obtenerMetodosDisponibles();

        $detalles = [];
        foreach ($metodos as $nombreMetodo) {
            $estrategia = $this->calculadoraCuotas->obtenerEstrategia($nombreMetodo);
            $detalles[] = [
                'id' => $estrategia->obtenerNombre(),
                'nombre' => $estrategia->obtenerNombreDescriptivo(),
                'tasaInteres' => $estrategia->obtenerTasaInteres() * 100 . '%',
                'tasaTarifa' => $estrategia->obtenerTasaTarifa() * 100 . '%',
                'descripcion' => sprintf(
                    'Interés del %s sobre saldo pendiente + Tarifa del %s por pago',
                    $estrategia->obtenerTasaInteres() * 100 . '%',
                    $estrategia->obtenerTasaTarifa() * 100 . '%'
                )
            ];
        }

        return $this->json([
            'metodosPago' => $detalles
        ]);
    }

    /**
     * Calcula el resumen de cuotas.
     * 
     * @param array<array<string, mixed>> $cuotas Cuotas
     * @return array<string, mixed> Resumen
     */
    private function calcularResumen(array $cuotas): array
    {
        $totalInteres = array_sum(array_column($cuotas, 'interes'));
        $totalTarifa = array_sum(array_column($cuotas, 'tarifaPago'));
        $totalPagar = array_sum(array_column($cuotas, 'total'));

        return [
            'totalCuotas' => count($cuotas),
            'totalInteres' => round($totalInteres, 2),
            'totalTarifa' => round($totalTarifa, 2),
            'totalAPagar' => round($totalPagar, 2)
        ];
    }
}
