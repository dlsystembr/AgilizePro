-- Script para corrigir a constraint UNIQUE de CPF/CNPJ na tabela pessoas
-- Permite o mesmo CPF/CNPJ em tenants diferentes

-- Remover constraint UNIQUE antiga (se existir)
ALTER TABLE `pessoas` DROP INDEX IF EXISTS `uk_pessoas_cpfcnpj`;
ALTER TABLE `pessoas` DROP INDEX IF EXISTS `PES_CPFCNPJ`;
ALTER TABLE `pessoas` DROP INDEX IF EXISTS `idx_pessoas_cpfcnpj`;

-- Criar nova constraint UNIQUE que inclui ten_id
-- Isso permite o mesmo CPF/CNPJ em tenants diferentes, mas não duplica no mesmo tenant
ALTER TABLE `pessoas` 
ADD UNIQUE INDEX `uk_pessoas_cpfcnpj_tenant` (`ten_id`, `PES_CPFCNPJ`);

