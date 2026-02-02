<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Enum\EstadoUsuario;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * ============================================================
 * CONTROLADOR API - USUARIOS (CRUD)
 * ============================================================
 * 
 * Gestiona el CRUD completo de usuarios.
 * - POST /api/usuarios: Crear (Registrar)
 * - GET /api/usuarios/perfil: Obtener mi perfil
 * - GET /api/usuarios/{id}: Ver perfil de otro usuario
 * - PATCH /api/usuarios/{id}: Actualizar usuario
 * - DELETE /api/usuarios/{id}: Eliminar usuario
 */
#[Route('/api/usuarios', name: 'api_usuarios_')]
class UsuarioApiController extends AbstractController
{
    // ============ C - CREATE (Registrar) ============
    
    /**
     * POST /api/usuarios
     * 
     * Registra un nuevo usuario en el sistema.
     * Este es un endpoint público (sin autenticación).
     * 
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            // Validar campos requeridos
            if (!isset($data['email']) || !isset($data['password']) || !isset($data['nombre'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Faltan campos requeridos: email, password, nombre'
                ], 400);
            }

            // Verificar si el email ya existe
            $userExisting = $userRepository->findOneBy(['email' => $data['email']]);
            if ($userExisting) {
                return $this->json([
                    'success' => false,
                    'message' => 'El email ya está registrado'
                ], 422);
            }

            // Crear nuevo usuario
            $user = new User();
            $user->setEmail($data['email']);
            $user->setNombre($data['nombre']);
            $user->setRoles(['ROLE_USER']);
            
            // Hash de la contraseña
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
            
            // Generar token único
            $user->setToken(bin2hex(random_bytes(32)));

            // Persistir el usuario
            $em->persist($user);
            $em->flush();

            // Respuesta exitosa
            return $this->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'data' => [
                    'user_token' => 'usr_tok_' . $user->getToken(),
                    'usuario' => [
                        'email' => $user->getEmail(),
                        'nombre' => $user->getNombre(),
                        'estado' => $user->getEstado()->value,
                        'fecha_registro' => $user->getFechaRegistro()?->format('Y-m-d\TH:i:s\Z')
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error al registrar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ R - READ (Mi Perfil) ============
    
    /**
     * GET /api/usuarios/perfil
     * 
     * Obtiene el perfil completo del usuario autenticado.
     * Requiere token válido en header Authorization.
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/perfil', name: 'read_my_profile', methods: ['GET'])]
    public function readMyProfile(
        Request $request,
        UserRepository $userRepository
    ): JsonResponse {
        try {
            // Obtener usuario autenticado
            $user = $this->getAuthenticatedUser($request, $userRepository);
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'No autenticado'
                ], 401);
            }

            // Devolver perfil
            return $this->json([
                'success' => true,
                'message' => 'Perfil obtenido exitosamente',
                'data' => $this->serializeUser($user)
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error al obtener perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ R - READ (Perfil Específico) ============
    
    /**
     * GET /api/usuarios/{id}
     * 
     * Obtiene el perfil público de un usuario específico.
     * Requiere autenticación.
     * 
     * @param int $id ID del usuario a consultar
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'read_user', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function readUser(
        int $id,
        Request $request,
        UserRepository $userRepository
    ): JsonResponse {
        try {
            // Validar autenticación
            $authenticatedUser = $this->getAuthenticatedUser($request, $userRepository);
            if (!$authenticatedUser) {
                return $this->json([
                    'success' => false,
                    'message' => 'No autenticado'
                ], 401);
            }

            // Obtener usuario específico
            $user = $userRepository->find($id);
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Devolver perfil público
            return $this->json([
                'success' => true,
                'message' => 'Perfil obtenido exitosamente',
                'data' => $this->serializeUser($user)
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error al obtener perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ U - UPDATE ============
    
    /**
     * PATCH /api/usuarios/{id}
     * 
     * Actualiza los datos de un usuario.
     * Solo el propietario puede actualizar su propio perfil.
     * 
     * @param int $id ID del usuario a actualizar
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'update', methods: ['PATCH'], requirements: ['id' => '\d+'])]
    public function update(
        int $id,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            // Obtener usuario autenticado
            $authenticatedUser = $this->getAuthenticatedUser($request, $userRepository);
            if (!$authenticatedUser) {
                return $this->json([
                    'success' => false,
                    'message' => 'No autenticado'
                ], 401);
            }

            // Obtener usuario a actualizar
            $user = $userRepository->find($id);
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar que solo puede actualizar su propio perfil
            if ($authenticatedUser->getId() !== $user->getId()) {
                return $this->json([
                    'success' => false,
                    'message' => 'No tienes permisos para actualizar este usuario'
                ], 403);
            }

            $data = json_decode($request->getContent(), true);

            // Actualizar campos opcionales
            if (isset($data['nombre'])) {
                $user->setNombre($data['nombre']);
            }
            
            if (isset($data['estado'])) {
                try {
                    $estado = EstadoUsuario::from($data['estado']);
                    $user->setEstado($estado);
                } catch (\ValueError $e) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Estado inválido. Valores permitidos: online, offline, ocupado'
                    ], 422);
                }
            }
            
            if (isset($data['avatar_url'])) {
                $user->setAvatarUrl($data['avatar_url']);
            }

            // Persistir cambios
            $em->persist($user);
            $em->flush();

            // Respuesta exitosa
            return $this->json([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente',
                'data' => $this->serializeUser($user)
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ D - DELETE ============
    
    /**
     * DELETE /api/usuarios/{id}
     * 
     * Elimina/desactiva una cuenta de usuario.
     * Solo el propietario puede eliminar su cuenta.
     * 
     * @param int $id ID del usuario a eliminar
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(
        int $id,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            // Obtener usuario autenticado
            $authenticatedUser = $this->getAuthenticatedUser($request, $userRepository);
            if (!$authenticatedUser) {
                return $this->json([
                    'success' => false,
                    'message' => 'No autenticado'
                ], 401);
            }

            // Obtener usuario a eliminar
            $user = $userRepository->find($id);
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar que solo puede eliminar su propia cuenta
            if ($authenticatedUser->getId() !== $user->getId()) {
                return $this->json([
                    'success' => false,
                    'message' => 'No tienes permisos para eliminar este usuario'
                ], 403);
            }

            // Eliminar usuario
            $em->remove($user);
            $em->flush();

            // Respuesta exitosa
            return $this->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente',
                'data' => [
                    'usuario_id' => $id,
                    'email' => $user->getEmail()
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ MÉTODOS AUXILIARES ============
    
    /**
     * Obtiene el usuario autenticado desde el token del header
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @return User|null Usuario autenticado o null si no existe token válido
     */
    private function getAuthenticatedUser(Request $request, UserRepository $userRepository): ?User
    {
        $token = $this->extractTokenFromHeader($request);
        if (!$token) {
            return null;
        }
        return $userRepository->findOneBy(['token' => $token]);
    }

    /**
     * Extrae el token del header Authorization
     * Formato esperado: "Bearer <token>"
     * 
     * @param Request $request
     * @return string|null Token sin el prefijo
     */
    private function extractTokenFromHeader(Request $request): ?string
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }
        return substr($authHeader, 7);
    }

    /**
     * Serializa un usuario a array para la respuesta JSON
     * 
     * @param User $user
     * @return array Datos del usuario formateados
     */
    private function serializeUser(User $user): array
    {
        return [
            'usuario_id' => $user->getId(),
            'email' => $user->getEmail(),
            'nombre' => $user->getNombre(),
            'estado' => $user->getEstado()->value,
            'avatar_url' => $user->getAvatarUrl(),
            'ultima_actividad' => $user->getUltimaActividad()?->format('Y-m-d\TH:i:s\Z'),
            // Datos de ubicación y visibilidad
            'latitud' => $user->getLatitud(),
            'longitud' => $user->getLongitud(),
            'compartir_ubicacion' => $user->isCompartirUbicacion(),
            'activo' => $user->isActivo(),
            'radio_visibilidad_km' => $user->getRadioVisibilidadKm(),
            'puedo_chatear' => true
        ];
    }
}
