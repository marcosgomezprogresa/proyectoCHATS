<?php

namespace App\Enum;

enum TipoChat: string
{
    case GENERAL = 'general';
    case PRIVADO = 'privado';
    case GRUPAL = 'grupal';
}
