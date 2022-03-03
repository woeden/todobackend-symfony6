<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220302210352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_todo (tag_id INT NOT NULL, todo_id INT NOT NULL, INDEX IDX_B4010916BAD26311 (tag_id), INDEX IDX_B4010916EA1EBC33 (todo_id), PRIMARY KEY(tag_id, todo_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag_todo ADD CONSTRAINT FK_B4010916BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_todo ADD CONSTRAINT FK_B4010916EA1EBC33 FOREIGN KEY (todo_id) REFERENCES todo (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag_todo DROP FOREIGN KEY FK_B4010916BAD26311');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_todo');
        $this->addSql('ALTER TABLE todo CHANGE title title VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
