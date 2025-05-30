CREATE TABLE IF NOT EXISTS `aliquotas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uf_origem` varchar(2) NOT NULL,
  `uf_destino` varchar(2) NOT NULL,
  `aliquota_origem` decimal(5,2) NOT NULL,
  `aliquota_destino` decimal(5,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uf_origem_destino` (`uf_origem`,`uf_destino`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 