<?php

namespace App\Service;

use App\Entity\User;

/**
 * Servicio para cálculos geográficos
 * Incluye cálculo de distancia entre dos puntos usando Haversine formula
 */
class GeoLocationService
{
    /**
     * Radio de la Tierra en kilómetros
     */
    private const EARTH_RADIUS_KM = 6371;

    /**
     * Calcula la distancia en km entre dos puntos usando Haversine formula
     * 
     * @param float $lat1 Latitud punto 1
     * @param float $lon1 Longitud punto 1
     * @param float $lat2 Latitud punto 2
     * @param float $lon2 Longitud punto 2
     * @return float Distancia en kilómetros
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) * sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_KM * $c;
    }

    /**
     * Calcula la distancia entre dos usuarios
     * 
     * @param User $user1
     * @param User $user2
     * @return float|null Distancia en km o null si falta coordenadas
     */
    public function getDistanceBetweenUsers(User $user1, User $user2): ?float
    {
        if (!$user1->getLatitud() || !$user1->getLongitud() || 
            !$user2->getLatitud() || !$user2->getLongitud()) {
            return null;
        }

        return $this->calculateDistance(
            $user1->getLatitud(),
            $user1->getLongitud(),
            $user2->getLatitud(),
            $user2->getLongitud()
        );
    }

    /**
     * Verifica si un usuario está dentro del radio de 5km de otro
     * 
     * @param User $user1
     * @param User $user2
     * @param float $radiusKm Radio en km (default 5)
     * @return bool
     */
    public function isWithinRadius(User $user1, User $user2, float $radiusKm = 5.0): bool
    {
        $distance = $this->getDistanceBetweenUsers($user1, $user2);
        
        if ($distance === null) {
            return false;
        }

        return $distance <= $radiusKm;
    }
}
