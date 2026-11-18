<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261111145157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Symfony 8.1 : DBAL 4 (suppression des commentaires DC2Type), refresh_tokens (Gesdinet 3), index messenger_messages';
    }

    /*
        - Générée avec 'doctrine:migrations:diff' puis relue :
          la création de 'messenger_processed_messages' (entité ProcessedMessage, sans lien avec la mise à jour)
          a été retirée pour garder cette migration centrée sur Symfony 8.1
    */
    public function up(Schema $schema): void
    {
        // DBAL 4 n'utilise plus les commentaires '(DC2Type:datetime_immutable)' pour reconnaître les types
        $this->addSql('ALTER TABLE category CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE delete_at delete_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE comment CHANGE published_at published_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE contact_message CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE image_collection CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE login_history CHANGE login_at login_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE notification CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE post CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE property CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE recipe CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE email_change_requested_at email_change_requested_at DATETIME DEFAULT NULL, CHANGE reset_requested_at reset_requested_at DATETIME DEFAULT NULL');
        // Gesdinet JWTRefreshTokenBundle 3 : "famille" de tokens (rotation / révocation en chaîne)
        $this->addSql('ALTER TABLE refresh_tokens ADD family VARCHAR(32) DEFAULT NULL, ADD family_valid DATETIME DEFAULT NULL');
        // symfony/doctrine-messenger 8 : un index composite remplace les 3 index simples
        $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delete_at delete_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE comment CHANGE published_at published_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE contact_message CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE image_collection CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE login_history CHANGE login_at login_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE message CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('ALTER TABLE notification CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE property CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE recipe CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE refresh_tokens DROP family, DROP family_valid');
        $this->addSql('ALTER TABLE user CHANGE email_change_requested_at email_change_requested_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE reset_requested_at reset_requested_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
