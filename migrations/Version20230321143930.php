<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321143930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE fields_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE object_props_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE objects_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE fields (id INT NOT NULL, name VARCHAR(255) NOT NULL, data_type VARCHAR(50) NOT NULL, options TEXT NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN fields.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN fields.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE object_props (id INT NOT NULL, object_id_id INT NOT NULL, field_id_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DF5DFE4AFB67CAB5 ON object_props (object_id_id)');
        $this->addSql('CREATE INDEX IDX_DF5DFE4A54E308AC ON object_props (field_id_id)');
        $this->addSql('CREATE TABLE objects (id INT NOT NULL, category_id INT NOT NULL, sector_id INT NOT NULL, oid VARCHAR(50) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN objects.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN objects.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE object_props ADD CONSTRAINT FK_DF5DFE4AFB67CAB5 FOREIGN KEY (object_id_id) REFERENCES objects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE object_props ADD CONSTRAINT FK_DF5DFE4A54E308AC FOREIGN KEY (field_id_id) REFERENCES fields (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE fields_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE object_props_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE objects_id_seq CASCADE');
        $this->addSql('ALTER TABLE object_props DROP CONSTRAINT FK_DF5DFE4AFB67CAB5');
        $this->addSql('ALTER TABLE object_props DROP CONSTRAINT FK_DF5DFE4A54E308AC');
        $this->addSql('DROP TABLE fields');
        $this->addSql('DROP TABLE object_props');
        $this->addSql('DROP TABLE objects');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
