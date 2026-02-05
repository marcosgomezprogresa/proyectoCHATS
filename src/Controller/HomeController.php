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
                'ruta' => 'GET /api/usuarios/{id}',
                'acceso' => 'Token',
                'descripcion' => 'Obtiene el perfil público de un usuario específico.',
                'request' => 'Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "message": "Perfil obtenido exitosamente",
  "data": {
    "usuario_id": 5,
    "email": "carlos@email.com",
    "nombre": "Carlos",
    "estado": "online",
    "avatar_url": "https://...",
    "ultima_actividad": "2024-01-16T10:58:00Z"
  }
}',
            ],
            [
                'id' => '2_3',
                'nombre' => 'API/Usuarios - Actualizar',
                'metodo' => 'PATCH',
                'necesita_body' => true,
                'ruta' => 'PATCH /api/usuarios/{id}',
                'acceso' => 'Token (solo tu propio usuario)',
                'descripcion' => 'Actualiza los datos de un usuario. Solo el propietario puede actualizar su propio perfil.',
                'request' => '{
  "nombre": "Juan Pérez",
  "estado": "ausente",
  "avatar_url": "https://..."
}',
                'response' => '{
  "success": true,
  "message": "Perfil actualizado exitosamente",
  "data": {
    "usuario_id": 1,
    "email": "juan@email.com",
    "nombre": "Juan Pérez",
    "estado": "ausente",
    "avatar_url": "https://..."
  }
}',
            ],
            [
                'id' => '2_4',
                'nombre' => 'API/Usuarios - Eliminar',
                'metodo' => 'DELETE',
                'necesita_body' => false,
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
                'request' => 'Headers:
Authorization: Bearer <token>
Content-Type: application/json

Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "data": {
    "usuario_actual": {
      "usuario_id": 37,
      "email": "admin@chat.com",
      "nombre": "Administrador",
      "estado": "online"
    },
    "chats_activos": [
      {
        "chat_token": "chat_priv_20",
        "tipo": "privado",
        "with_user": {
          "id": 38,
          "nombre": "Moderador",
          "estado": "online"
        },
        "ultimo_mensaje": "Hola",
        "ultimo_mensaje_time": "2026-02-02T19:00:00Z",
        "mensajes_no_leidos": 0
      }
    ],
    "usuarios_cercanos": [
      {
        "id": 38,
        "nombre": "Moderador",
        "estado": "online",
        "distancia_km": 0.87,
        "ultima_actividad": "2026-02-02T19:00:00Z"
      }
    ],
    "estadisticas": {
      "usuarios_online_cerca": 1,
      "radio_km": 5
    }
  }
}',
            ],
            [
                'id' => 4,
                'nombre' => 'API/General',
                'metodo' => 'GET / POST',
                'necesita_body' => true,
                'ruta' => 'GET /api/general?page=1&limit=20 / POST /api/general',
                'acceso' => 'Token',
                'descripcion' => 'Gestiona el chat grupal público. GET obtiene el historial con paginación, POST envía un mensaje nuevo al chat general.',
                'request' => 'GET: 
Método: GET
URL: http://localhost/proyectoChats/public/api/general?page=1&limit=20
Headers:
  Authorization: Bearer <token>

POST:
Método: POST
URL: http://localhost/proyectoChats/public/api/general
Headers:
  Authorization: Bearer <token>
  Content-Type: application/json
Body:
{
  "mensaje": "Hola a todos en el chat general"
}',
                'response' => 'GET Response (200):
{
  "success": true,
  "data": {
    "chat_token": "chat_27",
    "nombre": "General",
    "tipo": "general",
    "descripcion": "Chat general para todos",
    "activo": true,
    "fecha_creacion": "2026-02-02T19:16:16Z",
    "lat": 40.5518,
    "lng": -3.5688,
    "cantidad_usuarios": 4,
    "mensajes": [
      {
        "mensaje_token": "msg_1",
        "user_token": "usr_tok_30e3bbd2add6daf7a66b0b536b01efa99c9d42e3681035e78e66ac09f5dce84d",
        "nombre_usuario": "Administrador",
        "avatar_url": "",
        "mensaje": "Hola a todos en el chat general",
        "fecha_hora": "2026-02-03T13:04:11Z",
        "tipo": "texto"
      }
    ],
    "paginacion": {
      "total_mensajes": 1,
      "pagina_actual": 1,
      "mensajes_por_pagina": 20,
      "tiene_mas": false
    }
  }
}

POST Response (201):
{
  "success": true,
  "data": {
    "mensaje_token": "msg_1",
    "chat_token": "chat_general_1",
    "nombre_usuario": "Administrador",
    "mensaje": "Hola a todos en el chat general",
    "fecha_hora": "2026-02-03T13:04:11Z",
    "tipo": "texto",
    "estado": "entregado"
  }
}',
            ],
            [
                'id' => 5,
                'nombre' => 'API/Privado',
                'metodo' => 'POST',
                'necesita_body' => true,
                'ruta' => 'POST /api/privado',
                'acceso' => 'Token',
                'descripcion' => 'Crea o accede a un chat privado con otro usuario. Se valida que ambos usuarios estén dentro de 5km. Devuelve error si está fuera de rango o el usuario está bloqueado.',
                'request' => 'Headers:
Authorization: Bearer <token>

Body:
{
  "user_id_destino": 38
}',
                'response' => '{
  "success": true,
  "data": {
    "chat_token": "chat_priv_26",
    "tipo": "privado",
    "with_user": {
      "id": 38,
      "user_token": "usr_tok_c2fd62e7153a589ed49ddc79b3f5a6e00084aff7cd59c0961e5d70ef9511d408",
      "nombre": "Moderador",
      "estado": "online",
      "distancia_km": 0.87,
      "ultima_actividad": "2026-02-02T19:00:00Z"
    },
    "historial": [],
    "created": true,
    "timestamp": "2026-02-02T19:05:00Z"
  }
}

Errores posibles:
{
  "success": false,
  "error_code": "OUT_OF_RANGE",
  "message": "El usuario está fuera del rango de 5km"
}

{
  "success": false,
  "error_code": "USER_NOT_FOUND",
  "message": "Usuario no encontrado"
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
                'request' => 'Headers:
Authorization: Bearer <token>

Body:
{
  "chat_token": "chat_priv_26",
  "mensaje": "Hola, ¿cómo estás?",
  "tipo": "texto"
}',
                'response' => '{
  "success": true,
  "data": {
    "mensaje_token": "msg_789",
    "chat_token": "chat_priv_26",
    "nombre_usuario": "Administrador",
    "mensaje": "Hola, ¿cómo estás?",
    "fecha_hora": "2026-02-02T19:05:00Z",
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
                'acceso' => 'Token',
                'descripcion' => 'Abandona/archiva un chat privado específico. No elimina el historial, pero lo quita de tu lista activa.',
                'request' => '{
  "chat_token": "chat_priv_15"
}',
                'response' => '{
  "success": true,
  "message": "Has abandonado el chat",
  "data": {
    "chat_token": "chat_priv_15",
    "accion": "abandonado",
    "fecha_salida": "2024-01-16T10:40:00Z",
    "puede_volver": true
  }
}',
            ],
            [
                'id' => 8,
                'nombre' => 'API/Privado/CambiarChat',
                'metodo' => 'POST',
                'necesita_body' => true,
                'acceso' => 'Token',
                'descripcion' => 'Cambia el chat activo actual en el cliente. Es una señal para el servidor sobre a qué chat enviar notificaciones en tiempo real.',
                'request' => '{
  "nuevo_chat_token": "chat_priv_16",
  "accion": "activar"
}',
                'response' => '{
  "success": true,
  "message": "Chat activo cambiado",
  "data": {
    "chat_token": "chat_priv_16",
    "accion_realizada": "activado",
    "timestamp": "2024-01-16T10:45:00Z"
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
