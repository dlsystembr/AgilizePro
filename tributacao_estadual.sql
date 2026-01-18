CREATE TABLE IF NOT EXISTS `tributacao_estadual` (
  `tbe_id` int(11) NOT NULL AUTO_INCREMENT,
  `ncm_id` int(11) NOT NULL,
  `tbe_uf` char(2) NOT NULL,
  `tbe_tipo_tributacao` enum('ICMS Normal','ST','Serviço') NOT NULL,
  `tbe_aliquota_icms` decimal(5,2) DEFAULT '0.00',
  `tbe_mva` decimal(5,2) DEFAULT '0.00',
  `tbe_aliquota_icms_st` decimal(5,2) DEFAULT '0.00',
  `tbe_percentual_reducao_icms` decimal(5,2) DEFAULT '0.00',
  `tbe_percentual_reducao_st` decimal(5,2) DEFAULT '0.00',
  `tbe_aliquota_fcp` decimal(5,2) DEFAULT '0.00',
  `tbe_data_cadastro` datetime DEFAULT NULL,
  `tbe_data_alteracao` datetime DEFAULT NULL,
  PRIMARY KEY (`tbe_id`),
  KEY `ncm_id` (`ncm_id`),
  KEY `tbe_uf` (`tbe_uf`),
  CONSTRAINT `fk_tributacao_estadual_ncm` FOREIGN KEY (`ncm_id`) REFERENCES `ncms` (`ncm_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 