<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'app_index')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route(path: '/home', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route(path: '/endpoints', name: 'app_endpoints')]
    public function endpoints(): Response
    {
        $endpoints = [
            [
                'id' => 1,
                'nombre' => 'API/Login',
                'metodo' => 'POST',
                'necesita_body' => true,
                'acceso' => 'Público',
                'descripcion' => 'Autentica un usuario y devuelve su token JWT. Usa las credenciales de tu cuenta registrada.',
                'request' => '{
  "email": "miusuario@ejemplo.com",
  "password": "password123"
}',
                'response' => '{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "user_token": "usr_tok_58e61eefbeca42c8d513a2f12bc20dfbf0dccdedb8e95756a875263b1d172f4d",
    "user_profile": {
      "email": "miusuario@ejemplo.com",
      "nombre": "Mi Usuario",
      "estado": "offline"
    }
  }
}',
            ],
            [
                'id' => 2,
                'nombre' => 'API/Usuarios - Registrar',
                'metodo' => 'POST',
                'necesita_body' => true,
                'ruta' => 'POST /api/usuarios',
                'acceso' => 'Público',
                'descripcion' => 'Registra un nuevo usuario en el sistema.',
                'request' => '{
  "nombre": "Tu Nombre",
  "email": "tuemial@ejemplo.com",
  "password": "password123"
}',
                'response' => '{
  "success": true,
  "message": "Usuario registrado exitosamente",
  "data": {
    "user_token": "usr_tok_abc123def456...",
    "usuario": {
      "email": "tuemial@ejemplo.com",
      "nombre": "Tu Nombre",
      "estado": "offline",
      "fecha_registro": "2026-02-05T13:30:57Z"
    }
  }
}',
            ],
            [
                'id' => '2_1',
                'nombre' => 'API/Usuarios - Mi Perfil',
                'metodo' => 'GET',
                'necesita_body' => false,
                'ruta' => 'GET /api/usuarios/perfil',
                'acceso' => 'Token (usuario autenticado)',
                'descripcion' => 'Obtiene el perfil completo del usuario autenticado con estadísticas de chats y mensajes.',
                'request' => 'Headers:
Authorization: Bearer <token>
Content-Type: application/json

Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "message": "Perfil obtenido exitosamente",
  "data": {
    "usuario_id": 37,
    "email": "admin@chat.com",
    "nombre": "Administrador",
    "estado": "online",
    "avatar_url": null,
    "fecha_registro": "2026-02-02T18:52:19Z",
    "estadisticas": {
      "chats_activos": 5,
      "mensajes_totales": 0
    }
  }
}',
            ],
            [
                'id' => '2_2',
                'nombre' => 'API/Usuarios - Ver Perfil Específico',
                'metodo' => 'GET',
                'necesita_body' => false,
                'params' => ['id'],
                'ruta' => 'GET /api/usuarios/{id}',
                'url_completa_ejemplo' => 'GET /api/usuarios/9',
                'parametros_ejemplo' => 'id = 9 (ID del usuario a consultar)',
                'acceso' => 'Token',
                'descripcion' => 'Obtiene el perfil público de un usuario específico. Ingresa el ID del usuario en la URL.',
                'request' => 'Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "message": "Perfil obtenido exitosamente",
  "data": {
    "usuario_id": 9,
    "email": "miusuario@ejemplo.com",
    "nombre": "Mi Usuario",
    "estado": "offline",
    "avatar_url": null,
    "ultima_actividad": null
  }
}',
            ],
            [
                'id' => '2_3',
                'nombre' => 'API/Usuarios - Actualizar',
                'metodo' => 'PATCH',
                'necesita_body' => true,
                'params' => ['id'],
                'ruta' => 'PATCH /api/usuarios/{id}',
                'url_completa_ejemplo' => 'PATCH /api/usuarios/9',
                'parametros_ejemplo' => 'id = 9 (ID del usuario a actualizar)',
                'acceso' => 'Token (solo tu propio usuario)',
                'descripcion' => 'Actualiza los datos de un usuario. Solo el propietario puede actualizar su propio perfil.',
                'request' => '{
  "nombre": "Juaaaaaan Pérez"
}',
                'response' => '{
  "success": true,
  "message": "Perfil actualizado exitosamente",
  "data": {
    "usuario_id": 9,
    "email": "miusuario@ejemplo.com",
    "nombre": "Juaaaaaan Pérez",
    "estado": "offline",
    "avatar_url": null,
    "fecha_registro": "2026-02-05T14:24:17Z"
  }
}',
            ],
            [
                'id' => '2_4',
                'nombre' => 'API/Usuarios - Eliminar',
                'metodo' => 'DELETE',
                'necesita_body' => false,
                'params' => ['id'],
                'ruta' => 'DELETE /api/usuarios/{id}',
                'acceso' => 'Token (solo tu propio usuario)',
                'descripcion' => 'Elimina/desactiva una cuenta de usuario. Solo el propietario puede eliminar su cuenta.',
                'request' => 'Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "message": "Usuario eliminado exitosamente",
  "data": {
    "usuario_id": 1,
    "email": "juan@email.com"
  }
}',
            ],
            [
                'id' => 3,
                'nombre' => 'API/Home',
                'metodo' => 'GET',
                'necesita_body' => false,
                'ruta' => 'GET /api/home',
                'acceso' => 'Token',
                'descripcion' => 'Punto de entrada principal tras el login. Devuelve datos resumen: usuario, lista de chats activos con último mensaje, usuarios cercanos (dentro de 5km) con distancias en km.',
                'request' => 'Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "data": {
    "usuario_actual": {
      "id": 9,
      "nombre": "Juaaaaaan Pérez",
      "estado": "offline"
    },
    "chats_activos": [],
    "usuarios_cercanos": [],
    "estadisticas": {
      "total_chats": 0,
      "mensajes_no_leidos": 0,
      "usuarios_online_cerca": 0,
      "radio_km": 5
    }
  }
}',
            ],
            [
                'id' => 4,
                'nombre' => 'API/General - Obtener Mensajes',
                'metodo' => 'GET',
                'necesita_body' => false,
                'ruta' => 'GET /api/general?page=1&limit=20',
                'acceso' => 'Token',
                'descripcion' => 'Obtiene el historial del chat general con paginación. Nota: El chat general debe ser creado previamente por un administrador.',
                'request' => 'Sin parámetros (opcionalmente: ?page=1&limit=20)',
                'response' => '{
  "success": false,
  "message": "Chat general no disponible"
}',
            ],
            [
                'id' => '4_1',
                'nombre' => 'API/General - Enviar Mensaje',
                'metodo' => 'POST',
                'necesita_body' => true,
                'ruta' => 'POST /api/general',
                'acceso' => 'Token',
                'descripcion' => 'Envía un nuevo mensaje al chat general. Nota: El chat general debe ser creado previamente.',
                'request' => '{
  "contenido": "Hola a todos en el chat general"
}',
                'response' => '{
  "success": false,
  "message": "Chat general no disponible"
}',
            ],
            [
                'id' => 5,
                'nombre' => 'API/Privado',
                'metodo' => 'POST',
                'necesita_body' => true,
                'ruta' => 'POST /api/privado',
                'acceso' => 'Token',
                'descripcion' => 'Crea o accede a un chat privado con otro usuario.',
                'request' => '{
  "user_id_destino": 10
}',
                'response' => '{
  "success": true,
  "data": {
    "chat_token": "chat_1",
    "tipo": "privado",
    "with_user": {
      "id": 10,
      "user_token": "usr_tok_36552b16fecba5cbef9dbbeae6cca0778dd932134e7cd89aaee37c6bc861cdd4",
      "nombre": "ususario2",
      "estado": "offline",
      "distancia_km": 0,
      "ultima_actividad": "2026-02-05T16:59:45Z"
    },
    "historial": [],
    "created": true,
    "timestamp": "2026-02-05T17:09:49Z"
  }
}',
            ],
            [
                'id' => 6,
                'nombre' => 'API/Mensaje',
                'metodo' => 'POST',
                'necesita_body' => true,
                'ruta' => 'POST /api/mensaje',
                'acceso' => 'Token',
                'descripcion' => 'Envía un mensaje a un chat (ya sea privado o general). Requiere el chat_token y el contenido del mensaje.',
                'request' => '{
  "chat_token": "chat_1",
  "mensaje": "Hola, ¿cómo estás?",
  "tipo": "texto"
}',
                'response' => '{
  "success": true,
  "data": {
    "mensaje_token": "msg_1",
    "chat_token": "chat_1",
    "nombre_usuario": "Juaaaaaan Pérez",
    "mensaje": "Hola, ¿cómo estás?",
    "fecha_hora": "2026-02-05T17:14:29Z",
    "tipo": "texto",
    "estado": "entregado"
  }
}',
            ],
            [
                'id' => 7,
                'nombre' => 'API/Privado/Salir',
                'metodo' => 'POST',
                'necesita_body' => true,
                'ruta' => 'POST /api/privado/salir',
                'acceso' => 'Token',
                'descripcion' => 'Abandona/archiva un chat privado específico. No elimina el historial, pero lo quita de tu lista activa.',
                'request' => '{
  "chat_token": "chat_1"
}',
                'response' => '{
  "success": true,
  "message": "Has abandonado el chat",
  "data": {
    "chat_token": "chat_1",
    "accion": "abandonado",
    "fecha_salida": "2026-02-05T17:21:05Z",
    "puede_volver": true
  }
}',
            ],
            [
                'id' => 8,
                'nombre' => 'API/Privado/CambiarChat',
                'metodo' => 'POST',
                'necesita_body' => true,
                'ruta' => 'POST /api/privado/cambiar-chat',
                'acceso' => 'Token',
                'descripcion' => 'Cambia el chat activo actual en el cliente. Es una señal para el servidor sobre a qué chat enviar notificaciones en tiempo real.',
                'request' => '{
  "nuevo_chat_token": "chat_1",
  "accion": "activar"
}',
                'response' => '{
  "success": true,
  "message": "Chat activo cambiado",
  "data": {
    "chat_token": "chat_1",
    "accion_realizada": "activado",
    "timestamp": "2026-02-05T17:21:05Z"
  }
}',
            ],
            [
                'id' => 9,
                'nombre' => 'API/Invitar',
                'metodo' => 'POST',
                'necesita_body' => true,
                'acceso' => 'Token',
                'descripcion' => 'Invita a otro usuario a unirse a un chat grupal (función futura para grupos temáticos).',
                'request' => '{
  "user_token_invitado": "usr_tok_laura456...",
  "chat_token_grupo": "chat_grupo_7",
  "mensaje_invitacion": "¡Únete a nuestro grupo!"
}',
                'response' => '{
  "success": true,
  "message": "Invitación enviada",
  "data": {
    "invitacion_token": "inv_abc123",
    "chat_token": "chat_grupo_7",
    "estado_invitacion": "pendiente",
    "fecha_envio": "2024-01-16T10:50:00Z",
    "expiracion": "2024-01-23T10:50:00Z"
  }
}',
            ],
            [
                'id' => 10,
                'nombre' => 'API/Perfil',
                'metodo' => 'GET / PATCH',
                'necesita_body' => true,
                'acceso' => 'Token',
                'descripcion' => 'Obtiene o actualiza la información pública del perfil del usuario (nombre, foto, estado).',
                'request' => '{
  "nombre": "AnaNueva",
  "estado": "ausente",
  "preferencias": {
    "notificaciones": true
  }
}',
                'response' => '{
  "success": true,
  "data": {
    "user_token": "usr_tok_carlos123...",
    "nombre": "Carlos",
    "estado": "online",
    "ultima_actividad": "2024-01-16T10:58:00Z",
    "puedo_chatear": true
  }
}',
            ],
            [
                'id' => 11,
                'nombre' => 'API/Actualizar',
                'metodo' => 'GET / POST',
                'necesita_body' => true,
                'acceso' => 'Token',
                'descripcion' => 'Endpoint crítico. Consulta si hay novedades (mensajes nuevos, usuarios que se conectaron/desconectaron) desde la última vez. Se usa para polling o como parte de un sistema de notificaciones en tiempo real.',
                'request' => 'Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "data": {
    "nuevos_mensajes": [...],
    "usuarios_estado": [...]
  }
}',
            ],
            [
                'id' => 12,
                'nombre' => 'API/Logout',
                'metodo' => 'POST',
                'necesita_body' => true,
                'acceso' => 'Token',
                'descripcion' => 'Invalida el token del usuario, cerrando la sesión en el servidor.',
                'request' => '{
  "cerrar_todas_sesiones": false
}',
                'response' => '{
  "success": true,
  "message": "Sesión cerrada correctamente",
  "data": {
    "user_token_invalidado": "usr_tok_ana789...",
    "timestamp_cierre": "2024-01-16T11:05:00Z",
    "sesiones_restantes": 2
  }
}',
            ],
            
            // ============ ENDPOINTS DE ADMINISTRACIÓN ============
            
            [
                'id' => 13,
                'nombre' => 'API/Admin/Usuarios - Listar',
                'metodo' => 'GET',
                'necesita_body' => false,
                'ruta' => 'GET /api/admin/usuarios',
                'acceso' => 'Token (solo admin)',
                'descripcion' => 'Lista todos los usuarios del sistema con posibilidad de filtros.',
                'request' => 'Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "message": "Usuarios obtenidos",
  "data": {
    "total": 150,
    "usuarios": [
      {
        "usuario_id": 1,
        "email": "juan@email.com",
        "nombre": "Juan",
        "estado": "online",
        "fecha_registro": "2024-01-16T14:30:00Z"
      }
    ]
  }
}',
            ],
            [
                'id' => 14,
                'nombre' => 'API/Admin/Bloqueos - Ver',
                'metodo' => 'GET',
                'necesita_body' => false,
                'ruta' => 'GET /api/admin/usuarios/{id}/bloqueos',
                'acceso' => 'Token (solo admin)',
                'descripcion' => 'Obtiene la lista de usuarios bloqueados por un usuario específico.',
                'request' => 'Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "message": "Bloqueos obtenidos",
  "data": {
    "usuario_id": 1,
    "total_bloqueados": 3,
    "bloqueados": [
      {
        "bloqueado_id": 5,
        "bloqueado_nombre": "Carlos",
        "fecha_bloqueo": "2024-01-15T10:00:00Z"
      }
    ]
  }
}',
            ],
            [
                'id' => 15,
                'nombre' => 'API/Admin/Bloqueos - Crear',
                'metodo' => 'POST',
                'necesita_body' => true,
                'ruta' => 'POST /api/admin/usuarios/{id}/bloquear',
                'acceso' => 'Token',
                'descripcion' => 'Bloquea a otro usuario. El usuario bloqueado no podrá contactar ni ver tu perfil.',
                'request' => '{
  "usuario_bloqueado_id": 5
}',
                'response' => '{
  "success": true,
  "message": "Usuario bloqueado exitosamente",
  "data": {
    "bloqueador_id": 1,
    "bloqueado_id": 5,
    "bloqueado_nombre": "Carlos",
    "fecha_bloqueo": "2024-01-16T10:00:00Z"
  }
}',
            ],
            [
                'id' => 16,
                'nombre' => 'API/Admin/Bloqueos - Eliminar',
                'metodo' => 'DELETE',
                'necesita_body' => false,
                'ruta' => 'DELETE /api/admin/usuarios/{id}/desbloquear/{bloqueado_id}',
                'acceso' => 'Token',
                'descripcion' => 'Desbloquea a un usuario previamente bloqueado.',
                'request' => 'Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "message": "Usuario desbloqueado exitosamente",
  "data": {
    "bloqueador_id": 1,
    "bloqueado_id": 5
  }
}',
            ],
            [
                'id' => 17,
                'nombre' => 'API/Admin/Chats - Listar',
                'metodo' => 'GET',
                'necesita_body' => false,
                'ruta' => 'GET /api/admin/chats',
                'acceso' => 'Token (solo admin)',
                'descripcion' => 'Lista todos los chats del sistema.',
                'request' => 'Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "message": "Chats obtenidos",
  "data": {
    "total": 45,
    "chats": [
      {
        "chat_id": 1,
        "nombre": "Chat General",
        "tipo": "general",
        "total_miembros": 28,
        "activo": true
      }
    ]
  }
}',
            ],
            [
                'id' => 18,
                'nombre' => 'API/Admin/Chats - Miembros',
                'metodo' => 'GET',
                'necesita_body' => false,
                'ruta' => 'GET /api/admin/chats/{id}/miembros',
                'acceso' => 'Token (solo admin)',
                'descripcion' => 'Obtiene la lista de miembros de un chat específico.',
                'request' => 'Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "message": "Miembros obtenidos",
  "data": {
    "chat_id": 1,
    "total_miembros": 28,
    "miembros": [
      {
        "usuario_id": 1,
        "usuario_nombre": "Juan",
        "es_admin": true,
        "fecha_union": "2024-01-16T14:30:00Z"
      }
    ]
  }
}',
            ],
            [
                'id' => 19,
                'nombre' => 'API/Admin/Chats - Expulsar Miembro',
                'metodo' => 'DELETE',
                'necesita_body' => false,
                'ruta' => 'DELETE /api/admin/chats/{id}/miembros/{usuario_id}',
                'acceso' => 'Token (admin del chat)',
                'descripcion' => 'Expulsa a un usuario de un chat específico.',
                'request' => 'Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "message": "Usuario expulsado del chat",
  "data": {
    "chat_id": 1,
    "usuario_id": 5,
    "usuario_nombre": "Carlos"
  }
}',
            ],
            [
                'id' => 20,
                'nombre' => 'API/Admin/Chats - Eliminar',
                'metodo' => 'DELETE',
                'necesita_body' => false,
                'ruta' => 'DELETE /api/admin/chats/{id}',
                'acceso' => 'Token (solo admin)',
                'descripcion' => 'Elimina un chat (inactivación). Solo los administradores del sistema pueden hacerlo.',
                'request' => 'Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "message": "Chat eliminado exitosamente",
  "data": {
    "chat_id": 1,
    "chat_nombre": "Chat General"
  }
}',
            ],
        ];

        return $this->render('endpoints.html.twig', [
            'endpoints' => $endpoints,
        ]);
    }
}
