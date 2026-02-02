<?php

namespace App\Controller\Api;

use App\Entity\Chat;
use App\Entity\UsuarioChat;
use App\Enum\TipoChat;
use App\Enum\EstadoUsuario;
use App\Repository\ChatRepository;
use App\Repository\UsuarioChatRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * ============================================================
 * CONTROLADOR API - CHATS
 * ============================================================
 * 
 * Gestiona los chats (crear, listar, obtener, actualizar, eliminar).
 * Requiere autenticación mediante token en header Authorization.
 */
#[Route('/api', name: 'api_')]
class ChatApiController extends AbstractController
{
    // ============ HOME - INFORMACIÓN DE INICIO ============
    
    /**
     * GET /api/home
     * 
     * Endpoint principal tras el login.
     * Devuelve información de resumen del usuario autenticado.
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ChatRepository $chatRepository
     * @return JsonResponse
     */
    #[Route('/home', name: 'home', methods: ['GET'])]
    public function home(
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

            // Obtener chats del usuario
            $chats = $chatRepository->findChatsByUser($user);
            $chatsData = array_map(function (Chat $chat) {
                return $this->serializeChat($chat);
            }, $chats);

            return $this->json([
                'success' => true,
                'data' => [
                    'usuario_actual' => [
                        'id' => $user->getId(),
                        'nombre' => $user->getNombre(),
                        'estado' => $user->getEstado()->value
                    ],
                    'chats_activos' => $chatsData,
                    'usuarios_cercanos' => $this->getUsuariosCercanos($user->getId(), $userRepository),
                    'estadisticas' => [
                        'total_chats' => count($chatsData),
                        'mensajes_no_leidos' => 0
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ CHAT GENERAL ============
    
    /**
     * GET /api/general
     * 
     * Obtiene el historial del chat general (zona).
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ChatRepository $chatRepository
     * @return JsonResponse
     */
    #[Route('/general', name: 'general_get', methods: ['GET'])]
    public function getGeneral(
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

            // Obtener o crear chat general
            $chat = $chatRepository->findOneBy(['tipo' => TipoChat::GENERAL]);
            if (!$chat) {
                return $this->json([
                    'success' => false,
                    'message' => 'Chat general no disponible'
                ], 404);
            }

            return $this->json([
                'success' => true,
                'data' => $this->serializeChat($chat)
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/general
     * 
     * Envía un mensaje al chat general.
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ChatRepository $chatRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/general', name: 'general_post', methods: ['POST'])]
    public function postGeneral(
        Request $request,
        UserRepository $userRepository,
        ChatRepository $chatRepository,
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

            $data = json_decode($request->getContent(), true);

            if (!isset($data['mensaje'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Campo requerido: mensaje'
                ], 400);
            }

            // Obtener chat general
            $chat = $chatRepository->findOneBy(['tipo' => TipoChat::GENERAL]);
            if (!$chat) {
                return $this->json([
                    'success' => false,
                    'message' => 'Chat general no disponible'
                ], 404);
            }

            return $this->json([
                'success' => true,
                'data' => [
                    'mensaje_token' => 'msg_' . uniqid(),
                    'chat_token' => 'chat_general_1',
                    'nombre_usuario' => $user->getNombre(),
                    'mensaje' => $data['mensaje'],
                    'fecha_hora' => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
                    'tipo' => $data['tipo'] ?? 'texto',
                    'estado' => 'entregado'
                ]
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ CHAT PRIVADO ============
    
    /**
     * POST /api/privado
     * 
     * Crea o accede a un chat privado con otro usuario.
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ChatRepository $chatRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/privado', name: 'private_chat', methods: ['POST'])]
    public function privateChat(
        Request $request,
        UserRepository $userRepository,
        ChatRepository $chatRepository,
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

            $data = json_decode($request->getContent(), true);

            if (!isset($data['user_token_destino'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Campo requerido: user_token_destino'
                ], 400);
            }

            // Buscar usuario destino
            $tokenDestino = str_replace('usr_tok_', '', $data['user_token_destino']);
            $userDestino = $userRepository->findOneBy(['token' => $tokenDestino]);
            
            if (!$userDestino) {
                return $this->json([
                    'success' => false,
                    'message' => 'Usuario destino no encontrado'
                ], 404);
            }

            return $this->json([
                'success' => true,
                'data' => [
                    'chat_token' => 'chat_priv_' . uniqid(),
                    'tipo' => 'privado',
                    'with_user' => [
                        'id' => $userDestino->getId(),
                        'nombre' => $userDestino->getNombre(),
                        'estado' => $userDestino->getEstado()->value
                    ],
                    'historial' => [],
                    'created' => true,
                    'timestamp' => (new \DateTime())->format('Y-m-d\TH:i:s\Z')
                ]
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ SALIR DE CHAT PRIVADO ============
    
    /**
     * POST /api/privado/salir
     * 
     * Abandona/archiva un chat privado.
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/privado/salir', name: 'private_leave', methods: ['POST'])]
    public function privateLeave(
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

            $data = json_decode($request->getContent(), true);

            if (!isset($data['chat_token'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Campo requerido: chat_token'
                ], 400);
            }

            return $this->json([
                'success' => true,
                'message' => 'Has abandonado el chat',
                'data' => [
                    'chat_token' => $data['chat_token'],
                    'accion' => 'abandonado',
                    'fecha_salida' => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
                    'puede_volver' => true
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ CAMBIAR CHAT ACTIVO ============
    
    /**
     * POST /api/privado/cambiar-chat
     * 
     * Cambia el chat activo en el cliente.
     * Señal para el servidor sobre qué chat debe recibir notificaciones.
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/privado/cambiar-chat', name: 'change_active_chat', methods: ['POST'])]
    public function changeActiveChat(
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

            $data = json_decode($request->getContent(), true);

            if (!isset($data['nuevo_chat_token']) || !isset($data['accion'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Campos requeridos: nuevo_chat_token, accion'
                ], 400);
            }

            return $this->json([
                'success' => true,
                'message' => 'Chat activo cambiado',
                'data' => [
                    'chat_token' => $data['nuevo_chat_token'],
                    'accion_realizada' => $data['accion'],
                    'timestamp' => (new \DateTime())->format('Y-m-d\TH:i:s\Z')
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ ENVIAR MENSAJE ============
    
    /**
     * POST /api/mensaje
     * 
     * Envía un mensaje a un chat.
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/mensaje', name: 'send_message', methods: ['POST'])]
    public function sendMessage(
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

            $data = json_decode($request->getContent(), true);

            if (!isset($data['chat_token']) || !isset($data['mensaje'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Campos requeridos: chat_token, mensaje'
                ], 400);
            }

            return $this->json([
                'success' => true,
                'data' => [
                    'mensaje_token' => 'msg_' . uniqid(),
                    'chat_token' => $data['chat_token'],
                    'nombre_usuario' => $user->getNombre(),
                    'mensaje' => $data['mensaje'],
                    'fecha_hora' => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
                    'tipo' => $data['tipo'] ?? 'texto',
                    'estado' => 'entregado'
                ]
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ INVITAR A USUARIO ============
    
    /**
     * POST /api/invitar
     * 
     * Invita a otro usuario a unirse a un chat grupal.
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/invitar', name: 'invite_user', methods: ['POST'])]
    public function inviteUser(
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

            $data = json_decode($request->getContent(), true);

            if (!isset($data['user_token_invitado']) || !isset($data['chat_token_grupo'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Campos requeridos: user_token_invitado, chat_token_grupo'
                ], 400);
            }

            return $this->json([
                'success' => true,
                'message' => 'Invitación enviada',
                'data' => [
                    'invitacion_token' => 'inv_' . uniqid(),
                    'chat_token' => $data['chat_token_grupo'],
                    'estado_invitacion' => 'pendiente',
                    'fecha_envio' => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
                    'expiracion' => (new \DateTime('+7 days'))->format('Y-m-d\TH:i:s\Z')
                ]
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ ACTUALIZAR/POLLING ============
    
    /**
     * GET /api/actualizar
     * 
     * Endpoint de polling. Consulta novedades desde la última vez.
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/actualizar', name: 'update_check', methods: ['GET', 'POST'])]
    public function updateCheck(
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

            return $this->json([
                'success' => true,
                'data' => [
                    'nuevos_mensajes' => [],
                    'usuarios_estado' => []
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ PERFIL (GET Y PATCH) ============
    
    /**
     * GET /api/perfil
     * 
     * Obtiene la información pública del perfil del usuario.
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/perfil', name: 'get_profile', methods: ['GET'])]
    public function getProfile(
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

            return $this->json([
                'success' => true,
                'data' => [
                    'user_token' => 'usr_tok_' . $user->getToken(),
                    'nombre' => $user->getNombre(),
                    'estado' => $user->getEstado()->value,
                    'ultima_actividad' => $user->getUltimaActividad()?->format('Y-m-d\TH:i:s\Z'),
                    'puedo_chatear' => true
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PATCH /api/perfil
     * 
     * Actualiza la información del perfil del usuario.
     * 
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/perfil', name: 'update_profile', methods: ['PATCH'])]
    public function updateProfile(
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

            $data = json_decode($request->getContent(), true);

            if (isset($data['nombre'])) {
                $user->setNombre($data['nombre']);
            }

            if (isset($data['estado'])) {
                try {
                    $user->setEstado(EstadoUsuario::from($data['estado']));
                } catch (\ValueError $e) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Estado inválido'
                    ], 422);
                }
            }

            $em->persist($user);
            $em->flush();

            return $this->json([
                'success' => true,
                'data' => [
                    'user_token' => 'usr_tok_' . $user->getToken(),
                    'nombre' => $user->getNombre(),
                    'estado' => $user->getEstado()->value,
                    'ultima_actividad' => $user->getUltimaActividad()?->format('Y-m-d\TH:i:s\Z'),
                    'puedo_chatear' => true
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
    private function getAuthenticatedUser(Request $request, UserRepository $userRepository)
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
        return substr($authHeader, 7);
    }

    /**
     * Serializa un chat a array para respuesta JSON
     */
    private function serializeChat(Chat $chat): array
    {
        // Generamos coordenadas simuladas para la demo si no existen en la entidad
        // Base en Madrid (40.4168, -3.7038) y desplazamientos pequeños por id
        $baseLat = 40.4168;
        $baseLng = -3.7038;
        $offset = ($chat->getId() ?? 0) * 0.005; // ~0.005 grados por id

        $lat = $baseLat + $offset;
        $lng = $baseLng + $offset;

        return [
            'chat_token' => 'chat_' . $chat->getId(),
            'nombre' => $chat->getNombre(),
            'tipo' => $chat->getTipo()->value,
            'descripcion' => $chat->getDescripcion(),
            'activo' => $chat->isActivo(),
            'fecha_creacion' => $chat->getFechaCreacion()?->format('Y-m-d\TH:i:s\Z'),
            'lat' => $lat,
            'lng' => $lng
        ];
    }

    /**
     * Obtiene usuarios cercanos para mostrar en el mapa (filtrados por estado, compartirUbicacion, actividad reciente)
     */
    private function getUsuariosCercanos(int $currentUserId, UserRepository $userRepository): array
    {
        $cutoff = (new \DateTime())->modify('-15 minutes');

        $qb = $userRepository->createQueryBuilder('u')
            ->where('u.activo = :activo')
            ->andWhere('u.compartirUbicacion = :compartir')
            ->andWhere('u.latitud IS NOT NULL')
            ->andWhere('u.longitud IS NOT NULL')
            ->andWhere('u.ultimaActividad >= :cutoff')
            ->setParameters([
                'activo' => true,
                'compartir' => true,
                'cutoff' => $cutoff
            ])
            ->setMaxResults(50);

        $users = $qb->getQuery()->getResult();

        $result = [];
        foreach ($users as $u) {
            // Omitir al propio usuario
            if ($u->getId() === $currentUserId) continue;

            $result[] = [
                'usuario_id' => $u->getId(),
                'nombre' => $u->getNombre(),
                'estado' => $u->getEstado()->value,
                'lat' => $u->getLatitud(),
                'lng' => $u->getLongitud(),
                'avatar_url' => $u->getAvatarUrl(),
                'ultima_actividad' => $u->getUltimaActividad()?->format('Y-m-d\TH:i:s\Z')
            ];
        }

        return $result;
    }
}
