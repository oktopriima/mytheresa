<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250919112152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price_rule_conditions (id INT AUTO_INCREMENT NOT NULL, price_rules_id INT DEFAULT NULL, price_rule_id INT NOT NULL, condition_key VARCHAR(255) NOT NULL, condition_value VARCHAR(255) NOT NULL, INDEX IDX_DBB126099DFA953 (price_rules_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price_rules (id INT AUTO_INCREMENT NOT NULL, rule_type VARCHAR(255) NOT NULL, value_type VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, amount INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, categories_id INT DEFAULT NULL, sku VARCHAR(6) NOT NULL, name VARCHAR(255) NOT NULL, original_price INT NOT NULL, UNIQUE INDEX UNIQ_D34A04AD12469DE2 (category_id), INDEX IDX_D34A04ADA21214B7 (categories_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE price_rule_conditions ADD CONSTRAINT FK_DBB126099DFA953 FOREIGN KEY (price_rules_id) REFERENCES price_rules (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE price_rule_conditions DROP FOREIGN KEY FK_DBB126099DFA953');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADA21214B7');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE price_rule_conditions');
        $this->addSql('DROP TABLE price_rules');
        $this->addSql('DROP TABLE product');
    }
}
