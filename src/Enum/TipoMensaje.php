<?php

namespace App\Enum;

enum TipoMensaje: string
{
    case TEXTO = 'texto';
    case IMAGEN = 'imagen';
    case UBICACION = 'ubicacion';
    case AUDIO = 'audio';
}
