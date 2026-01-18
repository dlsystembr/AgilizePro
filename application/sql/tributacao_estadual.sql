CREATE TABLE IF NOT EXISTS `tributacao_estadual` (
  `tbe_id` int(11) NOT NULL AUTO_INCREMENT,
  `ncm_id` int(11) NOT NULL,
  `tbe_uf` varchar(2) NOT NULL,
  `tbe_cst_icms` varchar(2) NOT NULL,
  `tbe_aliquota_icms` decimal(10,2) NOT NULL,
  `tbe_mva` decimal(10,2) NOT NULL,
  `tbe_aliquota_icms_st` decimal(10,2) NOT NULL,
  `tbe_aliquota_fcp` decimal(10,2) NOT NULL,
  `tbe_data_cadastro` datetime NOT NULL,
  `tbe_data_alteracao` datetime NOT NULL,
  PRIMARY KEY (`tbe_id`),
  KEY `ncm_id` (`ncm_id`),
  CONSTRAINT `fk_tributacao_estadual_ncm` FOREIGN KEY (`ncm_id`) REFERENCES `ncms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 