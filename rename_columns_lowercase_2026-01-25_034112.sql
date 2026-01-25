-- Script para padronizar nomes de colunas para minúsculas
-- Gerado em: 2026-01-25 03:41:12
-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!

-- Tabela: bairros
ALTER TABLE `bairros` CHANGE COLUMN `BAI_DATA_INCLUSAO` `bai_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;

-- Tabela: certificados_digitais
ALTER TABLE `certificados_digitais` CHANGE COLUMN `CER_DATA_UPLOAD` `cer_data_upload` datetime NULL DEFAULT 'current_timestamp()' ;

-- Tabela: clientes
ALTER TABLE `clientes` CHANGE COLUMN `CLN_DATA_INCLUSAO` `cln_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;

-- Tabela: clientes_vendedores
ALTER TABLE `clientes_vendedores` CHANGE COLUMN `CLV_DATA_INCLUSAO` `clv_data_inclusao` timestamp NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `clientes_vendedores` CHANGE COLUMN `CLV_DATA_ATUALIZACAO` `clv_data_atualizacao` timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();

-- Tabela: configuracoes_fiscais
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_DATA_CADASTRO` `cfg_data_cadastro` datetime NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_DATA_ATUALIZACAO` `cfg_data_atualizacao` datetime NULL DEFAULT 'current_timestamp()' on update current_timestamp();

-- Tabela: contratos_itens
ALTER TABLE `contratos_itens` CHANGE COLUMN `CTI_DATA_CADASTRO` `cti_data_cadastro` timestamp NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `contratos_itens` CHANGE COLUMN `CTI_DATA_ATUALIZACAO` `cti_data_atualizacao` timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();

-- Tabela: documentos
ALTER TABLE `documentos` CHANGE COLUMN `DOC_DATA_INCLUSAO` `doc_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;

-- Tabela: documentos_faturados
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_LASTUPDATE` `dcf_lastupdate` timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();

-- Tabela: emails
ALTER TABLE `emails` CHANGE COLUMN `EML_DATA_INCLUSAO` `eml_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;

-- Tabela: empresas
ALTER TABLE `empresas` CHANGE COLUMN `EMP_DATA_CADASTRO` `emp_data_cadastro` datetime NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_DATA_ATUALIZACAO` `emp_data_atualizacao` datetime NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();

-- Tabela: enderecos
ALTER TABLE `enderecos` CHANGE COLUMN `END_DATA_INCLUSAO` `end_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;

-- Tabela: estados
ALTER TABLE `estados` CHANGE COLUMN `EST_DATA_INCLUSAO` `est_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;

-- Tabela: itens_faturados
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_LASTUPDATE` `itf_lastupdate` timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();

-- Tabela: municipios
ALTER TABLE `municipios` CHANGE COLUMN `MUN_DATA_INCLUSAO` `mun_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;

-- Tabela: nfecom_capa
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_DATA_CADASTRO` `nfc_data_cadastro` timestamp NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_DATA_ATUALIZACAO` `nfc_data_atualizacao` timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();

-- Tabela: nfecom_itens
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_DATA_CADASTRO` `nfi_data_cadastro` timestamp NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_DATA_ATUALIZACAO` `nfi_data_atualizacao` timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();

-- Tabela: operacao_comercial
ALTER TABLE `operacao_comercial` CHANGE COLUMN `OPC_DATA_INCLUSAO` `opc_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;

-- Tabela: pessoas
ALTER TABLE `pessoas` CHANGE COLUMN `PES_DATA_INCLUSAO` `pes_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;

-- Tabela: telefones
ALTER TABLE `telefones` CHANGE COLUMN `TEL_DATA_INCLUSAO` `tel_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;

-- Tabela: vendedores
ALTER TABLE `vendedores` CHANGE COLUMN `VEN_DATA_CADASTRO` `ven_data_cadastro` timestamp NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `vendedores` CHANGE COLUMN `VEN_DATA_ATUALIZACAO` `ven_data_atualizacao` timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();
