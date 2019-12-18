<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191216181422 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mail_send (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, objet VARCHAR(255) NOT NULL, msg LONGTEXT NOT NULL, lu TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, dossier VARCHAR(255) NOT NULL, INDEX IDX_A08A9A4F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail_send_users (mail_send_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_A9224A53C92EBF14 (mail_send_id), INDEX IDX_A9224A5367B3B43D (users_id), PRIMARY KEY(mail_send_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mail_send ADD CONSTRAINT FK_A08A9A4F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE mail_send_users ADD CONSTRAINT FK_A9224A53C92EBF14 FOREIGN KEY (mail_send_id) REFERENCES mail_send (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mail_send_users ADD CONSTRAINT FK_A9224A5367B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mail ADD dossier VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mail_send_users DROP FOREIGN KEY FK_A9224A53C92EBF14');
        $this->addSql('DROP TABLE mail_send');
        $this->addSql('DROP TABLE mail_send_users');
        $this->addSql('ALTER TABLE mail DROP dossier');
    }
}
