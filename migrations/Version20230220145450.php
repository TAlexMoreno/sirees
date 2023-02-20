<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220145450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rutas (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, minimum_role VARCHAR(255) NOT NULL, INDEX IDX_FFC3AEF0727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rutas ADD CONSTRAINT FK_FFC3AEF0727ACA70 FOREIGN KEY (parent_id) REFERENCES rutas (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rutas DROP FOREIGN KEY FK_FFC3AEF0727ACA70');
        $this->addSql('DROP TABLE rutas');
    }
}
