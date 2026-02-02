<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
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
                'acceso' => 'Público (con API-KEY)',
                'descripcion' => 'Verifica usuario y contraseña. Devuelve el token JWT y datos del perfil.',
                'request' => '{
  "usuario": "ana@email.com",
  "password": "claveSegura123"
}',
                'response' => '{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "user_token": "usr_tok_eyJhbGciOiJIUzI1NiIs...",
    "user_profile": {
      "email": "ana@email.com",
      "nombre": "Ana",
      "estado": "online"
    }
  }
}',
            ],
            [
                'id' => 2,
                'nombre' => 'API/Usuarios - Registrar',
                'metodo' => 'POST',
                'ruta' => 'POST /api/usuarios',
                'acceso' => 'Público',
                'descripcion' => 'Registra un nuevo usuario en el sistema.',
                'request' => '{
  "email": "nuevo@email.com",
  "password": "clave123",
  "nombre": "Juan"
}',
                'response' => '{
  "success": true,
  "message": "Usuario registrado exitosamente",
  "data": {
    "user_token": "usr_tok_abc123def456...",
    "usuario": {
      "email": "nuevo@email.com",
      "nombre": "Juan",
      "estado": "online",
      "fecha_registro": "2024-01-16T14:30:00Z"
    }
  }
}',
            ],
            [
                'id' => 2.1,
                'nombre' => 'API/Usuarios - Mi Perfil',
                'metodo' => 'GET',
                'ruta' => 'GET /api/usuarios/perfil',
                'acceso' => 'Token (usuario autenticado)',
                'descripcion' => 'Obtiene el perfil completo del usuario autenticado.',
                'request' => 'Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "message": "Perfil obtenido exitosamente",
  "data": {
    "usuario_id": 1,
    "email": "juan@email.com",
    "nombre": "Juan",
    "estado": "online",
    "avatar_url": "https://...",
    "fecha_registro": "2024-01-16T14:30:00Z"
  }
}',
            ],
            [
                'id' => 2.2,
                'nombre' => 'API/Usuarios - Ver Perfil Específico',
                'metodo' => 'GET',
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
                'id' => 2.3,
                'nombre' => 'API/Usuarios - Actualizar',
                'metodo' => 'PATCH',
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
                'id' => 2.4,
                'nombre' => 'API/Usuarios - Eliminar',
                'metodo' => 'DELETE',
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
                'acceso' => 'Token',
                'descripcion' => 'Punto de entrada principal tras el login. Devuelve datos resumen: usuario, lista de chats activos y usuarios cercanos en línea.',
                'request' => 'Sin datos en el cuerpo de la solicitud',
                'response' => '{
  "success": true,
  "data": {
    "usuario_actual": {...},
    "chats_activos": [...],
    "usuarios_cercanos": [...],
    "estadisticas": {...}
  }
}',
            ],
            [
                'id' => 4,
                'nombre' => 'API/General',
                'metodo' => 'GET / POST',
                'acceso' => 'Token',
                'descripcion' => 'Gestiona el chat grupal público de la zona (~5 km). GET obtiene el historial, POST envía un mensaje nuevo.',
                'request' => '{
  "mensaje": "¿Alguien quiere charlar?",
  "tipo": "texto"
}',
                'response' => '{
  "success": true,
  "data": {
    "mensaje_token": "msg_003",
    "chat_token": "chat_general_1",
    "nombre_usuario": "Ana",
    "mensaje": "¿Alguien quiere charlar?",
    "fecha_hora": "2024-01-16T10:35:00Z",
    "tipo": "texto",
    "estado": "entregado"
  }
}',
            ],
            [
                'id' => 5,
                'nombre' => 'API/Privado',
                'metodo' => 'POST',
                'acceso' => 'Token',
                'descripcion' => 'Crea o accede a un chat privado con otro usuario. Se le pasa el user_token del destinatario.',
                'request' => '{
  "user_token_destino": "usr_tok_carlos123..."
}',
                'response' => '{
  "success": true,
  "data": {
    "chat_token": "chat_priv_16",
    "tipo": "privado",
    "with_user": {...},
    "historial": [...],
    "created": false,
    "timestamp": "2024-01-16T10:35:00Z"
  }
}',
            ],
            [
                'id' => 6,
                'nombre' => 'API/Mensaje',
                'metodo' => 'POST',
                'acceso' => 'Token',
                'descripcion' => 'Envía un mensaje a un chat (ya sea privado o general). Requiere el chat_token y el texto.',
                'request' => '{
  "chat_token": "chat_priv_15",
  "mensaje": "Perfecto, nos vemos a las 6",
  "tipo": "texto"
}',
                'response' => '{
  "success": true,
  "data": {
    "mensaje_token": "msg_789",
    "chat_token": "chat_priv_15",
    "nombre_usuario": "Ana",
    "mensaje": "Perfecto, nos vemos a las 6",
    "fecha_hora": "2024-01-16T10:35:00Z",
    "tipo": "texto",
    "estado": "entregado"
  }
}',
            ],
            [
                'id' => 7,
                'nombre' => 'API/Privado/Salir',
                'metodo' => 'POST',
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

    #[Route(path: '/home', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }
}
