<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250705231944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_conge ADD id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE demande_conge ADD CONSTRAINT FK_D80610616B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_D80610616B3CA4B ON demande_conge (id_user)');
        $this->addSql('ALTER TABLE solde_conge ADD id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE solde_conge ADD CONSTRAINT FK_EF1BB276B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateur (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EF1BB276B3CA4B ON solde_conge (id_user)');
        $this->addSql('ALTER TABLE utilisateur ADD id_departement INT DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3DC499668 FOREIGN KEY (id_role) REFERENCES role (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3D9649694 FOREIGN KEY (id_departement) REFERENCES departement (id)');
        $this->addSql('CREATE INDEX IDX_1D1C63B3DC499668 ON utilisateur (id_role)');
        $this->addSql('CREATE INDEX IDX_1D1C63B3D9649694 ON utilisateur (id_departement)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_conge DROP FOREIGN KEY FK_D80610616B3CA4B');
        $this->addSql('DROP INDEX IDX_D80610616B3CA4B ON demande_conge');
        $this->addSql('ALTER TABLE demande_conge DROP id_user');
        $this->addSql('ALTER TABLE solde_conge DROP FOREIGN KEY FK_EF1BB276B3CA4B');
        $this->addSql('DROP INDEX UNIQ_EF1BB276B3CA4B ON solde_conge');
        $this->addSql('ALTER TABLE solde_conge DROP id_user');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3DC499668');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3D9649694');
        $this->addSql('DROP INDEX IDX_1D1C63B3DC499668 ON utilisateur');
        $this->addSql('DROP INDEX IDX_1D1C63B3D9649694 ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur DROP id_departement');
    }
}
