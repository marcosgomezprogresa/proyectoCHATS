<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
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
                'nombre' => 'API/Usuarios',
                'metodo' => 'POST / GET / PATCH / DELETE',
                'acceso' => 'Token (para operaciones del propio usuario)',
                'descripcion' => 'Gestiona el perfil de usuario. POST registra, GET obtiene perfil, PATCH actualiza, DELETE elimina.',
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
