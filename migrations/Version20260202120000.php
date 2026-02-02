<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260202120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add activo, compartir_ubicacion and radio_visibilidad_km to user table';
    }

    public function up(Schema $schema): void
    {
        // Add columns to user
        $this->addSql("ALTER TABLE `user` ADD `activo` TINYINT(1) NOT NULL DEFAULT '1'");
        $this->addSql("ALTER TABLE `user` ADD `compartir_ubicacion` TINYINT(1) NOT NULL DEFAULT '0'");
        $this->addSql("ALTER TABLE `user` ADD `radio_visibilidad_km` DOUBLE PRECISION DEFAULT NULL");
    }

    public function down(Schema $schema): void
    {
        // Drop columns
        $this->addSql("ALTER TABLE `user` DROP `radio_visibilidad_km`");
        $this->addSql("ALTER TABLE `user` DROP `compartir_ubicacion`");
        $this->addSql("ALTER TABLE `user` DROP `activo`");
    }
}
