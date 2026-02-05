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
        // Find the admin user and grant ROLE_ADMIN role
        $this->addSql("UPDATE user SET roles = JSON_ARRAY('ROLE_ADMIN', 'ROLE_USER') WHERE email = 'admin@chat.com'");
    }

    public function down(Schema $schema): void
    {
        // Revert to ROLE_USER only
        $this->addSql("UPDATE user SET roles = JSON_ARRAY('ROLE_USER') WHERE email = 'admin@chat.com'");
    }
}
