-- Script SQL para importar municípios do CSV
-- IMPORTANTE: Este script deve ser executado após criar as tabelas estados e municipios

-- Primeiro, vamos limpar a tabela de municípios (caso já tenha dados)
-- TRUNCATE TABLE municipios;

-- Como o CSV tem caracteres especiais e é muito grande, 
-- recomendo usar o script PHP import_municipios.php
-- ou usar o comando LOAD DATA INFILE do MySQL:

-- LOAD DATA INFILE 'import/municipios.csv'
-- INTO TABLE municipios
-- FIELDS TERMINATED BY ';'
-- ENCLOSED BY '"'
-- LINES TERMINATED BY '\n'
-- IGNORE 1 ROWS
-- (@codigo_tom, @codigo_ibge, @municipio_tom, @municipio_ibge, @uf)
-- SET 
--     EST_ID = CASE @uf
--         WHEN 'AC' THEN 1 WHEN 'AL' THEN 2 WHEN 'AP' THEN 3 WHEN 'AM' THEN 4
--         WHEN 'BA' THEN 5 WHEN 'CE' THEN 6 WHEN 'DF' THEN 7 WHEN 'ES' THEN 8
--         WHEN 'GO' THEN 9 WHEN 'MA' THEN 10 WHEN 'MT' THEN 11 WHEN 'MS' THEN 12
--         WHEN 'MG' THEN 13 WHEN 'PA' THEN 14 WHEN 'PB' THEN 15 WHEN 'PR' THEN 16
--         WHEN 'PE' THEN 17 WHEN 'PI' THEN 18 WHEN 'RJ' THEN 19 WHEN 'RN' THEN 20
--         WHEN 'RS' THEN 21 WHEN 'RO' THEN 22 WHEN 'RR' THEN 23 WHEN 'SC' THEN 24
--         WHEN 'SP' THEN 25 WHEN 'SE' THEN 26 WHEN 'TO' THEN 27
--         ELSE NULL
--     END,
--     MUN_NOME = @municipio_ibge,
--     MUN_IBGE = @codigo_ibge;

-- Alternativa: Script para inserir alguns municípios principais manualmente
-- (Use este se preferir inserir apenas as principais cidades)

INSERT IGNORE INTO municipios (EST_ID, MUN_NOME, MUN_IBGE) VALUES
-- Acre
(1, 'Rio Branco', 1200401),
(1, 'Cruzeiro do Sul', 1200203),
(1, 'Sena Madureira', 1200500),

-- Alagoas  
(2, 'Maceió', 2704302),
(2, 'Arapiraca', 2700300),
(2, 'Rio Largo', 2707701),

-- Amapá
(3, 'Macapá', 1600303),
(3, 'Santana', 1600600),
(3, 'Laranjal do Jari', 1600279),

-- Amazonas
(4, 'Manaus', 1302603),
(4, 'Parintins', 1303403),
(4, 'Itacoatiara', 1301902),

-- Bahia
(5, 'Salvador', 2927408),
(5, 'Feira de Santana', 2910800),
(5, 'Vitória da Conquista', 2933307),
(5, 'Camaçari', 2910800),
(5, 'Itabuna', 2914802),

-- Ceará
(6, 'Fortaleza', 2304400),
(6, 'Caucaia', 2303709),
(6, 'Juazeiro do Norte', 2307650),
(6, 'Maracanaú', 2307700),
(6, 'Sobral', 2312908),

-- Distrito Federal
(7, 'Brasília', 5300108),

-- Espírito Santo
(8, 'Vitória', 3205309),
(8, 'Vila Velha', 3205200),
(8, 'Cariacica', 3201308),
(8, 'Serra', 3205002),
(8, 'Cachoeiro de Itapemirim', 3201209),

-- Goiás
(9, 'Goiânia', 5208707),
(9, 'Aparecida de Goiânia', 5201405),
(9, 'Anápolis', 5201108),
(9, 'Rio Verde', 5218805),
(9, 'Luziânia', 5212501),

-- Maranhão
(10, 'São Luís', 2111300),
(10, 'Imperatriz', 2105302),
(10, 'São José de Ribamar', 2111201),
(10, 'Timon', 2112209),
(10, 'Caxias', 2103000),

-- Mato Grosso
(11, 'Cuiabá', 5103403),
(11, 'Várzea Grande', 5108402),
(11, 'Rondonópolis', 5107602),
(11, 'Sinop', 5107909),
(11, 'Tangará da Serra', 5107958),

-- Mato Grosso do Sul
(12, 'Campo Grande', 5002704),
(12, 'Dourados', 5003702),
(12, 'Três Lagoas', 5008305),
(12, 'Corumbá', 5003207),
(12, 'Ponta Porã', 5006606),

-- Minas Gerais
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

-- Pará
(14, 'Belém', 1501402),
(14, 'Ananindeua', 1500800),
(14, 'Santarém', 1506807),
(14, 'Marabá', 1504208),
(14, 'Parauapebas', 1505494),

-- Paraíba
(15, 'João Pessoa', 2507507),
(15, 'Campina Grande', 2504009),
(15, 'Santa Rita', 2513703),
(15, 'Patos', 2510808),
(15, 'Bayeux', 2501807),

-- Paraná
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

-- Pernambuco
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

-- Piauí
(18, 'Teresina', 2211001),
(18, 'Parnaíba', 2207702),
(18, 'Picos', 2208007),
(18, 'Piripiri', 2208403),
(18, 'Campo Maior', 2202208),

-- Rio de Janeiro
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

-- Rio Grande do Norte
(20, 'Natal', 2408102),
(20, 'Mossoró', 2408003),
(20, 'Parnamirim', 2403251),
(20, 'São Gonçalo do Amarante', 2412005),
(20, 'Macaíba', 2407104),

-- Rio Grande do Sul
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

-- Rondônia
(22, 'Porto Velho', 1100205),
(22, 'Ji-Paraná', 1100122),
(22, 'Ariquemes', 1100023),
(22, 'Vilhena', 1100304),
(22, 'Cacoal', 1100049),

-- Roraima
(23, 'Boa Vista', 1400100),
(23, 'Rorainópolis', 1400472),
(23, 'Caracaraí', 1400209),
(23, 'Alto Alegre', 1400050),
(23, 'Mucajaí', 1400308),

-- Santa Catarina
(24, 'Florianópolis', 4205407),
(24, 'Joinville', 4209102),
(24, 'Blumenau', 4202404),
(24, 'São José', 4216602),
(24, 'Criciúma', 4204608),
(24, 'Chapecó', 4204202),
(24, 'Itajaí', 4208203),
(24, 'Lages', 4209102),
(24, 'Jaraguá do Sul', 4208906),
(24, 'Palhoça', 4211900),

-- São Paulo
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

-- Sergipe
(26, 'Aracaju', 2800308),
(26, 'Nossa Senhora do Socorro', 2804805),
(26, 'Lagarto', 2803500),
(26, 'Itabaiana', 2802908),
(26, 'São Cristóvão', 2806701),

-- Tocantins
(27, 'Palmas', 1721000),
(27, 'Araguaína', 1702109),
(27, 'Gurupi', 1709500),
(27, 'Porto Nacional', 1718204),
(27, 'Paraíso do Tocantins', 1716109);

