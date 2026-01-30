<?php

namespace App\Enum;

enum EstadoInvitacion: string
{
    case PENDIENTE = 'pendiente';
    case ACEPTADA = 'aceptada';
    case RECHAZADA = 'rechazada';
    case EXPIRADA = 'expirada';
}
