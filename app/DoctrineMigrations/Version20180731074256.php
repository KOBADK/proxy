<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180731074256 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE jms_cron_jobs (id INT UNSIGNED AUTO_INCREMENT NOT NULL, command VARCHAR(200) NOT NULL, lastRunAt DATETIME NOT NULL, UNIQUE INDEX UNIQ_55F5ED428ECAEAD4 (command), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jms_jobs (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, state VARCHAR(15) NOT NULL, queue VARCHAR(50) NOT NULL, priority SMALLINT NOT NULL, createdAt DATETIME NOT NULL, startedAt DATETIME DEFAULT NULL, checkedAt DATETIME DEFAULT NULL, workerName VARCHAR(50) DEFAULT NULL, executeAfter DATETIME DEFAULT NULL, closedAt DATETIME DEFAULT NULL, command VARCHAR(255) NOT NULL, args LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', output LONGTEXT DEFAULT NULL, errorOutput LONGTEXT DEFAULT NULL, exitCode SMALLINT UNSIGNED DEFAULT NULL, maxRuntime SMALLINT UNSIGNED NOT NULL, maxRetries SMALLINT UNSIGNED NOT NULL, stackTrace LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:jms_job_safe_object)\', runtime SMALLINT UNSIGNED DEFAULT NULL, memoryUsage INT UNSIGNED DEFAULT NULL, memoryUsageReal INT UNSIGNED DEFAULT NULL, originalJob_id BIGINT UNSIGNED DEFAULT NULL, INDEX IDX_704ADB9349C447F1 (originalJob_id), INDEX cmd_search_index (command), INDEX sorting_index (state, priority, id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jms_job_dependencies (source_job_id BIGINT UNSIGNED NOT NULL, dest_job_id BIGINT UNSIGNED NOT NULL, INDEX IDX_8DCFE92CBD1F6B4F (source_job_id), INDEX IDX_8DCFE92C32CF8D4C (dest_job_id), PRIMARY KEY(source_job_id, dest_job_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exchange_booking (id INT AUTO_INCREMENT NOT NULL, resource VARCHAR(255) DEFAULT NULL, exchange_id VARCHAR(255) DEFAULT NULL, ical_uid VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, api_key VARCHAR(255) DEFAULT NULL, start_time INT NOT NULL, end_time INT NOT NULL, subject VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, name LONGTEXT NOT NULL, mail LONGTEXT NOT NULL, status LONGTEXT NOT NULL, client_booking_id LONGTEXT DEFAULT NULL, INDEX IDX_A7C63B5FBC91F416 (resource), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exchange_resource (mail VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, alias VARCHAR(255) NOT NULL, PRIMARY KEY(mail)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE koba_apikey (api_key VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, configuration LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', callback VARCHAR(255) DEFAULT NULL, PRIMARY KEY(api_key)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jms_job_related_entities (job_id BIGINT UNSIGNED NOT NULL, related_class VARCHAR(150) NOT NULL, related_id VARCHAR(100) NOT NULL, INDEX IDX_E956F4E2BE04EA9 (job_id), PRIMARY KEY(job_id, related_class, related_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jms_job_statistics (job_id BIGINT UNSIGNED NOT NULL, characteristic VARCHAR(30) NOT NULL, createdAt DATETIME NOT NULL, charValue DOUBLE PRECISION NOT NULL, PRIMARY KEY(job_id, characteristic, createdAt)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE jms_jobs ADD CONSTRAINT FK_704ADB9349C447F1 FOREIGN KEY (originalJob_id) REFERENCES jms_jobs (id)');
        $this->addSql('ALTER TABLE jms_job_dependencies ADD CONSTRAINT FK_8DCFE92CBD1F6B4F FOREIGN KEY (source_job_id) REFERENCES jms_jobs (id)');
        $this->addSql('ALTER TABLE jms_job_dependencies ADD CONSTRAINT FK_8DCFE92C32CF8D4C FOREIGN KEY (dest_job_id) REFERENCES jms_jobs (id)');
        $this->addSql('ALTER TABLE exchange_booking ADD CONSTRAINT FK_A7C63B5FBC91F416 FOREIGN KEY (resource) REFERENCES exchange_resource (mail)');
        $this->addSql('ALTER TABLE jms_job_related_entities ADD CONSTRAINT FK_E956F4E2BE04EA9 FOREIGN KEY (job_id) REFERENCES jms_jobs (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE jms_jobs DROP FOREIGN KEY FK_704ADB9349C447F1');
        $this->addSql('ALTER TABLE jms_job_dependencies DROP FOREIGN KEY FK_8DCFE92CBD1F6B4F');
        $this->addSql('ALTER TABLE jms_job_dependencies DROP FOREIGN KEY FK_8DCFE92C32CF8D4C');
        $this->addSql('ALTER TABLE jms_job_related_entities DROP FOREIGN KEY FK_E956F4E2BE04EA9');
        $this->addSql('ALTER TABLE exchange_booking DROP FOREIGN KEY FK_A7C63B5FBC91F416');
        $this->addSql('DROP TABLE jms_cron_jobs');
        $this->addSql('DROP TABLE jms_jobs');
        $this->addSql('DROP TABLE jms_job_dependencies');
        $this->addSql('DROP TABLE exchange_booking');
        $this->addSql('DROP TABLE exchange_resource');
        $this->addSql('DROP TABLE koba_apikey');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE jms_job_related_entities');
        $this->addSql('DROP TABLE jms_job_statistics');
    }
}
