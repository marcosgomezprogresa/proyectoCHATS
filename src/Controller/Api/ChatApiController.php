<?php

namespace App\Controller\Api;

use App\Entity\Chat;
use App\Entity\UsuarioChat;
use App\Enum\TipoChat;
use App\Enum\EstadoUsuario;
use App\Repository\ChatRepository;
use App\Repository\UsuarioChatRepository;
use App\Repository\UserRepository;
use App\Repository\MensajeRepository;
use App\Service\GeoLocationService;
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
        ChatRepository $chatRepository,
        MensajeRepository $mensajeRepository,
        GeoLocationService $geoService
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
            $chatsData = array_map(function (Chat $chat) use ($mensajeRepository) {
                $data = $this->serializeChat($chat);
                
                // Obtener último mensaje del chat
                $ultimoMensaje = $mensajeRepository->findOneBy(['chat' => $chat], ['fechaHora' => 'DESC']);
                if ($ultimoMensaje) {
                    $data['ultimo_mensaje'] = $ultimoMensaje->getContenido();
                    $data['ultimo_mensaje_time'] = $ultimoMensaje->getFechaHora()?->format('Y-m-d\TH:i:s\Z');
                } else {
                    $data['ultimo_mensaje'] = null;
                    $data['ultimo_mensaje_time'] = null;
                }
                
                $data['mensajes_no_leidos'] = 0;
                
                return $data;
            }, $chats);

            // Obtener usuarios cercanos dentro de 5km
            $todosUsuarios = $userRepository->findAll();
            $usuariosCercanos = [];
            
            foreach ($todosUsuarios as $otroUsuario) {
                if ($otroUsuario->getId() === $user->getId()) {
                    continue;
                }
                
                // Calcular distancia
                $distancia = $geoService->getDistanceBetweenUsers($user, $otroUsuario);
                
                if ($distancia && $distancia <= 5.0 && $otroUsuario->getEstado()->value === 'online') {
                    $usuariosCercanos[] = [
                        'user_token' => 'usr_tok_' . $otroUsuario->getToken(),
                        'nombre' => $otroUsuario->getNombre(),
                        'estado' => $otroUsuario->getEstado()->value,
                        'distancia_km' => round($distancia, 2),
                        'ultima_actividad' => $otroUsuario->getUltimaActividad()?->format('Y-m-d\TH:i:s\Z'),
                        'avatar_url' => $otroUsuario->getAvatarUrl() ?? ''
                    ];
                }
            }

            return $this->json([
                'success' => true,
                'data' => [
                    'usuario_actual' => [
                        'id' => $user->getId(),
                        'nombre' => $user->getNombre(),
                        'estado' => $user->getEstado()->value
                    ],
                    'chats_activos' => $chatsData,
                    'usuarios_cercanos' => $usuariosCercanos,
                    'estadisticas' => [
                        'total_chats' => count($chatsData),
                        'mensajes_no_leidos' => 0,
                        'usuarios_online_cerca' => count($usuariosCercanos),
                        'radio_km' => 5.0
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
        ChatRepository $chatRepository,
        MensajeRepository $mensajeRepository
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

            // Paginación
            $page = max(1, (int)($request->query->get('page') ?? 1));
            $limit = min(50, (int)($request->query->get('limit') ?? 20));
            $offset = ($page - 1) * $limit;

            // Obtener total de mensajes
            $todosMensajes = $mensajeRepository->findBy(['chat' => $chat], ['fechaHora' => 'DESC']);
            $totalMensajes = count($todosMensajes);

            // Obtener mensajes paginados
            $mensajesPaginados = array_slice($todosMensajes, $offset, $limit);
            
            $mensajes = array_map(function ($msg) {
                return [
                    'mensaje_token' => 'msg_' . $msg->getId(),
                    'user_token' => 'usr_tok_' . $msg->getRemitente()->getToken(),
                    'nombre_usuario' => $msg->getRemitente()->getNombre(),
                    'avatar_url' => $msg->getRemitente()->getAvatarUrl() ?? '',
                    'mensaje' => $msg->getContenido(),
                    'fecha_hora' => $msg->getFechaHora()?->format('Y-m-d\TH:i:s\Z'),
                    'tipo' => $msg->getTipo()->value
                ];
            }, $mensajesPaginados);

            return $this->json([
                'success' => true,
                'data' => array_merge($this->serializeChat($chat), [
                    'cantidad_usuarios' => count($chat->getUsuariosChat()),
                    'mensajes' => $mensajes,
                    'paginacion' => [
                        'total_mensajes' => $totalMensajes,
                        'pagina_actual' => $page,
                        'mensajes_por_pagina' => $limit,
                        'tiene_mas' => ($offset + $limit) < $totalMensajes
                    ]
                ])
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
        MensajeRepository $mensajeRepository,
        GeoLocationService $geoService,
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

            // Aceptamos user_token_destino (prefijo usr_tok_) o user_id_destino (id numérico)
            if (!isset($data['user_token_destino']) && !isset($data['user_id_destino'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Campo requerido: user_token_destino o user_id_destino'
                ], 400);
            }

            // Buscar usuario destino por token o por id
            $userDestino = null;
            if (isset($data['user_token_destino'])) {
                $tokenDestino = str_replace('usr_tok_', '', $data['user_token_destino']);
                $userDestino = $userRepository->findOneBy(['token' => $tokenDestino]);
            } else {
                $userDestino = $userRepository->find((int)$data['user_id_destino']);
            }

            if (!$userDestino) {
                return $this->json([
                    'success' => false,
                    'error' => [
                        'code' => 'USER_NOT_FOUND',
                        'message' => 'Usuario destino no encontrado'
                    ]
                ], 404);
            }

            // VALIDAR: Verificar si están dentro del radio de 5km
            if (!$geoService->isWithinRadius($user, $userDestino, 5.0)) {
                $distancia = $geoService->getDistanceBetweenUsers($user, $userDestino) ?? 999;
                return $this->json([
                    'success' => false,
                    'error' => [
                        'code' => 'OUT_OF_RANGE',
                        'message' => 'El usuario está fuera de tu radio de 5km',
                        'details' => [
                            'distancia_km' => round($distancia, 2),
                            'max_distancia_km' => 5.0
                        ]
                    ]
                ], 400);
            }

            // TODO: Verificar bloqueos (USER_BLOCKED)
            
            // Obtener o crear chat privado
            $chat = $chatRepository->findOneBy([
                'tipo' => TipoChat::PRIVADO,
                // Buscar entre los usuariosChat del usuario actual
            ]);

            $created = false;
            if (!$chat) {
                $chat = new Chat();
                $chat->setNombre('Chat Privado - ' . $userDestino->getNombre());
                $chat->setDescripcion('Chat privado');
                $chat->setTipo(TipoChat::PRIVADO);
                $chat->setActivo(true);
                $chat->setFechaCreacion(new \DateTimeImmutable());
                $chat->setRadioKm(0); // No tiene radio, es privado
                
                $em->persist($chat);
                $em->flush();
                $created = true;
            }

            // Obtener historial de mensajes
            $mensajes = $mensajeRepository->findBy(['chat' => $chat], ['fechaHora' => 'ASC']);
            $historial = array_map(function ($msg) {
                return [
                    'mensaje_token' => 'msg_' . $msg->getId(),
                    'user_token' => 'usr_tok_' . ($msg->getUsuario()->getToken() ?? ''),
                    'nombre_usuario' => $msg->getUsuario()->getNombre(),
                    'mensaje' => $msg->getContenido(),
                    'fecha_hora' => $msg->getFechaHora()?->format('Y-m-d\TH:i:s\Z'),
                    'tipo' => $msg->getTipo()->value
                ];
            }, $mensajes);

            return $this->json([
                'success' => true,
                'data' => [
                    'chat_token' => 'chat_priv_' . $chat->getId(),
                    'tipo' => 'privado',
                    'with_user' => [
                        'id' => $userDestino->getId(),
                        'user_token' => 'usr_tok_' . $userDestino->getToken(),
                        'nombre' => $userDestino->getNombre(),
                        'estado' => $userDestino->getEstado()->value,
                        'distancia_km' => round($geoService->getDistanceBetweenUsers($user, $userDestino) ?? 0, 2),
                        'ultima_actividad' => $userDestino->getUltimaActividad()?->format('Y-m-d\TH:i:s\Z')
                    ],
                    'historial' => $historial,
                    'created' => $created,
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
        // Mostrar usuarios que están activos y comparten ubicación y que además estén en estado ONLINE
        $qb = $userRepository->createQueryBuilder('u')
            ->where('u.activo = :activo')
            ->andWhere('u.compartirUbicacion = :compartir')
            ->andWhere('u.latitud IS NOT NULL')
            ->andWhere('u.longitud IS NOT NULL')
            ->andWhere('u.estado = :estado')
            ->setParameter('activo', true)
            ->setParameter('compartir', true)
            ->setParameter('estado', EstadoUsuario::ONLINE->value)
            ->setMaxResults(100);

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
