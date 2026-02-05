<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260205185000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ensure admin@chat.com has ROLE_ADMIN';
    }

    public function up(Schema $schema): void
    {
        $adminRoles = json_encode(['ROLE_ADMIN', 'ROLE_USER']);
        $this->addSql("UPDATE user SET roles = ? WHERE email = 'admin@chat.com'", [$adminRoles]);
    }

    public function down(Schema $schema): void
    {
        $userRoles = json_encode(['ROLE_USER']);
        $this->addSql("UPDATE user SET roles = ? WHERE email = 'admin@chat.com'", [$userRoles]);
    }
}
