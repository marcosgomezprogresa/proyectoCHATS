<?php

namespace App\Enum;

enum EstadoUsuario: string
{
    case ONLINE = 'online';
    case AUSENTE = 'ausente';
    case OFFLINE = 'offline';
}
