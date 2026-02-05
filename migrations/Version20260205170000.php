<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260205170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Actualizar geolocalización de usuarios para estar en la misma ubicación (Madrid)';
    }

    public function up(Schema $schema): void
    {
        // Actualizar usuarios 9 y 10 a la misma ubicación (Madrid)
        $this->addSql("UPDATE user SET latitud = 40.4168, longitud = -3.7038 WHERE id IN (9, 10)");
    }

    public function down(Schema $schema): void
    {
        // Revertir a valores originales
        $this->addSql("UPDATE user SET latitud = NULL, longitud = NULL WHERE id IN (9, 10)");
    }
}
