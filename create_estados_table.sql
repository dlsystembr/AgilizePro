-- Tabela de Estados
CREATE TABLE IF NOT EXISTS `estados` (
    `EST_ID` INT(11) NOT NULL AUTO_INCREMENT,
    `EST_NOME` VARCHAR(100) NOT NULL,
    `EST_UF` VARCHAR(2) NOT NULL,
    `EST_CODIGO_UF` INT(2) NOT NULL,
    `EST_DATA_INCLUSAO` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `EST_DATA_ALTERACAO` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`EST_ID`),
    UNIQUE KEY `uk_estados_uf` (`EST_UF`),
    UNIQUE KEY `uk_estados_codigo` (`EST_CODIGO_UF`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Inserir dados dos estados brasileiros
INSERT INTO `estados` (`EST_NOME`, `EST_UF`, `EST_CODIGO_UF`) VALUES
('Acre', 'AC', 12),
('Alagoas', 'AL', 27),
('Amapá', 'AP', 16),
('Amazonas', 'AM', 13),
('Bahia', 'BA', 29),
('Ceará', 'CE', 23),
('Distrito Federal', 'DF', 53),
('Espírito Santo', 'ES', 32),
('Goiás', 'GO', 52),
('Maranhão', 'MA', 21),
('Mato Grosso', 'MT', 51),
('Mato Grosso do Sul', 'MS', 50),
('Minas Gerais', 'MG', 31),
('Pará', 'PA', 15),
('Paraíba', 'PB', 25),
('Paraná', 'PR', 41),
('Pernambuco', 'PE', 26),
('Piauí', 'PI', 22),
('Rio de Janeiro', 'RJ', 33),
('Rio Grande do Norte', 'RN', 24),
('Rio Grande do Sul', 'RS', 43),
('Rondônia', 'RO', 11),
('Roraima', 'RR', 14),
('Santa Catarina', 'SC', 42),
('São Paulo', 'SP', 35),
('Sergipe', 'SE', 28),
('Tocantins', 'TO', 17);

