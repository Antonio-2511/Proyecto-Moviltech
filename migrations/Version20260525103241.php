<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260525103241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE cupon (id INT AUTO_INCREMENT NOT NULL, codigo VARCHAR(50) NOT NULL, porcentaje DOUBLE PRECISION NOT NULL, activo TINYINT NOT NULL, UNIQUE INDEX UNIQ_58CFF94920332D99 (codigo), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE cupon');
    }
}
