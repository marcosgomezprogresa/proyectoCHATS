<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260129094930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE usuario_chat (id INT AUTO_INCREMENT NOT NULL, fecha_union DATETIME NOT NULL, fecha_salida DATETIME DEFAULT NULL, silenciado TINYINT NOT NULL, fijado TINYINT NOT NULL, notificaciones TINYINT NOT NULL, mensajes_no_leidos INT NOT NULL, es_admin TINYINT NOT NULL, ultimo_acceso DATETIME DEFAULT NULL, esta_activo TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user ADD nombre VARCHAR(100) NOT NULL, ADD avatar_url VARCHAR(255) DEFAULT NULL, ADD estado VARCHAR(20) NOT NULL, ADD latitud DOUBLE PRECISION DEFAULT NULL, ADD longitud DOUBLE PRECISION DEFAULT NULL, ADD ultima_ubicacion DATETIME DEFAULT NULL, ADD fecha_registro DATETIME NOT NULL, ADD ultima_actividad DATETIME NOT NULL, CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE usuario_chat');
        $this->addSql('ALTER TABLE user DROP nombre, DROP avatar_url, DROP estado, DROP latitud, DROP longitud, DROP ultima_ubicacion, DROP fecha_registro, DROP ultima_actividad, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
