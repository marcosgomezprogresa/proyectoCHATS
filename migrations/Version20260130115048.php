<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260130115048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE prueba');
        $this->addSql('ALTER TABLE bloqueo ADD bloqueador_id INT NOT NULL');
        $this->addSql('ALTER TABLE bloqueo ADD CONSTRAINT FK_A12F236649EB32B7 FOREIGN KEY (bloqueador_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_A12F236649EB32B7 ON bloqueo (bloqueador_id)');
        $this->addSql('ALTER TABLE invitacion ADD chat_id INT DEFAULT NULL, ADD invitador_id INT DEFAULT NULL, ADD invitado_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invitacion ADD CONSTRAINT FK_3CD30E841A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id)');
        $this->addSql('ALTER TABLE invitacion ADD CONSTRAINT FK_3CD30E8412F1EE36 FOREIGN KEY (invitador_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE invitacion ADD CONSTRAINT FK_3CD30E848E552E60 FOREIGN KEY (invitado_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3CD30E841A9A7125 ON invitacion (chat_id)');
        $this->addSql('CREATE INDEX IDX_3CD30E8412F1EE36 ON invitacion (invitador_id)');
        $this->addSql('CREATE INDEX IDX_3CD30E848E552E60 ON invitacion (invitado_id)');
        $this->addSql('ALTER TABLE mensaje ADD chat_id INT DEFAULT NULL, ADD remitente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mensaje ADD CONSTRAINT FK_9B631D011A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id)');
        $this->addSql('ALTER TABLE mensaje ADD CONSTRAINT FK_9B631D011C3E945F FOREIGN KEY (remitente_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9B631D011A9A7125 ON mensaje (chat_id)');
        $this->addSql('CREATE INDEX IDX_9B631D011C3E945F ON mensaje (remitente_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE avatar_url avatar_url VARCHAR(255) DEFAULT NULL, CHANGE latitud latitud DOUBLE PRECISION DEFAULT NULL, CHANGE longitud longitud DOUBLE PRECISION DEFAULT NULL, CHANGE ultima_ubicacion ultima_ubicacion DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE usuario_chat CHANGE fecha_salida fecha_salida DATETIME DEFAULT NULL, CHANGE ultimo_acceso ultimo_acceso DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prueba (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE bloqueo DROP FOREIGN KEY FK_A12F236649EB32B7');
        $this->addSql('DROP INDEX IDX_A12F236649EB32B7 ON bloqueo');
        $this->addSql('ALTER TABLE bloqueo DROP bloqueador_id');
        $this->addSql('ALTER TABLE invitacion DROP FOREIGN KEY FK_3CD30E841A9A7125');
        $this->addSql('ALTER TABLE invitacion DROP FOREIGN KEY FK_3CD30E8412F1EE36');
        $this->addSql('ALTER TABLE invitacion DROP FOREIGN KEY FK_3CD30E848E552E60');
        $this->addSql('DROP INDEX IDX_3CD30E841A9A7125 ON invitacion');
        $this->addSql('DROP INDEX IDX_3CD30E8412F1EE36 ON invitacion');
        $this->addSql('DROP INDEX IDX_3CD30E848E552E60 ON invitacion');
        $this->addSql('ALTER TABLE invitacion DROP chat_id, DROP invitador_id, DROP invitado_id');
        $this->addSql('ALTER TABLE mensaje DROP FOREIGN KEY FK_9B631D011A9A7125');
        $this->addSql('ALTER TABLE mensaje DROP FOREIGN KEY FK_9B631D011C3E945F');
        $this->addSql('DROP INDEX IDX_9B631D011A9A7125 ON mensaje');
        $this->addSql('DROP INDEX IDX_9B631D011C3E945F ON mensaje');
        $this->addSql('ALTER TABLE mensaje DROP chat_id, DROP remitente_id');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE avatar_url avatar_url VARCHAR(255) DEFAULT \'NULL\', CHANGE latitud latitud DOUBLE PRECISION DEFAULT \'NULL\', CHANGE longitud longitud DOUBLE PRECISION DEFAULT \'NULL\', CHANGE ultima_ubicacion ultima_ubicacion DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE usuario_chat CHANGE fecha_salida fecha_salida DATETIME DEFAULT \'NULL\', CHANGE ultimo_acceso ultimo_acceso DATETIME DEFAULT \'NULL\'');
    }
}
