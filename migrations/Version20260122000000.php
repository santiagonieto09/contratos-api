<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migración inicial: Crea las tablas usuarios, contratos y cuotas.
 */
final class Version20260122000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crea las tablas usuarios, contratos y cuotas para el sistema de tramitación de contratos';
    }

    public function up(Schema $schema): void
    {
        // Tabla de usuarios
        $this->addSql('CREATE TABLE usuarios (
            id UUID NOT NULL,
            email VARCHAR(180) NOT NULL,
            password VARCHAR(255) NOT NULL,
            roles JSON NOT NULL,
            creado_en TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EF687F2E7927C74 ON usuarios (email)');
        $this->addSql('COMMENT ON COLUMN usuarios.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN usuarios.creado_en IS \'(DC2Type:datetime_immutable)\'');

        // Tabla de contratos
        $this->addSql('CREATE TABLE contratos (
            id UUID NOT NULL,
            usuario_id UUID DEFAULT NULL,
            numero_contrato VARCHAR(50) NOT NULL,
            fecha_contrato DATE NOT NULL,
            valor_total NUMERIC(15, 2) NOT NULL,
            metodo_pago VARCHAR(20) NOT NULL,
            numero_meses INT NOT NULL,
            creado_en TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A39C05E9B5B7EDE ON contratos (numero_contrato)');
        $this->addSql('CREATE INDEX IDX_A39C05E9DB38439E ON contratos (usuario_id)');
        $this->addSql('COMMENT ON COLUMN contratos.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN contratos.usuario_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN contratos.fecha_contrato IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN contratos.creado_en IS \'(DC2Type:datetime_immutable)\'');

        // Tabla de cuotas
        $this->addSql('CREATE TABLE cuotas (
            id UUID NOT NULL,
            contrato_id UUID DEFAULT NULL,
            numero_cuota INT NOT NULL,
            valor_base NUMERIC(15, 2) NOT NULL,
            interes NUMERIC(15, 2) NOT NULL,
            tarifa_pago NUMERIC(15, 2) NOT NULL,
            total NUMERIC(15, 2) NOT NULL,
            fecha_pago DATE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_59B2A0EE94EA14EA ON cuotas (contrato_id)');
        $this->addSql('COMMENT ON COLUMN cuotas.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cuotas.contrato_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cuotas.fecha_pago IS \'(DC2Type:date_immutable)\'');

        // Foreign keys
        $this->addSql('ALTER TABLE contratos ADD CONSTRAINT FK_A39C05E9DB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cuotas ADD CONSTRAINT FK_59B2A0EE94EA14EA FOREIGN KEY (contrato_id) REFERENCES contratos (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cuotas DROP CONSTRAINT FK_59B2A0EE94EA14EA');
        $this->addSql('ALTER TABLE contratos DROP CONSTRAINT FK_A39C05E9DB38439E');
        $this->addSql('DROP TABLE cuotas');
        $this->addSql('DROP TABLE contratos');
        $this->addSql('DROP TABLE usuarios');
    }
}
