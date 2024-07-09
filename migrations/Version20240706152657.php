<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240706152657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price BIGINT NOT NULL, currency VARCHAR(3) NOT NULL, PRIMARY KEY(id))');

        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, total_price BIGINT NOT NULL, vat_price BIGINT NOT NULL, net_price BIGINT NOT NULL, currency VARCHAR(3) NOT NULL, PRIMARY KEY(id))');

        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_ORDER_ITEM_ORDER_ID (order_id), INDEX IDX_ORDER_ITEM_PRODUCT_ID (product_id), PRIMARY KEY(id))');
        
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_ORDER_ITEM_ORDER_ID FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_ORDER_ITEM_PRODUCT_ID FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE product');
    }
}
