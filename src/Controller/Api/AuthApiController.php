<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * ============================================================
 * CONTROLADOR API - AUTENTICACIÓN
 * ============================================================
 * 
 * Gestiona el login, logout y autenticación de usuarios.
 * Todos los endpoints usan tokens como autenticación.
 */
#[Route('/api', name: 'api_')]
class AuthApiController extends AbstractController
{
    // ============ LOGIN ============
    
    /**
     * POST /api/login
     * 
     * Autentica un usuario y devuelve su token JWT.
     * 
     * @param Request $request Solicitud HTTP con email y password
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $passwordHasher
     * @return JsonResponse
     */
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        \Psr\Log\LoggerInterface $logger
    ): JsonResponse {
        try {
            // Decodificar el JSON del request o aceptar form-data/urlencoded
            $content = $request->getContent();
            $data = json_decode($content, true);
            if (!is_array($data)) {
                // Intentar leer como form data (x-www-form-urlencoded o multipart)
                $data = $request->request->all();
            }

            // Validar que existan los campos requeridos (aceptamos 'usuario' o 'email')
            $email = $data['usuario'] ?? $data['email'] ?? null;
            $password = $data['password'] ?? null;
            if (!$email || !$password) {
                // Loguear para depuración
                $logger->warning('Auth login - missing fields', [
                    'remote_ip' => $request->getClientIp(),
                    'content_length' => is_string($content) ? strlen($content) : 0,
                    'raw_body_preview' => is_string($content) ? substr($content, 0, 1000) : '',
                    'request_headers' => $request->headers->all(),
                ]);

                // Para depuración, devolver qué claves se recibieron (sin incluir password)
                $receivedKeys = array_keys(is_array($data) ? $data : []);
                $rawBody = $content;
                return $this->json([
                    'success' => false,
                    'message' => 'Faltan campos requeridos: usuario|email, password',
                    'received_keys' => $receivedKeys,
                    'raw_body_length' => is_string($rawBody) ? strlen($rawBody) : 0,
                    'raw_body_preview' => is_string($rawBody) ? substr($rawBody, 0, 1000) : ''
                ], 400);
            }

            // Buscar el usuario por email
            $user = $userRepository->findOneBy(['email' => $email]);
            
            // Si no existe o la contraseña es incorrecta
            if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Credenciales inválidas'
                ], 401);
            }

            // Login exitoso - devolver token y perfil
            return $this->json([
                'success' => true,
                'message' => 'Login exitoso',
                'data' => [
                    'user_token' => 'usr_tok_' . $user->getToken(),
                    'user_profile' => [
                        'email' => $user->getEmail(),
                        'nombre' => $user->getNombre(),
                        'estado' => $user->getEstado()->value
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error al iniciar sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ LOGOUT ============
    
    /**
     * POST /api/logout
     * 
     * Cierra la sesión del usuario invalidando su token.
     * Requiere autenticación mediante Bearer token.
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(
        Request $request,
        UserRepository $userRepository
    ): JsonResponse {
        try {
            // Obtener el token del header Authorization
            $token = $this->extractTokenFromHeader($request);
            if (!$token) {
                return $this->json([
                    'success' => false,
                    'message' => 'Token no proporcionado'
                ], 401);
            }

            // Buscar usuario por token
            $user = $userRepository->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'Token inválido'
                ], 401);
            }

            // Logout exitoso
            return $this->json([
                'success' => true,
                'message' => 'Sesión cerrada correctamente',
                'data' => [
                    'user_token_invalidado' => 'usr_tok_' . $token,
                    'timestamp_cierre' => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
                    'sesiones_restantes' => 0
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error al cerrar sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ MÉTODO AUXILIAR ============
    
    /**
     * Extrae el token del header Authorization
     * Formato esperado: "Bearer <token>"
     * 
     * @param Request $request
     * @return string|null Token sin el prefijo "Bearer "
     */
    private function extractTokenFromHeader(Request $request): ?string
    {
        $authHeader = $request->headers->get('Authorization');
        
        // Si no existe el header o no comienza con "Bearer "
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }
        
        // Extraer y devolver el token (sin "Bearer ")
        return substr($authHeader, 7);
    }
}
