<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190102154629 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE orders (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, payment_instruction_id INTEGER DEFAULT NULL, amount NUMERIC(10, 5) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE8789B572 ON orders (payment_instruction_id)');
        $this->addSql('CREATE TABLE financial_transactions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, credit_id INTEGER DEFAULT NULL, payment_id INTEGER DEFAULT NULL, extended_data CLOB DEFAULT NULL --(DC2Type:extended_payment_data)
        , processed_amount NUMERIC(10, 5) NOT NULL, reason_code VARCHAR(100) DEFAULT NULL, reference_number VARCHAR(100) DEFAULT NULL, requested_amount NUMERIC(10, 5) NOT NULL, response_code VARCHAR(100) DEFAULT NULL, state SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, tracking_id VARCHAR(100) DEFAULT NULL, transaction_type SMALLINT NOT NULL)');
        $this->addSql('CREATE INDEX IDX_1353F2D9CE062FF9 ON financial_transactions (credit_id)');
        $this->addSql('CREATE INDEX IDX_1353F2D94C3A3BB ON financial_transactions (payment_id)');
        $this->addSql('CREATE TABLE credits (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, payment_instruction_id INTEGER NOT NULL, payment_id INTEGER DEFAULT NULL, attention_required BOOLEAN NOT NULL, created_at DATETIME NOT NULL, credited_amount NUMERIC(10, 5) NOT NULL, crediting_amount NUMERIC(10, 5) NOT NULL, reversing_amount NUMERIC(10, 5) NOT NULL, state SMALLINT NOT NULL, target_amount NUMERIC(10, 5) NOT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_4117D17E8789B572 ON credits (payment_instruction_id)');
        $this->addSql('CREATE INDEX IDX_4117D17E4C3A3BB ON credits (payment_id)');
        $this->addSql('CREATE TABLE payment_instructions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, amount NUMERIC(10, 5) NOT NULL, approved_amount NUMERIC(10, 5) NOT NULL, approving_amount NUMERIC(10, 5) NOT NULL, created_at DATETIME NOT NULL, credited_amount NUMERIC(10, 5) NOT NULL, crediting_amount NUMERIC(10, 5) NOT NULL, currency VARCHAR(3) NOT NULL, deposited_amount NUMERIC(10, 5) NOT NULL, depositing_amount NUMERIC(10, 5) NOT NULL, extended_data CLOB NOT NULL --(DC2Type:extended_payment_data)
        , payment_system_name VARCHAR(100) NOT NULL, reversing_approved_amount NUMERIC(10, 5) NOT NULL, reversing_credited_amount NUMERIC(10, 5) NOT NULL, reversing_deposited_amount NUMERIC(10, 5) NOT NULL, state SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE TABLE payments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, payment_instruction_id INTEGER NOT NULL, approved_amount NUMERIC(10, 5) NOT NULL, approving_amount NUMERIC(10, 5) NOT NULL, credited_amount NUMERIC(10, 5) NOT NULL, crediting_amount NUMERIC(10, 5) NOT NULL, deposited_amount NUMERIC(10, 5) NOT NULL, depositing_amount NUMERIC(10, 5) NOT NULL, expiration_date DATETIME DEFAULT NULL, reversing_approved_amount NUMERIC(10, 5) NOT NULL, reversing_credited_amount NUMERIC(10, 5) NOT NULL, reversing_deposited_amount NUMERIC(10, 5) NOT NULL, state SMALLINT NOT NULL, target_amount NUMERIC(10, 5) NOT NULL, attention_required BOOLEAN NOT NULL, expired BOOLEAN NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_65D29B328789B572 ON payments (payment_instruction_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE financial_transactions');
        $this->addSql('DROP TABLE credits');
        $this->addSql('DROP TABLE payment_instructions');
        $this->addSql('DROP TABLE payments');
    }
}
