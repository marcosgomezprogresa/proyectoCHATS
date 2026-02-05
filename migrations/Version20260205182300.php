<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260205182300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Grant ROLE_ADMIN to admin@chat.com user';
    }

    public function up(Schema $schema): void
    {
        // Update the roles column for admin@chat.com user
        $json = json_encode(['ROLE_ADMIN', 'ROLE_USER']);
        $this->addSql("UPDATE user SET roles = ? WHERE email = 'admin@chat.com'", [$json]);
    }

    public function down(Schema $schema): void
    {
        // Revert to ROLE_USER only
        $json = json_encode(['ROLE_USER']);
        $this->addSql("UPDATE user SET roles = ? WHERE email = 'admin@chat.com'", [$json]);
    }
}
