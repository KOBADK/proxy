<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161214101730 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE exchange_booking DROP FOREIGN KEY FK_A7C63B5FBC91F416');
        $this->addSql('ALTER TABLE exchange_booking ADD CONSTRAINT FK_A7C63B5FBC91F416 FOREIGN KEY (resource) REFERENCES exchange_resource (mail) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE exchange_booking DROP FOREIGN KEY FK_A7C63B5FBC91F416');
        $this->addSql('ALTER TABLE exchange_booking ADD CONSTRAINT FK_A7C63B5FBC91F416 FOREIGN KEY (resource) REFERENCES exchange_resource (mail)');
    }
}
