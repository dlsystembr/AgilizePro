-- Table structure for faturamento_entrada
CREATE TABLE IF NOT EXISTS `faturamento_entrada` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fornecedor_id` int(11) NOT NULL,
  `transportadora_id` int(11) DEFAULT NULL,
  `modalidade_frete` varchar(2) DEFAULT NULL,
  `peso_bruto` decimal(10,2) DEFAULT NULL,
  `peso_liquido` decimal(10,2) DEFAULT NULL,
  `volume` decimal(10,3) DEFAULT NULL,
  `operacao_comercial_id` int(11) DEFAULT NULL,
  `data_emissao` date NOT NULL,
  `data_entrada` date NOT NULL,
  `numero_nota` varchar(20) DEFAULT NULL,
  `chave_acesso` varchar(44) DEFAULT NULL,
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_produtos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_base_icms` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_icms` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_base_icms_st` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_icms_st` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_ipi` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_frete` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_outras_despesas` decimal(10,2) NOT NULL DEFAULT 0.00,
  `observacoes` text DEFAULT NULL,
  `data_cadastro` datetime NOT NULL,
  `data_atualizacao` datetime DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `status` enum('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  KEY `fornecedor_id` (`fornecedor_id`),
  KEY `operacao_comercial_id` (`operacao_comercial_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `fk_faturamento_entrada_fornecedor` FOREIGN KEY (`fornecedor_id`) REFERENCES `clientes` (`idClientes`) ON DELETE CASCADE,
  CONSTRAINT `fk_faturamento_entrada_operacao` FOREIGN KEY (`operacao_comercial_id`) REFERENCES `operacao_comercial` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_faturamento_entrada_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`idUsuarios`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for faturamento_entrada_itens
CREATE TABLE IF NOT EXISTS `faturamento_entrada_itens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faturamento_entrada_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `aliquota_icms` decimal(5,2) NOT NULL DEFAULT 0.00,
  `valor_icms` decimal(10,2) NOT NULL DEFAULT 0.00,
  `base_icms_st` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_icms_st` decimal(10,2) NOT NULL DEFAULT 0.00,
  `aliquota_ipi` decimal(5,2) NOT NULL DEFAULT 0.00,
  `valor_ipi` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `faturamento_entrada_id` (`faturamento_entrada_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `fk_faturamento_entrada_itens_faturamento` FOREIGN KEY (`faturamento_entrada_id`) REFERENCES `faturamento_entrada` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_faturamento_entrada_itens_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`idProdutos`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add data_atualizacao column if table already exists
ALTER TABLE `faturamento_entrada` 
ADD COLUMN IF NOT EXISTS `data_atualizacao` datetime DEFAULT NULL AFTER `data_cadastro`;

-- Add usuario_id column if table already exists
ALTER TABLE `faturamento_entrada` 
ADD COLUMN IF NOT EXISTS `usuario_id` int(11) NOT NULL AFTER `data_atualizacao`,
ADD KEY `usuario_id` (`usuario_id`),
ADD CONSTRAINT `fk_faturamento_entrada_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`idUsuarios`) ON DELETE CASCADE;

-- Add volume column if table already exists
ALTER TABLE `faturamento_entrada` 
ADD COLUMN IF NOT EXISTS `volume` decimal(10,3) DEFAULT NULL AFTER `peso_liquido`; 