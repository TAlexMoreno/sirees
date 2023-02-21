<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221005432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE modulo (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, titulo VARCHAR(255) NOT NULL, descripcion LONGTEXT NOT NULL, creditos INT NOT NULL, INDEX IDX_ECF1CF36727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE programa (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, titulo VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2F0140DB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE modulo ADD CONSTRAINT FK_ECF1CF36727ACA70 FOREIGN KEY (parent_id) REFERENCES modulo (id)');
        $this->addSql('ALTER TABLE programa ADD CONSTRAINT FK_2F0140DB03A8386 FOREIGN KEY (created_by_id) REFERENCES usuario (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE modulo DROP FOREIGN KEY FK_ECF1CF36727ACA70');
        $this->addSql('ALTER TABLE programa DROP FOREIGN KEY FK_2F0140DB03A8386');
        $this->addSql('DROP TABLE modulo');
        $this->addSql('DROP TABLE programa');
    }
}
