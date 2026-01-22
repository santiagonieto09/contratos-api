<?php

namespace App\presentation\controller;

use App\dataAccess\entity\Usuario;
use App\domain\exception\ConflictoException;
use App\domain\exception\ValidacionException;
use App\domain\services\AuthServicio;
use App\domain\services\ValidadorServicio;
use App\presentation\DTO\RegistrarUsuarioDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controlador de autenticación.
 * 
 * Controlador limpio (thin controller) que delega la lógica
 * al servicio de autenticación.
 */
#[Route('/api')]
class AuthController extends AbstractController
{
    /**
     * Constructor del controlador.
     * 
     * @param AuthServicio $authServicio Servicio de autenticación
     * @param ValidadorServicio $validador Servicio de validación
     */
    public function __construct(
        private readonly AuthServicio $authServicio,
        private readonly ValidadorServicio $validador
    ) {
    }

    /**
     * Registra un nuevo usuario.
     */
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function registrar(Request $request): JsonResponse
    {
        try {
            $datos = $this->validador->decodificarJson($request->getContent());
            $dto = RegistrarUsuarioDTO::desdeArray($datos);
            $this->validador->validar($dto);

            $usuario = $this->authServicio->registrar($dto);

            return $this->json([
                'mensaje' => 'Usuario registrado exitosamente',
                'usuario' => [
                    'id' => (string) $usuario->getId(),
                    'email' => $usuario->getEmail(),
                    'creadoEn' => $usuario->getCreadoEn()->format('Y-m-d H:i:s')
                ]
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
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al registrar usuario',
                'mensaje' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtiene el perfil del usuario autenticado.
     */
    #[Route('/perfil', name: 'api_perfil', methods: ['GET'])]
    public function perfil(): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $this->getUser();

        return $this->json([
            'usuario' => [
                'id' => (string) $usuario->getId(),
                'email' => $usuario->getEmail(),
                'roles' => $usuario->getRoles(),
                'creadoEn' => $usuario->getCreadoEn()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
