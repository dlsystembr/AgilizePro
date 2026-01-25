-- Script para padronizar nomes de colunas para minúsculas
-- Gerado em: 2026-01-25 03:40:13
-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!

-- Tabela: anexos
ALTER TABLE `anexos` CHANGE COLUMN `idAnexos` `idanexos` int(11) NOT NULL  auto_increment;

-- Tabela: anotacoes_os
ALTER TABLE `anotacoes_os` CHANGE COLUMN `idAnotacoes` `idanotacoes` int(11) NOT NULL  auto_increment;

-- Tabela: bairros
ALTER TABLE `bairros` CHANGE COLUMN `BAI_ID` `bai_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `bairros` CHANGE COLUMN `BAI_NOME` `bai_nome` varchar(100) NOT NULL  ;
ALTER TABLE `bairros` CHANGE COLUMN `MUN_ID` `mun_id` int(11) NOT NULL  ;
ALTER TABLE `bairros` CHANGE COLUMN `BAI_DATA_INCLUSAO` `bai_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `bairros` CHANGE COLUMN `BAI_DATA_ATUALIZACAO` `bai_data_atualizacao` datetime NULL  on update current_timestamp();

-- Tabela: categorias
ALTER TABLE `categorias` CHANGE COLUMN `idCategorias` `idcategorias` int(11) NOT NULL  auto_increment;

-- Tabela: certificados_digitais
ALTER TABLE `certificados_digitais` CHANGE COLUMN `CER_ID` `cer_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `certificados_digitais` CHANGE COLUMN `EMP_ID` `emp_id` int(11) NOT NULL  ;
ALTER TABLE `certificados_digitais` CHANGE COLUMN `CER_ARQUIVO` `cer_arquivo` longblob NOT NULL  ;
ALTER TABLE `certificados_digitais` CHANGE COLUMN `CER_SENHA` `cer_senha` varchar(255) NOT NULL  ;
ALTER TABLE `certificados_digitais` CHANGE COLUMN `CER_TIPO` `cer_tipo` varchar(5) NULL DEFAULT 'A1' ;
ALTER TABLE `certificados_digitais` CHANGE COLUMN `CER_CNPJ` `cer_cnpj` varchar(14) NULL  ;
ALTER TABLE `certificados_digitais` CHANGE COLUMN `CER_VALIDADE_FIM` `cer_validade_fim` date NOT NULL  ;
ALTER TABLE `certificados_digitais` CHANGE COLUMN `CER_ATIVO` `cer_ativo` tinyint(1) NULL DEFAULT '1' ;
ALTER TABLE `certificados_digitais` CHANGE COLUMN `CER_DATA_UPLOAD` `cer_data_upload` datetime NULL DEFAULT 'current_timestamp()' ;

-- Tabela: classificacao_fiscal
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_ID` `clf_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `OPC_ID` `opc_id` int(11) NOT NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `TPC_ID` `tpc_id` int(11) NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_CST` `clf_cst` varchar(2) NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_CSOSN` `clf_csosn` varchar(3) NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_NATUREZA_CONTRIBUINTE` `clf_natureza_contribuinte` enum('Contribuinte','Não Contribuinte') NOT NULL DEFAULT 'Não Contribuinte' ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_CFOP` `clf_cfop` varchar(4) NOT NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_DESTINACAO` `clf_destinacao` varchar(100) NOT NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_OBJETIVO_COMERCIAL` `clf_objetivo_comercial` enum('Consumo','Revenda','Industrialização','Orgão Público') NOT NULL DEFAULT 'Consumo' ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_FINALIDADE` `clf_finalidade` varchar(30) NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_TIPO_TRIBUTACAO` `clf_tipo_tributacao` enum('ICMS Normal','Substituição Tributaria','Serviço') NOT NULL DEFAULT 'ICMS Normal' ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_MENSAGEM` `clf_mensagem` text NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_CCLASSTRIB` `clf_cclasstrib` varchar(6) NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_CST_IBS` `clf_cst_ibs` varchar(3) NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_ALIQ_IBS` `clf_aliq_ibs` decimal(10,2) NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_CST_CBS` `clf_cst_cbs` varchar(3) NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_ALIQ_CBS` `clf_aliq_cbs` decimal(10,2) NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_DATA_INCLUSAO` `clf_data_inclusao` datetime NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_DATA_ALTERACAO` `clf_data_alteracao` datetime NULL  ;
ALTER TABLE `classificacao_fiscal` CHANGE COLUMN `CLF_SITUACAO` `clf_situacao` tinyint(1) NOT NULL DEFAULT '1' ;

-- Tabela: clientes
ALTER TABLE `clientes` CHANGE COLUMN `CLN_ID` `cln_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `clientes` CHANGE COLUMN `PES_ID` `pes_id` int(11) NOT NULL  ;
ALTER TABLE `clientes` CHANGE COLUMN `TPC_ID` `tpc_id` int(11) NULL  ;
ALTER TABLE `clientes` CHANGE COLUMN `CLN_LIMITE_CREDITO` `cln_limite_credito` decimal(15,2) NULL  ;
ALTER TABLE `clientes` CHANGE COLUMN `CLN_SITUACAO` `cln_situacao` tinyint(1) NOT NULL DEFAULT '1' ;
ALTER TABLE `clientes` CHANGE COLUMN `CLN_DATA_CADASTRO` `cln_data_cadastro` datetime NULL  ;
ALTER TABLE `clientes` CHANGE COLUMN `CLN_LASTUPDATE` `cln_lastupdate` datetime NULL  on update current_timestamp();
ALTER TABLE `clientes` CHANGE COLUMN `CLN_COMPRAR_APRAZO` `cln_comprar_aprazo` tinyint(1) NOT NULL DEFAULT '0' ;
ALTER TABLE `clientes` CHANGE COLUMN `CLN_BLOQUEIO_FINANCEIRO` `cln_bloqueio_financeiro` tinyint(1) NOT NULL DEFAULT '0' ;
ALTER TABLE `clientes` CHANGE COLUMN `CLN_DIAS_CARENCIA` `cln_dias_carencia` int(11) NULL  ;
ALTER TABLE `clientes` CHANGE COLUMN `CLN_EMITIR_NFE` `cln_emitir_nfe` tinyint(1) NOT NULL DEFAULT '0' ;
ALTER TABLE `clientes` CHANGE COLUMN `CLN_DATA_INCLUSAO` `cln_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `clientes` CHANGE COLUMN `CLN_DATA_ALTERACAO` `cln_data_alteracao` datetime NULL  on update current_timestamp();
ALTER TABLE `clientes` CHANGE COLUMN `CLN_OBJETIVO_COMERCIAL` `cln_objetivo_comercial` varchar(255) NULL  ;

-- Tabela: clientes_
ALTER TABLE `clientes_` CHANGE COLUMN `idClientes` `idclientes` int(11) NOT NULL  auto_increment;
ALTER TABLE `clientes_` CHANGE COLUMN `nomeCliente` `nomecliente` varchar(255) NOT NULL  ;
ALTER TABLE `clientes_` CHANGE COLUMN `dataCadastro` `datacadastro` date NULL  ;

-- Tabela: clientes_vendedores
ALTER TABLE `clientes_vendedores` CHANGE COLUMN `CLV_ID` `clv_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `clientes_vendedores` CHANGE COLUMN `CLN_ID` `cln_id` int(11) NOT NULL  ;
ALTER TABLE `clientes_vendedores` CHANGE COLUMN `VEN_ID` `ven_id` int(11) NOT NULL  ;
ALTER TABLE `clientes_vendedores` CHANGE COLUMN `CLV_PADRAO` `clv_padrao` tinyint(1) NULL DEFAULT '0' ;
ALTER TABLE `clientes_vendedores` CHANGE COLUMN `CLV_DATA_INCLUSAO` `clv_data_inclusao` timestamp NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `clientes_vendedores` CHANGE COLUMN `CLV_DATA_ATUALIZACAO` `clv_data_atualizacao` timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();

-- Tabela: cobrancas
ALTER TABLE `cobrancas` CHANGE COLUMN `idCobranca` `idcobranca` int(11) NOT NULL  auto_increment;

-- Tabela: configuracoes
ALTER TABLE `configuracoes` CHANGE COLUMN `idConfig` `idconfig` int(11) NOT NULL  auto_increment;

-- Tabela: configuracoes_fiscais
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_ID` `cfg_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `EMP_ID` `emp_id` int(11) NOT NULL  ;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CER_ID` `cer_id` int(11) NULL  ;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_TIPO_DOCUMENTO` `cfg_tipo_documento` varchar(20) NOT NULL  ;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_AMBIENTE` `cfg_ambiente` tinyint(1) NULL DEFAULT '2' ;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_SERIE` `cfg_serie` varchar(10) NULL DEFAULT '1' ;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_NUMERO_ATUAL` `cfg_numero_atual` int(11) NULL DEFAULT '1' ;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_CSC_ID` `cfg_csc_id` varchar(10) NULL  ;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_CSC_TOKEN` `cfg_csc_token` varchar(255) NULL  ;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_ALIQUOTA_ISS` `cfg_aliquota_iss` decimal(5,2) NULL  ;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_REGIME_ESPECIAL` `cfg_regime_especial` varchar(50) NULL  ;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_FORMATO_IMPRESSAO` `cfg_formato_impressao` varchar(20) NULL DEFAULT 'A4' ;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_ATIVO` `cfg_ativo` tinyint(1) NULL DEFAULT '1' ;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_DATA_CADASTRO` `cfg_data_cadastro` datetime NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `configuracoes_fiscais` CHANGE COLUMN `CFG_DATA_ATUALIZACAO` `cfg_data_atualizacao` datetime NULL DEFAULT 'current_timestamp()' on update current_timestamp();

-- Tabela: configuracoes_nfe
ALTER TABLE `configuracoes_nfe` CHANGE COLUMN `idConfiguracao` `idconfiguracao` int(11) NOT NULL  auto_increment;

-- Tabela: contas
ALTER TABLE `contas` CHANGE COLUMN `idContas` `idcontas` int(11) NOT NULL  auto_increment;

-- Tabela: contratos
ALTER TABLE `contratos` CHANGE COLUMN `CTR_ID` `ctr_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `contratos` CHANGE COLUMN `PES_ID` `pes_id` int(11) NOT NULL  ;
ALTER TABLE `contratos` CHANGE COLUMN `CTR_NUMERO` `ctr_numero` varchar(60) NOT NULL  ;
ALTER TABLE `contratos` CHANGE COLUMN `CTR_DATA_INICIO` `ctr_data_inicio` date NOT NULL  ;
ALTER TABLE `contratos` CHANGE COLUMN `CTR_DATA_FIM` `ctr_data_fim` date NULL  ;
ALTER TABLE `contratos` CHANGE COLUMN `CTR_TIPO_ASSINANTE` `ctr_tipo_assinante` enum('1','2','3','4','5','6','7','8','99') NOT NULL  ;
ALTER TABLE `contratos` CHANGE COLUMN `CTR_ANEXO` `ctr_anexo` varchar(255) NULL  ;
ALTER TABLE `contratos` CHANGE COLUMN `CTR_OBSERVACAO` `ctr_observacao` text NULL  ;
ALTER TABLE `contratos` CHANGE COLUMN `CTR_SITUACAO` `ctr_situacao` tinyint(1) NOT NULL DEFAULT '1' ;
ALTER TABLE `contratos` CHANGE COLUMN `CTR_DATA_CADASTRO` `ctr_data_cadastro` datetime NOT NULL  ;
ALTER TABLE `contratos` CHANGE COLUMN `CTR_DATA_ALTERACAO` `ctr_data_alteracao` datetime NULL  ;

-- Tabela: contratos_itens
ALTER TABLE `contratos_itens` CHANGE COLUMN `CTI_ID` `cti_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `contratos_itens` CHANGE COLUMN `CTR_ID` `ctr_id` int(11) NOT NULL  ;
ALTER TABLE `contratos_itens` CHANGE COLUMN `PRO_ID` `pro_id` int(11) NOT NULL  ;
ALTER TABLE `contratos_itens` CHANGE COLUMN `CTI_PRECO` `cti_preco` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `contratos_itens` CHANGE COLUMN `CTI_QUANTIDADE` `cti_quantidade` decimal(15,4) NOT NULL DEFAULT '1.0000' ;
ALTER TABLE `contratos_itens` CHANGE COLUMN `CTI_ATIVO` `cti_ativo` tinyint(1) NOT NULL DEFAULT '1' ;
ALTER TABLE `contratos_itens` CHANGE COLUMN `CTI_OBSERVACAO` `cti_observacao` mediumtext NULL  ;
ALTER TABLE `contratos_itens` CHANGE COLUMN `CTI_DATA_CADASTRO` `cti_data_cadastro` timestamp NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `contratos_itens` CHANGE COLUMN `CTI_DATA_ATUALIZACAO` `cti_data_atualizacao` timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();

-- Tabela: documentos
ALTER TABLE `documentos` CHANGE COLUMN `DOC_ID` `doc_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `documentos` CHANGE COLUMN `PES_ID` `pes_id` int(11) NOT NULL  ;
ALTER TABLE `documentos` CHANGE COLUMN `DOC_TIPO_DOCUMENTO` `doc_tipo_documento` varchar(60) NOT NULL  ;
ALTER TABLE `documentos` CHANGE COLUMN `END_ID` `end_id` int(11) NULL  ;
ALTER TABLE `documentos` CHANGE COLUMN `DOC_ORGAO_EXPEDIDOR` `doc_orgao_expedidor` varchar(60) NULL  ;
ALTER TABLE `documentos` CHANGE COLUMN `DOC_NUMERO` `doc_numero` varchar(60) NOT NULL  ;
ALTER TABLE `documentos` CHANGE COLUMN `DOC_NATUREZA_CONTRIBUINTE` `doc_natureza_contribuinte` enum('Contribuinte','Não Contribuinte') NULL  ;
ALTER TABLE `documentos` CHANGE COLUMN `DOC_DATA_INCLUSAO` `doc_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `documentos` CHANGE COLUMN `DOC_DATA_ATUALIZACAO` `doc_data_atualizacao` datetime NULL  on update current_timestamp();

-- Tabela: documentos_faturados
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_ID` `dcf_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `ORV_ID` `orv_id` int(11) NOT NULL  ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `PES_ID` `pes_id` int(11) NOT NULL  ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_NUMERO` `dcf_numero` varchar(20) NOT NULL  ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_SERIE` `dcf_serie` varchar(10) NULL  ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_MODELO` `dcf_modelo` varchar(5) NULL  ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_TIPO` `dcf_tipo` char(1) NOT NULL  ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_DATA_EMISSAO` `dcf_data_emissao` date NOT NULL  ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_DATA_SAIDA` `dcf_data_saida` date NULL  ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_VALOR_PRODUTOS` `dcf_valor_produtos` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_VALOR_DESCONTO` `dcf_valor_desconto` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_VALOR_FRETE` `dcf_valor_frete` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_VALOR_SEGURO` `dcf_valor_seguro` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_VALOR_OUTRAS_DESPESAS` `dcf_valor_outras_despesas` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_BASE_ICMS` `dcf_base_icms` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_VALOR_ICMS` `dcf_valor_icms` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_VALOR_ICMS_DESONERADO` `dcf_valor_icms_desonerado` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_BASE_IPI` `dcf_base_ipi` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_VALOR_IPI` `dcf_valor_ipi` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_BASE_PIS` `dcf_base_pis` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_VALOR_PIS` `dcf_valor_pis` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_BASE_COFINS` `dcf_base_cofins` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_VALOR_COFINS` `dcf_valor_cofins` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_BASE_IBS` `dcf_base_ibs` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_VALOR_IBS` `dcf_valor_ibs` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_BASE_CBS` `dcf_base_cbs` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_VALOR_CBS` `dcf_valor_cbs` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_RETENCAO_IRRF` `dcf_retencao_irrf` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_RETENCAO_PIS` `dcf_retencao_pis` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_RETENCAO_COFINS` `dcf_retencao_cofins` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_RETENCAO_CSLL` `dcf_retencao_csll` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_VALOR_TOTAL` `dcf_valor_total` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_STATUS` `dcf_status` varchar(20) NOT NULL DEFAULT 'ABERTO' ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_INFORMACOES_ADICIONAIS` `dcf_informacoes_adicionais` text NULL  ;
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_LASTUPDATE` `dcf_lastupdate` timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();
ALTER TABLE `documentos_faturados` CHANGE COLUMN `DCF_DATA_FATURAMENTO` `dcf_data_faturamento` date NULL  ;

-- Tabela: emails
ALTER TABLE `emails` CHANGE COLUMN `EML_ID` `eml_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `emails` CHANGE COLUMN `PES_ID` `pes_id` int(11) NOT NULL  ;
ALTER TABLE `emails` CHANGE COLUMN `EML_TIPO` `eml_tipo` enum('Geral','Comercial','Financeiro','Nota Fiscal') NOT NULL  ;
ALTER TABLE `emails` CHANGE COLUMN `EML_EMAIL` `eml_email` varchar(150) NOT NULL  ;
ALTER TABLE `emails` CHANGE COLUMN `EML_NOME` `eml_nome` varchar(150) NULL  ;
ALTER TABLE `emails` CHANGE COLUMN `EML_DATA_INCLUSAO` `eml_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `emails` CHANGE COLUMN `EML_DATA_ATUALIZACAO` `eml_data_atualizacao` datetime NULL  on update current_timestamp();

-- Tabela: empresas
ALTER TABLE `empresas` CHANGE COLUMN `EMP_ID` `emp_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_RAZAO_SOCIAL` `emp_razao_social` varchar(255) NOT NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_NOME_FANTASIA` `emp_nome_fantasia` varchar(255) NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_CNPJ` `emp_cnpj` varchar(18) NOT NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_IE` `emp_ie` varchar(20) NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_IM` `emp_im` varchar(20) NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_CNAE` `emp_cnae` varchar(10) NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_CEP` `emp_cep` varchar(10) NOT NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_LOGRADOURO` `emp_logradouro` varchar(255) NOT NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_NUMERO` `emp_numero` varchar(20) NOT NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_COMPLEMENTO` `emp_complemento` varchar(100) NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_BAIRRO` `emp_bairro` varchar(100) NOT NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_CIDADE` `emp_cidade` varchar(100) NOT NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_UF` `emp_uf` char(2) NOT NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_IBGE` `emp_ibge` varchar(10) NOT NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_TELEFONE` `emp_telefone` varchar(20) NOT NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_CELULAR` `emp_celular` varchar(20) NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_EMAIL` `emp_email` varchar(255) NOT NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_SITE` `emp_site` varchar(255) NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_REGIME_TRIBUTARIO` `emp_regime_tributario` enum('Simples Nacional','Lucro Presumido','Lucro Real') NOT NULL DEFAULT 'Simples Nacional' ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_ALIQ_CRED_ICMS` `emp_aliq_cred_icms` decimal(5,2) NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_MENSAGEM_SIMPLES` `emp_mensagem_simples` text NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_LOGO_PATH` `emp_logo_path` varchar(255) NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_MENSAGEM_NOTA` `emp_mensagem_nota` text NULL  ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_COR_PRIMARIA` `emp_cor_primaria` varchar(7) NULL DEFAULT '#1a73e8' ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_COR_SECUNDARIA` `emp_cor_secundaria` varchar(7) NULL DEFAULT '#34a853' ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_ATIVO` `emp_ativo` tinyint(1) NOT NULL DEFAULT '1' ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_DATA_CADASTRO` `emp_data_cadastro` datetime NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `empresas` CHANGE COLUMN `EMP_DATA_ATUALIZACAO` `emp_data_atualizacao` datetime NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();

-- Tabela: enderecos
ALTER TABLE `enderecos` CHANGE COLUMN `END_ID` `end_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `enderecos` CHANGE COLUMN `PES_ID` `pes_id` int(11) NOT NULL  ;
ALTER TABLE `enderecos` CHANGE COLUMN `EST_ID` `est_id` int(11) NOT NULL  ;
ALTER TABLE `enderecos` CHANGE COLUMN `MUN_ID` `mun_id` int(11) NOT NULL  ;
ALTER TABLE `enderecos` CHANGE COLUMN `BAI_ID` `bai_id` int(11) NULL  ;
ALTER TABLE `enderecos` CHANGE COLUMN `END_TIPO_ENDENRECO` `end_tipo_endenreco` enum('Geral','Faturamento','Entrega','Cobranca') NOT NULL DEFAULT 'Geral' ;
ALTER TABLE `enderecos` CHANGE COLUMN `END_TIPO_LOGRADOURO` `end_tipo_logradouro` varchar(30) NULL  ;
ALTER TABLE `enderecos` CHANGE COLUMN `END_LOGRADOURO` `end_logradouro` varchar(150) NOT NULL  ;
ALTER TABLE `enderecos` CHANGE COLUMN `END_NUMERO` `end_numero` varchar(15) NULL  ;
ALTER TABLE `enderecos` CHANGE COLUMN `END_COMPLEMENTO` `end_complemento` varchar(60) NULL  ;
ALTER TABLE `enderecos` CHANGE COLUMN `END_CEP` `end_cep` varchar(10) NULL  ;
ALTER TABLE `enderecos` CHANGE COLUMN `END_ZONA` `end_zona` enum('Rural','Urbana') NULL  ;
ALTER TABLE `enderecos` CHANGE COLUMN `END_OBSERVACAO` `end_observacao` varchar(255) NULL  ;
ALTER TABLE `enderecos` CHANGE COLUMN `END_PADRAO` `end_padrao` tinyint(1) NOT NULL DEFAULT '0' ;
ALTER TABLE `enderecos` CHANGE COLUMN `END_SITUACAO` `end_situacao` tinyint(1) NOT NULL DEFAULT '1' ;
ALTER TABLE `enderecos` CHANGE COLUMN `END_DATA_INCLUSAO` `end_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `enderecos` CHANGE COLUMN `END_DATA_ATUALIZACAO` `end_data_atualizacao` datetime NULL  on update current_timestamp();

-- Tabela: equipamentos
ALTER TABLE `equipamentos` CHANGE COLUMN `idEquipamentos` `idequipamentos` int(11) NOT NULL  auto_increment;

-- Tabela: equipamentos_os
ALTER TABLE `equipamentos_os` CHANGE COLUMN `idEquipamentos_os` `idequipamentos_os` int(11) NOT NULL  auto_increment;

-- Tabela: estados
ALTER TABLE `estados` CHANGE COLUMN `EST_ID` `est_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `estados` CHANGE COLUMN `EST_NOME` `est_nome` varchar(100) NOT NULL  ;
ALTER TABLE `estados` CHANGE COLUMN `EST_UF` `est_uf` varchar(2) NOT NULL  ;
ALTER TABLE `estados` CHANGE COLUMN `EST_CODIGO_UF` `est_codigo_uf` int(2) NOT NULL  ;
ALTER TABLE `estados` CHANGE COLUMN `EST_DATA_INCLUSAO` `est_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `estados` CHANGE COLUMN `EST_DATA_ALTERACAO` `est_data_alteracao` datetime NULL  on update current_timestamp();

-- Tabela: faturamento_entrada_itens
ALTER TABLE `faturamento_entrada_itens` CHANGE COLUMN `FEI_ID` `fei_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `faturamento_entrada_itens` CHANGE COLUMN `PRO_ID` `pro_id` int(11) NULL  ;
ALTER TABLE `faturamento_entrada_itens` CHANGE COLUMN `FEI_QUANTIDADE` `fei_quantidade` decimal(10,2) NULL  ;
ALTER TABLE `faturamento_entrada_itens` CHANGE COLUMN `FEI_VALOR_TOTAL` `fei_valor_total` decimal(10,2) NULL  ;

-- Tabela: fornecedores
ALTER TABLE `fornecedores` CHANGE COLUMN `idFornecedores` `idfornecedores` int(11) NOT NULL  auto_increment;
ALTER TABLE `fornecedores` CHANGE COLUMN `nomeFornecedor` `nomefornecedor` varchar(255) NOT NULL  ;

-- Tabela: garantias
ALTER TABLE `garantias` CHANGE COLUMN `idGarantias` `idgarantias` int(11) NOT NULL  auto_increment;
ALTER TABLE `garantias` CHANGE COLUMN `dataGarantia` `datagarantia` date NULL  ;
ALTER TABLE `garantias` CHANGE COLUMN `refGarantia` `refgarantia` varchar(15) NULL  ;
ALTER TABLE `garantias` CHANGE COLUMN `textoGarantia` `textogarantia` mediumtext NULL  ;

-- Tabela: itens_de_vendas
ALTER TABLE `itens_de_vendas` CHANGE COLUMN `ITV_ID` `itv_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `itens_de_vendas` CHANGE COLUMN `ITV_SUBTOTAL` `itv_subtotal` decimal(10,2) NULL  ;
ALTER TABLE `itens_de_vendas` CHANGE COLUMN `ITV_QUANTIDADE` `itv_quantidade` int(11) NULL  ;
ALTER TABLE `itens_de_vendas` CHANGE COLUMN `ITV_PRECO` `itv_preco` decimal(10,2) NULL  ;
ALTER TABLE `itens_de_vendas` CHANGE COLUMN `PRO_ID` `pro_id` int(11) NULL  ;

-- Tabela: itens_faturados
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_ID` `itf_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_QUANTIDADE` `itf_quantidade` decimal(10,3) NOT NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_VALOR_UNITARIO` `itf_valor_unitario` decimal(15,2) NOT NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_VALOR_TOTAL` `itf_valor_total` decimal(15,2) NOT NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_DESCONTO` `itf_desconto` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_UNIDADE` `itf_unidade` varchar(6) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `DCF_ID` `dcf_id` int(11) NOT NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `PRO_ID` `pro_id` int(11) NOT NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `NCM_ID` `ncm_id` int(11) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `CLF_ID` `clf_id` int(11) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_PRO_DESCRICAO` `itf_pro_descricao` varchar(255) NOT NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_PRO_NCM` `itf_pro_ncm` varchar(8) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_NCM_CEST` `itf_ncm_cest` varchar(7) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_CFOP` `itf_cfop` varchar(4) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_ICMS_CST` `itf_icms_cst` varchar(3) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_CSOSN` `itf_csosn` varchar(4) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_ICMS_ALIQUOTA` `itf_icms_aliquota` decimal(5,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_ICMS_VALOR_BASE` `itf_icms_valor_base` decimal(15,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_ICMS_VALOR` `itf_icms_valor` decimal(15,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_COD_BENEFICIO` `itf_cod_beneficio` varchar(10) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_MOT_DESONERADO` `itf_mot_desonerado` varchar(2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_BASE_DESONERADO_ICMS` `itf_base_desonerado_icms` decimal(15,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_VALOR_DESONERADO_ICMS` `itf_valor_desonerado_icms` decimal(15,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_PIS_CST` `itf_pis_cst` varchar(3) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_PIS_ALIQUOTA` `itf_pis_aliquota` decimal(5,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_PIS_VALOR_BASE` `itf_pis_valor_base` decimal(15,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_PIS_VALOR` `itf_pis_valor` decimal(15,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_COFINS_CST` `itf_cofins_cst` varchar(3) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_COFINS_ALIQUOTA` `itf_cofins_aliquota` decimal(5,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_COFINS_VALOR_BASE` `itf_cofins_valor_base` decimal(15,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_COFINS_VALOR` `itf_cofins_valor` decimal(15,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_IPI_CST` `itf_ipi_cst` varchar(3) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_IPI_ALIQUOTA` `itf_ipi_aliquota` decimal(5,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_IPI_VALOR_BASE` `itf_ipi_valor_base` decimal(15,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_IPI_VALOR` `itf_ipi_valor` decimal(15,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_CCLASS_TRIB` `itf_cclass_trib` varchar(10) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_ALIQ_IBS` `itf_aliq_ibs` decimal(5,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_VALOR_IBS` `itf_valor_ibs` decimal(15,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_ALIQ_CBS` `itf_aliq_cbs` decimal(5,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_VALOR_CBS` `itf_valor_cbs` decimal(15,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_RETENCAO_IRRF` `itf_retencao_irrf` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_RETENCAO_PIS` `itf_retencao_pis` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_RETENCAO_COFINS` `itf_retencao_cofins` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_RETENCAO_CSLL` `itf_retencao_csll` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_IBT` `itf_ibt` char(1) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_IBT_CST` `itf_ibt_cst` varchar(3) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_IBT_ALIQUOTA` `itf_ibt_aliquota` decimal(5,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_IBT_VALOR_BASE` `itf_ibt_valor_base` decimal(15,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_IBT_VALOR` `itf_ibt_valor` decimal(15,2) NULL  ;
ALTER TABLE `itens_faturados` CHANGE COLUMN `ITF_LASTUPDATE` `itf_lastupdate` timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();

-- Tabela: itens_pedido
ALTER TABLE `itens_pedido` CHANGE COLUMN `ITP_ID` `itp_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `itens_pedido` CHANGE COLUMN `PRO_ID` `pro_id` int(11) NULL  ;
ALTER TABLE `itens_pedido` CHANGE COLUMN `ITP_QUANTIDADE` `itp_quantidade` int(11) NULL  ;
ALTER TABLE `itens_pedido` CHANGE COLUMN `ITP_PRECO_UNIT` `itp_preco_unit` decimal(10,2) NULL  ;
ALTER TABLE `itens_pedido` CHANGE COLUMN `ITP_SUBTOTAL` `itp_subtotal` decimal(10,2) NULL  ;

-- Tabela: itens_pedidos
ALTER TABLE `itens_pedidos` CHANGE COLUMN `ITP_ID` `itp_id` int(11) unsigned NOT NULL  auto_increment;
ALTER TABLE `itens_pedidos` CHANGE COLUMN `ITP_SUBTOTAL` `itp_subtotal` decimal(10,2) NULL DEFAULT '0.00' ;
ALTER TABLE `itens_pedidos` CHANGE COLUMN `ITP_QUANTIDADE` `itp_quantidade` int(11) NULL  ;
ALTER TABLE `itens_pedidos` CHANGE COLUMN `ITP_PRECO` `itp_preco` decimal(10,2) NULL DEFAULT '0.00' ;
ALTER TABLE `itens_pedidos` CHANGE COLUMN `PDS_ID` `pds_id` int(11) unsigned NOT NULL  ;
ALTER TABLE `itens_pedidos` CHANGE COLUMN `PRO_ID` `pro_id` int(11) NOT NULL  ;

-- Tabela: lancamentos
ALTER TABLE `lancamentos` CHANGE COLUMN `idLancamentos` `idlancamentos` int(11) NOT NULL  auto_increment;

-- Tabela: logs
ALTER TABLE `logs` CHANGE COLUMN `idLogs` `idlogs` int(11) NOT NULL  auto_increment;

-- Tabela: marcas_equipamentos
ALTER TABLE `marcas_equipamentos` CHANGE COLUMN `idMarcas` `idmarcas` int(11) NOT NULL  auto_increment;

-- Tabela: municipios
ALTER TABLE `municipios` CHANGE COLUMN `MUN_ID` `mun_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `municipios` CHANGE COLUMN `EST_ID` `est_id` int(11) NOT NULL  ;
ALTER TABLE `municipios` CHANGE COLUMN `MUN_NOME` `mun_nome` varchar(100) NOT NULL  ;
ALTER TABLE `municipios` CHANGE COLUMN `MUN_IBGE` `mun_ibge` int(7) NOT NULL  ;
ALTER TABLE `municipios` CHANGE COLUMN `MUN_DATA_INCLUSAO` `mun_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `municipios` CHANGE COLUMN `MUN_DATA_ATUALIZACAO` `mun_data_atualizacao` datetime NULL  on update current_timestamp();

-- Tabela: ncms
ALTER TABLE `ncms` CHANGE COLUMN `NCM_ID` `ncm_id` int(11) unsigned NOT NULL  auto_increment;
ALTER TABLE `ncms` CHANGE COLUMN `NCM_CODIGO` `ncm_codigo` varchar(8) NULL  ;
ALTER TABLE `ncms` CHANGE COLUMN `NCM_DESCRICAO` `ncm_descricao` varchar(255) NULL  ;

-- Tabela: nfecom_capa
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_ID` `nfc_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `CLN_ID` `cln_id` int(11) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_CUF` `nfc_cuf` varchar(2) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_TIPO_AMBIENTE` `nfc_tipo_ambiente` tinyint(1) NOT NULL DEFAULT '2' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_MOD` `nfc_mod` varchar(2) NOT NULL DEFAULT '62' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_SERIE` `nfc_serie` varchar(3) NOT NULL DEFAULT '1' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_NNF` `nfc_nnf` int(9) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_CNF` `nfc_cnf` varchar(8) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_CDV` `nfc_cdv` varchar(1) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_DHEMI` `nfc_dhemi` datetime NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_TP_EMIS` `nfc_tp_emis` tinyint(1) NOT NULL DEFAULT '1' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_N_SITE_AUTORIZ` `nfc_n_site_autoriz` int(3) NOT NULL DEFAULT '0' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_C_MUN_FG` `nfc_c_mun_fg` varchar(7) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_FIN_NFCOM` `nfc_fin_nfcom` tinyint(1) NOT NULL DEFAULT '0' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_TP_FAT` `nfc_tp_fat` tinyint(1) NOT NULL DEFAULT '0' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_VER_PROC` `nfc_ver_proc` varchar(20) NOT NULL DEFAULT '1.0.0' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_CNPJ_EMIT` `nfc_cnpj_emit` varchar(14) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_IE_EMIT` `nfc_ie_emit` varchar(14) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_CRT_EMIT` `nfc_crt_emit` tinyint(1) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_X_NOME_EMIT` `nfc_x_nome_emit` varchar(60) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_X_FANT_EMIT` `nfc_x_fant_emit` varchar(60) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_X_LGR_EMIT` `nfc_x_lgr_emit` varchar(60) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_NRO_EMIT` `nfc_nro_emit` varchar(60) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_X_CPL_EMIT` `nfc_x_cpl_emit` varchar(60) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_X_BAIRRO_EMIT` `nfc_x_bairro_emit` varchar(60) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_C_MUN_EMIT` `nfc_c_mun_emit` varchar(7) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_X_MUN_EMIT` `nfc_x_mun_emit` varchar(60) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_CEP_EMIT` `nfc_cep_emit` varchar(8) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_UF_EMIT` `nfc_uf_emit` varchar(2) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_FONE_EMIT` `nfc_fone_emit` varchar(14) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_X_NOME_DEST` `nfc_x_nome_dest` varchar(60) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_CNPJ_DEST` `nfc_cnpj_dest` varchar(14) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_IND_IE_DEST` `nfc_ind_ie_dest` tinyint(1) NOT NULL DEFAULT '9' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_X_LGR_DEST` `nfc_x_lgr_dest` varchar(60) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_NRO_DEST` `nfc_nro_dest` varchar(60) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_X_BAIRRO_DEST` `nfc_x_bairro_dest` varchar(60) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_C_MUN_DEST` `nfc_c_mun_dest` varchar(7) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_X_MUN_DEST` `nfc_x_mun_dest` varchar(60) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_X_CPL_DEST` `nfc_x_cpl_dest` varchar(200) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_CEP_DEST` `nfc_cep_dest` varchar(8) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_UF_DEST` `nfc_uf_dest` varchar(2) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_I_COD_ASSINANTE` `nfc_i_cod_assinante` varchar(14) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_TP_ASSINANTE` `nfc_tp_assinante` tinyint(1) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_TP_SERV_UTIL` `nfc_tp_serv_util` tinyint(1) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_N_CONTRATO` `nfc_n_contrato` varchar(20) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_D_CONTRATO_INI` `nfc_d_contrato_ini` date NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_D_CONTRATO_FIM` `nfc_d_contrato_fim` date NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_PROD` `nfc_v_prod` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_BC_ICMS` `nfc_v_bc_icms` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_ICMS` `nfc_v_icms` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_ICMS_DESON` `nfc_v_icms_deson` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_FCP` `nfc_v_fcp` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_COFINS` `nfc_v_cofins` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_PIS` `nfc_v_pis` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_FUST` `nfc_v_fust` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_FUNTEL` `nfc_v_funtel` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_RET_PIS` `nfc_v_ret_pis` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_RET_COFINS` `nfc_v_ret_cofins` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_RET_CSLL` `nfc_v_ret_csll` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_IRRF` `nfc_v_irrf` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_RET_TRIB_TOT` `nfc_v_ret_trib_tot` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_DESC` `nfc_v_desc` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_OUTRO` `nfc_v_outro` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_V_NF` `nfc_v_nf` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_COMPET_FAT` `nfc_compet_fat` varchar(6) NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_D_VENC_FAT` `nfc_d_venc_fat` date NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_D_PER_USO_INI` `nfc_d_per_uso_ini` date NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_D_PER_USO_FIM` `nfc_d_per_uso_fim` date NOT NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_COD_BARRAS` `nfc_cod_barras` varchar(50) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_INF_CPL` `nfc_inf_cpl` mediumtext NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_STATUS` `nfc_status` tinyint(1) NOT NULL DEFAULT '0' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_CH_NFCOM` `nfc_ch_nfcom` varchar(44) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_N_PROT` `nfc_n_prot` varchar(16) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_DH_RECBTO` `nfc_dh_recbto` datetime NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_C_STAT` `nfc_c_stat` varchar(3) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_X_MOTIVO` `nfc_x_motivo` varchar(255) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_DIG_VAL` `nfc_dig_val` varchar(28) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_XML` `nfc_xml` longtext NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_DATA_CADASTRO` `nfc_data_cadastro` timestamp NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_DATA_ATUALIZACAO` `nfc_data_atualizacao` timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_CHAVE_PIX` `nfc_chave_pix` varchar(255) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_LINHA_DIGITAVEL` `nfc_linha_digitavel` varchar(255) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `OPC_ID` `opc_id` int(11) NULL  ;
ALTER TABLE `nfecom_capa` CHANGE COLUMN `NFC_N_PROT_CANC` `nfc_n_prot_canc` varchar(100) NULL  ;

-- Tabela: nfecom_itens
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_ID` `nfi_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFC_ID` `nfc_id` int(11) NOT NULL  ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_N_ITEM` `nfi_n_item` int(3) NOT NULL  ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_C_PROD` `nfi_c_prod` varchar(60) NOT NULL  ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_X_PROD` `nfi_x_prod` varchar(120) NOT NULL  ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_C_CLASS` `nfi_c_class` varchar(7) NOT NULL  ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_CFOP` `nfi_cfop` varchar(4) NOT NULL  ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_U_MED` `nfi_u_med` varchar(6) NOT NULL  ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_Q_FATURADA` `nfi_q_faturada` decimal(15,4) NOT NULL DEFAULT '0.0000' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_ITEM` `nfi_v_item` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_DESC` `nfi_v_desc` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_OUTRO` `nfi_v_outro` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_PROD` `nfi_v_prod` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_CST_ICMS` `nfi_cst_icms` varchar(3) NULL  ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_CSOSN` `nfi_csosn` varchar(3) NULL  ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_BC_ICMS` `nfi_v_bc_icms` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_P_ICMS` `nfi_p_icms` decimal(5,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_ICMS` `nfi_v_icms` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_ICMS_DESON` `nfi_v_icms_deson` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_MOT_DES_ICMS` `nfi_mot_des_icms` varchar(2) NULL  ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_BC_ICMS_ST` `nfi_v_bc_icms_st` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_P_ICMS_ST` `nfi_p_icms_st` decimal(5,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_ICMS_ST` `nfi_v_icms_st` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_BC_ST_RET` `nfi_v_bc_st_ret` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_ICMS_ST_RET` `nfi_v_icms_st_ret` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_P_ST` `nfi_p_st` decimal(5,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_ICMS_SUBST` `nfi_v_icms_subst` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_BC_FCP` `nfi_v_bc_fcp` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_P_FCP` `nfi_p_fcp` decimal(5,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_FCP` `nfi_v_fcp` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_FCP_ST` `nfi_v_fcp_st` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_FCP_ST_RET` `nfi_v_fcp_st_ret` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_CST_PIS` `nfi_cst_pis` varchar(2) NOT NULL DEFAULT '01' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_BC_PIS` `nfi_v_bc_pis` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_P_PIS` `nfi_p_pis` decimal(5,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_PIS` `nfi_v_pis` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_CST_COFINS` `nfi_cst_cofins` varchar(2) NOT NULL DEFAULT '01' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_BC_COFINS` `nfi_v_bc_cofins` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_P_COFINS` `nfi_p_cofins` decimal(5,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_COFINS` `nfi_v_cofins` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_BC_FUST` `nfi_v_bc_fust` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_P_FUST` `nfi_p_fust` decimal(5,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_FUST` `nfi_v_fust` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_BC_FUNTEL` `nfi_v_bc_funtel` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_P_FUNTEL` `nfi_p_funtel` decimal(5,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_FUNTEL` `nfi_v_funtel` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_BC_IRRF` `nfi_v_bc_irrf` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_V_IRRF` `nfi_v_irrf` decimal(15,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_DATA_CADASTRO` `nfi_data_cadastro` timestamp NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `nfecom_itens` CHANGE COLUMN `NFI_DATA_ATUALIZACAO` `nfi_data_atualizacao` timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();

-- Tabela: operacao_comercial
ALTER TABLE `operacao_comercial` CHANGE COLUMN `OPC_ID` `opc_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `operacao_comercial` CHANGE COLUMN `OPC_SIGLA` `opc_sigla` varchar(10) NOT NULL  ;
ALTER TABLE `operacao_comercial` CHANGE COLUMN `OPC_NOME` `opc_nome` varchar(100) NOT NULL  ;
ALTER TABLE `operacao_comercial` CHANGE COLUMN `OPC_NATUREZA_OPERACAO` `opc_natureza_operacao` enum('Compra','Venda','Transferencia','Outras') NOT NULL  ;
ALTER TABLE `operacao_comercial` CHANGE COLUMN `OPC_TIPO_MOVIMENTO` `opc_tipo_movimento` enum('Entrada','Saida') NOT NULL  ;
ALTER TABLE `operacao_comercial` CHANGE COLUMN `OPC_AFETA_CUSTO` `opc_afeta_custo` tinyint(1) NOT NULL DEFAULT '0' ;
ALTER TABLE `operacao_comercial` CHANGE COLUMN `OPC_FATO_FISCAL` `opc_fato_fiscal` tinyint(1) NOT NULL DEFAULT '0' ;
ALTER TABLE `operacao_comercial` CHANGE COLUMN `OPC_GERA_FINANCEIRO` `opc_gera_financeiro` tinyint(1) NOT NULL DEFAULT '0' ;
ALTER TABLE `operacao_comercial` CHANGE COLUMN `OPC_MOVIMENTA_ESTOQUE` `opc_movimenta_estoque` tinyint(1) NOT NULL DEFAULT '0' ;
ALTER TABLE `operacao_comercial` CHANGE COLUMN `OPC_SITUACAO` `opc_situacao` tinyint(1) NOT NULL DEFAULT '1' ;
ALTER TABLE `operacao_comercial` CHANGE COLUMN `OPC_FINALIDADE_NFE` `opc_finalidade_nfe` tinyint(1) NOT NULL DEFAULT '1' ;
ALTER TABLE `operacao_comercial` CHANGE COLUMN `OPC_DATA_INCLUSAO` `opc_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `operacao_comercial` CHANGE COLUMN `OPC_DATA_ALTERACAO` `opc_data_alteracao` datetime NULL  on update current_timestamp();

-- Tabela: ordem_servico
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_ID` `orv_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_DATA_INICIAL` `orv_data_inicial` date NULL  ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_DATA_FINAL` `orv_data_final` date NULL  ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_GARANTIA` `orv_garantia` varchar(45) NULL  ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_DESCRICAO_PRODUTO` `orv_descricao_produto` text NULL  ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_DEFEITO` `orv_defeito` text NULL  ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_STATUS` `orv_status` varchar(45) NULL  ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_OBSERVACOES` `orv_observacoes` text NULL  ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_LAUDO_TECNICO` `orv_laudo_tecnico` text NULL  ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_VALOR_TOTAL` `orv_valor_total` decimal(10,2) NULL DEFAULT '0.00' ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_DESCONTO` `orv_desconto` decimal(10,2) NULL DEFAULT '0.00' ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_VALOR_DESCONTO` `orv_valor_desconto` decimal(10,2) NULL DEFAULT '0.00' ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_TIPO_DESCONTO` `orv_tipo_desconto` varchar(8) NULL  ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_PESS_ID` `orv_pess_id` int(11) unsigned NOT NULL  ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_USUARIOS_ID` `orv_usuarios_id` int(11) NOT NULL  ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_LANCAMENTO` `orv_lancamento` int(11) NULL  ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_FATURADO` `orv_faturado` tinyint(1) NOT NULL DEFAULT '0' ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_GARANTIAS_ID` `orv_garantias_id` int(11) NULL  ;
ALTER TABLE `ordem_servico` CHANGE COLUMN `ORV_OPC_ID` `orv_opc_id` int(11) NULL  ;

-- Tabela: os
ALTER TABLE `os` CHANGE COLUMN `idOs` `idos` int(11) NOT NULL  auto_increment;
ALTER TABLE `os` CHANGE COLUMN `dataInicial` `datainicial` date NULL  ;
ALTER TABLE `os` CHANGE COLUMN `dataFinal` `datafinal` date NULL  ;
ALTER TABLE `os` CHANGE COLUMN `descricaoProduto` `descricaoproduto` mediumtext NULL  ;
ALTER TABLE `os` CHANGE COLUMN `laudoTecnico` `laudotecnico` mediumtext NULL  ;
ALTER TABLE `os` CHANGE COLUMN `valorTotal` `valortotal` decimal(10,2) NULL DEFAULT '0.00' ;

-- Tabela: pedidos
ALTER TABLE `pedidos` CHANGE COLUMN `PDS_ID` `pds_id` int(11) unsigned NOT NULL  auto_increment;
ALTER TABLE `pedidos` CHANGE COLUMN `PDS_DATA` `pds_data` date NULL  ;
ALTER TABLE `pedidos` CHANGE COLUMN `PDS_VALOR_TOTAL` `pds_valor_total` decimal(10,2) NULL DEFAULT '0.00' ;
ALTER TABLE `pedidos` CHANGE COLUMN `PDS_DESCONTO` `pds_desconto` decimal(10,2) NULL DEFAULT '0.00' ;
ALTER TABLE `pedidos` CHANGE COLUMN `PDS_VALOR_DESCONTO` `pds_valor_desconto` decimal(10,2) NULL DEFAULT '0.00' ;
ALTER TABLE `pedidos` CHANGE COLUMN `PDS_TIPO_DESCONTO` `pds_tipo_desconto` varchar(8) NULL  ;
ALTER TABLE `pedidos` CHANGE COLUMN `PDS_FATURADO` `pds_faturado` tinyint(1) NULL DEFAULT '0' ;
ALTER TABLE `pedidos` CHANGE COLUMN `PDS_OBSERVACOES` `pds_observacoes` text NULL  ;
ALTER TABLE `pedidos` CHANGE COLUMN `PDS_OBSERVACOES_CLIENTE` `pds_observacoes_cliente` text NULL  ;
ALTER TABLE `pedidos` CHANGE COLUMN `PDS_STATUS` `pds_status` varchar(45) NULL  ;
ALTER TABLE `pedidos` CHANGE COLUMN `PDS_GARANTIA` `pds_garantia` int(11) NULL  ;
ALTER TABLE `pedidos` CHANGE COLUMN `PDS_TIPO` `pds_tipo` enum('COMPRA','VENDA') NOT NULL DEFAULT 'VENDA' ;
ALTER TABLE `pedidos` CHANGE COLUMN `PDS_OPERACAO_COMERCIAL` `pds_operacao_comercial` int(11) NULL  ;
ALTER TABLE `pedidos` CHANGE COLUMN `PES_ID` `pes_id` int(11) NOT NULL  ;
ALTER TABLE `pedidos` CHANGE COLUMN `CLN_ID` `cln_id` int(11) NOT NULL  ;
ALTER TABLE `pedidos` CHANGE COLUMN `USU_ID` `usu_id` int(11) NULL  ;
ALTER TABLE `pedidos` CHANGE COLUMN `LAN_ID` `lan_id` int(11) NULL  ;

-- Tabela: pedidos_compra
ALTER TABLE `pedidos_compra` CHANGE COLUMN `idPedido` `idpedido` int(11) NOT NULL  auto_increment;

-- Tabela: permissoes
ALTER TABLE `permissoes` CHANGE COLUMN `idPermissao` `idpermissao` int(11) NOT NULL  auto_increment;
ALTER TABLE `permissoes` CHANGE COLUMN `vEmpresa` `vempresa` varchar(1) NULL  ;
ALTER TABLE `permissoes` CHANGE COLUMN `eEmpresa` `eempresa` varchar(1) NULL  ;
ALTER TABLE `permissoes` CHANGE COLUMN `cEmpresa` `cempresa` varchar(1) NULL  ;
ALTER TABLE `permissoes` CHANGE COLUMN `vClassificacaoFiscal` `vclassificacaofiscal` tinyint(1) NULL DEFAULT '0' ;

-- Tabela: pessoas
ALTER TABLE `pessoas` CHANGE COLUMN `PES_ID` `pes_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_CPFCNPJ` `pes_cpfcnpj` varchar(20) NOT NULL  ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_NOME` `pes_nome` varchar(150) NOT NULL  ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_RAZAO_SOCIAL` `pes_razao_social` varchar(150) NULL  ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_CODIGO` `pes_codigo` varchar(50) NOT NULL  ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_FISICO_JURIDICO` `pes_fisico_juridico` char(1) NOT NULL  ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_NASCIMENTO_ABERTURA` `pes_nascimento_abertura` date NULL  ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_NACIONALIDADES` `pes_nacionalidades` varchar(100) NULL  ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_RG` `pes_rg` varchar(20) NULL  ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_ORGAO_EXPEDIDOR` `pes_orgao_expedidor` varchar(20) NULL  ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_SEXO` `pes_sexo` char(1) NULL  ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_ESTADO_CIVIL` `pes_estado_civil` int(11) NULL  ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_ESCOLARIDADE` `pes_escolaridade` int(11) NULL  ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_PROFISSAO` `pes_profissao` varchar(100) NULL  ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_OBSERVACAO` `pes_observacao` text NULL  ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_SITUACAO` `pes_situacao` tinyint(1) NOT NULL DEFAULT '1' ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_DATA_INCLUSAO` `pes_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `pessoas` CHANGE COLUMN `PES_DATA_ATUALIZACAO` `pes_data_atualizacao` datetime NULL  on update current_timestamp();

-- Tabela: produtos
ALTER TABLE `produtos` CHANGE COLUMN `PRO_ID` `pro_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_COD_BARRA` `pro_cod_barra` varchar(70) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_DESCRICAO` `pro_descricao` varchar(80) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_UNID_MEDIDA` `pro_unid_medida` varchar(20) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_NCM` `pro_ncm` varchar(8) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `NCM_ID` `ncm_id` int(11) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_PESO_BRUTO` `pro_peso_bruto` decimal(10,3) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_PESO_LIQUIDO` `pro_peso_liquido` decimal(10,3) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_LARGURA` `pro_largura` decimal(10,2) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_ALTURA` `pro_altura` decimal(10,2) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_COMPRIMENTO` `pro_comprimento` decimal(10,2) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `TBP_ID` `tbp_id` int(11) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_PRECO_COMPRA` `pro_preco_compra` decimal(10,2) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_PRECO_VENDA` `pro_preco_venda` decimal(10,2) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_ESTOQUE` `pro_estoque` int(11) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_ORIGEM` `pro_origem` tinyint(1) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_ESTOQUE_MINIMO` `pro_estoque_minimo` int(11) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_SAIDA` `pro_saida` tinyint(1) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_ENTRADA` `pro_entrada` tinyint(1) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_TIPO` `pro_tipo` tinyint(1) NULL DEFAULT '1' ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_FINALIDADE` `pro_finalidade` varchar(30) NULL  ;
ALTER TABLE `produtos` CHANGE COLUMN `PRO_CCLASS_SERV` `pro_cclass_serv` varchar(7) NULL  ;

-- Tabela: produtos_movimentados
ALTER TABLE `produtos_movimentados` CHANGE COLUMN `PDM_ID` `pdm_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `produtos_movimentados` CHANGE COLUMN `PDM_QTDE` `pdm_qtde` decimal(10,2) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `produtos_movimentados` CHANGE COLUMN `PDM_TIPO` `pdm_tipo` varchar(10) NOT NULL  ;
ALTER TABLE `produtos_movimentados` CHANGE COLUMN `ITF_ID` `itf_id` int(11) NOT NULL  ;
ALTER TABLE `produtos_movimentados` CHANGE COLUMN `PDM_DATA` `pdm_data` datetime NOT NULL  ;

-- Tabela: produtos_os
ALTER TABLE `produtos_os` CHANGE COLUMN `PRO_OS_ID` `pro_os_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `produtos_os` CHANGE COLUMN `PRO_OS_QUANTIDADE` `pro_os_quantidade` int(11) NULL  ;
ALTER TABLE `produtos_os` CHANGE COLUMN `PRO_OS_DESCRICAO` `pro_os_descricao` varchar(80) NULL  ;
ALTER TABLE `produtos_os` CHANGE COLUMN `PRO_OS_PRECO` `pro_os_preco` decimal(10,2) NULL  ;
ALTER TABLE `produtos_os` CHANGE COLUMN `PRO_ID` `pro_id` int(11) NULL  ;
ALTER TABLE `produtos_os` CHANGE COLUMN `PRO_OS_SUBTOTAL` `pro_os_subtotal` decimal(10,2) NULL  ;

-- Tabela: protocolos
ALTER TABLE `protocolos` CHANGE COLUMN `PRT_ID` `prt_id` int(11) unsigned NOT NULL  auto_increment;
ALTER TABLE `protocolos` CHANGE COLUMN `NFC_ID` `nfc_id` int(11) NOT NULL  ;
ALTER TABLE `protocolos` CHANGE COLUMN `PRT_NUMERO_PROTOCOLO` `prt_numero_protocolo` varchar(60) NOT NULL  ;
ALTER TABLE `protocolos` CHANGE COLUMN `PRT_TIPO` `prt_tipo` varchar(30) NOT NULL  ;
ALTER TABLE `protocolos` CHANGE COLUMN `PRT_MOTIVO` `prt_motivo` varchar(255) NULL  ;
ALTER TABLE `protocolos` CHANGE COLUMN `PRT_DATA` `prt_data` datetime NOT NULL  ;

-- Tabela: servicos
ALTER TABLE `servicos` CHANGE COLUMN `SRV_ID` `srv_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `servicos` CHANGE COLUMN `SRV_NOME` `srv_nome` varchar(45) NOT NULL  ;
ALTER TABLE `servicos` CHANGE COLUMN `SRV_DESCRICAO` `srv_descricao` varchar(45) NULL  ;
ALTER TABLE `servicos` CHANGE COLUMN `SRV_CODIGO` `srv_codigo` varchar(45) NULL  ;
ALTER TABLE `servicos` CHANGE COLUMN `SRV_CCLASS` `srv_cclass` varchar(7) NULL  ;
ALTER TABLE `servicos` CHANGE COLUMN `SRV_UNID_MEDIDA` `srv_unid_medida` varchar(2) NULL  ;
ALTER TABLE `servicos` CHANGE COLUMN `SRV_PRECO` `srv_preco` decimal(10,2) NOT NULL  ;

-- Tabela: servicos_os
ALTER TABLE `servicos_os` CHANGE COLUMN `SOS_ID` `sos_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `servicos_os` CHANGE COLUMN `SOS_QUANTIDADE` `sos_quantidade` double NULL  ;
ALTER TABLE `servicos_os` CHANGE COLUMN `SOS_PRECO` `sos_preco` decimal(10,2) NULL  ;
ALTER TABLE `servicos_os` CHANGE COLUMN `PRO_ID` `pro_id` int(11) NULL  ;
ALTER TABLE `servicos_os` CHANGE COLUMN `SOS_SUBTOTAL` `sos_subtotal` decimal(10,2) NULL  ;

-- Tabela: telefones
ALTER TABLE `telefones` CHANGE COLUMN `TEL_ID` `tel_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `telefones` CHANGE COLUMN `PES_ID` `pes_id` int(11) NOT NULL  ;
ALTER TABLE `telefones` CHANGE COLUMN `TEL_TIPO` `tel_tipo` enum('Celular','Comercial','Residencial','Whatsapp','Outros') NOT NULL  ;
ALTER TABLE `telefones` CHANGE COLUMN `TEL_DDD` `tel_ddd` varchar(3) NOT NULL  ;
ALTER TABLE `telefones` CHANGE COLUMN `TEL_NUMERO` `tel_numero` varchar(12) NOT NULL  ;
ALTER TABLE `telefones` CHANGE COLUMN `TEL_OBSERVACAO` `tel_observacao` varchar(255) NULL  ;
ALTER TABLE `telefones` CHANGE COLUMN `TEL_DATA_INCLUSAO` `tel_data_inclusao` datetime NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `telefones` CHANGE COLUMN `TEL_DATA_ATUALIZACAO` `tel_data_atualizacao` datetime NULL  on update current_timestamp();

-- Tabela: tenant_permissoes_menu
ALTER TABLE `tenant_permissoes_menu` CHANGE COLUMN `TPM_ID` `tpm_id` int(11) unsigned NOT NULL  auto_increment;
ALTER TABLE `tenant_permissoes_menu` CHANGE COLUMN `TPM_TEN_ID` `tpm_ten_id` int(11) unsigned NOT NULL  ;
ALTER TABLE `tenant_permissoes_menu` CHANGE COLUMN `TPM_MENU_CODIGO` `tpm_menu_codigo` varchar(50) NOT NULL  ;
ALTER TABLE `tenant_permissoes_menu` CHANGE COLUMN `TPM_PERMISSAO` `tpm_permissao` varchar(50) NOT NULL  ;
ALTER TABLE `tenant_permissoes_menu` CHANGE COLUMN `TPM_ATIVO` `tpm_ativo` tinyint(1) NOT NULL DEFAULT '1' ;
ALTER TABLE `tenant_permissoes_menu` CHANGE COLUMN `TPM_DATA_CADASTRO` `tpm_data_cadastro` datetime NOT NULL  ;

-- Tabela: tipos_clientes
ALTER TABLE `tipos_clientes` CHANGE COLUMN `TPC_ID` `tpc_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `tipos_clientes` CHANGE COLUMN `TPC_NOME` `tpc_nome` varchar(200) NOT NULL  ;
ALTER TABLE `tipos_clientes` CHANGE COLUMN `TPC_CODIGO_CLIENTE` `tpc_codigo_cliente` varchar(20) NULL  ;
ALTER TABLE `tipos_clientes` CHANGE COLUMN `TPC_DATA_CADASTRO` `tpc_data_cadastro` datetime NOT NULL  ;

-- Tabela: tributacao_produto
ALTER TABLE `tributacao_produto` CHANGE COLUMN `TBP_ID` `tbp_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `tributacao_produto` CHANGE COLUMN `TBP_DESCRICAO` `tbp_descricao` varchar(100) NULL  ;
ALTER TABLE `tributacao_produto` CHANGE COLUMN `TBP_CST_IPI_SAIDA` `tbp_cst_ipi_saida` varchar(100) NULL  ;

-- Tabela: usuarios
ALTER TABLE `usuarios` CHANGE COLUMN `idUsuarios` `idusuarios` int(11) NOT NULL  auto_increment;
ALTER TABLE `usuarios` CHANGE COLUMN `dataCadastro` `datacadastro` date NOT NULL  ;
ALTER TABLE `usuarios` CHANGE COLUMN `dataExpiracao` `dataexpiracao` date NULL  ;

-- Tabela: usuarios_super
ALTER TABLE `usuarios_super` CHANGE COLUMN `USS_ID` `uss_id` int(11) unsigned NOT NULL  auto_increment;
ALTER TABLE `usuarios_super` CHANGE COLUMN `USS_NOME` `uss_nome` varchar(80) NOT NULL  ;
ALTER TABLE `usuarios_super` CHANGE COLUMN `USS_RG` `uss_rg` varchar(20) NULL  ;
ALTER TABLE `usuarios_super` CHANGE COLUMN `USS_CPF` `uss_cpf` varchar(20) NOT NULL  ;
ALTER TABLE `usuarios_super` CHANGE COLUMN `USS_EMAIL` `uss_email` varchar(80) NOT NULL  ;
ALTER TABLE `usuarios_super` CHANGE COLUMN `USS_SENHA` `uss_senha` varchar(200) NOT NULL  ;
ALTER TABLE `usuarios_super` CHANGE COLUMN `USS_TELEFONE` `uss_telefone` varchar(20) NOT NULL  ;
ALTER TABLE `usuarios_super` CHANGE COLUMN `USS_CELULAR` `uss_celular` varchar(20) NULL  ;
ALTER TABLE `usuarios_super` CHANGE COLUMN `USS_SITUACAO` `uss_situacao` tinyint(1) NOT NULL DEFAULT '1' ;
ALTER TABLE `usuarios_super` CHANGE COLUMN `USS_DATA_CADASTRO` `uss_data_cadastro` date NOT NULL  ;
ALTER TABLE `usuarios_super` CHANGE COLUMN `USS_DATA_EXPIRACAO` `uss_data_expiracao` date NULL  ;
ALTER TABLE `usuarios_super` CHANGE COLUMN `USS_URL_IMAGE_USER` `uss_url_image_user` varchar(255) NULL  ;

-- Tabela: vendas
ALTER TABLE `vendas` CHANGE COLUMN `idVendas` `idvendas` int(11) NOT NULL  auto_increment;
ALTER TABLE `vendas` CHANGE COLUMN `dataVenda` `datavenda` date NULL  ;
ALTER TABLE `vendas` CHANGE COLUMN `valorTotal` `valortotal` decimal(10,2) NULL DEFAULT '0.00' ;

-- Tabela: vendedores
ALTER TABLE `vendedores` CHANGE COLUMN `VEN_ID` `ven_id` int(11) NOT NULL  auto_increment;
ALTER TABLE `vendedores` CHANGE COLUMN `PES_ID` `pes_id` int(11) NOT NULL  ;
ALTER TABLE `vendedores` CHANGE COLUMN `VEN_PERCENTUAL_COMISSAO` `ven_percentual_comissao` decimal(5,2) NULL  ;
ALTER TABLE `vendedores` CHANGE COLUMN `VEN_TIPO_COMISSAO` `ven_tipo_comissao` varchar(20) NULL  ;
ALTER TABLE `vendedores` CHANGE COLUMN `VEN_META_MENSAL` `ven_meta_mensal` decimal(10,2) NULL  ;
ALTER TABLE `vendedores` CHANGE COLUMN `VEN_SITUACAO` `ven_situacao` tinyint(1) NULL DEFAULT '1' ;
ALTER TABLE `vendedores` CHANGE COLUMN `VEN_DATA_CADASTRO` `ven_data_cadastro` timestamp NOT NULL DEFAULT 'current_timestamp()' ;
ALTER TABLE `vendedores` CHANGE COLUMN `VEN_DATA_ATUALIZACAO` `ven_data_atualizacao` timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp();
