<?php

namespace App\Enum;

enum EstadoMensaje: string
{
    case ENVIADO = 'enviado';
    case ENTREGADO = 'entregado';
    case LEIDO = 'leido';
    case ERROR = 'error';
}
