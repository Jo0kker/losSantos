<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191215160749 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE rapp_arrest (id INT AUTO_INCREMENT NOT NULL, date_arrest DATETIME NOT NULL, user_arrest VARCHAR(255) NOT NULL, lieux VARCHAR(255) NOT NULL, other_users VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, author VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, permi_num VARCHAR(255) DEFAULT NULL, birth_day VARCHAR(255) NOT NULL, img VARCHAR(255) NOT NULL, sexe VARCHAR(255) NOT NULL, charge LONGTEXT NOT NULL, amande VARCHAR(255) NOT NULL, peine VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE rapp_arrest');
    }
}
