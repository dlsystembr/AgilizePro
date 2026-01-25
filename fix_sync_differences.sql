-- Script para corrigir diferenças de estrutura entre MapOS e AgilizePro
-- Execute este script ANTES de executar os scripts de sincronização
-- 
-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!
-- 
-- Uso:
-- mysql -u root -p agilizepro < fix_sync_differences.sql
-- ou execute no phpMyAdmin

-- ============================================
-- 1. ADICIONAR ten_id NAS TABELAS QUE FALTAM
-- ============================================
-- O ten_id é uma coluna de multi-tenancy (tenant ID)
-- Primeiro, verifique se existe a tabela tenants e obtenha o ID padrão
-- Se não existir, crie um tenant padrão primeiro

-- Verificar se existe tenant padrão (ajuste o ID conforme necessário)
SET @default_tenant_id = 1;

-- Se a tabela tenants não existir, crie:
-- CREATE TABLE IF NOT EXISTS `tenants` (
--     `ten_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
--     `ten_nome` VARCHAR(100) NOT NULL,
--     `ten_data_cadastro` DATETIME NOT NULL,
--     PRIMARY KEY (`ten_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- 
-- INSERT INTO `tenants` (`ten_nome`, `ten_data_cadastro`) VALUES ('Matriz', NOW());
-- SET @default_tenant_id = LAST_INSERT_ID();

-- Adicionar ten_id nas tabelas que não têm
-- ⚠️ NOTA: Se a coluna já existir, o comando falhará. Execute o script PHP primeiro para verificar.

-- Aliquotas
-- ALTER TABLE `aliquotas` ADD COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT @default_tenant_id;
-- ALTER TABLE `aliquotas` ADD INDEX `idx_aliquotas_ten_id` (`ten_id`);

-- Anexos
-- ALTER TABLE `anexos` ADD COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT @default_tenant_id;
-- ALTER TABLE `anexos` ADD INDEX `idx_anexos_ten_id` (`ten_id`);

-- Anotacoes OS
-- ALTER TABLE `anotacoes_os` ADD COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT @default_tenant_id;
-- ALTER TABLE `anotacoes_os` ADD INDEX `idx_anotacoes_os_ten_id` (`ten_id`);

-- Bairros
-- ALTER TABLE `bairros` ADD COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT @default_tenant_id;
-- ALTER TABLE `bairros` ADD INDEX `idx_bairros_ten_id` (`ten_id`);

-- Categorias
-- ALTER TABLE `categorias` ADD COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT @default_tenant_id;
-- ALTER TABLE `categorias` ADD INDEX `idx_categorias_ten_id` (`ten_id`);

-- Certificados Digitais
-- ALTER TABLE `certificados_digitais` ADD COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT @default_tenant_id;
-- ALTER TABLE `certificados_digitais` ADD INDEX `idx_certificados_digitais_ten_id` (`ten_id`);

-- ============================================
-- 2. CORRIGIR CLASSIFICAÇÃO FISCAL
-- ============================================
-- No MapOS, as colunas corretas são:
-- - CLF_NATUREZA_CONTRIBUINTE (não CLF_NATUREZA_CONTRIB)
-- - CLF_TIPO_TRIBUTACAO ou CLF_TIPO_ICMS (dependendo da versão)

-- ⚠️ NOTA: Execute o script PHP primeiro para verificar quais colunas existem!

-- Adicionar CLF_NATUREZA_CONTRIBUINTE (descomente se não existir)
-- ALTER TABLE `classificacao_fiscal` 
-- ADD COLUMN `CLF_NATUREZA_CONTRIBUINTE` ENUM('Contribuinte','Não Contribuinte') NOT NULL DEFAULT 'Não Contribuinte';

-- Migrar dados de CLF_NATUREZA_CONTRIB para CLF_NATUREZA_CONTRIBUINTE (se existir)
-- UPDATE `classificacao_fiscal` 
-- SET `CLF_NATUREZA_CONTRIBUINTE` = CASE 
--     WHEN `CLF_NATUREZA_CONTRIB` = 'inscrito' OR `CLF_NATUREZA_CONTRIB` = 'Contribuinte' THEN 'Contribuinte'
--     ELSE 'Não Contribuinte'
-- END
-- WHERE `CLF_NATUREZA_CONTRIBUINTE` = 'Não Contribuinte' 
--   AND `CLF_NATUREZA_CONTRIB` IS NOT NULL;

-- Ajustar CLF_TIPO_ICMS (descomente se necessário)
-- ALTER TABLE `classificacao_fiscal` 
-- MODIFY COLUMN `CLF_TIPO_ICMS` ENUM('ICMS Normal','Substituição Tributaria','Serviço') NOT NULL DEFAULT 'ICMS Normal';

-- Adicionar ten_id na classificacao_fiscal (descomente se não existir)
-- ALTER TABLE `classificacao_fiscal` ADD COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT @default_tenant_id;
-- ALTER TABLE `classificacao_fiscal` ADD INDEX `idx_classificacao_fiscal_ten_id` (`ten_id`);

-- ============================================
-- 3. REMOVER COLUNAS ANTIGAS (OPCIONAL)
-- ============================================
-- ⚠️ ATENÇÃO: Execute apenas se tiver certeza que os dados foram migrados!
-- 
-- Remover CLF_NATUREZA_CONTRIB se CLF_NATUREZA_CONTRIBUINTE já tiver os dados
-- ALTER TABLE `classificacao_fiscal` DROP COLUMN IF EXISTS `CLF_NATUREZA_CONTRIB`;

-- ============================================
-- 4. VERIFICAR SE FOI CORRIGIDO
-- ============================================
-- Execute estas queries para verificar:

-- Verificar se ten_id foi adicionado:
-- SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE, IS_NULLABLE
-- FROM information_schema.COLUMNS
-- WHERE TABLE_SCHEMA = 'agilizepro'
-- AND TABLE_NAME IN ('aliquotas', 'anexos', 'anotacoes_os', 'bairros', 'categorias', 'certificados_digitais', 'classificacao_fiscal')
-- AND COLUMN_NAME = 'ten_id';

-- Verificar estrutura da classificacao_fiscal:
-- DESCRIBE `classificacao_fiscal`;
