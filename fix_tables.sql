SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS itens_pedido;
DROP TABLE IF EXISTS pedidos_compra;

CREATE TABLE IF NOT EXISTS `pedidos_compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_pedido` date NOT NULL,
  `data_aprovacao` date DEFAULT NULL,
  `fornecedor_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pendente',
  PRIMARY KEY (`id`),
  KEY `fk_pedidos_compra_fornecedor` (`fornecedor_id`),
  KEY `fk_pedidos_compra_usuario` (`usuario_id`),
  CONSTRAINT `fk_pedidos_compra_fornecedor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores` (`idFornecedores`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pedidos_compra_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`idUsuarios`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `itens_pedido` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_itens_pedido_pedido` (`pedido_id`),
  KEY `fk_itens_pedido_produto` (`produto_id`),
  CONSTRAINT `fk_itens_pedido_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos_compra` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_itens_pedido_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`idProdutos`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS=1; 