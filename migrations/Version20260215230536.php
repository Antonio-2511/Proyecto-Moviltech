<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260215230536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categoria (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, descripcion LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE detalle_pedido (id INT AUTO_INCREMENT NOT NULL, cantidad INT NOT NULL, precio_unitario DOUBLE PRECISION NOT NULL, pedido_id INT NOT NULL, producto_id INT NOT NULL, INDEX IDX_A834F5694854653A (pedido_id), INDEX IDX_A834F5697645698E (producto_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE pedido (id INT AUTO_INCREMENT NOT NULL, fecha DATETIME NOT NULL, estado VARCHAR(20) NOT NULL, total DOUBLE PRECISION NOT NULL, usuario_id INT NOT NULL, INDEX IDX_C4EC16CEDB38439E (usuario_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE producto (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(150) NOT NULL, descripcion LONGTEXT NOT NULL, precio DOUBLE PRECISION NOT NULL, stock INT NOT NULL, color VARCHAR(50) DEFAULT NULL, marca VARCHAR(50) DEFAULT NULL, imagen VARCHAR(255) DEFAULT NULL, categoria_id INT NOT NULL, INDEX IDX_A7BB06153397707A (categoria_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE resena (id INT AUTO_INCREMENT NOT NULL, puntuacion INT NOT NULL, comentario LONGTEXT DEFAULT NULL, fecha DATETIME NOT NULL, usuario_id INT NOT NULL, producto_id INT NOT NULL, INDEX IDX_50A7E40ADB38439E (usuario_id), INDEX IDX_50A7E40A7645698E (producto_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, fecha_registro DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE detalle_pedido ADD CONSTRAINT FK_A834F5694854653A FOREIGN KEY (pedido_id) REFERENCES pedido (id)');
        $this->addSql('ALTER TABLE detalle_pedido ADD CONSTRAINT FK_A834F5697645698E FOREIGN KEY (producto_id) REFERENCES producto (id)');
        $this->addSql('ALTER TABLE pedido ADD CONSTRAINT FK_C4EC16CEDB38439E FOREIGN KEY (usuario_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE producto ADD CONSTRAINT FK_A7BB06153397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('ALTER TABLE resena ADD CONSTRAINT FK_50A7E40ADB38439E FOREIGN KEY (usuario_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE resena ADD CONSTRAINT FK_50A7E40A7645698E FOREIGN KEY (producto_id) REFERENCES producto (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detalle_pedido DROP FOREIGN KEY FK_A834F5694854653A');
        $this->addSql('ALTER TABLE detalle_pedido DROP FOREIGN KEY FK_A834F5697645698E');
        $this->addSql('ALTER TABLE pedido DROP FOREIGN KEY FK_C4EC16CEDB38439E');
        $this->addSql('ALTER TABLE producto DROP FOREIGN KEY FK_A7BB06153397707A');
        $this->addSql('ALTER TABLE resena DROP FOREIGN KEY FK_50A7E40ADB38439E');
        $this->addSql('ALTER TABLE resena DROP FOREIGN KEY FK_50A7E40A7645698E');
        $this->addSql('DROP TABLE categoria');
        $this->addSql('DROP TABLE detalle_pedido');
        $this->addSql('DROP TABLE pedido');
        $this->addSql('DROP TABLE producto');
        $this->addSql('DROP TABLE resena');
        $this->addSql('DROP TABLE user');
    }
}
