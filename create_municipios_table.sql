-- Tabela de Municípios
CREATE TABLE IF NOT EXISTS `municipios` (
    `MUN_ID` INT(11) NOT NULL AUTO_INCREMENT,
    `EST_ID` INT(11) NOT NULL,
    `MUN_NOME` VARCHAR(100) NOT NULL,
    `MUN_IBGE` INT(7) NOT NULL,
    `MUN_DATA_INCLUSAO` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `MUN_DATA_ATUALIZACAO` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`MUN_ID`),
    UNIQUE KEY `uk_municipios_ibge` (`MUN_IBGE`),
    KEY `idx_municipios_estado` (`EST_ID`),
    KEY `idx_municipios_nome` (`MUN_NOME`),
    CONSTRAINT `fk_municipios_estados` FOREIGN KEY (`EST_ID`) REFERENCES `estados` (`EST_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Inserir alguns municípios de exemplo (principais capitais e cidades)
INSERT INTO `municipios` (`EST_ID`, `MUN_NOME`, `MUN_IBGE`) VALUES
-- São Paulo (EST_ID = 25)
(25, 'São Paulo', 3550308),
(25, 'Guarulhos', 3518800),
(25, 'Campinas', 3509502),
(25, 'São Bernardo do Campo', 3548708),
(25, 'Santo André', 3547809),
(25, 'Osasco', 3534401),
(25, 'Ribeirão Preto', 3543402),
(25, 'Sorocaba', 3552205),
(25, 'Mauá', 3529006),
(25, 'São José dos Campos', 3549904),

-- Rio de Janeiro (EST_ID = 19)
(19, 'Rio de Janeiro', 3304557),
(19, 'São Gonçalo', 3304904),
(19, 'Duque de Caxias', 3301702),
(19, 'Nova Iguaçu', 3303500),
(19, 'Niterói', 3303302),
(19, 'Belford Roxo', 3300456),
(19, 'São João de Meriti', 3305109),
(19, 'Campos dos Goytacazes', 3301009),
(19, 'Petrópolis', 3303906),
(19, 'Volta Redonda', 3306305),

-- Minas Gerais (EST_ID = 13)
(13, 'Belo Horizonte', 3106200),
(13, 'Uberlândia', 3170206),
(13, 'Contagem', 3118601),
(13, 'Juiz de Fora', 3136702),
(13, 'Betim', 3106705),
(13, 'Montes Claros', 3143302),
(13, 'Ribeirão das Neves', 3154606),
(13, 'Uberaba', 3170107),
(13, 'Governador Valadares', 3127701),
(13, 'Ipatinga', 3131307),

-- Bahia (EST_ID = 5)
(5, 'Salvador', 2927408),
(5, 'Feira de Santana', 2910800),
(5, 'Vitória da Conquista', 2933307),
(5, 'Camaçari', 2910800),
(5, 'Itabuna', 2914802),
(5, 'Juazeiro', 2918407),
(5, 'Lauro de Freitas', 2919207),
(5, 'Ilhéus', 2913606),
(5, 'Jequié', 2918001),
(5, 'Teixeira de Freitas', 2931350),

-- Paraná (EST_ID = 16)
(16, 'Curitiba', 4106902),
(16, 'Londrina', 4113700),
(16, 'Maringá', 4115200),
(16, 'Ponta Grossa', 4119905),
(16, 'Cascavel', 4104808),
(16, 'São José dos Pinhais', 4125506),
(16, 'Foz do Iguaçu', 4108304),
(16, 'Colombo', 4105805),
(16, 'Guarapuava', 4109401),
(16, 'Paranaguá', 4118204),

-- Rio Grande do Sul (EST_ID = 21)
(21, 'Porto Alegre', 4314902),
(21, 'Caxias do Sul', 4305108),
(21, 'Pelotas', 4314407),
(21, 'Canoas', 4304606),
(21, 'Santa Maria', 4316907),
(21, 'Gravataí', 4309209),
(21, 'Viamão', 4323002),
(21, 'Novo Hamburgo', 4313409),
(21, 'São Leopoldo', 4318705),
(21, 'Rio Grande', 4315602),

-- Pernambuco (EST_ID = 17)
(17, 'Recife', 2611606),
(17, 'Jaboatão dos Guararapes', 2607901),
(17, 'Olinda', 2609600),
(17, 'Caruaru', 2604106),
(17, 'Petrolina', 2611101),
(17, 'Paulista', 2610707),
(17, 'Cabo de Santo Agostinho', 2602902),
(17, 'Camaragibe', 2603454),
(17, 'Garanhuns', 2606002),
(17, 'Vitória de Santo Antão', 2616407),

-- Ceará (EST_ID = 6)
(6, 'Fortaleza', 2304400),
(6, 'Caucaia', 2303709),
(6, 'Juazeiro do Norte', 2307650),
(6, 'Maracanaú', 2307700),
(6, 'Sobral', 2312908),
(6, 'Crato', 2304202),
(6, 'Itapipoca', 2306405),
(6, 'Maranguape', 2307700),
(6, 'Iguatu', 2305506),
(6, 'Quixadá', 2311306),

-- Pará (EST_ID = 14)
(14, 'Belém', 1501402),
(14, 'Ananindeua', 1500800),
(14, 'Santarém', 1506807),
(14, 'Marabá', 1504208),
(14, 'Parauapebas', 1505494),
(14, 'Castanhal', 1502400),
(14, 'Abaetetuba', 1500107),
(14, 'Itaituba', 1503606),
(14, 'Cametá', 1502103),
(14, 'Bragança', 1501709),

-- Santa Catarina (EST_ID = 24)
(24, 'Florianópolis', 4205407),
(24, 'Joinville', 4209102),
(24, 'Blumenau', 4202404),
(24, 'São José', 4216602),
(24, 'Criciúma', 4204608),
(24, 'Chapecó', 4204202),
(24, 'Itajaí', 4208203),
(24, 'Lages', 4209102),
(24, 'Jaraguá do Sul', 4208906),
(24, 'Palhoça', 4211900);

