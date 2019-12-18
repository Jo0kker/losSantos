<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191216172239 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mail (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, objet VARCHAR(255) NOT NULL, msg LONGTEXT NOT NULL, lu TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_5126AC48F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail_users (mail_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_20400786C8776F01 (mail_id), INDEX IDX_2040078667B3B43D (users_id), PRIMARY KEY(mail_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mail ADD CONSTRAINT FK_5126AC48F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE mail_users ADD CONSTRAINT FK_20400786C8776F01 FOREIGN KEY (mail_id) REFERENCES mail (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mail_users ADD CONSTRAINT FK_2040078667B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mail_users DROP FOREIGN KEY FK_20400786C8776F01');
        $this->addSql('DROP TABLE mail');
        $this->addSql('DROP TABLE mail_users');
    }
}
