CREATE TABLE IF NOT EXISTS fornecedores (
    idFornecedores INT NOT NULL AUTO_INCREMENT,
    nomeFornecedor VARCHAR(255) NOT NULL,
    cnpj VARCHAR(20) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    celular VARCHAR(20) NULL,
    email VARCHAR(100) NOT NULL,
    rua VARCHAR(70) NULL,
    numero VARCHAR(15) NULL,
    bairro VARCHAR(45) NULL,
    cidade VARCHAR(45) NULL,
    estado VARCHAR(20) NULL,
    cep VARCHAR(20) NULL,
    fornecedor TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (idFornecedores)
) ENGINE=InnoDB; 