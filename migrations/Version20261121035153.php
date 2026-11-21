<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261121035153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE audit_log (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(100) NOT NULL, entity_class VARCHAR(150) NOT NULL, entity_id INT NOT NULL, details JSON DEFAULT NULL, created_at DATETIME NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_F6E1C0F5A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, contact_number VARCHAR(50) DEFAULT NULL, address LONGTEXT DEFAULT NULL, credit_limit NUMERIC(10, 2) NOT NULL, is_archived TINYINT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE debt_payment (id INT AUTO_INCREMENT NOT NULL, amount NUMERIC(10, 2) NOT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, customer_id INT NOT NULL, received_by_id INT NOT NULL, INDEX IDX_751D57EF9395C3F3 (customer_id), INDEX IDX_751D57EF6F8DDD17 (received_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, barcode VARCHAR(64) DEFAULT NULL, name VARCHAR(150) NOT NULL, cost_price NUMERIC(10, 2) NOT NULL, selling_price NUMERIC(10, 2) NOT NULL, quantity INT NOT NULL, reorder_level INT NOT NULL, expiration_date DATE DEFAULT NULL, is_archived TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, category_id INT NOT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sale (id INT AUTO_INCREMENT NOT NULL, invoice_number VARCHAR(32) NOT NULL, total NUMERIC(10, 2) NOT NULL, amount_tendered NUMERIC(10, 2) NOT NULL, change_amount NUMERIC(10, 2) NOT NULL, payment_type VARCHAR(10) NOT NULL, status VARCHAR(12) NOT NULL, void_reason LONGTEXT DEFAULT NULL, voided_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, cashier_id INT NOT NULL, customer_id INT DEFAULT NULL, voided_by_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_E54BC0052DA68207 (invoice_number), INDEX IDX_E54BC0052EDB0489 (cashier_id), INDEX IDX_E54BC0059395C3F3 (customer_id), INDEX IDX_E54BC005291CD485 (voided_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sale_item (id INT AUTO_INCREMENT NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, unit_cost NUMERIC(10, 2) NOT NULL, quantity INT NOT NULL, subtotal NUMERIC(10, 2) NOT NULL, sale_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_A35551FB4A7E4868 (sale_id), INDEX IDX_A35551FB4584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE stock_adjustment (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, quantity_change INT NOT NULL, reason LONGTEXT NOT NULL, created_at DATETIME NOT NULL, product_id INT NOT NULL, adjusted_by_id INT NOT NULL, INDEX IDX_27B08FBA4584665A (product_id), INDEX IDX_27B08FBA85EDDAD8 (adjusted_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE stock_in (id INT AUTO_INCREMENT NOT NULL, reference_number VARCHAR(50) NOT NULL, total_cost NUMERIC(10, 2) NOT NULL, received_at DATETIME NOT NULL, notes LONGTEXT DEFAULT NULL, supplier_id INT DEFAULT NULL, received_by_id INT NOT NULL, INDEX IDX_ED18880E2ADD6D8C (supplier_id), INDEX IDX_ED18880E6F8DDD17 (received_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE stock_in_item (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, unit_cost NUMERIC(10, 2) NOT NULL, subtotal NUMERIC(10, 2) NOT NULL, stock_in_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_2C060AD98CE962C2 (stock_in_id), INDEX IDX_2C060AD94584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE supplier (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, contact_person VARCHAR(100) DEFAULT NULL, contact_number VARCHAR(50) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(100) NOT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE audit_log ADD CONSTRAINT FK_F6E1C0F5A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE debt_payment ADD CONSTRAINT FK_751D57EF9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE debt_payment ADD CONSTRAINT FK_751D57EF6F8DDD17 FOREIGN KEY (received_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT FK_E54BC0052EDB0489 FOREIGN KEY (cashier_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT FK_E54BC0059395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT FK_E54BC005291CD485 FOREIGN KEY (voided_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE sale_item ADD CONSTRAINT FK_A35551FB4A7E4868 FOREIGN KEY (sale_id) REFERENCES sale (id)');
        $this->addSql('ALTER TABLE sale_item ADD CONSTRAINT FK_A35551FB4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE stock_adjustment ADD CONSTRAINT FK_27B08FBA4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE stock_adjustment ADD CONSTRAINT FK_27B08FBA85EDDAD8 FOREIGN KEY (adjusted_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE stock_in ADD CONSTRAINT FK_ED18880E2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE stock_in ADD CONSTRAINT FK_ED18880E6F8DDD17 FOREIGN KEY (received_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE stock_in_item ADD CONSTRAINT FK_2C060AD98CE962C2 FOREIGN KEY (stock_in_id) REFERENCES stock_in (id)');
        $this->addSql('ALTER TABLE stock_in_item ADD CONSTRAINT FK_2C060AD94584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE audit_log DROP FOREIGN KEY FK_F6E1C0F5A76ED395');
        $this->addSql('ALTER TABLE debt_payment DROP FOREIGN KEY FK_751D57EF9395C3F3');
        $this->addSql('ALTER TABLE debt_payment DROP FOREIGN KEY FK_751D57EF6F8DDD17');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE sale DROP FOREIGN KEY FK_E54BC0052EDB0489');
        $this->addSql('ALTER TABLE sale DROP FOREIGN KEY FK_E54BC0059395C3F3');
        $this->addSql('ALTER TABLE sale DROP FOREIGN KEY FK_E54BC005291CD485');
        $this->addSql('ALTER TABLE sale_item DROP FOREIGN KEY FK_A35551FB4A7E4868');
        $this->addSql('ALTER TABLE sale_item DROP FOREIGN KEY FK_A35551FB4584665A');
        $this->addSql('ALTER TABLE stock_adjustment DROP FOREIGN KEY FK_27B08FBA4584665A');
        $this->addSql('ALTER TABLE stock_adjustment DROP FOREIGN KEY FK_27B08FBA85EDDAD8');
        $this->addSql('ALTER TABLE stock_in DROP FOREIGN KEY FK_ED18880E2ADD6D8C');
        $this->addSql('ALTER TABLE stock_in DROP FOREIGN KEY FK_ED18880E6F8DDD17');
        $this->addSql('ALTER TABLE stock_in_item DROP FOREIGN KEY FK_2C060AD98CE962C2');
        $this->addSql('ALTER TABLE stock_in_item DROP FOREIGN KEY FK_2C060AD94584665A');
        $this->addSql('DROP TABLE audit_log');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE debt_payment');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE sale');
        $this->addSql('DROP TABLE sale_item');
        $this->addSql('DROP TABLE stock_adjustment');
        $this->addSql('DROP TABLE stock_in');
        $this->addSql('DROP TABLE stock_in_item');
        $this->addSql('DROP TABLE supplier');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
