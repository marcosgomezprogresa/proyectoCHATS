<?php

namespace App\Controller\Api;

use App\Entity\Bloqueo;
use App\Entity\User;
use App\Repository\BloqueoRepository;
use App\Repository\ChatRepository;
use App\Repository\UserRepository;
use App\Repository\UsuarioChatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * ============================================================
 * CONTROLADOR API - ADMINISTRACIÓN
 * ============================================================
 * 
 * Gestiona funcionalidades de administración:
 * - Listado de usuarios
 * - Bloqueos de usuarios
 * - Gestión de chats
 * - Eliminación de miembros de chats
 * 
 * Requiere autenticación mediante token en header Authorization.
 */
#[Route('/api/admin', name: 'api_admin_')]
class AdminApiController extends AbstractController
{
    // ============ USUARIOS - LISTAR ============
    
    /**
     * GET /api/admin/usuarios
     * 
     * Lista todos los usuarios del sistema.
     * Solo para administradores.
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/usuarios', name: 'list_usuarios', methods: ['GET'])]
    public function listUsuarios(
        Request $request,
        UserRepository $userRepository
    ): JsonResponse {
        try {
            $user = $this->getAuthenticatedUser($request, $userRepository);
            if (!$user || !$this->isAdmin($user)) {
                return $this->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }

            // Obtener todos los usuarios
            $usuarios = $userRepository->findAll();

            $usuariosData = array_map(function (User $u) {
                return [
                    'usuario_id' => $u->getId(),
                    'email' => $u->getEmail(),
                    'nombre' => $u->getNombre(),
                    'estado' => $u->getEstado()->value,
                    'fecha_registro' => $u->getFechaRegistro()?->format('Y-m-d\TH:i:s\Z')
                ];
            }, $usuarios);

            return $this->json([
                'success' => true,
                'message' => 'Usuarios obtenidos',
                'data' => [
                    'total' => count($usuariosData),
                    'usuarios' => $usuariosData
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ BLOQUEOS - VER ============
    
    /**
     * GET /api/admin/usuarios/{id}/bloqueos
     * 
     * Obtiene la lista de usuarios bloqueados por un usuario específico.
     * 
     * @param int $id ID del usuario
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/usuarios/{id}/bloqueos', name: 'get_bloqueos', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getBloqueos(
        int $id,
        Request $request,
        UserRepository $userRepository
    ): JsonResponse {
        try {
            $user = $this->getAuthenticatedUser($request, $userRepository);
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'No autenticado'
                ], 401);
            }

            // Obtener usuario del que queremos ver bloqueos
            $usuario = $userRepository->find($id);
            if (!$usuario) {
                return $this->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Obtener bloqueos de este usuario
            $bloqueos = $usuario->getBloqueos();

            $bloqueadosData = array_map(function (Bloqueo $b) {
                return [
                    'bloqueado_id' => $b->getBloqueado()->getId(),
                    'bloqueado_nombre' => $b->getBloqueado()->getNombre(),
                    'fecha_bloqueo' => $b->getFechaBloqueo()?->format('Y-m-d\TH:i:s\Z')
                ];
            }, $bloqueos->toArray());

            return $this->json([
                'success' => true,
                'message' => 'Bloqueos obtenidos',
                'data' => [
                    'usuario_id' => $id,
                    'total_bloqueados' => count($bloqueadosData),
                    'bloqueados' => $bloqueadosData
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ BLOQUEOS - CREAR ============
    
    /**
     * POST /api/admin/usuarios/{id}/bloquear
     * 
     * Bloquea a otro usuario.
     * El usuario bloqueado no podrá contactar ni ver el perfil del bloqueador.
     * 
     * @param int $id ID del usuario a bloquear
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/usuarios/{id}/bloquear', name: 'block_user', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function blockUser(
        int $id,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            $user = $this->getAuthenticatedUser($request, $userRepository);
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'No autenticado'
                ], 401);
            }

            // Validar que no intente bloquearse a sí mismo
            if ($user->getId() === $id) {
                return $this->json([
                    'success' => false,
                    'message' => 'No puedes bloquearte a ti mismo'
                ], 422);
            }

            // Obtener usuario a bloquear
            $userABloquear = $userRepository->find($id);
            if (!$userABloquear) {
                return $this->json([
                    'success' => false,
                    'message' => 'Usuario a bloquear no encontrado'
                ], 404);
            }

            $data = json_decode($request->getContent(), true);

            // Crear bloqueo
            $bloqueo = new Bloqueo();
            $bloqueo->setBloqueador($user);
            $bloqueo->setBloqueado($userABloquear);
            $bloqueo->setFechaBloqueo(new \DateTime());
            $bloqueo->setMotivo($data['motivo'] ?? 'Sin especificar');
            $bloqueo->setActivo(true);

            $em->persist($bloqueo);
            $em->flush();

            return $this->json([
                'success' => true,
                'message' => 'Usuario bloqueado exitosamente',
                'data' => [
                    'bloqueador_id' => $user->getId(),
                    'bloqueado_id' => $userABloquear->getId(),
                    'bloqueado_nombre' => $userABloquear->getNombre(),
                    'fecha_bloqueo' => $bloqueo->getFechaBloqueo()?->format('Y-m-d\TH:i:s\Z')
                ]
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ BLOQUEOS - ELIMINAR ============
    
    /**
     * DELETE /api/admin/usuarios/{id}/desbloquear/{bloqueado_id}
     * 
     * Desbloquea a un usuario previamente bloqueado.
     * 
     * @param int $id ID del usuario bloqueador
     * @param int $bloqueado_id ID del usuario a desbloquear
     * @param Request $request
     * @param UserRepository $userRepository
     * @param BloqueoRepository $bloqueoRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/usuarios/{id}/desbloquear/{bloqueado_id}', name: 'unblock_user', methods: ['DELETE'], requirements: ['id' => '\d+', 'bloqueado_id' => '\d+'])]
    public function unblockUser(
        int $id,
        int $bloqueado_id,
        Request $request,
        UserRepository $userRepository,
        BloqueoRepository $bloqueoRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            $user = $this->getAuthenticatedUser($request, $userRepository);
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'No autenticado'
                ], 401);
            }

            // Verificar permisos
            if ($user->getId() !== $id) {
                return $this->json([
                    'success' => false,
                    'message' => 'No tienes permisos para desbloquear este usuario'
                ], 403);
            }

            // Obtener el bloqueo
            $bloqueo = $bloqueoRepository->findOneBy([
                'bloqueador' => $user,
                'bloqueado' => $bloqueado_id
            ]);

            if (!$bloqueo) {
                return $this->json([
                    'success' => false,
                    'message' => 'Bloqueo no encontrado'
                ], 404);
            }

            // Eliminar bloqueo
            $em->remove($bloqueo);
            $em->flush();

            return $this->json([
                'success' => true,
                'message' => 'Usuario desbloqueado exitosamente',
                'data' => [
                    'bloqueador_id' => $id,
                    'bloqueado_id' => $bloqueado_id
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ CHATS - LISTAR ============
    
    /**
     * GET /api/admin/chats
     * 
     * Lista todos los chats del sistema.
     * Solo para administradores.
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ChatRepository $chatRepository
     * @return JsonResponse
     */
    #[Route('/chats', name: 'list_chats', methods: ['GET'])]
    public function listChats(
        Request $request,
        UserRepository $userRepository,
        ChatRepository $chatRepository
    ): JsonResponse {
        try {
            $user = $this->getAuthenticatedUser($request, $userRepository);
            if (!$user || !$this->isAdmin($user)) {
                return $this->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }

            $chats = $chatRepository->findAll();

            $chatsData = array_map(function ($chat) {
                return [
                    'chat_id' => $chat->getId(),
                    'nombre' => $chat->getNombre(),
                    'tipo' => $chat->getTipo()->value,
                    'total_miembros' => $chat->getUsuariosChat()->count(),
                    'activo' => $chat->isActivo()
                ];
            }, $chats);

            return $this->json([
                'success' => true,
                'message' => 'Chats obtenidos',
                'data' => [
                    'total' => count($chatsData),
                    'chats' => $chatsData
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ CHATS - VER MIEMBROS ============
    
    /**
     * GET /api/admin/chats/{id}/miembros
     * 
     * Obtiene la lista de miembros de un chat específico.
     * 
     * @param int $id ID del chat
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ChatRepository $chatRepository
     * @return JsonResponse
     */
    #[Route('/chats/{id}/miembros', name: 'get_chat_members', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getChatMembers(
        int $id,
        Request $request,
        UserRepository $userRepository,
        ChatRepository $chatRepository
    ): JsonResponse {
        try {
            $user = $this->getAuthenticatedUser($request, $userRepository);
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'No autenticado'
                ], 401);
            }

            $chat = $chatRepository->find($id);
            if (!$chat) {
                return $this->json([
                    'success' => false,
                    'message' => 'Chat no encontrado'
                ], 404);
            }

            $miembros = $chat->getUsuariosChat();

            $miembrosData = array_map(function ($usuarioChat) {
                return [
                    'usuario_id' => $usuarioChat->getUsuario()->getId(),
                    'usuario_nombre' => $usuarioChat->getUsuario()->getNombre(),
                    'es_admin' => $usuarioChat->isEsAdmin(),
                    'fecha_union' => $usuarioChat->getFechaUnion()?->format('Y-m-d\TH:i:s\Z')
                ];
            }, $miembros->toArray());

            return $this->json([
                'success' => true,
                'message' => 'Miembros obtenidos',
                'data' => [
                    'chat_id' => $id,
                    'total_miembros' => count($miembrosData),
                    'miembros' => $miembrosData
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ CHATS - EXPULSAR MIEMBRO ============
    
    /**
     * DELETE /api/admin/chats/{id}/miembros/{usuario_id}
     * 
     * Expulsa a un usuario de un chat específico.
     * Solo el administrador del chat puede hacerlo.
     * 
     * @param int $id ID del chat
     * @param int $usuario_id ID del usuario a expulsar
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ChatRepository $chatRepository
     * @param UsuarioChatRepository $usuarioChatRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/chats/{id}/miembros/{usuario_id}', name: 'remove_chat_member', methods: ['DELETE'], requirements: ['id' => '\d+', 'usuario_id' => '\d+'])]
    public function removeChatMember(
        int $id,
        int $usuario_id,
        Request $request,
        UserRepository $userRepository,
        ChatRepository $chatRepository,
        UsuarioChatRepository $usuarioChatRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            $user = $this->getAuthenticatedUser($request, $userRepository);
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'No autenticado'
                ], 401);
            }

            $chat = $chatRepository->find($id);
            if (!$chat) {
                return $this->json([
                    'success' => false,
                    'message' => 'Chat no encontrado'
                ], 404);
            }

            // Verificar que el usuario actual es admin del chat
            if (!$this->userIsAdminOfChat($user, $chat)) {
                return $this->json([
                    'success' => false,
                    'message' => 'No tienes permisos para expulsar miembros'
                ], 403);
            }

            // Obtener la relación UsuarioChat a eliminar
            $usuarioChat = $usuarioChatRepository->findOneBy([
                'usuario' => $usuario_id,
                'chat' => $id
            ]);

            if (!$usuarioChat) {
                return $this->json([
                    'success' => false,
                    'message' => 'Usuario no es miembro del chat'
                ], 404);
            }

            // Eliminar
            $em->remove($usuarioChat);
            $em->flush();

            return $this->json([
                'success' => true,
                'message' => 'Usuario expulsado del chat',
                'data' => [
                    'chat_id' => $id,
                    'usuario_id' => $usuario_id,
                    'usuario_nombre' => $usuarioChat->getUsuario()->getNombre()
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ CHATS - ELIMINAR ============
    
    /**
     * DELETE /api/admin/chats/{id}
     * 
     * Elimina un chat del sistema.
     * Solo los administradores pueden hacerlo.
     * 
     * @param int $id ID del chat
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ChatRepository $chatRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/chats/{id}', name: 'delete_chat', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function deleteChat(
        int $id,
        Request $request,
        UserRepository $userRepository,
        ChatRepository $chatRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            $user = $this->getAuthenticatedUser($request, $userRepository);
            if (!$user || !$this->isAdmin($user)) {
                return $this->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }

            $chat = $chatRepository->find($id);
            if (!$chat) {
                return $this->json([
                    'success' => false,
                    'message' => 'Chat no encontrado'
                ], 404);
            }

            // Inactivar chat
            $chat->setActivo(false);
            $em->persist($chat);
            $em->flush();

            return $this->json([
                'success' => true,
                'message' => 'Chat eliminado exitosamente',
                'data' => [
                    'chat_id' => $id,
                    'chat_nombre' => $chat->getNombre()
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ MÉTODOS AUXILIARES ============
    
    /**
     * Obtiene el usuario autenticado desde el token del header
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
     */
    private function extractTokenFromHeader(Request $request): ?string
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }
        $token = substr($authHeader, 7);
        // Remover el prefijo "usr_tok_" si está presente
        if (str_starts_with($token, 'usr_tok_')) {
            $token = substr($token, 8);
        }
        return $token;
    }

    /**
     * Verifica si un usuario es administrador del sistema
     */
    private function isAdmin(User $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    /**
     * Verifica si un usuario es administrador de un chat específico
     */
    private function userIsAdminOfChat(User $user, $chat): bool
    {
        foreach ($chat->getUsuariosChat() as $usuarioChat) {
            if ($usuarioChat->getUsuario()->getId() === $user->getId() && $usuarioChat->isEsAdmin()) {
                return true;
            }
        }
        return false;
    }
}
