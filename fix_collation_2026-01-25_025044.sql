-- Gerado em: 2026-01-25 02:50:44
-- Banco: mapos

-- Alterar collation do banco de dados
ALTER DATABASE `mapos` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Alterar collation das tabelas
ALTER TABLE `aliquotas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `anexos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `anotacoes_os` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `bairros` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `categorias` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `certificados_digitais` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ci_sessions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `classificacao_fiscal` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_vendedores` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cobrancas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_fiscais` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_nfce` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_nfe` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `contas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `contratos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `contratos_itens` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `documentos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_queue` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emails` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emitente` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `enderecos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `equipamentos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `equipamentos_os` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `estados` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `faturamento_entrada` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `faturamento_entrada_itens` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fornecedores` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `garantias` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_de_vendas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_pedido` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_pedidos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `lancamentos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `logs` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `marcas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `marcas_equipamentos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `migrations` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `municipios` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ncms` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfe_certificates` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfe_documentos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfe_emitidas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_itens` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `operacao_comercial` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `operacao_comercial_old` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ordem_servico` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `os` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pedidos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pedidos_compra` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `permissoes` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pessoas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `produtos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `produtos_os` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `protocolos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `resets_de_senha` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `servicos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `servicos_os` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `telefones` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tenants` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tipos_pessoa` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tributacao_estadual` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tributacao_federal` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tributacao_produto` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `usuarios` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vendas` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vendedores` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Alterar collation de colunas específicas
-- Tabela: aliquotas
ALTER TABLE `aliquotas` MODIFY COLUMN `uf_origem` CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `aliquotas` MODIFY COLUMN `uf_destino` CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: anexos
ALTER TABLE `anexos` MODIFY COLUMN `anexo` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `anexos` MODIFY COLUMN `thumb` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `anexos` MODIFY COLUMN `url` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `anexos` MODIFY COLUMN `path` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: anotacoes_os
ALTER TABLE `anotacoes_os` MODIFY COLUMN `anotacao` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: bairros
ALTER TABLE `bairros` MODIFY COLUMN `BAI_NOME` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: categorias
ALTER TABLE `categorias` MODIFY COLUMN `categoria` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `categorias` MODIFY COLUMN `tipo` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: certificados_digitais
ALTER TABLE `certificados_digitais` MODIFY COLUMN `CER_SENHA` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `certificados_digitais` MODIFY COLUMN `CER_TIPO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `certificados_digitais` MODIFY COLUMN `CER_CNPJ` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: ci_sessions
ALTER TABLE `ci_sessions` MODIFY COLUMN `id` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ci_sessions` MODIFY COLUMN `ip_address` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: classificacao_fiscal
ALTER TABLE `classificacao_fiscal` MODIFY COLUMN `CLF_CST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `classificacao_fiscal` MODIFY COLUMN `CLF_CSOSN` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `classificacao_fiscal` MODIFY COLUMN `CLF_NATUREZA_CONTRIBUINTE` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `classificacao_fiscal` MODIFY COLUMN `CLF_CFOP` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `classificacao_fiscal` MODIFY COLUMN `CLF_DESTINACAO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `classificacao_fiscal` MODIFY COLUMN `CLF_OBJETIVO_COMERCIAL` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `classificacao_fiscal` MODIFY COLUMN `CLF_FINALIDADE` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `classificacao_fiscal` MODIFY COLUMN `CLF_TIPO_TRIBUTACAO` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `classificacao_fiscal` MODIFY COLUMN `CLF_MENSAGEM` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `classificacao_fiscal` MODIFY COLUMN `CLF_CCLASSTRIB` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `classificacao_fiscal` MODIFY COLUMN `CLF_CST_IBS` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `classificacao_fiscal` MODIFY COLUMN `CLF_CST_CBS` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: clientes
ALTER TABLE `clientes` MODIFY COLUMN `CLN_OBJETIVO_COMERCIAL` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: clientes_
ALTER TABLE `clientes_` MODIFY COLUMN `asaas_id` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `nomeCliente` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `sexo` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `documento` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `natureza_contribuinte` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `telefone` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `celular` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `email` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `senha` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `rua` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `numero` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `bairro` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `cidade` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `estado` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `cep` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `objetivo_comercial` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `inscricao` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `ibge` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `contato` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `clientes_` MODIFY COLUMN `complemento` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: cobrancas
ALTER TABLE `cobrancas` MODIFY COLUMN `message` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cobrancas` MODIFY COLUMN `payment_method` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cobrancas` MODIFY COLUMN `payment_url` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cobrancas` MODIFY COLUMN `request_delivery_address` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cobrancas` MODIFY COLUMN `status` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cobrancas` MODIFY COLUMN `barcode` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cobrancas` MODIFY COLUMN `link` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cobrancas` MODIFY COLUMN `payment` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cobrancas` MODIFY COLUMN `pdf` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cobrancas` MODIFY COLUMN `payment_gateway` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: configuracoes
ALTER TABLE `configuracoes` MODIFY COLUMN `config` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes` MODIFY COLUMN `valor` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes` MODIFY COLUMN `versao_nfe` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes` MODIFY COLUMN `orientacao_danfe` CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes` MODIFY COLUMN `csc` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes` MODIFY COLUMN `csc_id` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: configuracoes_fiscais
ALTER TABLE `configuracoes_fiscais` MODIFY COLUMN `CFG_TIPO_DOCUMENTO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_fiscais` MODIFY COLUMN `CFG_SERIE` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_fiscais` MODIFY COLUMN `CFG_CSC_ID` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_fiscais` MODIFY COLUMN `CFG_CSC_TOKEN` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_fiscais` MODIFY COLUMN `CFG_REGIME_ESPECIAL` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_fiscais` MODIFY COLUMN `CFG_FORMATO_IMPRESSAO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: configuracoes_nfce
ALTER TABLE `configuracoes_nfce` MODIFY COLUMN `tipo_documento` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_nfce` MODIFY COLUMN `versao_nfce` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_nfce` MODIFY COLUMN `csc` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_nfce` MODIFY COLUMN `csc_id` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: configuracoes_nfe
ALTER TABLE `configuracoes_nfe` MODIFY COLUMN `tipo_documento` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_nfe` MODIFY COLUMN `versao_nfe` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_nfe` MODIFY COLUMN `orientacao_danfe` CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_nfe` MODIFY COLUMN `csc` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `configuracoes_nfe` MODIFY COLUMN `csc_id` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: contas
ALTER TABLE `contas` MODIFY COLUMN `conta` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `contas` MODIFY COLUMN `banco` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `contas` MODIFY COLUMN `numero` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `contas` MODIFY COLUMN `tipo` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: contratos
ALTER TABLE `contratos` MODIFY COLUMN `CTR_NUMERO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `contratos` MODIFY COLUMN `CTR_TIPO_ASSINANTE` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `contratos` MODIFY COLUMN `CTR_ANEXO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `contratos` MODIFY COLUMN `CTR_OBSERVACAO` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: contratos_itens
ALTER TABLE `contratos_itens` MODIFY COLUMN `CTI_OBSERVACAO` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: documentos
ALTER TABLE `documentos` MODIFY COLUMN `DOC_TIPO_DOCUMENTO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `documentos` MODIFY COLUMN `DOC_ORGAO_EXPEDIDOR` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `documentos` MODIFY COLUMN `DOC_NUMERO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `documentos` MODIFY COLUMN `DOC_NATUREZA_CONTRIBUINTE` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: emails
ALTER TABLE `emails` MODIFY COLUMN `EML_TIPO` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emails` MODIFY COLUMN `EML_EMAIL` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emails` MODIFY COLUMN `EML_NOME` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: email_queue
ALTER TABLE `email_queue` MODIFY COLUMN `to` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_queue` MODIFY COLUMN `cc` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_queue` MODIFY COLUMN `bcc` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_queue` MODIFY COLUMN `message` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_queue` MODIFY COLUMN `status` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_queue` MODIFY COLUMN `headers` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: emitente
ALTER TABLE `emitente` MODIFY COLUMN `nome` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emitente` MODIFY COLUMN `cnpj` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emitente` MODIFY COLUMN `ie` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emitente` MODIFY COLUMN `rua` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emitente` MODIFY COLUMN `numero` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emitente` MODIFY COLUMN `bairro` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emitente` MODIFY COLUMN `cidade` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emitente` MODIFY COLUMN `uf` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emitente` MODIFY COLUMN `telefone` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emitente` MODIFY COLUMN `email` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emitente` MODIFY COLUMN `url_logo` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `emitente` MODIFY COLUMN `cep` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: enderecos
ALTER TABLE `enderecos` MODIFY COLUMN `END_TIPO_ENDENRECO` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `enderecos` MODIFY COLUMN `END_TIPO_LOGRADOURO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `enderecos` MODIFY COLUMN `END_LOGRADOURO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `enderecos` MODIFY COLUMN `END_NUMERO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `enderecos` MODIFY COLUMN `END_COMPLEMENTO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `enderecos` MODIFY COLUMN `END_CEP` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `enderecos` MODIFY COLUMN `END_ZONA` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `enderecos` MODIFY COLUMN `END_OBSERVACAO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: equipamentos
ALTER TABLE `equipamentos` MODIFY COLUMN `equipamento` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `equipamentos` MODIFY COLUMN `num_serie` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `equipamentos` MODIFY COLUMN `modelo` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `equipamentos` MODIFY COLUMN `cor` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `equipamentos` MODIFY COLUMN `descricao` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `equipamentos` MODIFY COLUMN `tensao` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `equipamentos` MODIFY COLUMN `potencia` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `equipamentos` MODIFY COLUMN `voltagem` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: equipamentos_os
ALTER TABLE `equipamentos_os` MODIFY COLUMN `defeito_declarado` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `equipamentos_os` MODIFY COLUMN `defeito_encontrado` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `equipamentos_os` MODIFY COLUMN `solucao` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: estados
ALTER TABLE `estados` MODIFY COLUMN `EST_NOME` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `estados` MODIFY COLUMN `EST_UF` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: faturamento_entrada
ALTER TABLE `faturamento_entrada` MODIFY COLUMN `modalidade_frete` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `faturamento_entrada` MODIFY COLUMN `numero_nota` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `faturamento_entrada` MODIFY COLUMN `chave_acesso` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `faturamento_entrada` MODIFY COLUMN `observacoes` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `faturamento_entrada` MODIFY COLUMN `status` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `faturamento_entrada` MODIFY COLUMN `xml_conteudo` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: fornecedores
ALTER TABLE `fornecedores` MODIFY COLUMN `nomeFornecedor` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fornecedores` MODIFY COLUMN `cnpj` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fornecedores` MODIFY COLUMN `telefone` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fornecedores` MODIFY COLUMN `celular` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fornecedores` MODIFY COLUMN `email` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fornecedores` MODIFY COLUMN `rua` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fornecedores` MODIFY COLUMN `numero` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fornecedores` MODIFY COLUMN `bairro` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fornecedores` MODIFY COLUMN `cidade` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fornecedores` MODIFY COLUMN `estado` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fornecedores` MODIFY COLUMN `cep` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: garantias
ALTER TABLE `garantias` MODIFY COLUMN `refGarantia` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `garantias` MODIFY COLUMN `textoGarantia` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: itens_faturados
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_UNIDADE` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_PRO_DESCRICAO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_PRO_NCM` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_NCM_CEST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_CFOP` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_ICMS_CST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_CSOSN` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_COD_BENEFICIO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_MOT_DESONERADO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_PIS_CST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_COFINS_CST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_IPI_CST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_CCLASS_TRIB` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_IBT` CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `itens_faturados` MODIFY COLUMN `ITF_IBT_CST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: lancamentos
ALTER TABLE `lancamentos` MODIFY COLUMN `descricao` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `lancamentos` MODIFY COLUMN `tipo_desconto` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `lancamentos` MODIFY COLUMN `cliente_fornecedor` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `lancamentos` MODIFY COLUMN `forma_pgto` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `lancamentos` MODIFY COLUMN `tipo` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `lancamentos` MODIFY COLUMN `anexo` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `lancamentos` MODIFY COLUMN `observacoes` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: logs
ALTER TABLE `logs` MODIFY COLUMN `usuario` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `logs` MODIFY COLUMN `tarefa` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `logs` MODIFY COLUMN `ip` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: marcas
ALTER TABLE `marcas` MODIFY COLUMN `mrc_nome` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `marcas` MODIFY COLUMN `mrc_descricao` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: marcas_equipamentos
ALTER TABLE `marcas_equipamentos` MODIFY COLUMN `marca` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: municipios
ALTER TABLE `municipios` MODIFY COLUMN `MUN_NOME` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: ncms
ALTER TABLE `ncms` MODIFY COLUMN `NCM_CODIGO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ncms` MODIFY COLUMN `NCM_DESCRICAO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ncms` MODIFY COLUMN `tipo_ato` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ncms` MODIFY COLUMN `numero_ato` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ncms` MODIFY COLUMN `ano_ato` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: nfecom_capa
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_CUF` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_MOD` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_SERIE` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_CNF` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_CDV` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_C_MUN_FG` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_VER_PROC` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_CNPJ_EMIT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_IE_EMIT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_X_NOME_EMIT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_X_FANT_EMIT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_X_LGR_EMIT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_NRO_EMIT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_X_CPL_EMIT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_X_BAIRRO_EMIT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_C_MUN_EMIT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_X_MUN_EMIT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_CEP_EMIT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_UF_EMIT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_FONE_EMIT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_X_NOME_DEST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_CNPJ_DEST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_X_LGR_DEST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_NRO_DEST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_X_BAIRRO_DEST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_C_MUN_DEST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_X_MUN_DEST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_X_CPL_DEST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_CEP_DEST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_UF_DEST` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_I_COD_ASSINANTE` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_N_CONTRATO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_COMPET_FAT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_COD_BARRAS` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_INF_CPL` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_CH_NFCOM` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_N_PROT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_C_STAT` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_X_MOTIVO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_DIG_VAL` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_XML` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_CHAVE_PIX` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_LINHA_DIGITAVEL` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_capa` MODIFY COLUMN `NFC_N_PROT_CANC` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: nfecom_itens
ALTER TABLE `nfecom_itens` MODIFY COLUMN `NFI_C_PROD` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_itens` MODIFY COLUMN `NFI_X_PROD` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_itens` MODIFY COLUMN `NFI_C_CLASS` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_itens` MODIFY COLUMN `NFI_CFOP` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_itens` MODIFY COLUMN `NFI_U_MED` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_itens` MODIFY COLUMN `NFI_CST_ICMS` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_itens` MODIFY COLUMN `NFI_CSOSN` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_itens` MODIFY COLUMN `NFI_MOT_DES_ICMS` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_itens` MODIFY COLUMN `NFI_CST_PIS` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfecom_itens` MODIFY COLUMN `NFI_CST_COFINS` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: nfe_certificates
ALTER TABLE `nfe_certificates` MODIFY COLUMN `senha_certificado` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfe_certificates` MODIFY COLUMN `nome_certificado` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: nfe_documentos
ALTER TABLE `nfe_documentos` MODIFY COLUMN `tipo` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfe_documentos` MODIFY COLUMN `justificativa` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfe_documentos` MODIFY COLUMN `protocolo` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfe_documentos` MODIFY COLUMN `xml` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: nfe_emitidas
ALTER TABLE `nfe_emitidas` MODIFY COLUMN `numero_nfe` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfe_emitidas` MODIFY COLUMN `chave_nfe` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfe_emitidas` MODIFY COLUMN `status` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfe_emitidas` MODIFY COLUMN `xml` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfe_emitidas` MODIFY COLUMN `xml_protocolo` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfe_emitidas` MODIFY COLUMN `protocolo` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfe_emitidas` MODIFY COLUMN `motivo` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `nfe_emitidas` MODIFY COLUMN `chave_retorno_evento` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: operacao_comercial
ALTER TABLE `operacao_comercial` MODIFY COLUMN `OPC_SIGLA` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `operacao_comercial` MODIFY COLUMN `OPC_NOME` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `operacao_comercial` MODIFY COLUMN `OPC_NATUREZA_OPERACAO` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `operacao_comercial` MODIFY COLUMN `OPC_TIPO_MOVIMENTO` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: ordem_servico
ALTER TABLE `ordem_servico` MODIFY COLUMN `ORV_GARANTIA` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ordem_servico` MODIFY COLUMN `ORV_DESCRICAO_PRODUTO` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ordem_servico` MODIFY COLUMN `ORV_DEFEITO` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ordem_servico` MODIFY COLUMN `ORV_STATUS` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ordem_servico` MODIFY COLUMN `ORV_OBSERVACOES` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ordem_servico` MODIFY COLUMN `ORV_LAUDO_TECNICO` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `ordem_servico` MODIFY COLUMN `ORV_TIPO_DESCONTO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: os
ALTER TABLE `os` MODIFY COLUMN `garantia` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `os` MODIFY COLUMN `descricaoProduto` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `os` MODIFY COLUMN `defeito` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `os` MODIFY COLUMN `status` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `os` MODIFY COLUMN `observacoes` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `os` MODIFY COLUMN `laudoTecnico` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `os` MODIFY COLUMN `tipo_desconto` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: pedidos
ALTER TABLE `pedidos` MODIFY COLUMN `PDS_TIPO_DESCONTO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pedidos` MODIFY COLUMN `PDS_OBSERVACOES` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pedidos` MODIFY COLUMN `PDS_OBSERVACOES_CLIENTE` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pedidos` MODIFY COLUMN `PDS_STATUS` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pedidos` MODIFY COLUMN `PDS_TIPO` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: pedidos_compra
ALTER TABLE `pedidos_compra` MODIFY COLUMN `status` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pedidos_compra` MODIFY COLUMN `observacoes` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: permissoes
ALTER TABLE `permissoes` MODIFY COLUMN `nome` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `permissoes` MODIFY COLUMN `permissoes` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `permissoes` MODIFY COLUMN `vEmpresa` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `permissoes` MODIFY COLUMN `eEmpresa` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `permissoes` MODIFY COLUMN `cEmpresa` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: pessoas
ALTER TABLE `pessoas` MODIFY COLUMN `PES_CPFCNPJ` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pessoas` MODIFY COLUMN `PES_NOME` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pessoas` MODIFY COLUMN `PES_RAZAO_SOCIAL` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pessoas` MODIFY COLUMN `PES_CODIGO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pessoas` MODIFY COLUMN `PES_FISICO_JURIDICO` CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pessoas` MODIFY COLUMN `PES_NACIONALIDADES` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pessoas` MODIFY COLUMN `PES_RG` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pessoas` MODIFY COLUMN `PES_ORGAO_EXPEDIDOR` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pessoas` MODIFY COLUMN `PES_SEXO` CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pessoas` MODIFY COLUMN `PES_PROFISSAO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pessoas` MODIFY COLUMN `PES_OBSERVACAO` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: produtos
ALTER TABLE `produtos` MODIFY COLUMN `PRO_COD_BARRA` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `produtos` MODIFY COLUMN `PRO_DESCRICAO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `produtos` MODIFY COLUMN `PRO_UNID_MEDIDA` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `produtos` MODIFY COLUMN `PRO_NCM` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `produtos` MODIFY COLUMN `PRO_FINALIDADE` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `produtos` MODIFY COLUMN `PRO_CCLASS_SERV` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: produtos_os
ALTER TABLE `produtos_os` MODIFY COLUMN `PRO_OS_DESCRICAO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: protocolos
ALTER TABLE `protocolos` MODIFY COLUMN `PRT_NUMERO_PROTOCOLO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `protocolos` MODIFY COLUMN `PRT_TIPO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `protocolos` MODIFY COLUMN `PRT_MOTIVO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: resets_de_senha
ALTER TABLE `resets_de_senha` MODIFY COLUMN `email` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `resets_de_senha` MODIFY COLUMN `token` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: servicos
ALTER TABLE `servicos` MODIFY COLUMN `SRV_NOME` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `servicos` MODIFY COLUMN `SRV_DESCRICAO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `servicos` MODIFY COLUMN `SRV_CODIGO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `servicos` MODIFY COLUMN `SRV_CCLASS` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `servicos` MODIFY COLUMN `SRV_UNID_MEDIDA` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: servicos_os
ALTER TABLE `servicos_os` MODIFY COLUMN `servico` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: telefones
ALTER TABLE `telefones` MODIFY COLUMN `TEL_TIPO` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `telefones` MODIFY COLUMN `TEL_DDD` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `telefones` MODIFY COLUMN `TEL_NUMERO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `telefones` MODIFY COLUMN `TEL_OBSERVACAO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: tenants
ALTER TABLE `tenants` MODIFY COLUMN `ten_nome` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tenants` MODIFY COLUMN `ten_cnpj` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tenants` MODIFY COLUMN `ten_email` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tenants` MODIFY COLUMN `ten_telefone` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: tipos_pessoa
ALTER TABLE `tipos_pessoa` MODIFY COLUMN `nome` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tipos_pessoa` MODIFY COLUMN `descricao` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: tributacao_estadual
ALTER TABLE `tributacao_estadual` MODIFY COLUMN `tbe_uf` CHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tributacao_estadual` MODIFY COLUMN `tbe_tipo_tributacao` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: tributacao_federal
ALTER TABLE `tributacao_federal` MODIFY COLUMN `tbf_cst_ipi_entrada` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tributacao_federal` MODIFY COLUMN `tbf_cst_ipi_saida` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tributacao_federal` MODIFY COLUMN `tbf_cst_pis_cofins_entrada` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tributacao_federal` MODIFY COLUMN `tbf_cst_pis_cofins_saida` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: tributacao_produto
ALTER TABLE `tributacao_produto` MODIFY COLUMN `TBP_DESCRICAO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tributacao_produto` MODIFY COLUMN `TBP_CST_IPI_SAIDA` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tributacao_produto` MODIFY COLUMN `cst_pis_saida` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tributacao_produto` MODIFY COLUMN `cst_cofins_saida` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tributacao_produto` MODIFY COLUMN `regime_fiscal_tributario` ENUM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: usuarios
ALTER TABLE `usuarios` MODIFY COLUMN `nome` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `usuarios` MODIFY COLUMN `rg` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `usuarios` MODIFY COLUMN `cpf` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `usuarios` MODIFY COLUMN `rua` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `usuarios` MODIFY COLUMN `numero` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `usuarios` MODIFY COLUMN `bairro` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `usuarios` MODIFY COLUMN `cidade` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `usuarios` MODIFY COLUMN `estado` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `usuarios` MODIFY COLUMN `email` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `usuarios` MODIFY COLUMN `senha` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `usuarios` MODIFY COLUMN `telefone` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `usuarios` MODIFY COLUMN `celular` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `usuarios` MODIFY COLUMN `url_image_user` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `usuarios` MODIFY COLUMN `cep` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: vendas
ALTER TABLE `vendas` MODIFY COLUMN `tipo_desconto` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vendas` MODIFY COLUMN `status` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vendas` MODIFY COLUMN `observacoes` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vendas` MODIFY COLUMN `observacoes_cliente` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Tabela: vendedores
ALTER TABLE `vendedores` MODIFY COLUMN `VEN_TIPO_COMISSAO` VARCHAR CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;